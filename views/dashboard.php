<?php 
$pageTitle = 'Dashboard';
if (!isset($data) || !is_array($data)) $data = [];
$role = Auth::role();
require_once APP_ROOT . '/views/partials/header.php';
require_once APP_ROOT . '/views/partials/sidebar.php';
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active">Home</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <!-- Admin Dashboard -->
        <?php if($role == 'admin'): ?>
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $data['stats']['admin'] ?? 0 ?></h3>
                        <p>Admins</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-shield"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $data['stats']['doctor'] ?? 0 ?></h3>
                        <p>Doctors</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-md"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= $data['stats']['patient'] ?? 0 ?></h3>
                        <p>Patients</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $data['today_appointments'] ?? 0 ?></h3>
                        <p>Today's Appointments</p>
                    </div>
                    <div class="icon"><i class="fas fa-calendar-day"></i></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="index.php?page=users&action=create" class="btn btn-primary m-1">
                            <i class="fas fa-user-plus"></i> Add User
                        </a>
                        <a href="index.php?page=doctors&action=create" class="btn btn-success m-1">
                            <i class="fas fa-user-md"></i> Add Doctor
                        </a>
                        <a href="index.php?page=specializations&action=create" class="btn btn-info m-1">
                            <i class="fas fa-tag"></i> Add Specialization
                        </a>
                        <a href="index.php?page=reports" class="btn btn-warning m-1">
                            <i class="fas fa-chart-line"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Doctor Dashboard -->
        <?php if($role == 'doctor'): ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-calendar-day"></i> Today's Appointments</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($data['today_appointments'])): ?>
                            <p class="text-muted">No appointments today</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr><th>Time</th><th>Patient</th><th>Reason</th><th>Status</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($data['today_appointments'] as $app): ?>
                                        <tr>
                                            <td><?= date('h:i A', strtotime($app['appt_time'])) ?></td>
                                            <td><?= htmlspecialchars($app['patient_name']) ?></td>
                                            <td><?= htmlspecialchars(substr($app['reason'], 0, 50)) ?></td>
                                            <td><span class="status-badge status-<?= $app['status'] ?>"><?= $app['status'] ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Appointments</span>
                        <span class="info-box-number"><?= array_sum($data['stats'] ?? []) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Patient Dashboard -->
        <?php if($role == 'patient'): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= $data['active_count'] ?? 0 ?></h3>
                        <p>Active Appointments</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <a href="index.php?page=appointments" class="small-box-footer">View Appointments <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $data['prescriptions_count'] ?? 0 ?></h3>
                        <p>Prescriptions</p>
                    </div>
                    <div class="icon"><i class="fas fa-prescription"></i></div>
                    <a href="index.php?page=prescriptions" class="small-box-footer">View Prescriptions <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-calendar-alt"></i> Upcoming Appointments</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($data['active_appointments'])): ?>
                            <p class="text-muted">No upcoming appointments</p>
                            <a href="index.php?page=appointments&action=book" class="btn btn-primary">Book Appointment</a>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead><tr><th>Date</th><th>Time</th><th>Doctor</th><th>Status</th><th>Action</th></tr></thead>
                                    <tbody>
                                        <?php foreach($data['active_appointments'] as $app): ?>
                                        <tr>
                                            <td><?= date('Y-m-d', strtotime($app['appt_date'])) ?></td>
                                            <td><?= date('h:i A', strtotime($app['appt_time'])) ?></td>
                                            <td><?= htmlspecialchars($app['doctor_name']) ?></td>
                                            <td><span class="status-badge status-<?= $app['status'] ?>"><?= $app['status'] ?></span></td>
                                            <td><a href="index.php?page=appointments" class="btn btn-sm btn-info">View</a></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php require_once APP_ROOT . '/views/partials/footer.php'; ?>