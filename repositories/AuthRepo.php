<?php
require_once __DIR__ . '/BaseRepo.php';

class AuthRepo 
{
    private PDO $db;
    public function __construct()
    {
        // $this->db = new Database();
        $this->db = Database::getInstance()->getConnection();
    }
    
    // tìm kiếm email và lấy password để check đăng nhập
    public function findByEmail(string $email): ?array
    {
        try {
            $sql = "SELECT 'admins' AS source, id, password, role_type, NULL AS facebook_id
            FROM admins
            WHERE email = :email AND del_flag = 0
            UNION
            SELECT 'users' AS source, id, password, NULL AS role_type, facebook_id
            FROM users
            WHERE email = :email AND del_flag = 0
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('Lỗi database: ' . $e->getMessage());
            return null;
        }
    }

}
