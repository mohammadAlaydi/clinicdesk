<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Book Appointment</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= url("appointments","book") ?>">
                        <?= CSRF::field() ?>
                        <div class="form-group">
                            <label>Doctor</label>
                            <select name="doctor_id" id="doctorSel" class="form-control" required>
                                <option value="">-- choose --</option>
                                <?php foreach ($doctors as $d): ?>
                                    <option value="<?= e($d["id"]) ?>" data-days="<?= e($d["available_days"]) ?>">
                                        Dr. <?= e($d["name"]) ?> — <?= e($d["specialization"]) ?> ($<?= e(number_format($d["consultation_fee"],2)) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Available days: <span id="daysHint">—</span></small>
                        </div>

                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="appt_date" min="<?= date("Y-m-d") ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Time Slot</label>
                                    <select name="appt_time" class="form-control" required>
                                        <option value="">-- pick --</option>
                                        <?php foreach ($TIME_SLOTS as $slot): ?>
                                            <option value="<?= $slot ?>"><?= $slot ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Reason (optional)</label>
                            <input type="text" name="reason" class="form-control" maxlength="200">
                        </div>

                        <button class="btn btn-primary">Book</button>
                        <a href="<?= url("appointments") ?>" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . "/../partials/footer.php"; ?>
<script>
    $("#doctorSel").on("change", function() {
        var d = $(this).find(":selected").data("days") || "-";
        $("#daysHint").text(d);
    });
</script>
