<?php 
$pageTitle = 'Edit Doctor';
Auth::requireRole('admin');
require_once APP_ROOT . '/views/partials/header.php';
require_once APP_ROOT . '/views/partials/sidebar.php';

$daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
$selectedDays = explode(',', $doctor['available_days']);
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Edit Doctor: Dr. <?= htmlspecialchars($doctor['name']) ?></h1></div>
            <div class="col-sm-6 text-right"><a href="index.php?page=doctors" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?= CSRF::getTokenField() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3"><label>Specialization *</label>
                                <select name="specialization_id" class="form-control" required>
                                    <?php foreach($specializations as $spec): ?>
                                    <option value="<?= $spec['id'] ?>" <?= $spec['id'] == $doctor['specialization_id'] ? 'selected' : '' ?>><?= htmlspecialchars($spec['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3"><label>Consultation Fee (JD) *</label><input type="number" step="0.01" name="consultation_fee" class="form-control" value="<?= $doctor['consultation_fee'] ?>" required></div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3"><label>Available Days</label>
                                <div class="row">
                                    <?php foreach($daysOfWeek as $day): ?>
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input type="checkbox" name="available_days[]" value="<?= $day ?>" class="form-check-input" <?= in_array($day, $selectedDays) ? 'checked' : '' ?>>
                                            <label class="form-check-label"><?= $day ?></label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3"><label>Bio</label><textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($doctor['bio'] ?? '') ?></textarea></div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3"><label>Profile Photo</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Doctor</button>
                    <a href="index.php?page=doctors" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once APP_ROOT . '/views/partials/footer.php'; ?>