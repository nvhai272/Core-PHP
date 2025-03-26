<?php
require_once __DIR__ . "/../repositories/AuthRepo.php";
 require_once __DIR__ . "/../dto/ServiceResponse.php";
 require_once __DIR__ . "/../config/Const.php";
class AuthService
{
    private $authRepo;

    public function __construct()
    {
        $this->authRepo = new AuthRepo();
    }

    public function login(string $email, string $password): ServiceResponse
    {
        try {
            $errors = [];

            if (empty(trim($email))) {
                $errors['email'] = INVALID_EMAIL;
            }
            if (empty(trim($password))) {
                $errors['password'] = INVALID_PASS;
            }

            if (! empty($errors)) {
                return new ServiceResponse(false, $errors);
            }

            $user = $this->authRepo->findByEmail($email);
            if (! $user) {
                return new ServiceResponse(false, ['email' => NO_EXISTED_EMAIL]);
            }

            if (! password_verify($password, $user['password'])) {
                return new ServiceResponse(false, ['password' => WRONG_PASSWORD]);
            }

            $role = match (true) {
                $user["source"] === "admins" && $user['role_type'] == 1 => 'super_admin',
                $user["source"] === "admins" && $user['role_type'] == 2 => 'admin',
                default => 'user',
            };
            
            $data = ['id' => $user['id'], 'role' => $role];

            return new ServiceResponse(true, [], $data);
        } catch (PDOException $e) {
            error_log(DATABASE_ERR . $e->getMessage());
            return new ServiceResponse(false, ['database' => DATABASE_ERR]);
        } catch (Throwable $e) {
            error_log(SYSTEM_ERR . $e->getMessage());
            return new ServiceResponse(false, ['system' => SYSTEM_ERR]);
        }
    }

    public function loginWithFB(string $email, string $fb_id)
    {
        $user = $this->authRepo->findByEmail($email);
        if (! $user) {
            return new ServiceResponse(false, ['email' => NO_EXISTED_EMAIL]);
        }

        // // check id = nhau
        // if ($fb_id !== $user['facebook_id']) {
        //     return new ServiceResponse(false, ['fb_err' => 'Login fail']);
        // }

        $role = match (true) {
            $user["source"] === "admins" && $user['role_type'] == 1 => 'super_admin',
            $user["source"] === "admins" && $user['role_type'] == 2 => 'admin',
            default => 'user',
        };
        
        $data = ['id' => $user['id'], 'role' => $role];

        return new ServiceResponse(true, [], $data);
    }

    public function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();

    }

}