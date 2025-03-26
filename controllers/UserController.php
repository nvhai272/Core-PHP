<?php
require_once '../config/Const.php';
require_once '../services/UserService.php';
require_once '../helpers/Helper.php';
class AdminController
{
    private $userService;
    private $adminService;
    public function __construct()
    {
        // Dùng Singleton
        $fileHelper = Helper::getInstance();

        // DI
        $userRepo           = new UserRepo();
        $this->userService  = new UserService($fileHelper, $userRepo);
    }

    public function index(){
        require_once '../views/user/details.php';
        exit;
    }   
}