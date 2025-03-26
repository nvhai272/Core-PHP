<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo($danhSachDuLieu == 'admin') ? 'Admin List' : 'User List'; ?></title>
</head>

<body class="">

    <?php
        include __DIR__ . '/../layout/nav.php';
    ?>

    <div class="">
        <?php
            if (isset($_GET['message'])) {
                echo '<div id="success-alert"
                class="alert alert-success position-fixed top-0 start-0 m-5" role="alert">';
                $me = $_GET['message'];
                print_r($me);
                echo '</div>';
            }
        ?>


        <h1 class="text-center"><?php echo($danhSachDuLieu == 'admin') ? 'List Admin' : 'List User'; ?></h1>

        <?php
            if (! isset($data) || empty($data)) {
                echo "<p class='text-danger text-center'>Chưa có dữ liệu nào</p>";
            } else {
            ?>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>

                    <?php
                        // Nếu người dùng bấm vào cột khác, reset về 'asc'
                            $newSort  = $_GET['sort'] ?? 'id';
                            $newOrder = ($sort === $newSort) ? ($order === 'asc' ? 'desc' : 'asc') : 'asc';
                        ?>

                    <th class="px-2 ">
                        <a href="?sort=id&order=<?php echo $newSort === 'id' ? $newOrder : 'asc'; ?>"
                            class="text-danger text-decoration-none">
                            ID
                            <?php if ($sort == 'id'): ?>
                            <i class="bi bi-sort-<?php echo $order === 'asc' ? 'up' : 'down'; ?> text-white"></i>
                            <?php endif; ?>

                        </a>
                    </th>
                    <th class="px-3 ">
                        <a href="?sort=name&order=<?php echo $newSort === 'name' ? $newOrder : 'asc'; ?>"
                            class="text-danger text-decoration-none">
                            Name
                            <?php if ($sort == 'name'): ?>
                            <i class="bi bi-sort-<?php echo $order === 'asc' ? 'up' : 'down'; ?> text-white"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-3 ">
                        <a href="?sort=email&order=<?php echo $newSort === 'email' ? $newOrder : 'asc'; ?>"
                            class="text-danger text-decoration-none">
                            Email
                            <?php if ($sort == 'email'): ?>
                            <i class="bi bi-sort-<?php echo $order === 'asc' ? 'up' : 'down'; ?> text-white"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <?php
                        if ($danhSachDuLieu == "user") {
                                echo '
            <th class="px-3">Facebook_Id</th>
            <th class="px-3"> <a href="?sort=status&order=' . ($newSort === 'status' ? $newOrder : 'asc') . '"
               class="text-danger text-decoration-none">
                Status
                ' . ($sort == 'status' ? '<i class="bi bi-sort-' . ($order === 'asc' ? 'up' : 'down') . ' text-white"></i>' : '') . '
            </a></th>
    ';
                            } else {
                            ?>

                    <th class="px-3">
                        <a href="?sort=role_type&order=<?php echo $newSort === 'role_type' ? $newOrder : 'asc'; ?>"
                            class="text-danger text-decoration-none">
                            Role_Admin
                            <?php if ($sort == 'role_type'): ?>
                            <i class="bi bi-sort-<?php echo $order === 'asc' ? 'up' : 'down'; ?> text-white"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <?php
                        }
                        ?>

                    <!-- <th class="px-3">Ins_Id</th>
                    <th class="px-3">Upd_Id</th> -->
                    <th class="px-3">Ins_DateTime</th>
                    <th class="px-3">Upd_DateTime</th>
                    <th class="px-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $admin): ?>
                <tr>
                    <td class="px-3"><?php echo $admin['id']; ?></td>
                    <td class="px-3"><?php echo $admin['name']; ?></td>
                    <td class="px-3"><?php echo $admin['email']; ?></td>

                    <?php
                        if ($danhSachDuLieu == "user") {
                            ?>
                    <td class="px-3"><?php echo $admin['facebook_id']; ?></td>
                    <td class="px-3 text-danger"><?php echo $admin['status'] == 1 ? 'Active' : 'Banned'; ?></td>
                    <?php
                        } else {
                            ?>
                    <td class="px-3 text-danger"><?php echo $admin['role_type'] == 1 ? 'Super Admin' : 'Admin'; ?></td>

                    <?php
                        }
                        ?>

                    <!-- <td class="px-3"><?php echo $admin['ins_id']; ?></td>
                    <td class="px-3"><?php echo $admin['upd_id']; ?></td> -->
                    <td class="px-3"><?php echo $admin['ins_datetime']; ?></td>
                    <td class="px-3"><?php echo $admin['upd_datetime'] ?? 'Null'; ?></td>

                    <td class="px-3">
                        <?php
                            $detailsPath = ($danhSachDuLieu == "user") ? "details-user" : "details-admin";
                                $editPath    = ($danhSachDuLieu == "user") ? "edit-user" : "edit-admin";
                                $deletePath  = ($danhSachDuLieu == "user") ? "delete-user" : "delete-admin";
                            ?>
                        <a href="/admin/<?php echo $detailsPath; ?>?id=<?php echo $admin['id']; ?>"
                            class="btn btn-success btn-sm">
                            <i class="bi bi-person"></i> Details
                        </a>
                        <a href="/admin/<?php echo $editPath; ?>?id=<?php echo $admin['id']; ?>"
                            class="btn btn-warning btn-sm">
                            <i class="bi bi-person-fill-gear"></i> Edit
                        </a>
                        <form action="/admin/<?php echo $deletePath; ?>" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure to delete?');">
                                <i class="bi bi-trash text-white"></i> Delete
                            </button>
                        </form>
                    </td>


                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
            }
        ?>

        <nav>
            <ul class="pagination justify-content-center">
                <li
                    class="page-item                                                                                                             <?php echo($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item<?php echo($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>

                <li
                    class="page-item                                                                                                             <?php echo($page >= $totalPages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>

    </div>
    <script>
    setTimeout(function() {
        var alertBox = document.getElementById("success-alert");
        if (alertBox) {
            alertBox.style.transition = "opacity 0.5s";
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 500);
        }
    }, 3000);
    </script>

    <!-- footer -->
    <?php include __DIR__ . '/../layout/footer.php'; ?>
</body>

</html>