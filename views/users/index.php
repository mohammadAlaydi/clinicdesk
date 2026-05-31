<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Users</h1>
            <a href="<?= url("users","create") ?>" class="btn btn-primary"><i class="fas fa-plus"></i> New User</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-body">
                    <form method="GET" class="form-inline mb-3">
                        <input type="hidden" name="page" value="users">
                        <input type="text" name="search" value="<?= e($_GET["search"] ?? "") ?>" placeholder="Name or email" class="form-control form-control-sm mr-2">
                        <select name="role" class="form-control form-control-sm mr-2">
                            <option value="">All roles</option>
                            <?php foreach (["admin","doctor","patient"] as $r): ?>
                                <option value="<?= $r ?>" <?= ($_GET["role"] ?? "")===$r?"selected":"" ?>><?= ucfirst($r) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-info">Filter</button>
                        <a href="<?= url("users") ?>" class="btn btn-sm btn-light ml-2">Reset</a>
                    </form>

                    <table class="table table-vmid table-hover">
                        <thead>
                            <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Phone</th><th>Active</th><th>Joined</th><th></th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= e($u["id"]) ?></td>
                                <td><?= e($u["name"]) ?></td>
                                <td><?= e($u["email"]) ?></td>
                                <td><span class="badge badge-secondary"><?= e($u["role"]) ?></span></td>
                                <td><?= e($u["phone"]) ?></td>
                                <td>
                                    <?php if ((int)$u["is_active"]===1): ?>
                                        <span class="badge badge-success">Yes</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e(formatDate($u["created_at"])) ?></td>
                                <td class="text-right">
                                    <a href="<?= url("users","edit",["id"=>$u["id"]]) ?>" class="btn btn-xs btn-outline-primary">Edit</a>
                                    <?php if ($u["id"] != Auth::id()): ?>
                                        <form method="POST" action="<?= url("users","toggle") ?>" style="display:inline">
                                            <?= CSRF::field() ?>
                                            <input type="hidden" name="id" value="<?= e($u["id"]) ?>">
                                            <button class="btn btn-xs btn-outline-warning" onclick="return confirm('Toggle status?')">Toggle</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                            <tr><td colspan="8" class="text-center text-muted py-3">No users.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                    <?php
                    $base = url("users") . "&role=" . urlencode($_GET["role"]??"") . "&search=" . urlencode($_GET["search"]??"");
                    require __DIR__ . "/../partials/paginator.php";
                    ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . "/../partials/footer.php"; ?>
