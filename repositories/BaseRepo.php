<?php
require_once '../config/Database.php';
require_once dirname(__DIR__) . "/interfaces/IRepository.php";

abstract class BaseRepo implements IRepository
{
    protected PDO $db;
    protected $table;
    protected $model;
    protected $log;

    // nếu không truyền vào contructor thì sẽ là null => không có
    public function __construct($table = null, $model = null)
    {
        $this->table = $table;
        $this->model = $model;

        $this->log = Helper::getInstance();
        
        $this->db = Database::getInstance()->getConnection();
    }

    // public function logError($message)
    // {
    //     $logFile = __DIR__ . '/../logs/error.log';
    //     file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $message" . PHP_EOL, FILE_APPEND);
    // }

    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id AND del_flag = 0");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            // return $data ?: null;
            return $data ? $data : null;

        } catch (PDOException $e) {
            $this->log->logError(DATABASE_ERR . $e->getMessage());
            
            throw new Exception(SYSTEM_ERR . $e->getMessage());
        } catch (Throwable $e) {
            $this->log->logError(SYSTEM_ERR . $e->getMessage());
        }
    }

    public function fetchAll()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE del_flag = 0");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->log->logError(DATABASE_ERR . $e->getMessage());
            throw new Exception(SYSTEM_ERR . $e->getMessage());
        } catch (Throwable $e) {
            $this->log->logError(SYSTEM_ERR . $e->getMessage());
        }
    }

    public function create(array $data): bool
    {
        try {
            $columns = implode(", ", array_keys($data));
            $values  = ":" . implode(", :", array_keys($data));

            $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$values})");

            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            return $stmt->execute();

        } catch (PDOException $e) {
            $this->log->logError(DATABASE_ERR . $e->getMessage());
            // throw new Exception(SYSTEM_ERR . $e->getMessage());
            return false;
        } catch (Throwable $e) {
            $this->log->logError(SYSTEM_ERR . $e->getMessage());
            return false;
        }
    }

    public function update($id, array $data): bool
    {
        try {
            // Lọc dữ liệu rỗng hoặc null
            $filteredData = array_filter($data, fn($value) => ! is_null($value) && ! (is_string($value) && $value === ''));

            // Tạo danh sách fields chỉ với các giá trị hợp lệ
            $fields = implode(", ", array_map(fn($key) => "{$key} = :{$key}", array_keys($filteredData)));

            $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE id = :id");

            foreach ($filteredData as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            $this->log->logError(DATABASE_ERR . $e->getMessage());
            // throw new Exception(SYSTEM_ERR . $e->getMessage());
            return false;
        } catch (Throwable $e) {
            $this->log->logError(SYSTEM_ERR . $e->getMessage());
            return false;

        }
    }

    public function delete($id): bool
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET del_flag = 1 WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->log->logError(DATABASE_ERR . $e->getMessage());
            throw new Exception(SYSTEM_ERR . $e->getMessage());
        }
        catch (Throwable $e) {
            $this->log->logError(SYSTEM_ERR . $e->getMessage());
            return false;
        }
    }

    // check existed email when create new
    public function checkExistedEmail(string $email): bool
    {
        try {
            $sql = "SELECT EXISTS (
                    -- SELECT 1 FROM users WHERE email = :email AND del_flag = 0
                    SELECT 1 FROM users WHERE email = :email

                )
                OR EXISTS (
                    -- SELECT 1 FROM admins WHERE email = :email AND del_flag = 0
                    SELECT 1 FROM admins WHERE email = :email
                )";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            return (bool) $stmt->fetchColumn(); // Trả về true nếu email tồn tại ở một trong hai bảng
        } catch (PDOException $e) {
            $this->log->logError(DATABASE_ERR . $e->getMessage());
            // throw new Exception(SYSTEM_ERR . $e->getMessage());
            return false;
        } catch (Throwable $e) {
            $this->log->logError(SYSTEM_ERR . $e->getMessage());
            return false;
        }
    }

    // check existed email when update data
    // kiểm tra email tồn tại trong database chưa, ngoại trừ id hiện tại
    public function isEmailAvailable(string $email, int $id, string $table): bool
    {
        try {

            if ($table === 'admin') {
                $sql = "SELECT NOT EXISTS (
                SELECT 1 FROM users WHERE email = :email
            ) AND NOT EXISTS (
                SELECT 1 FROM admins WHERE email = :email AND id != :id
            )";
            } else {
                $sql = "SELECT NOT EXISTS (
                SELECT 1 FROM users WHERE email = :email AND id != :id
            ) AND NOT EXISTS (
                SELECT 1 FROM admins WHERE email = :email
            )";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return (bool) $stmt->fetchColumn(); // Trả về true nếu email hợp lệ
        } catch (PDOException $e) {
            $this->log->logError(DATABASE_ERR . $e->getMessage());
            // throw new Exception(SYSTEM_ERR . $e->getMessage());
            return false;
        } catch (Throwable $e) {
            $this->log->logError(SYSTEM_ERR . $e->getMessage());
            return false;
        }
    }

    // pagination và sort/order

    // private function sanitizeSortOrder($sort, $order)
    // {
    //     $allowedColumns = ['id', 'name', 'status', 'role_type'];
    //     if (! in_array($sort, $allowedColumns)) {
    //         $sort = 'id';
    //     }
    //     $order = ($order === 'desc') ? 'DESC' : 'ASC';

    //     return [$sort, $order];
    // }

    // lấy danh sách có sắp xếp

    public function fetchAllWithSort($sort, $order)
    {
        try {

            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE del_flag = 0 ORDER BY $sort COLLATE utf8mb4_unicode_ci $order");
            $stmt->execute();

            // $result = $stmt->fetchAll();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // trả về cái này không tối ưu
            // return array_map(fn($data) => new $this->model($data), $results);

        } catch (PDOException $e) {
            $this->log->logError(DATABASE_ERR . $e->getMessage());
            throw new Exception(SYSTEM_ERR . $e->getMessage());
        } catch (Throwable $e) {
            $this->log->logError(SYSTEM_ERR . $e->getMessage());
        }
    }

    // Lấy danh sách có phân trang
    public function fetchAllWithPagination($limit, $offset)
    {
        try {
            $sql  = "SELECT * FROM {$this->table} WHERE del_flag = 0 LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->log->logError(DATABASE_ERR . $e->getMessage());
            throw new Exception(SYSTEM_ERR . $e->getMessage());
        } catch (Throwable $e) {
            $this->log->logError(SYSTEM_ERR . $e->getMessage());
        }
    }

    // Kết hợp vừa sắp xếp vừa phân trang
    private function columnExists($table, $column)
    {
        $stmt = $this->db->prepare("SHOW COLUMNS FROM {$table} LIKE :column");
        $stmt->bindValue(':column', $column, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }

    public function fetchAllWithSortAndPagination($sort, $order, $limit, $offset)
    {
        try {
            $table        = $this->table;
            $allowedOrder = ['ASC', 'DESC'];

            if (! $this->columnExists($table, $sort)) {
                $sort = 'id'; // Nếu cột không tồn tại, mặc định sắp xếp theo ID
            }

            if (! in_array(strtoupper($order), $allowedOrder)) {
                $order = 'ASC';
            }

            // Chỉ cho phép các bảng hợp lệ
            $allowedTables = ['admins', 'users'];
            if (! in_array($table, $allowedTables)) {
                throw new Exception("Bảng không hợp lệ");
            }

            $sql  = "SELECT * FROM {$table} WHERE del_flag = 0 ORDER BY $sort $order LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->log->logError(DATABASE_ERR . $e->getMessage());
            throw new Exception(SYSTEM_ERR . $e->getMessage());
        } catch (Throwable $e) {
            $this->log->logError(SYSTEM_ERR . $e->getMessage());
        }
    }

    // Tìm kiếm theo tên và email có phân trang và sắp xếp
    public function searchByNameOrEmailWithPaginationAndSort($name, $email, $limit, $offset, $sortBy, $sortOrder)
    {
        $table         = $this->table;
        $allowedTables = [
            'admins' => ['id', 'name', 'email'],
            'users'  => ['id', 'name', 'email'],
        ];

        if (! array_key_exists($table, $allowedTables)) {
            throw new Exception(DATABASE_ERR . INVALID_DB);
        }

        // Lấy danh sách cột hợp lệ theo bảng
        $validSortColumns = $allowedTables[$table];

        // Kiểm tra cột sắp xếp hợp lệ
        if (! in_array($sortBy, $validSortColumns)) {
            $sortBy = $validSortColumns[0]; // Cột mặc định (thường là ID)
        }

        $allowedOrder = ['ASC', 'DESC'];
        if (! in_array(strtoupper($sortOrder), $allowedOrder)) {
            $sortOrder = 'ASC';
        }

        $query  = "SELECT * FROM {$table} WHERE del_flag = 0";
        $params = [];

        if (! empty($name)) {
            $query .= " AND name LIKE :name";
            $params[':name'] = "%$name%";
        }

        if (! empty($email)) {
            $query .= " AND email LIKE :email";
            $params[':email'] = "%$email%";
        }

        $query .= " ORDER BY $sortBy $sortOrder LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tổng số để tính số trang
    public function getTotal()
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE del_flag = 0";
        return $this->db->query($sql)->fetchColumn();
    }

    // Lấy tổng số tìm kiếm để tính số trang cho tìm kiếm
    public function getTotalByNameOrEmail($name, $email)
    {
        $query  = "SELECT COUNT(*) as total FROM {$this->table} WHERE del_flag = 0";
        $params = [];

        if (! empty($name)) {
            $query .= " AND name LIKE :name";
            $params[':name'] = "%$name%";
        }

        if (! empty($email)) {
            $query .= " AND email LIKE :email";
            $params[':email'] = "%$email%";
        }

        $stmt = $this->db->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

}
