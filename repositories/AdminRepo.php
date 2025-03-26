<?php
require_once __DIR__ . '/BaseRepo.php';
require_once '../models/Admin.php';

class AdminRepo extends BaseRepo
{
    public function __construct()
    {
        parent::__construct('admins', Admin::class);
    }   

}