<?php 
$role = Auth::role();
$currentPage = $_GET['page'] ?? 'dashboard';
?>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle"></i> <?= htmlspecialchars(Auth::name()) ?>
                <i class="fas fa-angle-down"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="index.php?page=profile" class="dropdown-item">
                    <i class="fas fa-user"></i> My Profile
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="index.php?page=logout" class="d-inline">
                    <?= CSRF::getTokenField() ?>
                    <button type="submit" class="dropdown-item" style="color: #dc3545;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php?page=dashboard" class="brand-link">
        <span class="brand-text font-weight-light"><?= APP_NAME ?></span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block"><?= htmlspecialchars(Auth::name()) ?></a>
                <small class="text-muted"><?= ucfirst($role) ?></small>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="index.php?page=dashboard" class="nav-link <?= $currentPage == 'dashboard' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="index.php?page=appointments" class="nav-link <?= $currentPage == 'appointments' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Appointments</p>
                    </a>
                </li>

                <?php if($role == 'admin'): ?>
                <li class="nav-header">MANAGEMENT</li>
                <li class="nav-item">
                    <a href="index.php?page=users" class="nav-link <?= $currentPage == 'users' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=doctors" class="nav-link <?= $currentPage == 'doctors' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-md"></i>
                        <p>Doctors</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=specializations" class="nav-link <?= $currentPage == 'specializations' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Specializations</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=reports" class="nav-link <?= $currentPage == 'reports' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reports</p>
                    </a>
                </li>
                <?php endif; ?>

                <?php if($role == 'doctor'): ?>
                <li class="nav-item">
                    <a href="index.php?page=prescriptions" class="nav-link <?= $currentPage == 'prescriptions' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-prescription"></i>
                        <p>Prescriptions</p>
                    </a>
                </li>
                <?php endif; ?>

                <?php if($role == 'patient'): ?>
                <li class="nav-item">
                    <a href="index.php?page=prescriptions" class="nav-link <?= $currentPage == 'prescriptions' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-prescription"></i>
                        <p>My Prescriptions</p>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>

<!-- Content Wrapper -->
<div class="content-wrapper">