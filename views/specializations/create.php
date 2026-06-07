<?php 
$pageTitle = 'Add Specialization';
Auth::requireRole('admin');
require_once APP_ROOT . '/views/partials/header.php';
require_once APP_ROOT . '/views/partials/sidebar.php';
?>
<div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1>Add Specialization</h1></div><div class="col-sm-6 text-right"><a href="index.php?page=specializations" class="btn btn-secondary">Back</a></div></div></div></div>
<section class="content"><div class="container-fluid"><div class="card"><div class="card-body">
<form method="POST">
    <?= CSRF::getTokenField() ?>
    <div class="mb-3"><label>Name *</label><input type="text" name="name" class="form-control" required></div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="index.php?page=specializations" class="btn btn-secondary">Cancel</a>
</form>
</div></div></div></section>
<?php require_once APP_ROOT . '/views/partials/footer.php'; ?> 