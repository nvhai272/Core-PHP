<?php
require_once __DIR__ . '/ReadEnv.php';
class Database
{
    private static ?Database $instance = null;
    private ?PDO $conn                 = null;

    private function __construct()
    {
        ReadEnv::loadEnv(__DIR__ . '/../.env');

        // sẽ trả về false và sẽ lỗi nếu không xử lí
        $host = getenv('DB_HOST');

        // hàm tự định nghĩa trong Env class có thể trả về null nếu không có biến môi trường cần tìm
         // Env::get('DB_HOST','localhost');
        // hoặc trả về giá trị theo mong muốn ví dụ như localhost nếu không tìm thấy giá trị DB_HOST
       

        $dbname   = getenv('DB_NAME');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASS');

        try {
            $this->conn = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("❌ Lỗi kết nối DB: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): ?PDO
    {
        return $this->conn;
    }

    // public function fetchAll($sql, $params = [])
    // {
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->execute($params);
    //     return $stmt->fetchAll();
    // }

    // public function fetch($sql, $params = [])
    // {
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->execute($params);
    //     return $stmt->fetch();
    // }

    // public function execute($sql, $params = [])
    // {
    //     $stmt = $this->conn->prepare($sql);
    //     return $stmt->execute($params);
    // }

}

// Sử dụng:
// $db = Database::getInstance();
// $conn = $db->getConnection();
// có thể gọi hàm getConnect thông qua hàm static getInstance
// vì nó trả về đối tượng của class nên có thể gọi được hàm kp static
?>