<?php
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/Paginator.php";
require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/AppointmentLogModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";

class AppointmentController {

    public function index() {
        Auth::requireRole("admin","doctor","patient");
        $role = Auth::role();

        $page = max(1, (int)($_GET["p"] ?? 1));
        $filters = [
            "status"         => trim($_GET["status"]     ?? ""),
            "doctor_id"      => trim($_GET["doctor_id"]  ?? ""),
            "start_date"     => trim($_GET["start_date"] ?? ""),
            "end_date"       => trim($_GET["end_date"]   ?? ""),
            "patient_search" => trim($_GET["patient"]    ?? ""),
        ];

        $am = new AppointmentModel();
        $dm = new DoctorModel();

        if ($role === "admin") {
            $total = $am->countAll($filters);
            $rows  = $am->getAll($page, $filters);
            $pg    = new Paginator($total, ITEMS_PER_PAGE, $page);
            $doctors = $dm->getAll();
            $pageTitle = "All Appointments";
            require __DIR__ . "/../views/appointments/admin_index.php";
            return;
        }

        if ($role === "doctor") {
            $doctor = $dm->findByUserId(Auth::id());
            if (!$doctor) {
                set_flash("warning", "No doctor profile.");
                redirect(url("dashboard"));
            }
            $total = $am->countByDoctor((int)$doctor["id"], $filters);
            $rows  = $am->getByDoctor((int)$doctor["id"], $page, $filters);
            $pg    = new Paginator($total, ITEMS_PER_PAGE, $page);
            $pageTitle = "My Schedule";
            require __DIR__ . "/../views/appointments/doctor_index.php";
            return;
        }

        $total = $am->countByPatient(Auth::id(), $filters);
        $rows  = $am->getByPatient(Auth::id(), $page, $filters);
        $pg    = new Paginator($total, ITEMS_PER_PAGE, $page);
        $pageTitle = "My Appointments";
        require __DIR__ . "/../views/appointments/patient_index.php";
    }

    public function book() {
        Auth::requireRole("patient");
        $dm = new DoctorModel();
        $doctors = $dm->getAll();
        global $TIME_SLOTS;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleBook();
            return;
        }

        $pageTitle = "Book Appointment";
        require __DIR__ . "/../views/appointments/book.php";
    }

    private function handleBook() {
        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            set_flash("danger", "Invalid CSRF.");
            redirect(url("appointments", "book"));
        }

        $doctorId = (int)($_POST["doctor_id"] ?? 0);
        $date     = trim($_POST["appt_date"] ?? "");
        $time     = trim($_POST["appt_time"] ?? "");
        $reason   = sanitize($_POST["reason"] ?? "");

        if (!$doctorId || !$date || !$time) {
            set_flash("danger", "Fill all fields.");
            redirect(url("appointments", "book"));
        }

        if (strtotime($date) < strtotime(date("Y-m-d"))) {
            set_flash("warning", "Date is in the past.");
            redirect(url("appointments", "book"));
        }

        $dm = new DoctorModel();
        $days = $dm->getAvailableDays($doctorId);
        $dow  = dow3($date);
        if (!in_array($dow, $days, true)) {
            set_flash("warning", "Doctor not available on $dow.");
            redirect(url("appointments", "book"));
        }

        $am = new AppointmentModel();
        if ($am->hasConflict($doctorId, $date, $time)) {
            set_flash("warning", "This slot is taken. Pick another time.");
            redirect(url("appointments", "book"));
        }

        $ok = $am->book([
            "patient_id" => Auth::id(),
            "doctor_id"  => $doctorId,
            "appt_date"  => $date,
            "appt_time"  => $time,
            "reason"     => $reason,
        ]);

        if (!$ok) {
            set_flash("danger", "Could not book. Slot may be taken.");
            redirect(url("appointments", "book"));
        }

        set_flash("success", "Booked. Waiting for doctor confirmation.");
        redirect(url("appointments"));
    }

    public function view() {
        Auth::requireRole("admin","doctor","patient");
        $id = (int)($_GET["id"] ?? 0);
        $am = new AppointmentModel();
        $appt = $am->findById($id);
        if (!$appt) {
            set_flash("danger", "Appointment not found.");
            redirect(url("appointments"));
        }

        if (!$this->canAccess($appt)) {
            redirect(url("errors", "403"));
        }

        require_once __DIR__ . "/../models/PrescriptionModel.php";
        $pm = new PrescriptionModel();
        $pres = $pm->findByAppointmentId($id);

        $pageTitle = "Appointment #" . $id;
        require __DIR__ . "/../views/appointments/view.php";
    }

    private function canAccess($appt) {
        $role = Auth::role();
        if ($role == "admin") return true;
        if ($role == "patient") {
            return (int)$appt["patient_id"] == Auth::id();
        }
        if ($role == "doctor") {
            $dm = new DoctorModel();
            $d = $dm->findByUserId(Auth::id());
            if (!$d) return false;
            return (int)$appt["doctor_id"] == (int)$d["id"];
        }
        return false;
    }

    public function updateStatus() {
        Auth::requireRole("admin","doctor","patient");
        if ($_SERVER["REQUEST_METHOD"] !== "POST") redirect(url("appointments"));
        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            set_flash("danger", "Invalid CSRF.");
            redirect(url("appointments"));
        }

        $id     = (int)($_POST["id"] ?? 0);
        $status = $_POST["status"] ?? "";
        $notes  = sanitize($_POST["doctor_notes"] ?? "");

        if (!in_array($status, ["pending","confirmed","completed","cancelled"], true)) {
            set_flash("danger", "Bad status.");
            redirect(url("appointments"));
        }

        $am = new AppointmentModel();
        $appt = $am->findById($id);
        if (!$appt) {
            set_flash("danger", "Appointment not found.");
            redirect(url("appointments"));
        }
        if (!$this->canAccess($appt)) {
            redirect(url("errors", "403"));
        }

        if ($status === "cancelled") {
            redirect(url("appointments", "cancel", ["id" => $id]));
        }

        if (Auth::role() === "patient") {
            set_flash("warning", "You can't do that.");
            redirect(url("appointments"));
        }

        $oldStatus = $appt["status"];
        if ($am->updateStatus($id, $status, $notes)) {
            (new AppointmentLogModel())->log($id, Auth::id(), $oldStatus, $status, $notes);
        }
        set_flash("success", "Status updated.");
        redirect(url("appointments", "view", ["id" => $id]));
    }

    public function cancel() {
        Auth::requireRole("admin","doctor","patient");
        $id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);
        $am = new AppointmentModel();
        $appt = $am->findById($id);
        if (!$appt) {
            set_flash("danger", "Appointment not found.");
            redirect(url("appointments"));
        }
        if (!$this->canAccess($appt)) {
            redirect(url("errors","403"));
        }

        if (Auth::role() === "patient" && $appt["status"] !== "pending") {
            set_flash("warning", "You can only cancel pending ones.");
            redirect(url("appointments"));
        }

        if ($appt["status"] === "cancelled") {
            set_flash("warning", "Already cancelled.");
            redirect(url("appointments","view",["id"=>$id]));
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
                set_flash("danger", "Invalid CSRF.");
                redirect(url("appointments","cancel",["id"=>$id]));
            }
            $reason = sanitize($_POST["cancellation_reason"] ?? "");
            if (strlen($reason) < 10) {
                set_flash("warning", "Reason must be at least 10 characters.");
                redirect(url("appointments","cancel",["id"=>$id]));
            }
            $oldStatus = $appt["status"];
            if ($am->cancel($id, Auth::id(), $reason)) {
                (new AppointmentLogModel())->log($id, Auth::id(), $oldStatus, "cancelled", $reason);
                set_flash("success", "Cancelled.");
            } else {
                set_flash("danger", "Could not cancel.");
            }
            redirect(url("appointments","view",["id"=>$id]));
        }

        $pageTitle = "Cancel Appointment";
        require __DIR__ . "/../views/appointments/cancel.php";
    }
}
