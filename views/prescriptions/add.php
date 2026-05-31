<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Add Prescription</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>
            <div class="card">
                <div class="card-body">
                    <p class="text-muted">Appointment #<?= e($appt["id"]) ?> &mdash; <?= e($appt["patient_name"]) ?> &mdash; <?= e(formatDate($appt["appt_date"])) ?></p>
                    <form method="POST" action="<?= url("prescriptions","add") ?>" enctype="multipart/form-data">
                        <?= CSRF::field() ?>
                        <input type="hidden" name="appt_id" value="<?= e($appt["id"]) ?>">

                        <div class="form-group">
                            <label>Diagnosis *</label>
                            <textarea name="diagnosis" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Medications *</label>
                            <textarea name="medications" class="form-control" rows="3" required placeholder="One per line"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label>PDF (optional, max 3MB)</label>
                            <input type="file" name="prescription_file" class="form-control-file" accept="application/pdf">
                        </div>
                        <button class="btn btn-primary">Save Prescription</button>
                        <a href="<?= url("appointments","view",["id"=>$appt["id"]]) ?>" class="btn btn-light">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . "/../partials/footer.php"; ?>
