<?php

$pageTitle = "Change Password";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo"><b><?= e(APP_NAME) ?></b></div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">
                <i class="fas fa-key"></i> First login - change your password to continue.
            </p>
            <?php require __DIR__ . "/../partials/alerts.php"; ?>
            <form method="POST" action="<?= url("auth","change_password") ?>">
                <?= CSRF::field() ?>
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-control" required autofocus>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control" required minlength="8">
                    <small class="text-muted">Min 8 chars, with lowercase, UPPERCASE and a number.</small>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update Password</button>
            </form>

            <form method="POST" action="<?= url("auth","logout") ?>" class="mt-3 text-center">
                <?= CSRF::field() ?>
                <button class="btn btn-link btn-sm">Logout</button>
            </form>
        </div>
    </div>
</div>
<script src="<?= BASE_URL ?>public/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
