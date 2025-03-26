<?php
$name = isset($_GET['name']) ? trim($_GET['name']) : '';
$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$sortBy = in_array($_GET['sortBy'] ?? 'id', ['id', 'name', 'email', 'role_type']) ? $_GET['sortBy'] : 'id';
$sortOrder = ($_GET['sortOrder'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
$currentPage = max((int) ($_GET['page'] ?? 1), 1);

$queryString = http_build_query(['name' => $name, 'email' => $email]);
$nextSortOrder = $sortOrder === 'ASC' ? 'DESC' : 'ASC';

function getSortUrl($column, $queryString, $nextSortOrder) {
    return "?$queryString&sortBy=$column&sortOrder=$nextSortOrder";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Search</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/nav.php'; ?>

    <?php if (! empty($error)): ?>
    <div class="alert alert-danger text-center mt-3">
        <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <div class="container mt-4">
        <form class="border p-4" method="GET">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="text" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary" name="search">Search</button>
                <a href="/admin/search-admin" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="container mt-4">
        <table class="table table-bordered">
            <thead class="table-primary">
                <tr>
                    <th><a href="<?php echo getSortUrl('id', $queryString, $nextSortOrder); ?>">ID</a></th>
                    <th><a href="<?php echo getSortUrl('name', $queryString, $nextSortOrder); ?>">Name</a></th>
                    <th><a href="<?php echo getSortUrl('email', $queryString, $nextSortOrder); ?>">Email</a></th>
                    <th><a href="<?php echo getSortUrl('role_type', $queryString, $nextSortOrder); ?>">Role</a></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data ?? [] as $admin): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['id']); ?></td>
                        <td><?php echo htmlspecialchars($admin['name']); ?></td>
                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                        <td><?php echo $admin['role_type'] == 1 ? 'Super Admin' : 'Admin'; ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                    <tr><td colspan="4" class="text-center">No data found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <nav>
            <ul class="pagination justify-content-center">
                <?php $paginationQuery = "$queryString&sortBy=$sortBy&sortOrder=$sortOrder"; ?>

                <?php if ($currentPage > 1): ?>

                    <li class="page-item"><a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&<?php echo $paginationQuery; ?>">Prev</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item<?php echo ($i == $currentPage) ? ' active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo $paginationQuery; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&<?php echo $paginationQuery; ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <?php include __DIR__ . '/../layout/footer.php'; ?>
</body>
</html>
