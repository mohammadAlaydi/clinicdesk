<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<?php
$role = Auth::role();

$logs = [];
if ($role === "admin") {
    require_once __DIR__ . "/../../models/AppointmentLogModel.php";
    $logs = (new AppointmentLogModel())->getByAppointment((int)$appt["id"]);
}
?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Appointment #<?= e($appt["id"]) ?></h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Details</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Patient</dt><dd class="col-sm-8"><?= e($appt["patient_name"]) ?></dd>
                                <dt class="col-sm-4">Doctor</dt><dd class="col-sm-8"><?= e($appt["doctor_name"]) ?> (<?= e($appt["specialization"]) ?>)</dd>
                                <dt class="col-sm-4">Date</dt><dd class="col-sm-8"><?= e(formatDate($appt["appt_date"])) ?></dd>
                                <dt class="col-sm-4">Time</dt><dd class="col-sm-8"><?= e(formatTime($appt["appt_time"])) ?></dd>
                                <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><?= status_badge($appt["status"]) ?></dd>
                                <dt class="col-sm-4">Reason</dt><dd class="col-sm-8"><?= e($appt["reason"] ?? "—") ?></dd>
                                <?php if (!empty($appt["doctor_notes"])): ?>
                                    <dt class="col-sm-4">Doctor's Notes</dt>
                                    <dd class="col-sm-8"><?= nl2br(e($appt["doctor_notes"])) ?></dd>
                                <?php endif; ?>
                                <?php if (!empty($appt["cancellation_reason"])): ?>
                                    <dt class="col-sm-4 text-danger">Cancellation Reason</dt>
                                    <dd class="col-sm-8"><?= nl2br(e($appt["cancellation_reason"])) ?></dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </div>

                    <?php if ($pres): ?>
                        <div class="card mt-3">
                            <div class="card-header"><h5 class="card-title mb-0">Prescription</h5></div>
                            <div class="card-body">
                                <p><strong>Diagnosis:</strong><br><?= nl2br(e($pres["diagnosis"])) ?></p>
                                <p><strong>Medications:</strong><br><?= nl2br(e($pres["medications"])) ?></p>
                                <?php if (!empty($pres["notes"])): ?>
                                    <p><strong>Notes:</strong><br><?= nl2br(e($pres["notes"])) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($pres["file_path"])): ?>
                                    <a href="<?= url("prescriptions","download",["id"=>$pres["id"]]) ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-file-pdf"></i> Download PDF
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($role === "admin" && !empty($logs)): ?>
                        <div class="card mt-3">
                            <div class="card-header"><h5 class="card-title mb-0">Status History</h5></div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-vmid mb-0">
                                    <thead>
                                        <tr><th>When</th><th>Who</th><th>From</th><th>To</th><th>Note</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logs as $lg): ?>
                                            <tr>
                                                <td><?= e(formatDateTimeNice($lg["changed_at"])) ?></td>
                                                <td><?= e($lg["user_name"]) ?> <small class="text-muted">(<?= e($lg["user_role"]) ?>)</small></td>
                                                <td><?= status_badge($lg["old_status"]) ?></td>
                                                <td><?= status_badge($lg["new_status"]) ?></td>
                                                <td><small><?= e($lg["note"] ?? "") ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-5">
                    <?php if (($role === "doctor" || $role === "admin") && $appt["status"] !== "cancelled"): ?>
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Update Status</h5></div>
                            <div class="card-body">
                                <form method="POST" action="<?= url("appointments","update_status") ?>">
                                    <?= CSRF::field() ?>
                                    <input type="hidden" name="id" value="<?= e($appt["id"]) ?>">
                                    <div class="form-group">
                                        <label>New Status</label>
                                        <select name="status" class="form-control">
                                            <?php foreach (["pending","confirmed","completed"] as $s): ?>
                                                <option value="<?= $s ?>" <?= $appt["status"]===$s?"selected":"" ?>><?= ucfirst($s) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Doctor Notes (optional)</label>
                                        <textarea name="doctor_notes" class="form-control" rows="3"><?= e($appt["doctor_notes"] ?? "") ?></textarea>
                                    </div>
                                    <button class="btn btn-primary">Update</button>
                                </form>

                                <hr>
                                <a href="<?= url("appointments","cancel",["id"=>$appt["id"]]) ?>" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-times"></i> Cancel Appointment
                                </a>
                            </div>
                        </div>

                        <?php if ($role==="doctor" && $appt["status"]==="completed" && !$pres): ?>
                            <div class="card mt-3">
                                <div class="card-body text-center">
                                    <a href="<?= url("prescriptions","add",["appt_id"=>$appt["id"]]) ?>" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Add Prescription
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($role === "patient" && $appt["status"]==="pending"): ?>
                        <div class="card">
                            <div class="card-body text-center">
                                <a href="<?= url("appointments","cancel",["id"=>$appt["id"]]) ?>" class="btn btn-danger">
                                    Cancel Appointment
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <a href="<?= url("appointments") ?>" class="btn btn-light mt-2">Back to list</a>
        </div>
    </section>
</div>
<?php require __DIR__ . "/../partials/footer.php"; ?>
