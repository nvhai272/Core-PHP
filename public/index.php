<?php
// Đây là điểm khởi đầu của ứng dụng Entry point => Tải Router, load routes, lấy URL và xử lý request.

require_once __DIR__ . '/../routes/Router.php';
// Khởi tạo router mới nếu có request (mỗi request mới tạo mới router)
// mỗi request trong PHP độc lập (không giống Node.js hay Java giữ trạng thái giữa các request

// $router = new Router();
// Singleton pattern chỉ sử dụng 1 Router duy nhất thay vì tạo mới để tối ưu
$router = Router::getInstance();

// Load các routes
require_once __DIR__ . '/../routes/web.php';

// Lấy URI và method
// lấy toàn bộ URL sau domain
// các request đều phải đi vào đây để check 

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Xử lý request,kiểm tra web.php để tìm route phù hợp và gọi tới Controller tương ứng
$router->dispatch($requestUri,$requestMethod);

?>


