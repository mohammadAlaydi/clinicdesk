<?php

if ($pg->totalPages() <= 1) return;

function pagerLink($base, $page, $label, $disabled = false, $active = false) {
    $cls = "page-item";
    if ($disabled) $cls .= " disabled";
    if ($active)   $cls .= " active";
    $sep = strpos($base, "?") !== false ? "&" : "?";
    $href = $base . $sep . "p=" . $page;
    echo '<li class="'.$cls.'"><a class="page-link" href="'.e($href).'">'.$label.'</a></li>';
}
?>
<nav>
    <ul class="pagination pagination-sm justify-content-center mt-3">
        <?php pagerLink($base, max(1, $pg->currentPage() - 1), "&laquo;", !$pg->hasPrev()); ?>
        <?php for ($i = 1; $i <= $pg->totalPages(); $i++): ?>
            <?php pagerLink($base, $i, (string)$i, false, $i === $pg->currentPage()); ?>
        <?php endfor; ?>
        <?php pagerLink($base, min($pg->totalPages(), $pg->currentPage() + 1), "&raquo;", !$pg->hasNext()); ?>
    </ul>
</nav>
