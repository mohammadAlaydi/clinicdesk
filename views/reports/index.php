<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Reports</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-body">
                    <form method="GET" class="form-row">
                        <input type="hidden" name="page" value="reports">
                        <div class="col-md-2">
                            <label>Start Date *</label>
                            <input type="date" name="start_date" class="form-control" value="<?= e($_GET["start_date"]??"") ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label>End Date *</label>
                            <input type="date" name="end_date" class="form-control" value="<?= e($_GET["end_date"]??"") ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label>Doctor</label>
                            <select name="doctor_id" class="form-control">
                                <option value="">All</option>
                                <?php foreach ($doctors as $d): ?>
                                    <option value="<?= e($d["id"]) ?>" <?= ($_GET["doctor_id"]??"")==$d["id"]?"selected":"" ?>>Dr. <?= e($d["name"]) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All</option>
                                <?php foreach (["pending","confirmed","completed","cancelled"] as $s): ?>
                                    <option value="<?= $s ?>" <?= ($_GET["status"]??"")===$s?"selected":"" ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-info mr-2">Run</button>
                            <?php
                                $qs = http_build_query(array_merge($_GET, ["export"=>"csv"]));
                            ?>
                            <a class="btn btn-success" href="<?= BASE_URL ?>index.php?<?= e($qs) ?>">
                                <i class="fas fa-file-csv"></i> CSV
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($rows)): ?>
                <div class="card">
                    <div class="card-header">
                        <strong>Total:</strong> <?= (int)$summary["total"] ?>
                        | Pending: <?= (int)$summary["pending"] ?>
                        | Confirmed: <?= (int)$summary["confirmed"] ?>
                        | Completed: <?= (int)$summary["completed"] ?>
                        | Cancelled: <?= (int)$summary["cancelled"] ?>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-vmid mb-0">
                            <thead>
                                <tr><th>#</th><th>Patient</th><th>Doctor</th><th>Specialization</th><th>Date</th><th>Time</th><th>Status</th><th>Reason</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rows as $r): ?>
                                <tr>
                                    <td>#<?= e($r["id"]) ?></td>
                                    <td><?= e($r["patient_name"]) ?></td>
                                    <td><?= e($r["doctor_name"]) ?></td>
                                    <td><?= e($r["specialization"]) ?></td>
                                    <td><?= e(formatDate($r["appt_date"])) ?></td>
                                    <td><?= e(formatTime($r["appt_time"])) ?></td>
                                    <td><?= status_badge($r["status"]) ?></td>
                                    <td><?= e($r["reason"] ?? "") ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php require __DIR__ . "/../partials/footer.php"; ?>
