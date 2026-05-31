<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<?php $currentDays = explode(",", $doc["available_days"]); ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Edit Doctor</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= url("doctors","edit",["id"=>$doc["id"]]) ?>" enctype="multipart/form-data">
                        <?= CSRF::field() ?>

                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" value="<?= e($doc["name"]) ?>" disabled>
                        </div>

                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Specialization</label>
                                    <select name="specialization_id" class="form-control">
                                        <?php foreach ($specializations as $s): ?>
                                            <option value="<?= e($s["id"]) ?>" <?= (int)$s["id"]===(int)$doc["specialization_id"] ? "selected":"" ?>>
                                                <?= e($s["name"]) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Consultation Fee</label>
                                    <input type="number" step="0.01" name="consultation_fee" class="form-control" value="<?= e($doc["consultation_fee"]) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Bio</label>
                            <textarea name="bio" class="form-control" rows="3"><?= e($doc["bio"] ?? "") ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Available Days</label><br>
                            <?php foreach (["Sun","Mon","Tue","Wed","Thu","Fri","Sat"] as $d): ?>
                                <label class="mr-3">
                                    <input type="checkbox" name="available_days[]" value="<?= $d ?>" <?= in_array($d,$currentDays)?"checked":"" ?>>
                                    <?= $d ?>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="form-group">
                            <label>Profile Photo (JPG/PNG, max 1MB)</label>
                            <input type="file" name="photo" class="form-control-file" accept="image/*">
                        </div>

                        <button class="btn btn-primary">Save</button>
                        <a href="<?= url("doctors") ?>" class="btn btn-light">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . "/../partials/footer.php"; ?>
