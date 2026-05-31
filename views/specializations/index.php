<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Specializations</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>
            <div class="row">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Add</h5></div>
                        <div class="card-body">
                            <form method="POST" action="<?= url("specializations","create") ?>">
                                <?= CSRF::field() ?>
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <button class="btn btn-primary">Add</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">All Specializations</h5></div>
                        <div class="card-body p-0">
                            <table class="table table-vmid mb-0">
                                <tbody>
                                <?php foreach ($specs as $s): ?>
                                    <tr>
                                        <td><?= e($s["name"]) ?></td>
                                        <td class="text-right">
                                            <form method="POST" action="<?= url("specializations","delete") ?>" style="display:inline">
                                                <?= CSRF::field() ?>
                                                <input type="hidden" name="id" value="<?= e($s["id"]) ?>">
                                                <button class="btn btn-xs btn-outline-danger" onclick="return confirm('Delete?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . "/../partials/footer.php"; ?>
