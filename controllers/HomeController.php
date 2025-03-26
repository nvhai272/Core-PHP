<?php
// session_start();

class HomeController
{

    public function home()
    {
            require_once __DIR__ . '/../views/layout/home.php';
            exit;
    }
    public function index()
    {
        $role = $_SESSION['account']['role'] ?? 'guest'; 

        if ($role === 'guest') {
            require_once __DIR__ . '/../views/layout/home.php';
            exit;
        }

        header('Location: ' . ($role === 'admin' || $role ==='super_admin' ? '/' : '/user/profile'));
        exit;
    }

}
