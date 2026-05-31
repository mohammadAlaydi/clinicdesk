<?php
require_once __DIR__ . "/../config/config.php";

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function e($val) {
    return htmlspecialchars((string)$val, ENT_QUOTES, "UTF-8");
}

function set_flash($type, $msg) {
    $_SESSION["flash"][] = ["type" => $type, "msg" => $msg];
}

function get_flashes() {
    $f = $_SESSION["flash"] ?? [];
    unset($_SESSION["flash"]);
    return $f;
}

function sanitize($v) {
    return trim(strip_tags((string)$v));
}

function formatDate($d) {
    if (!$d) return "";
    return date("Y-m-d", strtotime($d));
}

function formatTime($t) {
    if (!$t) return "";
    return date("H:i", strtotime($t));
}

function formatDateTimeNice($d) {
    if (!$d) return "";
    return date("d M Y, H:i", strtotime($d));
}

function url($page, $action = null, $extra = []) {
    $u = BASE_URL . "index.php?page=" . urlencode($page);
    if ($action) $u .= "&action=" . urlencode($action);
    foreach ($extra as $k => $v) {
        $u .= "&" . urlencode($k) . "=" . urlencode($v);
    }
    return $u;
}

function uploads_url($path) {
    return BASE_URL . "public/uploads/" . ltrim($path, "/");
}

function status_badge($status) {
    $map = [
        "pending"   => "warning",
        "confirmed" => "info",
        "completed" => "success",
        "cancelled" => "danger",
    ];
    $cls = $map[$status] ?? "secondary";
    return '<span class="badge badge-' . $cls . '">' . e(ucfirst($status)) . '</span>';
}

function dow3($date) {
    return date("D", strtotime($date));
}
