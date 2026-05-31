<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Admin Dashboard</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= (int)$byRole["doctor"] ?></h3>
                            <p>Doctors</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-md"></i></div>
                        <a href="<?= url("doctors") ?>" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= (int)$byRole["patient"] ?></h3>
                            <p>Patients</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="<?= url("users",null,["role"=>"patient"]) ?>" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= (int)$todayCount ?></h3>
                            <p>Today's Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        <a href="<?= url("appointments") ?>" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= (int)$byRole["admin"] ?></h3>
                            <p>Admins</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-shield"></i></div>
                        <a href="<?= url("users",null,["role"=>"admin"]) ?>" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">This Week's Appointments by Status</h5></div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><td>Pending</td><td><span class="badge badge-warning"><?= $weekStats["pending"] ?></span></td></tr>
                        <tr><td>Confirmed</td><td><span class="badge badge-info"><?= $weekStats["confirmed"] ?></span></td></tr>
                        <tr><td>Completed</td><td><span class="badge badge-success"><?= $weekStats["completed"] ?></span></td></tr>
                        <tr><td>Cancelled</td><td><span class="badge badge-danger"><?= $weekStats["cancelled"] ?></span></td></tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Recent Appointments</h5></div>
                <div class="card-body p-0">
                    <table class="table table-vmid mb-0">
                        <thead>
                            <tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recent as $a): ?>
                            <tr>
                                <td>#<?= e($a["id"]) ?></td>
                                <td><?= e($a["patient_name"]) ?></td>
                                <td><?= e($a["doctor_name"]) ?></td>
                                <td><?= e(formatDate($a["appt_date"])) ?></td>
                                <td><?= e(formatTime($a["appt_time"])) ?></td>
                                <td><?= status_badge($a["status"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recent)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">No appointments yet.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . "/../partials/footer.php"; ?>
