<?php
require_once '../config/Const.php';
require_once '../services/AdminService.php';
require_once '../services/UserService.php';
require_once '../helpers/Helper.php';
require_once '../dto/NewUser.php';
class AdminController
{
    private $userService;
    private $adminService;
    public function __construct()
    {
        // Dùng Singleton
        $fileHelper = Helper::getInstance();

        // DI
        $adminRepo          = new AdminRepo();
        $userRepo           = new UserRepo();
        $this->userService  = new UserService($fileHelper, $userRepo);
        $this->adminService = new AdminService($fileHelper, $adminRepo);
    }

    public function showAllAdmin(): void
    {
        $sort  = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'asc';

        $page       = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $limit      = $_GET['limit'] ?? 5; // Số bản ghi trên mỗi trang
        $offset     = ($page - 1) * $limit;
        $totalRows  = $this->adminService->getTotal();
        $totalPages = ceil($totalRows / $limit);
        $data       = $this->adminService->getAll($sort, $order, $limit, $offset);

        $danhSachDuLieu = 'admin';

        // echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        // exit;

        include '../views/admin/list.php';
    }

    public function showDetailAdmin(): void
    {
        $admin   = [];
        $adminId = $_GET['id'] ?? $_SESSION['account']['id'] ?? null;

        if (! is_numeric($adminId) || $adminId <= 0) {
            header("Location: /admin/list-admin?message=" . urlencode(INVALID_ID));
            exit;
        }

        $admin = $this->adminService->getById($adminId);

        if (empty($admin)) {
            header("Location: /admin/list-admin?message=" . urlencode(NOT_FOUND));
            exit;
        }

        include '../views/admin/details-admin.php';
    }

    public function deleteAdmin(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
                $id = $_POST['id'];

                if (! is_numeric($id) || $id <= 0) {
                    header("Location: /admin/list-admin?message=" . urlencode(INVALID_ID));
                    exit;
                }

                $result = $this->adminService->delete($id);

                if ($result) {
                    header("Location: /admin/list-admin?&message=" . urlencode(DELETE_SUCCESS));
                    exit();
                }
                header("Location: /admin/list-admin?&message=" . urlencode(ACTION_ERROR));
                exit();
            }
        } catch (Exception $e) {
            header("Location: /admin/list-admin?&message=" . urlencode($e->getMessage()));
            exit();
        }
    }

    public function searchAdmin(): void
    {
        $page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        $page  = max($page, 1);
        $limit = 5;

        $name  = isset($_GET['name']) ? trim($_GET['name']) : '';
        $email = isset($_GET['email']) ? trim($_GET['email']) : '';

        $sortBy = isset($_GET['sortBy']) ? trim($_GET['sortBy']) : 'id';

        $sortOrder = isset($_GET['sortOrder']) && strtoupper($_GET['sortOrder']) === 'ASC' ? 'ASC' : 'DESC';

        $offset = ($page - 1) * $limit;

        $error = '';
        $data  = [];
        // $currentPage = $page;

        try {
            if (isset($_GET['search']) && $name === '' && $email === '') {
                $error        = INVALID_INPUT_SEARCH;
                $data         = $this->adminService->getAll($sortBy, $sortOrder, $limit, $offset);
                $totalRecords = $this->adminService->getTotal();
                $totalPages   = max(ceil($totalRecords / $limit), 1);

            } else {
                if ($name !== '' || $email !== '') {
                    $result       = $this->adminService->searchByNameOrEmailWithPaginationAndSort($name, $email, $limit, $page, $sortBy, $sortOrder);
                    $totalRecords = $this->adminService->getTotalSearch($name, $email);
                    $data         = $result ?? [];
                } else {
                    // Nếu không nhấn search, vẫn lấy danh sách bình thường
                    $data         = $this->adminService->getAll($sortBy, $sortOrder, $limit, $offset);
                    $totalRecords = $this->adminService->getTotal();
                }

                $totalPages = max(ceil($totalRecords / $limit), 1);
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        include '../views/admin/search.php';
    }

    public function showEditAdmin(): void
    {
        // // Kiểm tra referrer (nếu có) để xác định trang đến trước đó
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        $refPath  = parse_url($referrer, PHP_URL_PATH);

        // Nếu referrer không phải là trang create-admin, xóa các biến session liên quan
        if ($refPath !== '/admin/edit-admin') {
            unset($_SESSION['old_data_update'], $_SESSION['errors'], );
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            die(INVALID_ID);
        }

        $id = $_GET['id'];

        try {
            $admin = $this->adminService->getById($id);

            if (! $admin) {

                header("Location: /admin/list-admin?&message=" . urlencode(NOT_FOUND));
                exit;
            }
        } catch (Exception $e) {
            header("Location: /admin/list-admin?&message=" . urlencode($e->getMessage()));
            exit;
        }
        // unset($_SESSION['errors'], $_SESSION['old_data_update']);
        // var_dump($admin);
        include '../views/admin/edit.php';
    }

    public function editAdmin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id'        => $_POST['id'],
                'name'      => $_POST['name'] ?? '',
                'email'     => $_POST['email'] ?? '',
                'password'  => $_POST['password'] ?? '',
                'role_type' => $_POST['role_type'],

                //  'current_ava' => $_POST['current_ava'] ?? '',

            ];

            try {
                $result = $this->adminService->updateAdmin($data);
// var_dump($result);
// exit;
                if ($result->isSuccess()) {
                    unset($_SESSION['old_data_update']);
                    header("Location: /admin/list-admin?message=" . urlencode(UPDATE_SUCCESS));
                    exit;
                }

                // Lưu lỗi và data cũ vào session để hiển thị cho người dùng
                $_SESSION['errors_update']   = $result->getErrors();
                $_SESSION['old_data_update'] = $result->getData();

                // var_dump($_SESSION['old_data_update']);
                // exit;

                header("Location: /admin/edit-admin?id=" . $_POST['id']);
                exit;
            } catch (Exception $e) {
                // die($e->getMessage());
                // $_SESSION['old_data_update'] = $result->getData();
                header("Location: /admin/edit-admin?id=" . $_POST['id']);
                exit;
            }
        }
    }

    public function showCreatePageAdmin(): void
    {
        session_start();

        // Kiểm tra referrer (nếu có) để xác định trang đến trước đó
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        $refPath  = parse_url($referrer, PHP_URL_PATH);

        // Nếu referrer không phải là trang create-admin, xóa các biến session liên quan
        if ($refPath !== '/admin/create-admin') {
            unset($_SESSION['old_data'], $_SESSION['errors'], $_SESSION['uploaded_avatar']);
        }

        if (isset($_GET['resetAvatar'])) {
            unset($_SESSION['old_data']);
            unset($_SESSION['errors']);
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
            exit();
        }

        if (! empty($_SESSION['errors']) || ! empty($_SESSION['old_data'])) {
            $errors   = $_SESSION['errors'];
            $old_data = $_SESSION['old_data'];
            extract(['errors' => $errors, 'old_data' => $old_data]);
        }
        require __DIR__ . '/../views/admin/create.php';
    }

    public function createAdmin(): void
    {
        session_start();
        try {
            $data   = $_POST;
            $result = $this->adminService->createAdmin($data);

            if (! empty($result->getErrors())) {
                $_SESSION['errors']   = $result->getErrors();
                $_SESSION['old_data'] = $result->getData();
                // var_dump($result->getData());
                // exit;
                header("Location: /admin/create-admin");
                exit();
            }
            header("Location: /admin/list-admin?message=" . urlencode(CREATE_SUCCESS));
            exit();
        } catch (Exception $e) {
            // die($e->getMessage());
            $_SESSION['old_data'] = $data;
            header("Location: /admin/create-admin");
            exit();
        }
    }

    // User
    public function showAllUser(): void
    {
        $sort  = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'asc';

        $page           = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $limit          = $_GET['limit'] ?? 5;
        $offset         = ($page - 1) * $limit;
        $totalRows      = $this->userService->getTotal();
        $totalPages     = ceil($totalRows / $limit);
        $data           = $this->userService->getAll($sort, $order, $limit, $offset);
        $danhSachDuLieu = 'user';
        include '../views/admin/list.php';
    }

    public function showDetailUser(): void
    {
        $user   = [];
        $userId = $_GET['id'] ?? null;

        if (! is_numeric($userId) || $userId <= 0) {
            header("Location: /admin/list-user?message=" . urlencode(INVALID_ID));
            exit;
        }

        $u = $this->userService->getById($userId);

        if (empty($u)) {
            header("Location: /admin/list-user?message=" . urlencode(NOT_FOUND));
            exit;
        }
        include '../views/user/details.php';
    }

    public function deleteUser(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
                $id = $_POST['id'];

                if (! is_numeric($id) || $id <= 0) {
                    header("Location: /admin/list-user?message=" . urlencode(INVALID_ID));
                    exit;
                }

                $result = $this->userService->delete($id);

                if ($result) {
                    header("Location: /admin/list-user?&message=" . urlencode(DELETE_SUCCESS));
                    exit();
                }
                header("Location: /admin/list-user?&message=" . urlencode(SYSTEM_ERR));
                exit();
            }
        } catch (Exception $e) {
            header("Location: /admin/list-user?&message=" . urlencode($e->getMessage()));
            exit();
        }
    }

    public function searchUser(): void
    {
        $page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        $page  = max($page, 1);
        $limit = 5;

        $name  = isset($_GET['name']) ? trim($_GET['name']) : '';
        $email = isset($_GET['email']) ? trim($_GET['email']) : '';

        $sortBy = isset($_GET['sortBy']) ? trim($_GET['sortBy']) : 'id';

        $sortOrder = isset($_GET['sortOrder']) && strtoupper($_GET['sortOrder']) === 'ASC' ? 'ASC' : 'DESC';

        $offset = ($page - 1) * $limit;

        $error = '';
        $data  = [];
        // $currentPage = $page;

        try {
            if (isset($_GET['search']) && $name === '' && $email === '') {
                $error        = INVALID_INPUT_SEARCH;
                $data         = $this->userService->getAll($sortBy, $sortOrder, $limit, $offset);
                $totalRecords = $this->userService->getTotal();
                $totalPages   = max(ceil($totalRecords / $limit), 1);

            } else {
                if ($name !== '' || $email !== '') {
                    $result       = $this->userService->searchByNameOrEmailWithPaginationAndSort($name, $email, $limit, $page, $sortBy, $sortOrder);
                    $totalRecords = $this->userService->getTotalSearch($name, $email);
                    $data         = $result ?? [];
                } else {
                    // Nếu không nhấn search, vẫn lấy danh sách bình thường
                    $data         = $this->userService->getAll($sortBy, $sortOrder, $limit, $offset);
                    $totalRecords = $this->userService->getTotal();
                }

                $totalPages = max(ceil($totalRecords / $limit), 1);
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        include '../views/user/search.php';
    }

    public function showEditUser(): void
    {
        // // Kiểm tra referrer (nếu có) để xác định trang đến trước đó
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        $refPath  = parse_url($referrer, PHP_URL_PATH);

        // Nếu referrer không phải là trang create-admin, xóa các biến session liên quan
        if ($refPath !== '/admin/edit-user') {
            unset($_SESSION['old_data_update'], $_SESSION['errors'], );
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            die(INVALID_ID);
        }

        $id = $_GET['id'];

        try {
            $admin = $this->userService->getById($id);

            if (! $admin) {

                header("Location: /admin/list-user?&message=" . urlencode(NOT_FOUND));
                exit;
            }
        } catch (Exception $e) {
            header("Location: /admin/list-user?&message=" . urlencode($e->getMessage()));
            exit;
        }
        // unset($_SESSION['errors'], $_SESSION['old_data_update']);
        // var_dump($admin);
        include '../views/user/edit.php';
    }

    public function editUser(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id'          => $_POST['id'],
                'name'        => $_POST['name'] ?? '',
                'email'       => $_POST['email'] ?? '',
                'password'    => $_POST['password'] ?? '',
                'status'      => $_POST['status'],
                'facebook_id' => $_POST['facebook_id'] ?? '',
            ];

            try {
                $result = $this->userService->updateUser($data);
// var_dump($result);
// exit;

                if ($result->isSuccess()) {
                    unset($_SESSION['old_data_update']);
                    $send = 'Updated success!';
                    header("Location: /admin/list-user?message=$send");
                    exit;
                }

                // Lưu lỗi và data cũ vào session để hiển thị cho người dùng
                $_SESSION['errors_update']   = $result->getErrors();
                $_SESSION['old_data_update'] = $result->getData();

                header("Location: /admin/edit-user?id=" . $_POST['id']);
                exit;
            } catch (Exception $e) {
                $_SESSION['errors_update']   = ['general' => $e->getMessage()];
                $_SESSION['old_data_update'] = $result->getData();
                header("Location: /admin/edit-user?id=" . $_POST['id']);
                exit;
            }
        }
    }

    public function showCreateUser(): void
    {
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        
        

        // Kiểm tra referrer (nếu có) để xác định trang đến trước đó
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        $refPath  = parse_url($referrer, PHP_URL_PATH);

        // Nếu referrer không phải là trang create-admin, xóa các biến session liên quan
        if ($refPath !== '/admin/create-user') {
            unset($_SESSION['old_data'], $_SESSION['errors'], $_SESSION['uploaded_avatar']);
        }

        if (isset($_GET['resetAvatar'])) {
            unset($_SESSION['old_data']);
            unset($_SESSION['errors']);
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
            exit();
        }

        if (! empty($_SESSION['errors']) || ! empty($_SESSION['old_data'])) {
            $errors          = $_SESSION['errors'];
            $old_data        = $_SESSION['old_data'];
            $uploaded_avatar = $_SESSION['uploaded_avatar'];
            // unset($_SESSION['uploaded_avatar']);
            // unset($_SESSION['errors'], $_SESSION['old_data']);
            extract(['errors' => $errors, 'old_data' => $old_data, 'uploaded_avatar' => $uploaded_avatar]);

        }

        require __DIR__ . '/../views/user/create.php';
    }

    public function createUser(): void
    {
        session_start();

        try {
            $data = $_POST;

            $result = $this->userService->createUser($data);

            if (! empty($result->getErrors())) {
                $_SESSION['errors']   = $result->getErrors();
                $_SESSION['old_data'] = $result->getData();
                // var_dump($result->getData());
                // exit;
                header("Location: /admin/create-user");
                exit();
            }

            $send = 'Created success!';
            header("Location: /admin/list-user?message=$send");
            exit();
        } catch (Exception $e) {
            $_SESSION['errors']   = ['general' => $e->getMessage()];
            $_SESSION['old_data'] = $data;
            header("Location: /admin/create-user");
            exit();
        }
    }

}
