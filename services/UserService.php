<?php
require_once __DIR__ . "/BaseService.php";
require_once __DIR__ . '/../repositories/UserRepo.php';
require_once __DIR__ . '/../helpers/Helper.php';
require_once __DIR__ . '/../dto/ServiceResponse.php';
class UserService extends BaseService
{
    private UserRepo $userRepo;

    public function __construct(Helper $fileHelper, UserRepo $userRepo)
    {
        parent::__construct($fileHelper, $userRepo);
        $this->userRepo = $userRepo;
    }

    public function createUser(array $data)
    {
        $loiAnh         = '';
        $avatarFileName = null;

        if (! empty($_FILES['fileAvatar']['tmp_name'])) {

            if (! $this->fileHelper->validateImageFile($_FILES['fileAvatar'])) {
                $loiAnh = ERR_FILE_UP;
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
                    // ghi log
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

        $errors = $this->validateNewUser($data, $loiAnh);
        if (! empty($errors)) {

            return new ServiceResponse(false, $errors, $data);
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            $user = User::fromNewUser($data);
            $user->setPassword($hashedPassword);
            if ($avatarFileName) {
                $user->setAvatar($avatarFileName);
            }

            $userD = $user->toArray();
            $newU  = $this->userRepo->create($userD);

            if ($newU) {
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
                    return new ServiceResponse(false, ['avatar' => "Không chuyển được ảnh sang thư mục chính: "], $data);

                }
            }
            return new ServiceResponse(true, [], $newU);
        } catch (Exception $e) {
            return new ServiceResponse(false, ['exception' => "Failed to create user: " . $e->getMessage()]);
        }
    }

    private function validateNewUser(array $data, $loiAnh): array
    {
        $errors = [];

        if (! $this->fileHelper->validateRequired($data['name'] ?? '')) {
            $errors['name'] = "Name is required.";
        }

        if (! $this->fileHelper->validateRequired($data['email'] ?? '')) {
            $errors['email'] = "Email is required.";
        } elseif (! $this->fileHelper->validateEmail($data['email'])) {
            $errors['email'] = "Invalid email format.";
        } elseif ($this->userRepo->checkExistedEmail($data['email'])) {
            $errors['email'] = "Email already exists";
        }

        // if (! $this->fileHelper->validateRequired($data['password'] ?? '')) {
        //     $errors['password'] = "Password is required.";
        // } elseif (! $this->fileHelper->validatePassword($data['password'], 6)) {
        //     $errors['password'] = "Password must be at least 6 characters.";
        // }

        if (! $this->fileHelper->validateRequired($data['status'] ?? '')) {
            $errors['status'] = "Status is required.";
        }

        if (! empty($loiAnh)) {
            $errors['avatar'] = $loiAnh;

        } else {

            if (empty($data['avatar'])) {
                $errors['avatar'] = "Avatar is required";
            }
        }

        return $errors;
    }

    public function updateUser(array $data)
    {
        $avatarName = '';
        $loiAnh     = '';
        if (! empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (isset($_FILES['uploadFileAvatar']) && $_FILES['uploadFileAvatar']['error'] === UPLOAD_ERR_OK) {
            if (! $this->fileHelper->validateImageFile($_FILES['uploadFileAvatar'])) {
                $loiAnh = "File tải lên không hợp lệ nên vẫn dùng ảnh cũ";
            } else {

                $avatarName = $this->fileHelper->generateFileName($_FILES['uploadFileAvatar']);
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
        } else {
            $avatarName = $_SESSION['old_data_update']['newAvatar'];
        }

        // var_dump($loiAnh);
        // exit;
        // $data['newName'] = $avatarName;
        $errs = self::validateUserUpdate($data, $loiAnh);

        $data['newAvatar'] = $avatarName;
        if (! empty($errs)) {
            return new ServiceResponse(false, $errs, $data);
        }

        $dataUpdate = [
            'id'           => $data['id'],
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => $data['password'] ?? '',
            'status'       => $data['status'],
            'facebook_id'  => $data['facebook_id'],
            'avatar'       => (! empty($avatarName)) ? $avatarName : '',

            // bo sung
            'upd_datetime' => date('Y-m-d H:i:s'),
            'upd_id'       => $_SESSION['account']['id'],

        ];

        $up = $this->userRepo->update($dataUpdate['id'], $dataUpdate);

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

    private function validateUserUpdate(array $data, $loiAnh): array
    {
        $errors = [];

        if (! $this->fileHelper->validateRequired($data['name'])) {
            $errors['name'] = "Name is required";
        }

        if (! $this->fileHelper->validateRequired($data['email'])) {
            $errors['email'] = "Email is required";
        } elseif (! $this->fileHelper->validateEmail($data['email'])) {
            $errors['email'] = "Invalid email format";
        } elseif (! $this->userRepo->isEmailAvailable($data['email'], $data['id'], 'users')) {
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

        if (! $this->fileHelper->validateRequired($data['status'])) {
            $errors['status'] = "Status is required.";
        }

        return $errors;
    }

}
