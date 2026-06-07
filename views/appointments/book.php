<?php 
$pageTitle = 'Book Appointment';
require_once APP_ROOT . '/views/partials/header.php';
require_once APP_ROOT . '/views/partials/sidebar.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Book New Appointment</h1></div>
            <div class="col-sm-6 text-right"><a href="index.php?page=appointments" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white"><h5><i class="fas fa-calendar-plus"></i> Appointment Information</h5></div>
                    <div class="card-body">
                        <form method="POST">
                            <?= CSRF::getTokenField() ?>
                            <div class="mb-3"><label>Select Doctor *</label>
                                <select name="doctor_id" class="form-control" required>
                                    <option value="">-- Select Doctor --</option>
                                    <?php foreach($doctors as $doc): ?>
                                    <option value="<?= $doc['id'] ?>">Dr. <?= htmlspecialchars($doc['name']) ?> - <?= $doc['specialization_name'] ?> (<?= number_format($doc['consultation_fee'],2) ?> JD)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3"><label>Date *</label><input type="date" name="date" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required></div>
                            <div class="mb-3"><label>Time *</label>
                                <select name="time" class="form-control" required>
                                    <option value="">-- Select Time --</option>
                                    <?php for($h=9; $h<=16; $h++): ?>
                                    <option value="<?= sprintf('%02d:00:00', $h) ?>"><?= sprintf('%02d:00', $h) ?></option>
                                    <option value="<?= sprintf('%02d:30:00', $h) ?>"><?= sprintf('%02d:30', $h) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="mb-3"><label>Reason</label><textarea name="reason" class="form-control" rows="3" placeholder="Describe your symptoms..."></textarea></div>
                            <button type="submit" class="btn btn-primary w-100">Book Appointment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once APP_ROOT . '/views/partials/footer.php'; ?>