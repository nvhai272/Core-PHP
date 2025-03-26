<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
<?php include __DIR__ . '/../layout/nav.php'; ?>

    <div class="container mt-4">
        <h3>PROFILE</h3>
        <div class="border p-4">
            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    <img src="/uploads/images/avatar/<?php echo htmlspecialchars($u['avatar']); ?>" 
                        alt="Avatar" class="rounded-circle" width="120" height="120">
                </div>
                <div class="flex-grow-1">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($u['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($u['email']); ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php include __DIR__ . '/../layout/footer.php'; ?>

</body>
</html>
