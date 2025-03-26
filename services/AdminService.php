<?php
require_once __DIR__ . "/BaseService.php";
require_once __DIR__ . '/../repositories/AdminRepo.php';
require_once __DIR__ . '/../helpers/Helper.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../dto/ServiceResponse.php';
class AdminService extends BaseService
{
    private AdminRepo $adminRepo;

    public function __construct(Helper $fileHelper, AdminRepo $adminRepo)
    {
        parent::__construct($fileHelper, $adminRepo);
        $this->adminRepo = $adminRepo; 
    }

    public function createAdmin(array $data)
    {

        $loiAnh         = '';
        $avatarFileName = null;

        if (! empty($_FILES['fileAvatar']['tmp_name'])) {

            if (! $this->fileHelper->validateImageFile($_FILES['fileAvatar'])) {
                $loiAnh = "File tải lên không hợp lệ";
            } else {
                $avatarFileName = uniqid() . '_' . $_FILES['fileAvatar']['name'];

                $data['avatar'] = $avatarFileName;

                $targetDir = dirname(__DIR__, 1) . '/public/uploads/images/tmp/avatar/';

                if (! file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);

                }

                $tempPath = $targetDir . $avatarFileName;

                if (move_uploaded_file($_FILES['fileAvatar']['tmp_name'], $tempPath)) {

                } else {
                    return new ServiceResponse(false, ['avatar' => 'Lỗi khi lưu file ảnh tạm'], $data);
                }
            }

        } elseif (! empty($data["tenAnhCu"])) {
            $avatarFileName = $data["tenAnhCu"];
            $data['avatar'] = $avatarFileName;
        }

// var_dump($data);
// var_dump($_SESSION['uploaded_avatar']);
// exit;

        $errors = $this->validateNewAdmin($data, $loiAnh);
        if (! empty($errors)) {

            return new ServiceResponse(false, $errors, $data);
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            $admin = Admin::fromNewAdmin($data);
            $admin->setPassword($hashedPassword);
            if ($avatarFileName) {
                $admin->setAvatar($avatarFileName);
            }

            $adminData = $admin->toArray();
            $newAd     = $this->adminRepo->create($adminData);

            if ($newAd) {
                $sourceDir = dirname(__DIR__, 1) . '/public/uploads/images/tmp/avatar/';
                $targetDir = dirname(__DIR__, 1) . '/public/uploads/images/avatar/';
                if (! file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $finalPath = $targetDir . $avatarFileName;
                if (rename($sourceDir . $avatarFileName, $finalPath)) {
                    unset($_SESSION['old_data']);
                    unset($_SESSION['errors']);

                } else {
                    return new ServiceResponse(false, ['avatar' => "Không chuyển được ảnh sang thư mục chính: "],$data);

                }
            }
            return new ServiceResponse(true, [], $newAd);
        } catch (Exception $e) {
            return new ServiceResponse(false, ['exception' => "Failed to create admin: " . $e->getMessage()]);
        }
    }

    private function validateNewAdmin(array $data, $loiAnh): array
    {
        $errors = [];

        if (! $this->fileHelper->validateRequired($data['name'] ?? '')) {
            $errors['name'] = "Name is required.";
        }

        if (! $this->fileHelper->validateRequired($data['email'] ?? '')) {
            $errors['email'] = "Email is required.";
        } elseif (! $this->fileHelper->validateEmail($data['email'])) {
            $errors['email'] = "Invalid email format.";
        } elseif ($this->adminRepo->checkExistedEmail($data['email'])) {
            $errors['email'] = "Email already exists";
        }

        if (! $this->fileHelper->validateRequired($data['password'] ?? '')) {
            $errors['password'] = "Password is required.";
        } elseif (! $this->fileHelper->validatePassword($data['password'], 6)) {
            $errors['password'] = "Password must be at least 6 characters.";
        }

        if (! $this->fileHelper->validateRequired($data['role_type'] ?? '')) {
            $errors['role_type'] = "Role Admin is required.";
        }

        if (! empty($loiAnh)) {
            $errors['avatar'] = $loiAnh;

        } else {

            if (empty($data['avatar'])) {
                $errors['avatar'] = "Avatar is reqired";
            }
        }

        return $errors;
    }

    public function updateAdmin(array $data)
    {
        session_start();
        
        $avatarName = '';
        $loiAnh     = '';
        if (! empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // Kiểm tra file, nếu nó không được tải lên nó vẫn có giá trị chứ không null
        if (isset($_FILES['uploadFileAvatar']) && $_FILES['uploadFileAvatar']['error'] === UPLOAD_ERR_OK) {
            if (! $this->fileHelper->validateImageFile($_FILES['uploadFileAvatar'])) {
                $loiAnh = "File tải lên không hợp lệ nên vẫn dùng ảnh cũ";
                
            } else {

                $avatarName = $this->fileHelper->generateFileName($_FILES['uploadFileAvatar']);
                // lưu ảnh vào file tạm -> lưu tên để  nếu validate có lỗi vẫn còn tên ảnh
                $targetDir = dirname(__DIR__, 1) . '/public/uploads/images/tmp/avatar/';

                if (! file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);

                }

                $tempPath = $targetDir . $avatarName;

                if (move_uploaded_file($_FILES['uploadFileAvatar']['tmp_name'], $tempPath)) {
                    
                } else {
                    return new ServiceResponse(false, ['avatar' => 'Lỗi khi lưu file ảnh tạm'], $data);
                }
            }
        } else{
           $avatarName =$_SESSION['old_data_update']['newAvatar'];
        }

            
       $errs= self::validateAdminUpdate($data, $loiAnh);
       
       // thêm cái này để gửi ảnh về  data nếu có lỗi
        $data['newAvatar'] = $avatarName;
       // để gán tên vào session
        if (! empty($errs)) {
            return new ServiceResponse(false, $errs,$data);
        }

        $dataUpdate = [
            'id'           => $data['id'],
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => $data['password'] ?? '',
            'role_type'    => $data['role_type'],
            'avatar'       => (! empty($avatarName)) ? $avatarName : '',

            // bo sung
            'upd_datetime' => date('Y-m-d H:i:s'),
            'upd_id'       => $_SESSION['account']['id'],

        ];

        $up = $this->adminRepo->update($dataUpdate['id'], $dataUpdate);

        if ($up) {
            // Nếu cập nhật thành công và có ảnh mới, lưu ảnh và xóa ảnh cũ
            if (! empty($avatarName)) {
                $this->fileHelper->saveAvatar($avatarName);
                $this->fileHelper->deleteAvatar($data['current_ava']);
                
            }
            return new ServiceResponse(true, [], $dataUpdate);
        } else {
            return new ServiceResponse(false, ['Cập nhật thất bại, vui lòng thử lại']);
        }
    }

    private function validateAdminUpdate(array $data, $loiAnh): array
    {
        $errors = [];

        if (! $this->fileHelper->validateRequired($data['name'])) {
            $errors['name'] = "Name is required";
        }

        if (! $this->fileHelper->validateRequired($data['email'])) {
            $errors['email'] = "Email is required";
        } elseif (! $this->fileHelper->validateEmail($data['email'])) {
            $errors['email'] = "Invalid email format";
        } elseif (! $this->adminRepo->isEmailAvailable($data['email'], $data['id'], 'admin')) {
            $errors['email'] = "Email already exists";
        }

        if (! empty($data['password'])) { // Nếu password không rỗng thì mới kiểm tra độ dài
            if (! $this->fileHelper->validatePassword($data['password'], 6)) {
                $errors['password'] = "Password must be at least 6 characters";
            }
        }

        if (! empty($loiAnh)) {
            $errors['avatar'] = $loiAnh;
        }

        if (! $this->fileHelper->validateRequired($data['role_type'])) {
            $errors['role_type'] = "Role Admin is required.";
        }

        return $errors;
    }

}