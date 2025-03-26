<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> -->
</head>

<?php
    include __DIR__ . '/nav.php';
?>

<body class="container">


    <h1 class="text-center">Home Page 🏠</h1>

    <div class="text-center mt-3">
        <?php
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $role = $_SESSION['account']['role'] ?? 'guest';
        ?>

        <?php if ($role === 'admin' || $role === 'super_admin' || $role === 'user'): ?>
        <h3 class="text-success">
            👋 Xin chào, <?php echo($role === 'admin' || $role === 'super_admin' ? 'Admin' : 'User'); ?>!
        </h3>
        <a href="/logout" class="btn btn-danger">Logout</a>
        <?php else: ?>
        <a href="/login" class="btn btn-primary">Login</a>
        <?php endif; ?>
    </div>

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-6">
                <h4>🧑 Đăng nhập admin bằng tài khoản này</h4>
                <p><strong>Email: </strong>admin@gmail.com hoặc super_admin@gmail.com</p>
                <p><strong>Password: </strong>admin123</p>

                <p><strong>Múi giờ server đang dùng: </strong> <?php echo date_default_timezone_get();?></p>
            </div>

            <div class="col-md-6">
                <h4>📦 Thông tin lưu session</h4>
                <pre><?php print_r($_SESSION); ?></pre>
                <p><strong>Thư mục lưu session: </strong> <?php echo session_save_path();?></p>

            </div>
        </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>