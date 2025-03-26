<?php
require_once __DIR__ . '/BaseRepo.php';
require_once  '../models/User.php';

class UserRepo extends BaseRepo
{
    public function __construct()
    {
        parent::__construct("users", User::class);
    }

    // public function findByFacebookId(string $facebook_id)
    // {
    //     try {

    //         $stmt = $this->db->prepare(
    //             "SELECT id, status, del_flag FROM users WHERE facebook_id = :facebook_id LIMIT 1"
    //         );

    //         $stmt->execute([
    //             'facebook_id' => $facebook_id,
    //         ]);
    //         $data = $stmt->fetch(PDO::FETCH_ASSOC);
    //         // print_r($data);
    //         // exit;
    //         return $data ?: null;
    //     } catch (Throwable $e) {
    //         echo $e->getMessage();
    //         return null;
    //     }
    // }
}
