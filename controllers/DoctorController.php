<?php
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/Paginator.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__ . "/../models/SpecializationModel.php";

class DoctorController {

    public function index() {
        Auth::requireRole("admin");
        $dm = new DoctorModel();
        $page  = max(1, (int)($_GET["p"] ?? 1));
        $total = $dm->countAll();
        $docs  = $dm->getAllPaginated($page);
        $pg    = new Paginator($total, ITEMS_PER_PAGE, $page);

        $pageTitle = "Doctors";
        require __DIR__ . "/../views/doctors/index.php";
    }

    public function edit() {
        Auth::requireRole("admin", "doctor");
        $id = (int)($_GET["id"] ?? 0);
        $dm = new DoctorModel();
        $doc = $dm->findById($id);
        if (!$doc) {
            set_flash("danger", "Doctor not found.");
            redirect(url("doctors"));
        }

        if (Auth::role() === "doctor" && (int)$doc["user_id"] !== Auth::id()) {
            redirect(url("errors", "403"));
        }

        $sm = new SpecializationModel();
        $specializations = $sm->getAll();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleUpdate($id, $doc);
            return;
        }

        $pageTitle = "Edit Doctor";
        require __DIR__ . "/../views/doctors/edit.php";
    }

    private function handleUpdate($id, $doc) {
        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            set_flash("danger", "Invalid CSRF.");
            redirect(url("doctors", "edit", ["id" => $id]));
        }

        $days = $_POST["available_days"] ?? [];
        if (!is_array($days)) $days = [];
        if (empty($days)) $days = ["Sun"];

        $dm = new DoctorModel();
        $dm->update($id, [
            "specialization_id" => (int)($_POST["specialization_id"] ?? $doc["specialization_id"]),
            "bio"               => sanitize($_POST["bio"] ?? ""),
            "consultation_fee"  => (float)($_POST["consultation_fee"] ?? 0),
            "available_days"    => implode(",", $days),
        ]);

        if (!empty($_FILES["photo"]["name"])) {
            $r = $this->handlePhotoUpload($_FILES["photo"], $doc["user_id"]);
            if ($r["ok"]) {
                require_once __DIR__ . "/../models/UserModel.php";
                $um = new UserModel();
                $current = $um->findById((int)$doc["user_id"]);
                $um->update((int)$doc["user_id"], [
                    "name"   => $current["name"],
                    "phone"  => $current["phone"],
                    "avatar" => "doctor_photos/" . $r["name"],
                ]);
            } else {
                set_flash("warning", $r["msg"]);
            }
        }

        set_flash("success", "Doctor updated.");
        if (Auth::role() === "doctor") {
            redirect(url("doctors", "edit", ["id" => $id]));
        }
        redirect(url("doctors"));
    }

    private function handlePhotoUpload($file, $userId) {
        if ($file["error"] !== UPLOAD_ERR_OK) return ["ok"=>false,"msg"=>"Upload failed."];
        if ($file["size"] > MAX_IMAGE_SIZE)   return ["ok"=>false,"msg"=>"Photo too big (max 1MB)."];

        $info = @getimagesize($file["tmp_name"]);
        if (!$info) return ["ok"=>false,"msg"=>"Not a valid image."];
        $allowed = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
        if (!in_array($info[2], $allowed, true)) return ["ok"=>false,"msg"=>"Only JPG or PNG."];

        $ext  = $info[2] === IMAGETYPE_PNG ? "png" : "jpg";
        $name = "d" . $userId . "_" . time() . "." . $ext;
        $dest = UPLOAD_DOCTOR_PHOTO_PATH . $name;

        if (!move_uploaded_file($file["tmp_name"], $dest)) {
            return ["ok"=>false,"msg"=>"Could not save photo."];
        }
        return ["ok"=>true,"name"=>$name];
    }
}
