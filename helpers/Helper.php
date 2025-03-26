<?php
class Helper
{
    private static $instance = null;

    // Ngăn chặn tạo mới từ bên ngoài
    private function __construct()
    {}

    // Ngăn chặn clone instance
    private function __clone()
    {}

    // Ngăn chặn unserialize
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function logError($message)
    {
        $logFile = __DIR__ . '/../logs/error.log';
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $message" . PHP_EOL, FILE_APPEND);
    }

    public function validateRequired($value)
    {
        // if ($value !== trim($value)) {
        //     return false;
        // }

        if (empty(trim($value))) {
            return false;
        }
        return true;
    }

    public function validateEmail($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    public function validatePassword($password, $minLength = 6)
    {
        if (strlen($password) < $minLength) {
            return false;
        }
        return true;
    }

    public function validateImageFile(array $file): bool
    {
        if (empty($file) || ! isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Kiểm tra MIME type bằng fileinfo
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        // Các định dạng ảnh hợp lệ
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        return in_array($mimeType, $allowedMimeTypes, true);
    }

    public function saveAvatar($fileName)
    {
        // chuyển từ thư mục lưu tạm sang thư mục chính
        $sourceDir = dirname(__DIR__, 1) . '/public/uploads/images/tmp/avatar/';
        $targetDir = dirname(__DIR__, 1) . '/public/uploads/images/avatar/';
        if (! file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $finalPath = $targetDir . $fileName;
        if (rename($sourceDir . $fileName, $finalPath)) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteAvatar($fileName)
    {
        $uploadDir = __DIR__ . '/../public/uploads/images/avatar/';
        $filePath  = $uploadDir . $fileName;

        // Kiểm tra nếu tệp tồn tại
        if (file_exists($filePath)) {
            // Xóa tệp
            if (unlink($filePath)) {
                return true;
            }
        }

        return false;
    }

    public function generateFileName($file)
    {
        // var_dump($file);
        // exit;
        // Kiểm tra nếu file không tồn tại hoặc không có tên
        if (empty($file) || empty($file['name'])) {
            return null;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        // Nếu không có phần mở rộng (người dùng gửi file lỗi), trả về null
        if (empty($extension)) {
            return null;
        }

        return time() . '_' . uniqid() . '.' . $extension;
    }
}