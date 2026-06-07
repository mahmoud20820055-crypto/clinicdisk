<?php 
$pageTitle = 'Users Management';
Auth::requireRole('admin');
require_once APP_ROOT . '/views/partials/header.php';
require_once APP_ROOT . '/views/partials/sidebar.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Users Management</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="index.php?page=users&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New User
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <form method="GET" class="row">
                    <input type="hidden" name="page" value="users">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search by name/email" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="role" class="form-control">
                            <option value="">All Roles</option>
                            <option value="admin" <?= ($_GET['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="doctor" <?= ($_GET['role'] ?? '') == 'doctor' ? 'selected' : '' ?>>Doctor</option>
                            <option value="patient" <?= ($_GET['role'] ?? '') == 'patient' ? 'selected' : '' ?>>Patient</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="index.php?page=users" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Phone</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'doctor' ? 'info' : 'success') ?>"><?= ucfirst($user['role']) ?></span></td>
                            <td><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                            <td>
                                <span class="badge bg-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                                    <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?page=users&action=edit&id=<?= $user['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <?php if($user['id'] != Auth::id()): ?>
                                <form method="POST" action="index.php?page=users&action=toggleActive" class="d-inline">
                                    <?= CSRF::getTokenField() ?>
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-<?= $user['is_active'] ? 'warning' : 'success' ?>">
                                        <i class="fas fa-<?= $user['is_active'] ? 'ban' : 'check' ?>"></i>
                                    </button>
                                </form>
                                <form method="POST" action="index.php?page=users&action=delete" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                    <?= CSRF::getTokenField() ?>
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <?= $paginator->render() ?>
            </div>
        </div>
    </div>
</section>

<?php require_once APP_ROOT . '/views/partials/footer.php'; ?>