<?php

session_start();

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/core/helpers.php";
require_once __DIR__ . "/core/Auth.php";
require_once __DIR__ . "/core/CSRF.php";

$page   = $_GET["page"]   ?? "dashboard";
$action = $_GET["action"] ?? "index";

$routes = [
    "auth"            => ["AuthController",           "AuthController.php"],
    "dashboard"       => ["DashboardController",      "DashboardController.php"],
    "users"           => ["UserController",           "UserController.php"],
    "doctors"         => ["DoctorController",         "DoctorController.php"],
    "specializations" => ["SpecializationController", "SpecializationController.php"],
    "appointments"    => ["AppointmentController",    "AppointmentController.php"],
    "prescriptions"   => ["PrescriptionController",   "PrescriptionController.php"],
    "reports"         => ["ReportController",         "ReportController.php"],
    "errors"          => ["ErrorController",          "ErrorController.php"],
];

if (!isset($routes[$page])) {
    require_once __DIR__ . "/controllers/ErrorController.php";
    (new ErrorController())->notFound();
    exit;
}

[$cls, $file] = $routes[$page];
require_once __DIR__ . "/controllers/" . $file;
$controller = new $cls();

$method = lcfirst(str_replace("_", "", ucwords($action, "_")));

if ($page === "errors") {
    if ($action === "403") $method = "forbidden";
    if ($action === "404") $method = "notFound";
}

if (!method_exists($controller, $method)) {
    require_once __DIR__ . "/controllers/ErrorController.php";
    (new ErrorController())->notFound();
    exit;
}

$controller->$method();
