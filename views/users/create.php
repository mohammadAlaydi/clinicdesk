<?php require __DIR__ . "/../partials/header.php"; ?>
<?php require __DIR__ . "/../partials/navbar.php"; ?>
<?php require __DIR__ . "/../partials/sidebar.php"; ?>

<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>New User</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= url("users","create") ?>">
                        <?= CSRF::field() ?>

                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Temporary Password</label>
                                    <input type="text" name="password" class="form-control" minlength="6" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="role" id="roleSel" class="form-control" required>
                                        <option value="patient">Patient</option>
                                        <option value="doctor">Doctor</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div id="doctorFields" style="display:none;">
                            <hr>
                            <h5>Doctor Profile</h5>
                            <div class="form-row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Specialization</label>
                                        <select name="specialization_id" class="form-control">
                                            <?php foreach ($specializations as $s): ?>
                                                <option value="<?= e($s["id"]) ?>"><?= e($s["name"]) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Consultation Fee</label>
                                        <input type="number" step="0.01" name="consultation_fee" class="form-control" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Bio</label>
                                <textarea name="bio" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Available Days</label><br>
                                <?php foreach (["Sun","Mon","Tue","Wed","Thu","Fri","Sat"] as $d): ?>
                                    <label class="mr-3">
                                        <input type="checkbox" name="available_days[]" value="<?= $d ?>" <?= in_array($d,["Sun","Mon","Tue","Wed","Thu"])?"checked":"" ?>>
                                        <?= $d ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button class="btn btn-primary">Create</button>
                        <a href="<?= url("users") ?>" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . "/../partials/footer.php"; ?>
<script>

    $("#roleSel").on("change", function() {
        $("#doctorFields").toggle(this.value === "doctor");
    });
</script>
