<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Doctors</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-vmid">
                        <thead>
                            <tr><th>#</th><th>Name</th><th>Specialization</th><th>Fee</th><th>Available</th><th>Active</th><th></th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($docs as $d): ?>
                            <tr>
                                <td><?= e($d["id"]) ?></td>
                                <td><?= e($d["name"]) ?><br><small class="text-muted"><?= e($d["email"]) ?></small></td>
                                <td><?= e($d["specialization"]) ?></td>
                                <td>$<?= e(number_format((float)$d["consultation_fee"],2)) ?></td>
                                <td><small><?= e($d["available_days"]) ?></small></td>
                                <td><?= (int)$d["is_active"]===1 ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>' ?></td>
                                <td><a href="<?= url("doctors","edit",["id"=>$d["id"]]) ?>" class="btn btn-xs btn-outline-primary">Edit</a></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($docs)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-3">No doctors yet.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <?php $base = url("doctors"); require __DIR__ . "/../partials/paginator.php"; ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . "/../partials/footer.php"; ?>
