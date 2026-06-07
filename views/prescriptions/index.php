<?php 
$pageTitle = 'Prescriptions';
$role = Auth::role();
require_once APP_ROOT . '/views/partials/header.php';
require_once APP_ROOT . '/views/partials/sidebar.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Prescriptions</h1></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <?php if(empty($prescriptions)): ?>
                    <div class="alert alert-info text-center">No prescriptions found.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr><th>Date</th><th>Doctor</th><th>Diagnosis</th><th>Medications</th><th>Notes</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($prescriptions as $pres): ?>
                                <tr>
                                    <td><?= date('Y-m-d', strtotime($pres['appt_date'])) ?></td>
                                    <td>Dr. <?= htmlspecialchars($pres['doctor_name']) ?></td>
                                    <td><?= htmlspecialchars(substr($pres['diagnosis'], 0, 100)) ?></td>
                                    <td><?= htmlspecialchars(substr($pres['medications'], 0, 100)) ?></td>
                                    <td><?= htmlspecialchars(substr($pres['notes'] ?? '', 0, 50)) ?></td>
                                    <td>
                                        <?php if($pres['file_path']): ?>
                                        <a href="index.php?page=prescriptions&action=download&id=<?= $pres['id'] ?>" class="btn btn-sm btn-success"><i class="fas fa-download"></i> Download PDF</a>
                                        <?php else: ?>
                                        <span class="text-muted">No file</span>
                                        <?php endif; ?>
                                     </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once APP_ROOT . '/views/partials/footer.php'; ?>