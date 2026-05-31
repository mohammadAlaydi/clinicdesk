<?php
$cu = Auth::currentUser();
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= url("dashboard") ?>" class="nav-link">Home</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <?php if (!empty($cu["avatar"])): ?>
                    <img src="<?= uploads_url($cu["avatar"]) ?>" class="avatar-sm mr-1" alt="">
                <?php else: ?>
                    <i class="fas fa-user-circle mr-1"></i>
                <?php endif; ?>
                <?= e($cu["name"]) ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?= url("users","profile") ?>"><i class="fas fa-user mr-2"></i> Profile</a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="<?= url("auth","logout") ?>" class="px-2">
                    <?= CSRF::field() ?>
                    <button type="submit" class="btn btn-sm btn-link p-0"><i class="fas fa-sign-out-alt mr-1"></i> Logout</button>
                </form>
            </div>
        </li>
    </ul>
</nav>
