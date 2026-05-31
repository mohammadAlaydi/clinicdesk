<?php
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../models/SpecializationModel.php";

class SpecializationController {

    public function index() {
        Auth::requireRole("admin");
        $sm = new SpecializationModel();
        $specs = $sm->getAll();

        $pageTitle = "Specializations";
        require __DIR__ . "/../views/specializations/index.php";
    }

    public function create() {
        Auth::requireRole("admin");
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            redirect(url("specializations"));
        }
        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            set_flash("danger", "Invalid CSRF.");
            redirect(url("specializations"));
        }
        $name = sanitize($_POST["name"] ?? "");
        if ($name === "") {
            set_flash("warning", "Name required.");
            redirect(url("specializations"));
        }
        $sm = new SpecializationModel();
        $sm->create($name);
        set_flash("success", "Added.");
        redirect(url("specializations"));
    }

    public function delete() {
        Auth::requireRole("admin");
        if ($_SERVER["REQUEST_METHOD"] !== "POST") redirect(url("specializations"));
        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            set_flash("danger", "Invalid CSRF.");
            redirect(url("specializations"));
        }
        $id = (int)($_POST["id"] ?? 0);
        $sm = new SpecializationModel();
        if (!$sm->isSafeToDelete($id)) {
            set_flash("danger", "Some doctors still use this.");
            redirect(url("specializations"));
        }
        $sm->delete($id);
        set_flash("success", "Deleted.");
        redirect(url("specializations"));
    }
}
