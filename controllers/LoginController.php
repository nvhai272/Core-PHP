<?php
require_once __DIR__ . "/../services/AuthService.php";
class LoginController
{
    private $authService;
    public function __construct()
    {
        $this->authService = new AuthService();
    }
    public function index()
    {
        require_once __DIR__ . '/../views/layout/login.php';
    }

    public function login()
    {
//         ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

        // session_start();

        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        $response = $this->authService->login($email, $password);

        if (! $response->isSuccess()) {
            $_SESSION['errors']    = $response->getErrors();
            $_SESSION['old_input'] = ['email' => $email];

            // var_dump($_SESSION['errors'] );
            // var_dump(  $_SESSION['old_input'] );
            // exit;
            header('Location: /login');
            exit;
        }

        $_SESSION['account'] = $response->getData();

        if ($remember) {
            setcookie('remember_email', $email, time() + (30 * 24 * 60 * 60), "/"); // 30 ngày
        } else {
            setcookie('remember_email', '', time() - 3600, "/"); // Xóa cookie nếu không chọn
        }

        header('Location: /');
    }

    public function logout()
    {
        $this->authService->logout();
        header('Location: /login');
        exit;
    }

}