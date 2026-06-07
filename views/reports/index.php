<?php 
$pageTitle = 'Reports';
Auth::requireRole('admin');
require_once APP_ROOT . '/views/partials/header.php';
require_once APP_ROOT . '/views/partials/sidebar.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Appointment Reports</h1></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary text-white"><h5>Filter Appointments</h5></div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <input type="hidden" name="page" value="reports">
                    <div class="col-md-3"><label>Start Date</label><input type="date" name="start_date" class="form-control" value="<?= $filters['start_date'] ?>" required></div>
                    <div class="col-md-3"><label>End Date</label><input type="date" name="end_date" class="form-control" value="<?= $filters['end_date'] ?>" required></div>
                    <div class="col-md-3"><label>Doctor</label><select name="doctor_id" class="form-control"><option value="">All Doctors</option><?php foreach($doctors as $doc): ?><option value="<?= $doc['id'] ?>" <?= $filters['doctor_id'] == $doc['id'] ? 'selected' : '' ?>><?= htmlspecialchars($doc['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-3"><label>Status</label><select name="status" class="form-control"><option value="">All</option><option value="pending" <?= $filters['status']=='pending'?'selected':'' ?>>Pending</option><option value="confirmed" <?= $filters['status']=='confirmed'?'selected':'' ?>>Confirmed</option><option value="completed" <?= $filters['status']=='completed'?'selected':'' ?>>Completed</option><option value="cancelled" <?= $filters['status']=='cancelled'?'selected':'' ?>>Cancelled</option></select></div>
                    <div class="col-md-12"><button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply Filters</button><a href="?page=reports&export=csv&<?= http_build_query($filters) ?>" class="btn btn-success ms-2"><i class="fas fa-file-csv"></i> Export CSV</a></div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><h5>Summary</h5></div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3"><div class="alert alert-info"><h3><?= $summary['total'] ?></h3><p>Total</p></div></div>
                    <div class="col-md-3"><div class="alert alert-warning"><h3><?= $summary['pending'] ?></h3><p>Pending</p></div></div>
                    <div class="col-md-3"><div class="alert alert-primary"><h3><?= $summary['confirmed'] ?></h3><p>Confirmed</p></div></div>
                    <div class="col-md-3"><div class="alert alert-success"><h3><?= $summary['completed'] ?></h3><p>Completed</p></div></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Specialization</th><th>Date</th><th>Time</th><th>Status</th><th>Reason</th></tr></thead>
                    <tbody><?php foreach($appointments as $app): ?><tr><td><?= $app['id'] ?></td><td><?= htmlspecialchars($app['patient_name']) ?></td><td><?= htmlspecialchars($app['doctor_name']) ?></td><td><?= htmlspecialchars($app['specialization_name']) ?></td><td><?= $app['appt_date'] ?></td><td><?= $app['appt_time'] ?></td><td><span class="status-badge status-<?= $app['status'] ?>"><?= $app['status'] ?></span></td><td><?= htmlspecialchars(substr($app['reason']??'',0,50)) ?></td></tr><?php endforeach; ?></tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php require_once APP_ROOT . '/views/partials/footer.php'; ?>