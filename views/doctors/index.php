<?php 
$pageTitle = 'Doctors Management';
Auth::requireRole('admin');
require_once APP_ROOT . '/views/partials/header.php';
require_once APP_ROOT . '/views/partials/sidebar.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Doctors Management</h1></div>
            <div class="col-sm-6 text-right"><a href="index.php?page=users&action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Doctor</a></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <form method="GET" class="row">
                    <input type="hidden" name="page" value="doctors">
                    <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Search doctors..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary">Search</button><a href="index.php?page=doctors" class="btn btn-secondary ms-2">Reset</a></div>
                </form>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Specialization</th><th>Fee (JD)</th><th>Available Days</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach($doctors as $doc): ?>
                        <tr>
                            <td><?= $doc['id'] ?></td>
                            <td><?= htmlspecialchars($doc['name']) ?></td>
                            <td><?= htmlspecialchars($doc['email']) ?></td>
                            <td><?= htmlspecialchars($doc['specialization_name']) ?></td>
                            <td><?= number_format($doc['consultation_fee'], 2) ?></td>
                            <td><?= htmlspecialchars($doc['available_days']) ?></td>
                            <td>
                                <a href="index.php?page=doctors&action=edit&id=<?= $doc['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="index.php?page=doctors&action=delete" class="d-inline" onsubmit="return confirm('Delete this doctor?')">
                                    <?= CSRF::getTokenField() ?>
                                    <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer"><?= $paginator->render() ?></div>
        </div>
    </div>
</section>

<?php require_once APP_ROOT . '/views/partials/footer.php'; ?>