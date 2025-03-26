<?php
// Không cần require .env ở đây vì loadEnv() đã xử lý
class ReadEnv
{
    public static function loadEnv(string $filePath = null): void
    {
        if ($filePath === null) {
            $filePath = dirname(__DIR__) . '/.env'; // lấy file .env trong thư mục gốc
        }

        if (!file_exists($filePath)) {
            throw new Exception(".env file not found at " . $filePath);
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, "#") === 0) {
                continue; // Bỏ qua comment và dòng trống
            }

            $parts = explode("=", $line, 2);
            if (count($parts) !== 2) {
                throw new Exception("Invalid .env line: " . $line);
            }

            list($key, $value) = $parts;
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'"); // Loại bỏ dấu nháy nếu có

            putenv("$key=$value"); // Lưu vào biến môi trường
            $_ENV[$key] = $value; // Lưu vào $_ENV
            $_SERVER[$key] = $value; // Lưu vào $_SERVER
        }
    }

    public static function get(string $key, $default = null)
    {
        return getenv($key) ?: ($_ENV[$key] ?? $_SERVER[$key] ?? $default);
    }
}

// Load .env khi file được gọi
ReadEnv::loadEnv();
?>
