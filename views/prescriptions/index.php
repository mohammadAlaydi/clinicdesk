<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>My Prescriptions</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-vmid mb-0">
                        <thead><tr><th>Date</th><th>Doctor</th><th>Diagnosis</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($rows as $p): ?>
                            <tr>
                                <td><?= e(formatDate($p["appt_date"])) ?></td>
                                <td>Dr. <?= e($p["doctor_name"]) ?></td>
                                <td>
                                    <?php
                                        $diag = $p["diagnosis"];
                                        echo e(mb_strlen($diag) > 80 ? mb_substr($diag, 0, 80) . "..." : $diag);
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= url("appointments","view",["id"=>$p["appointment_id"]]) ?>" class="btn btn-xs btn-outline-info">View</a>
                                    <?php if (!empty($p["file_path"])): ?>
                                        <a href="<?= url("prescriptions","download",["id"=>$p["id"]]) ?>" class="btn btn-xs btn-outline-success">PDF</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($rows)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">No prescriptions.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . "/../partials/footer.php"; ?>
