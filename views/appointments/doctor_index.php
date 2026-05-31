<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>My Schedule</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-body">
                    <form method="GET" class="form-inline mb-3">
                        <input type="hidden" name="page" value="appointments">
                        <select name="status" class="form-control form-control-sm mr-2">
                            <option value="">All</option>
                            <?php foreach (["pending","confirmed","completed","cancelled"] as $s): ?>
                                <option value="<?= $s ?>" <?= ($_GET["status"]??"")===$s?"selected":"" ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="date" name="start_date" class="form-control form-control-sm mr-2" value="<?= e($_GET["start_date"]??"") ?>">
                        <input type="date" name="end_date" class="form-control form-control-sm mr-2" value="<?= e($_GET["end_date"]??"") ?>">
                        <button class="btn btn-sm btn-info">Filter</button>
                        <a href="<?= url("appointments") ?>" class="btn btn-sm btn-light ml-2">Reset</a>
                    </form>

                    <table class="table table-vmid">
                        <thead>
                            <tr><th>Date</th><th>Time</th><th>Patient</th><th>Phone</th><th>Status</th><th>Reason</th><th></th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rows as $a): ?>
                            <tr>
                                <td><?= e(formatDate($a["appt_date"])) ?></td>
                                <td><?= e(formatTime($a["appt_time"])) ?></td>
                                <td><?= e($a["patient_name"]) ?></td>
                                <td><?= e($a["patient_phone"]) ?></td>
                                <td><?= status_badge($a["status"]) ?></td>
                                <td><?= e($a["reason"] ?? "") ?></td>
                                <td class="text-right">
                                    <a href="<?= url("appointments","view",["id"=>$a["id"]]) ?>" class="btn btn-xs btn-outline-info">Open</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($rows)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-3">No appointments.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                    <?php
                    $base = url("appointments") . "&status=" . urlencode($_GET["status"]??"")
                        . "&start_date=" . urlencode($_GET["start_date"]??"")
                        . "&end_date="   . urlencode($_GET["end_date"]??"");
                    require __DIR__ . "/../partials/paginator.php";
                    ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . "/../partials/footer.php"; ?>
