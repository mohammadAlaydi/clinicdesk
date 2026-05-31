<?php
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/PrescriptionModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";

class PrescriptionController {

    public function add() {
        Auth::requireRole("doctor");

        $apptId = (int)($_GET["appt_id"] ?? $_POST["appt_id"] ?? 0);
        $am = new AppointmentModel();
        $appt = $am->findById($apptId);
        if (!$appt) {
            set_flash("danger", "Appointment not found.");
            redirect(url("appointments"));
        }

        $dm = new DoctorModel();
        $d = $dm->findByUserId(Auth::id());
        if (!$d || (int)$appt["doctor_id"] !== (int)$d["id"]) {
            redirect(url("errors", "403"));
        }

        if ($appt["status"] !== "completed") {
            set_flash("warning", "Mark the appointment as completed first.");
            redirect(url("appointments", "view", ["id" => $apptId]));
        }

        $pm = new PrescriptionModel();
        if ($pm->findByAppointmentId($apptId)) {
            set_flash("warning", "Prescription already added.");
            redirect(url("appointments", "view", ["id" => $apptId]));
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleAdd($apptId);
            return;
        }

        $pageTitle = "Add Prescription";
        require __DIR__ . "/../views/prescriptions/add.php";
    }

    private function handleAdd(int $apptId) {
        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            set_flash("danger", "Invalid CSRF.");
            redirect(url("prescriptions","add",["appt_id"=>$apptId]));
        }

        $diagnosis   = sanitize($_POST["diagnosis"]   ?? "");
        $medications = sanitize($_POST["medications"] ?? "");
        $notes       = sanitize($_POST["notes"]       ?? "");

        if ($diagnosis === "" || $medications === "") {
            set_flash("warning", "Diagnosis and medications required.");
            redirect(url("prescriptions","add",["appt_id"=>$apptId]));
        }

        $filePath = null;
        if (!empty($_FILES["prescription_file"]["name"])) {
            $r = $this->handlePdfUpload($_FILES["prescription_file"], $apptId);
            if (!$r["ok"]) {
                set_flash("danger", $r["msg"]);
                redirect(url("prescriptions","add",["appt_id"=>$apptId]));
            }
            $filePath = $r["name"];
        }

        $pm = new PrescriptionModel();
        $pm->create([
            "appointment_id" => $apptId,
            "diagnosis"      => $diagnosis,
            "medications"    => $medications,
            "notes"          => $notes,
            "file_path"      => $filePath,
        ]);

        set_flash("success", "Prescription added.");
        redirect(url("appointments","view",["id"=>$apptId]));
    }

    private function handlePdfUpload($file, $apptId) {
        if ($file["error"] !== UPLOAD_ERR_OK) return ["ok"=>false,"msg"=>"Upload failed."];
        if ($file["size"] > MAX_PDF_SIZE) return ["ok"=>false,"msg"=>"PDF too big (max 3MB)."];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file["tmp_name"]);
        if ($mime !== "application/pdf") {
            return ["ok"=>false,"msg"=>"Only PDF allowed."];
        }

        $name = "prescription_" . $apptId . "_" . time() . ".pdf";
        $dest = UPLOAD_PRESCRIPTION_PATH . $name;
        if (!move_uploaded_file($file["tmp_name"], $dest)) {
            return ["ok"=>false,"msg"=>"Could not save file."];
        }
        return ["ok"=>true,"name"=>$name];
    }

    public function index() {
        Auth::requireRole("patient");
        $pm = new PrescriptionModel();
        $rows = $pm->getByPatient(Auth::id());
        $pageTitle = "My Prescriptions";
        require __DIR__ . "/../views/prescriptions/index.php";
    }

    public function download() {
        Auth::requireRole("admin","doctor","patient");
        $id = (int)($_GET["id"] ?? 0);

        $pm = new PrescriptionModel();
        $pr = $pm->findById($id);
        if (!$pr || empty($pr["file_path"])) {
            set_flash("danger", "No file for this prescription.");
            redirect(url("dashboard"));
        }

        $am = new AppointmentModel();
        $appt = $am->findById((int)$pr["appointment_id"]);
        if (!$appt) {
            redirect(url("errors","404"));
        }

        $role = Auth::role();
        $ok = false;
        if ($role === "admin") {
            $ok = true;
        } elseif ($role === "patient") {
            $ok = ((int)$appt["patient_id"] === Auth::id());
        } elseif ($role === "doctor") {
            $dm = new DoctorModel();
            $d  = $dm->findByUserId(Auth::id());
            $ok = $d && ((int)$appt["doctor_id"] === (int)$d["id"]);
        }
        if (!$ok) {
            redirect(url("errors","403"));
        }

        $path = UPLOAD_PRESCRIPTION_PATH . basename($pr["file_path"]);
        if (!is_file($path)) {
            set_flash("danger", "File is missing.");
            redirect(url("dashboard"));
        }

        header("Content-Type: application/pdf");
        header('Content-Disposition: attachment; filename="prescription.pdf"');
        header("Content-Length: " . filesize($path));
        readfile($path);
        exit;
    }
}
