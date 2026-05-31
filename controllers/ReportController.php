<?php
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";

class ReportController {

    public function index() {
        Auth::requireRole("admin");

        $start = trim($_GET["start_date"] ?? "");
        $end   = trim($_GET["end_date"]   ?? "");
        $docId = trim($_GET["doctor_id"]  ?? "");
        $stat  = trim($_GET["status"]     ?? "");

        $rows = [];
        $summary = ["total"=>0,"pending"=>0,"confirmed"=>0,"completed"=>0,"cancelled"=>0];
        $error = null;

        if ($start && $end) {
            if (strtotime($start) > strtotime($end)) {
                $error = "Start date is after end date.";
            } else {
                $am = new AppointmentModel();
                $filters = [
                    "start_date" => $start,
                    "end_date"   => $end,
                    "doctor_id"  => $docId,
                    "status"     => $stat,
                ];

                $total = $am->countAll($filters);
                $pages = max(1, (int)ceil($total / ITEMS_PER_PAGE));
                for ($p = 1; $p <= $pages; $p++) {
                    $rows = array_merge($rows, $am->getAll($p, $filters));
                }

                foreach ($rows as $r) {
                    $summary["total"]++;
                    $summary[$r["status"]]++;
                }
            }
        }

        if (($_GET["export"] ?? "") === "csv" && !empty($rows)) {
            $this->exportCsv($rows);
            return;
        }

        $dm = new DoctorModel();
        $doctors = $dm->getAll();
        $pageTitle = "Reports";
        require __DIR__ . "/../views/reports/index.php";
    }

    private function exportCsv(array $rows) {
        header("Content-Type: text/csv; charset=utf-8");
        header('Content-Disposition: attachment; filename="report_' . date("Ymd_His") . '.csv"');

        $out = fopen("php://output", "w");
        fputcsv($out, ["ID","Patient","Doctor","Specialization","Date","Time","Status","Reason"]);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r["id"],
                $r["patient_name"],
                $r["doctor_name"],
                $r["specialization"],
                $r["appt_date"],
                $r["appt_time"],
                $r["status"],
                $r["reason"] ?? "",
            ]);
        }
        fclose($out);
        exit;
    }
}
