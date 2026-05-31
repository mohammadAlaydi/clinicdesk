<?php

$flashes = get_flashes();
if (!empty($flashes)):
    foreach ($flashes as $f):
        $type = $f["type"] ?? "info";
?>
    <div class="alert alert-<?= e($type) ?> alert-dismissible fade show" role="alert">
        <?= e($f["msg"]) ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php
    endforeach;
endif;
?>
