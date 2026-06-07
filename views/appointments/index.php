<?php 
$pageTitle = 'Appointments';
$role = Auth::role();
require_once APP_ROOT . '/views/partials/header.php';
require_once APP_ROOT . '/views/partials/sidebar.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Appointments</h1></div>
            <div class="col-sm-6 text-right">
                <?php if($role == 'patient'): ?>
                <a href="index.php?page=appointments&action=book" class="btn btn-primary"><i class="fas fa-plus"></i> Book Appointment</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <form method="GET" class="row">
                    <input type="hidden" name="page" value="appointments">
                    <div class="col-md-3">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="pending" <?= ($_GET['status']??'')=='pending'?'selected':'' ?>>Pending</option>
                            <option value="confirmed" <?= ($_GET['status']??'')=='confirmed'?'selected':'' ?>>Confirmed</option>
                            <option value="completed" <?= ($_GET['status']??'')=='completed'?'selected':'' ?>>Completed</option>
                            <option value="cancelled" <?= ($_GET['status']??'')=='cancelled'?'selected':'' ?>>Cancelled</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Date</th><th>Time</th><th><?= $role == 'patient' ? 'Doctor' : 'Patient' ?></th><th>Specialization</th><th>Status</th><th>Reason</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($appointments as $app): ?>
                        <tr>
                            <td><?= date('Y-m-d', strtotime($app['appt_date'])) ?></td>
                            <td><?= date('h:i A', strtotime($app['appt_time'])) ?></td>
                            <td><?= htmlspecialchars($role == 'patient' ? ($app['doctor_name'] ?? 'N/A') : ($app['patient_name'] ?? 'N/A')) ?></td>
                            <td><?= htmlspecialchars($app['specialization_name'] ?? $app['spec_name'] ?? 'N/A') ?></td>
                            <td><span class="status-badge status-<?= $app['status'] ?>"><?= ucfirst($app['status']) ?></span></td>
                            <td><?= htmlspecialchars(substr($app['reason'] ?? '', 0, 50)) ?></td>
                            <td>
                                <?php if($role == 'doctor' && in_array($app['status'], ['pending','confirmed'])): ?>
                                <form method="POST" action="index.php?page=appointments&action=updateStatus" class="d-inline">
                                    <?= CSRF::getTokenField() ?>
                                    <input type="hidden" name="id" value="<?= $app['id'] ?>">
                                    <select name="status" class="form-select form-select-sm d-inline w-auto">
                                        <option value="confirmed">Confirm</option>
                                        <option value="completed">Complete</option>
                                        <option value="cancelled">Cancel</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary mt-1">Update</button>
                                </form>
                                <?php endif; ?>
                                <?php if($role == 'patient' && $app['status'] == 'pending'): ?>
                                <form method="POST" action="index.php?page=appointments&action=cancel" class="d-inline" onsubmit="return confirm('Cancel this appointment?')">
                                    <?= CSRF::getTokenField() ?>
                                    <input type="hidden" name="id" value="<?= $app['id'] ?>">
                                    <input type="hidden" name="cancellation_reason" value="Cancelled by patient">
                                    <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                </form>
                                <?php endif; ?>
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