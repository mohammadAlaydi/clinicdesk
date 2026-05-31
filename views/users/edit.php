<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Edit User</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= url("users","edit",["id"=>$user["id"]]) ?>">
                        <?= CSRF::field() ?>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?= e($user["name"]) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" value="<?= e($user["email"]) ?>" disabled>
                            <small class="text-muted">Email can't be changed.</small>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= e($user["phone"]) ?>">
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <input type="text" class="form-control" value="<?= e($user["role"]) ?>" disabled>
                        </div>
                        <button class="btn btn-primary">Save</button>
                        <a href="<?= url("users") ?>" class="btn btn-light">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . "/../partials/footer.php"; ?>
