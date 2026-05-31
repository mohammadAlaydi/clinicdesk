<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Cancel Appointment</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-body">
                    <p>
                        Appointment <strong>#<?= e($appt["id"]) ?></strong>
                        - Dr. <?= e($appt["doctor_name"]) ?>
                        on <?= e(formatDate($appt["appt_date"])) ?> at <?= e(formatTime($appt["appt_time"])) ?>.
                    </p>
                    <p class="text-muted">Current status: <?= status_badge($appt["status"]) ?></p>

                    <form method="POST" action="<?= url("appointments","cancel") ?>">
                        <?= CSRF::field() ?>
                        <input type="hidden" name="id" value="<?= e($appt["id"]) ?>">
                        <div class="form-group">
                            <label>Reason <span class="text-danger">*</span></label>
                            <textarea name="cancellation_reason" class="form-control" rows="3" minlength="10" required></textarea>
                            <small class="text-muted">Min 10 characters.</small>
                        </div>
                        <button class="btn btn-danger" onclick="return confirm('Cancel this appointment?')">
                            <i class="fas fa-times"></i> Confirm Cancel
                        </button>
                        <a href="<?= url("appointments","view",["id"=>$appt["id"]]) ?>" class="btn btn-light">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . "/../partials/footer.php"; ?>
