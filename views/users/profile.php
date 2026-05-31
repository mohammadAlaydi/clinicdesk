<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>My Profile</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <?php if (!empty($user["avatar"])): ?>
                                <img src="<?= uploads_url($user["avatar"]) ?>" class="img-circle elevation-2 mb-2" style="width:120px;height:120px;object-fit:cover;">
                            <?php else: ?>
                                <i class="fas fa-user-circle fa-7x text-muted mb-2"></i>
                            <?php endif; ?>
                            <h4 class="mb-0"><?= e($user["name"]) ?></h4>
                            <small class="text-muted"><?= e(ucfirst($user["role"])) ?></small>
                            <p class="text-muted mt-2 mb-0"><?= e($user["email"]) ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="<?= url("users","profile") ?>" enctype="multipart/form-data">
                                <?= CSRF::field() ?>

                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="<?= e($user["name"]) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control" value="<?= e($user["phone"]) ?>">
                                </div>
                                <div class="form-group">
                                    <label>Avatar (JPG/PNG, max 1MB)</label>
                                    <input type="file" name="avatar" accept="image/*" class="form-control-file">
                                </div>

                                <hr>
                                <h6>Change Password (optional)</h6>
                                <div class="form-row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Current Password</label>
                                            <input type="password" name="current_password" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>New Password</label>
                                            <input type="password" name="new_password" class="form-control" minlength="6">
                                        </div>
                                    </div>
                                </div>

                                <button class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . "/../partials/footer.php"; ?>
