<?php 
$pageTitle = 'Create User';
Auth::requireRole('admin');
require_once APP_ROOT . '/views/partials/header.php';
require_once APP_ROOT . '/views/partials/sidebar.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Create New User</h1></div>
            <div class="col-sm-6 text-right"><a href="index.php?page=users" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <?= CSRF::getTokenField() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3"><label>Full Name *</label><input type="text" name="name" class="form-control" required></div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3"><label>Email *</label><input type="email" name="email" class="form-control" required></div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3"><label>Role *</label>
                                <select name="role" class="form-control" required>
                                    <option value="patient">Patient</option>
                                    <option value="doctor">Doctor</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Create User</button>
                    <a href="index.php?page=users" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once APP_ROOT . '/views/partials/footer.php'; ?>