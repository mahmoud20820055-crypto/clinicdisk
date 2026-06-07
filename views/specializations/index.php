<?php 
$pageTitle = 'Specializations';
Auth::requireRole('admin');
require_once APP_ROOT . '/views/partials/header.php';
require_once APP_ROOT . '/views/partials/sidebar.php';
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Specializations</h1></div>
            <div class="col-sm-6 text-right">
                <a href="index.php?page=specializations&action=create" class="btn btn-primary">Add Specialization</a>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr><th>ID</th><th>Name</th><th>Doctors Count</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if(isset($specializations) && !empty($specializations)): ?>
                            <?php foreach($specializations as $spec): ?>
                            <tr>
                                <td><?= $spec['id'] ?></td>
                                <td><?= htmlspecialchars($spec['name']) ?></td>
                                <td><?= (new SpecializationModel())->getDoctorsCount($spec['id']) ?></td>
                                <td>
                                    <a href="index.php?page=specializations&action=edit&id=<?= $spec['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <form method="POST" action="index.php?page=specializations&action=delete" class="d-inline" onsubmit="return confirm('Delete?')">
                                        <?= CSRF::getTokenField() ?>
                                        <input type="hidden" name="id" value="<?= $spec['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                 </div>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No specializations found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php require_once APP_ROOT . '/views/partials/footer.php'; ?> 