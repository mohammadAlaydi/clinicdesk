<?php
$role = Auth::role();
$cu   = Auth::currentUser();
$cur  = $_GET["page"] ?? "dashboard";
function active($pageName, $current) {
    return $pageName === $current ? "active" : "";
}
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?= url("dashboard") ?>" class="brand-link">
        <span class="brand-text font-weight-light"><i class="fas fa-clinic-medical mr-2"></i> <?= e(APP_NAME) ?></span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <?php if (!empty($cu["avatar"])): ?>
                    <img src="<?= uploads_url($cu["avatar"]) ?>" class="img-circle elevation-2" alt="">
                <?php else: ?>
                    <i class="fas fa-user-circle fa-2x text-white"></i>
                <?php endif; ?>
            </div>
            <div class="info">
                <a href="<?= url("users","profile") ?>" class="d-block"><?= e($cu["name"]) ?></a>
                <small class="text-muted"><?= e(ucfirst($role)) ?></small>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="<?= url("dashboard") ?>" class="nav-link <?= active("dashboard",$cur) ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if ($role === "admin"): ?>
                    <li class="nav-item">
                        <a href="<?= url("users") ?>" class="nav-link <?= active("users",$cur) ?>">
                            <i class="nav-icon fas fa-users"></i><p>Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("doctors") ?>" class="nav-link <?= active("doctors",$cur) ?>">
                            <i class="nav-icon fas fa-user-md"></i><p>Doctors</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("specializations") ?>" class="nav-link <?= active("specializations",$cur) ?>">
                            <i class="nav-icon fas fa-stethoscope"></i><p>Specializations</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("appointments") ?>" class="nav-link <?= active("appointments",$cur) ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i><p>All Appointments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("reports") ?>" class="nav-link <?= active("reports",$cur) ?>">
                            <i class="nav-icon fas fa-file-csv"></i><p>Reports</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === "doctor"): ?>
                    <li class="nav-item">
                        <a href="<?= url("appointments") ?>" class="nav-link <?= active("appointments",$cur) ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i><p>My Schedule</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === "patient"): ?>
                    <li class="nav-item">
                        <a href="<?= url("appointments","book") ?>" class="nav-link <?= ($cur==="appointments" && ($_GET["action"]??"")==="book") ? "active":"" ?>">
                            <i class="nav-icon fas fa-plus-circle"></i><p>Book Appointment</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("appointments") ?>" class="nav-link <?= active("appointments",$cur) ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i><p>My Appointments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("prescriptions") ?>" class="nav-link <?= active("prescriptions",$cur) ?>">
                            <i class="nav-icon fas fa-prescription-bottle-alt"></i><p>My Prescriptions</p>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item mt-3">
                    <a href="<?= url("users","profile") ?>" class="nav-link">
                        <i class="nav-icon fas fa-user"></i><p>Profile</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
