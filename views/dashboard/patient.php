<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Patient Dashboard</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <?php if ($nextAppt): ?>
                <div class="callout callout-info">
                    <h5>Next Appointment</h5>
                    <p class="mb-1">
                        <strong>Dr. <?= e($nextAppt["doctor_name"]) ?></strong>
                        (<?= e($nextAppt["specialization"]) ?>)
                        on <strong><?= e(formatDate($nextAppt["appt_date"])) ?></strong>
                        at <strong><?= e(formatTime($nextAppt["appt_time"])) ?></strong>
                    </p>
                    <p class="mb-0">Status: <?= status_badge($nextAppt["status"]) ?></p>
                </div>
            <?php else: ?>
                <div class="callout callout-warning">
                    <h5>No upcoming appointments</h5>
                    <p><a href="<?= url("appointments","book") ?>" class="btn btn-sm btn-primary">Book one</a></p>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner"><h3><?= (int)$stats["pending"] ?></h3><p>Pending</p></div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner"><h3><?= (int)$stats["confirmed"] ?></h3><p>Confirmed</p></div>
                        <div class="icon"><i class="fas fa-check"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner"><h3><?= (int)$stats["completed"] ?></h3><p>Completed</p></div>
                        <div class="icon"><i class="fas fa-check-double"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-secondary">
                        <div class="inner"><h3><?= (int)$prescCount ?></h3><p>Prescriptions</p></div>
                        <div class="icon"><i class="fas fa-prescription-bottle-alt"></i></div>
                        <a href="<?= url("prescriptions") ?>" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . "/../partials/footer.php"; ?>
