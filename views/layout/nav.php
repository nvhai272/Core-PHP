<?php
    $currentUrl         = $_SERVER['REQUEST_URI'];

    $isDetailsAdminPage = (strpos($currentUrl, 'details-admin') && !preg_match('/\?id=/', $currentUrl)) !== false;

    $isManagerAdmin     = strpos($currentUrl, 'list-admin') !== false
    || strpos($currentUrl, 'create-admin') !== false
    || strpos($currentUrl, 'search-admin') !== false
    || strpos($currentUrl, 'edit-admin') !== false;
    
    $isManagerUser = strpos($currentUrl, 'list-user') !== false
    || strpos($currentUrl, 'create-user') !== false 
    || strpos($currentUrl, 'search-user') !== false 
    || strpos($currentUrl, 'edit-user') !== false;
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-0">
    <div class="container">
        <a class="navbar-brand me-5" href="/">PHP_Core</a>
        <div class="collapse navbar-collapse ms-5">
            <ul class="navbar-nav me-auto">
              
            <!-- Infomation -->
                <li class="nav-item">
                    <?php
                        $checkkk = $_SESSION['account'] ?? '';
                        if ($checkkk): // Nếu không rỗng thì hiển thị
                        ?>

                    <a href="<?php echo($checkkk['role'] === 'admin' || $checkkk['role'] === 'super_admin' ?
                     '/admin/details-admin' : '/user/details-user'); ?>"
                      
                      class="btn text-success me-3		    
                        <?php echo $isDetailsAdminPage ? 'btn-outline-primary' : ''; ?>"

                        onmouseover="this.classList.add('text-white', 'bg-primary')"
                        onmouseout="this.classList.remove('text-white', 'bg-primary')">

                        <i class="bi bi-person"></i>
                        <?php echo($checkkk['role'] === 'admin' || $checkkk['role'] === 'super_admin' ? 'Profile Admin' : 'Profile User'); ?>
                    </a>
                    <?php endif; ?>
                </li>

                <!-- Team Management -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle btn text-success me-3                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     <?php echo $isManagerAdmin ? 'border border-primary' : ''; ?>"
                        href="#" id="teamDropdown" data-bs-toggle="dropdown" role="button"
                        onmouseover="this.classList.add('text-white', 'bg-primary')"
                        onmouseout="this.classList.remove('text-white', 'bg-primary')">
                        Admin Management
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/admin/list-admin"><i class="bi bi-list"></i> List Admin</a>
                        </li>
                        <li><a class="dropdown-item" href="/admin/create-admin"><i
                                    class="bi bi-plus-circle text-success"></i> Create Admin</a></li>
                        <li><a class="dropdown-item" href="/admin/search-admin"><i class="bi bi-search"></i> Search
                                Admin</a></li>
                    </ul>
                </li>

                <!-- Employee Management -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle btn text-success                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <?php echo $isManagerUser ? 'border border-primary' : ''; ?>"
                        href="#" id="employeeDropdown" data-bs-toggle="dropdown" role="button"
                        onmouseover="this.classList.add('text-white', 'bg-primary')"
                        onmouseout="this.classList.remove('text-white', 'bg-primary')">
                        User Management
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/admin/list-user"><i class="bi bi-list"></i> List User</a>
                        </li>
                        <li><a class="dropdown-item" href="/admin/create-user"><i
                                    class="bi bi-plus-circle text-success"></i> Add User</a></li>
                        <li><a class="dropdown-item" href="/admin/search-user"><i class="bi bi-search"></i> Search
                                User</a></li>
                    </ul>
                </li>
            </ul>
            <?php
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                $role  = $_SESSION['account']['role'] ?? null;
                $check = ($role === 'admin' || $role === 'user' || $role === 'super_admin');
                if ($check) {
                    echo '<a class="btn btn-danger" href="/logout">Logout</a>';
                } else {
                    echo '<a class="btn btn-primary" href="/login">Login</a>';
                }
            ?>


        </div>
    </div>
</nav>
<!-- Bootstrap Script (Cần có để dropdown hoạt động) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">