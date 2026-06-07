<?php 
$pageTitle = 'Edit User';
Auth::requireRole('admin');
require_once APP_ROOT . '/views/partials/header.php';
require_once APP_ROOT . '/views/partials/sidebar.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Edit User: <?= htmlspecialchars($user['name']) ?></h1></div>
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
                            <div class="mb-3"><label>Full Name *</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required></div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3"><label>Email</label><input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled></div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3"><label>Role</label><input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" disabled></div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"></div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="index.php?page=users" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once APP_ROOT . '/views/partials/footer.php'; ?>