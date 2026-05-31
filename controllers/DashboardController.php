<?php
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__ . "/../models/PrescriptionModel.php";

class DashboardController {

    public function index() {
        Auth::requireRole("admin", "doctor", "patient");
        $role = Auth::role();

        if ($role === "admin")   { $this->adminDashboard(); return; }
        if ($role === "doctor")  { $this->doctorDashboard(); return; }
        if ($role === "patient") { $this->patientDashboard(); return; }
    }

    private function adminDashboard() {
        $um = new UserModel();
        $am = new AppointmentModel();

        $byRole     = $um->countByRole();
        $todayCount = $am->countToday();
        $weekStats  = $am->thisWeekByStatus();
        $recent     = $am->recentForAdmin(5);

        $pageTitle = "Admin Dashboard";
        require __DIR__ . "/../views/dashboard/admin.php";
    }

    private function doctorDashboard() {
        $am = new AppointmentModel();
        $dm = new DoctorModel();

        $doctor = $dm->findByUserId(Auth::id());
        if (!$doctor) {
            set_flash("warning", "No doctor profile. Contact admin.");
            $today = []; $upcoming = []; $monthStats = ["total"=>0,"pending"=>0,"completed"=>0];
        } else {
            $today      = $am->todayForDoctor((int)$doctor["id"]);
            $upcoming   = $am->upcomingForDoctor((int)$doctor["id"], 5);
            $monthStats = $am->doctorMonthStats((int)$doctor["id"]);
        }

        $pageTitle = "Doctor Dashboard";
        require __DIR__ . "/../views/dashboard/doctor.php";
    }

    private function patientDashboard() {
        $am = new AppointmentModel();
        $pm = new PrescriptionModel();

        $stats        = $am->patientStats(Auth::id());
        $nextAppt     = $am->nextForPatient(Auth::id());
        $prescCount   = $pm->countByPatient(Auth::id());

        $pageTitle = "Patient Dashboard";
        require __DIR__ . "/../views/dashboard/patient.php";
    }
}
