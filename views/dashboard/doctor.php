<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Doctor Dashboard</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-calendar-day"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Today</span>
                            <span class="info-box-number"><?= count($today) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">This Month - Pending</span>
                            <span class="info-box-number"><?= (int)$monthStats["pending"] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">This Month - Completed</span>
                            <span class="info-box-number"><?= (int)$monthStats["completed"] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Today's Appointments</h5></div>
                <div class="card-body p-0">
                    <table class="table table-vmid mb-0">
                        <thead><tr><th>Time</th><th>Patient</th><th>Phone</th><th>Status</th><th>Reason</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($today as $a): ?>
                            <tr>
                                <td><?= e(formatTime($a["appt_time"])) ?></td>
                                <td><?= e($a["patient_name"]) ?></td>
                                <td><?= e($a["patient_phone"]) ?></td>
                                <td><?= status_badge($a["status"]) ?></td>
                                <td><?= e($a["reason"] ?? "") ?></td>
                                <td><a href="<?= url("appointments","view",["id"=>$a["id"]]) ?>" class="btn btn-xs btn-outline-info">Open</a></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($today)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">No appointments today.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Upcoming</h5></div>
                <div class="card-body p-0">
                    <table class="table table-vmid mb-0">
                        <thead><tr><th>Date</th><th>Time</th><th>Patient</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($upcoming as $a): ?>
                            <tr>
                                <td><?= e(formatDate($a["appt_date"])) ?></td>
                                <td><?= e(formatTime($a["appt_time"])) ?></td>
                                <td><?= e($a["patient_name"]) ?></td>
                                <td><?= status_badge($a["status"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($upcoming)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">No upcoming appointments.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . "/../partials/footer.php"; ?>
