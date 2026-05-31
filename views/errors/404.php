<?php require __DIR__ . "/../partials/header.php"; ?>
<?php if (Auth::check()): ?>
    <?php require __DIR__ . "/../partials/navbar.php"; ?>
    <?php require __DIR__ . "/../partials/sidebar.php"; ?>
<?php endif; ?>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="error-page mt-5">
                <h2 class="headline text-info">404</h2>
                <div class="error-content">
                    <h3><i class="fas fa-question-circle text-info"></i> Page not found</h3>
                    <p>This page doesn't exist.</p>
                    <a href="<?= url("dashboard") ?>" class="btn btn-primary">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . "/../partials/footer.php"; ?>
