<?php
require_once __DIR__ . "/BaseModel.php";

class AppointmentModel extends BaseModel {

    public function findById($id) {
        $sql = "SELECT a.*,
                       p.name AS patient_name, p.email AS patient_email, p.phone AS patient_phone,
                       du.name AS doctor_name, s.name AS specialization
                FROM appointments a
                JOIN users p ON p.id = a.patient_id
                JOIN doctors d ON d.id = a.doctor_id
                JOIN users du ON du.id = d.user_id
                JOIN specializations s ON s.id = d.specialization_id
                WHERE a.id = ?
                LIMIT 1";
        return $this->fetchOne($this->execute($sql, "i", [$id]));
    }

    public function hasConflict($doctorId, $date, $time) {
        $sql = "SELECT id FROM appointments
                WHERE doctor_id=? AND appt_date=? AND appt_time=?
                  AND status != 'cancelled'
                LIMIT 1";
        $res = $this->execute($sql, "iss", [$doctorId, $date, $time]);
        $row = $this->fetchOne($res);
        return $row !== null;
    }

    public function book($data) {
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, reason, status)
                VALUES (?,?,?,?,?, 'pending')";

        $r = $this->execute($sql, "iisss", [
            (int)$data["patient_id"],
            (int)$data["doctor_id"],
            $data["appt_date"],
            $data["appt_time"],
            $data["reason"] ?? null
        ]);
        return (bool)$r;
    }

    public function updateStatus($id, $status, $notes = "") {
        if ($notes !== "") {
            $sql = "UPDATE appointments SET status=?, doctor_notes=? WHERE id=?";
            $r = $this->execute($sql, "ssi", [$status, $notes, $id]);
        } else {
            $sql = "UPDATE appointments SET status=? WHERE id=?";
            $r = $this->execute($sql, "si", [$status, $id]);
        }
        return (bool)$r;
    }

    public function cancel($id, $cancelledBy, $reason) {
        $sql = "UPDATE appointments
                SET status='cancelled', cancelled_by=?, cancellation_reason=?
                WHERE id=?";
        return (bool)$this->execute($sql, "isi", [$cancelledBy, $reason, $id]);
    }

    public function getByPatient($patientId, $page, $filters = []) {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;

        $where  = ["a.patient_id = ?"];
        $types  = "i";
        $params = [$patientId];

        if (!empty($filters["status"])) {
            $where[] = "a.status = ?";
            $types .= "s";
            $params[] = $filters["status"];
        }
        if (!empty($filters["start_date"])) {
            $where[] = "a.appt_date >= ?";
            $types .= "s";
            $params[] = $filters["start_date"];
        }
        if (!empty($filters["end_date"])) {
            $where[] = "a.appt_date <= ?";
            $types .= "s";
            $params[] = $filters["end_date"];
        }

        $sql = "SELECT a.*, du.name AS doctor_name, s.name AS specialization,
                       (SELECT COUNT(*) FROM prescriptions pr WHERE pr.appointment_id=a.id) AS has_prescription
                FROM appointments a
                JOIN users p ON p.id = a.patient_id
                JOIN doctors d ON d.id = a.doctor_id
                JOIN users du ON du.id = d.user_id
                JOIN specializations s ON s.id = d.specialization_id
                WHERE " . implode(" AND ", $where) . "
                ORDER BY a.appt_date DESC, a.appt_time DESC
                LIMIT ? OFFSET ?";
        $types .= "ii";
        $params[] = $perPage;
        $params[] = $offset;

        return $this->fetchAll($this->execute($sql, $types, $params));
    }

    public function countByPatient($patientId, $filters = []) {
        $where  = ["a.patient_id = ?"];
        $types  = "i";
        $params = [$patientId];

        if (!empty($filters["status"])) {
            $where[] = "a.status = ?";
            $types .= "s";
            $params[] = $filters["status"];
        }
        if (!empty($filters["start_date"])) {
            $where[] = "a.appt_date >= ?";
            $types .= "s";
            $params[] = $filters["start_date"];
        }
        if (!empty($filters["end_date"])) {
            $where[] = "a.appt_date <= ?";
            $types .= "s";
            $params[] = $filters["end_date"];
        }

        $sql = "SELECT COUNT(*) AS c
                FROM appointments a
                JOIN users p ON p.id = a.patient_id
                WHERE " . implode(" AND ", $where);
        $row = $this->fetchOne($this->execute($sql, $types, $params));
        return (int)($row["c"] ?? 0);
    }

    public function getByDoctor($doctorId, $page, $filters = []) {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;

        $where  = ["a.doctor_id = ?"];
        $types  = "i";
        $params = [$doctorId];

        if (!empty($filters["status"])) {
            $where[] = "a.status = ?";
            $types .= "s";
            $params[] = $filters["status"];
        }
        if (!empty($filters["start_date"])) {
            $where[] = "a.appt_date >= ?";
            $types .= "s";
            $params[] = $filters["start_date"];
        }
        if (!empty($filters["end_date"])) {
            $where[] = "a.appt_date <= ?";
            $types .= "s";
            $params[] = $filters["end_date"];
        }
        if (!empty($filters["patient_search"])) {
            $where[] = "p.name LIKE ?";
            $types .= "s";
            $params[] = "%" . $filters["patient_search"] . "%";
        }

        $sql = "SELECT a.*, p.name AS patient_name, p.phone AS patient_phone,
                       (SELECT COUNT(*) FROM prescriptions pr WHERE pr.appointment_id=a.id) AS has_prescription
                FROM appointments a
                JOIN users p ON p.id = a.patient_id
                WHERE " . implode(" AND ", $where) . "
                ORDER BY a.appt_date DESC, a.appt_time DESC
                LIMIT ? OFFSET ?";
        $types .= "ii";
        $params[] = $perPage;
        $params[] = $offset;

        return $this->fetchAll($this->execute($sql, $types, $params));
    }

    public function countByDoctor($doctorId, $filters = []) {
        $where  = ["a.doctor_id = ?"];
        $types  = "i";
        $params = [$doctorId];

        if (!empty($filters["status"])) {
            $where[] = "a.status = ?";
            $types .= "s";
            $params[] = $filters["status"];
        }
        if (!empty($filters["start_date"])) {
            $where[] = "a.appt_date >= ?";
            $types .= "s";
            $params[] = $filters["start_date"];
        }
        if (!empty($filters["end_date"])) {
            $where[] = "a.appt_date <= ?";
            $types .= "s";
            $params[] = $filters["end_date"];
        }
        if (!empty($filters["patient_search"])) {
            $where[] = "p.name LIKE ?";
            $types .= "s";
            $params[] = "%" . $filters["patient_search"] . "%";
        }

        $sql = "SELECT COUNT(*) AS c FROM appointments a
                JOIN users p ON p.id = a.patient_id
                WHERE " . implode(" AND ", $where);
        $row = $this->fetchOne($this->execute($sql, $types, $params));
        return (int)($row["c"] ?? 0);
    }

    public function getAll($page, $filters = []) {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;

        $where  = [];
        $types  = "";
        $params = [];

        if (!empty($filters["status"])) {
            $where[] = "a.status = ?";
            $types .= "s";
            $params[] = $filters["status"];
        }
        if (!empty($filters["doctor_id"])) {
            $where[] = "a.doctor_id = ?";
            $types .= "i";
            $params[] = (int)$filters["doctor_id"];
        }
        if (!empty($filters["start_date"])) {
            $where[] = "a.appt_date >= ?";
            $types .= "s";
            $params[] = $filters["start_date"];
        }
        if (!empty($filters["end_date"])) {
            $where[] = "a.appt_date <= ?";
            $types .= "s";
            $params[] = $filters["end_date"];
        }
        if (!empty($filters["patient_search"])) {
            $where[] = "p.name LIKE ?";
            $types .= "s";
            $params[] = "%" . $filters["patient_search"] . "%";
        }

        $sql = "SELECT a.*, p.name AS patient_name, du.name AS doctor_name, s.name AS specialization
                FROM appointments a
                JOIN users p ON p.id = a.patient_id
                JOIN doctors d ON d.id = a.doctor_id
                JOIN users du ON du.id = d.user_id
                JOIN specializations s ON s.id = d.specialization_id";
        if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY a.appt_date DESC, a.appt_time DESC LIMIT ? OFFSET ?";
        $types .= "ii";
        $params[] = $perPage;
        $params[] = $offset;

        return $this->fetchAll($this->execute($sql, $types, $params));
    }

    public function countAll($filters = []) {
        $where  = [];
        $types  = "";
        $params = [];

        if (!empty($filters["status"])) {
            $where[] = "a.status = ?";
            $types .= "s";
            $params[] = $filters["status"];
        }
        if (!empty($filters["doctor_id"])) {
            $where[] = "a.doctor_id = ?";
            $types .= "i";
            $params[] = (int)$filters["doctor_id"];
        }
        if (!empty($filters["start_date"])) {
            $where[] = "a.appt_date >= ?";
            $types .= "s";
            $params[] = $filters["start_date"];
        }
        if (!empty($filters["end_date"])) {
            $where[] = "a.appt_date <= ?";
            $types .= "s";
            $params[] = $filters["end_date"];
        }
        if (!empty($filters["patient_search"])) {
            $where[] = "p.name LIKE ?";
            $types .= "s";
            $params[] = "%" . $filters["patient_search"] . "%";
        }

        $sql = "SELECT COUNT(*) AS c FROM appointments a
                JOIN users p ON p.id = a.patient_id";
        if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
        $row = $this->fetchOne($this->execute($sql, $types, $params));
        return (int)($row["c"] ?? 0);
    }

    public function countToday() {
        $row = $this->fetchOne($this->execute(
            "SELECT COUNT(*) AS c FROM appointments WHERE appt_date = CURDATE()"
        ));
        return (int)($row["c"] ?? 0);
    }

    public function thisWeekByStatus() {
        $sql = "SELECT status, COUNT(*) AS total FROM appointments
                WHERE YEARWEEK(appt_date, 1) = YEARWEEK(NOW(), 1)
                GROUP BY status";
        $rows = $this->fetchAll($this->execute($sql));
        $out  = ["pending"=>0, "confirmed"=>0, "completed"=>0, "cancelled"=>0];
        foreach ($rows as $r) $out[$r["status"]] = (int)$r["total"];
        return $out;
    }

    public function recentForAdmin($limit = 5) {
        $sql = "SELECT a.*, p.name AS patient_name, du.name AS doctor_name
                FROM appointments a
                JOIN users p ON p.id = a.patient_id
                JOIN doctors d ON d.id = a.doctor_id
                JOIN users du ON du.id = d.user_id
                ORDER BY a.created_at DESC
                LIMIT ?";
        return $this->fetchAll($this->execute($sql, "i", [$limit]));
    }

    public function todayForDoctor($doctorId) {
        $sql = "SELECT a.*, p.name AS patient_name, p.phone AS patient_phone
                FROM appointments a
                JOIN users p ON p.id = a.patient_id
                WHERE a.doctor_id = ? AND a.appt_date = CURDATE()
                ORDER BY a.appt_time ASC";
        return $this->fetchAll($this->execute($sql, "i", [$doctorId]));
    }

    public function upcomingForDoctor($doctorId, $limit = 5) {
        $sql = "SELECT a.*, p.name AS patient_name
                FROM appointments a
                JOIN users p ON p.id = a.patient_id
                WHERE a.doctor_id = ?
                  AND a.appt_date >= CURDATE()
                  AND a.status IN ('pending','confirmed')
                ORDER BY a.appt_date ASC, a.appt_time ASC
                LIMIT ?";
        return $this->fetchAll($this->execute($sql, "ii", [$doctorId, $limit]));
    }

    public function doctorMonthStats($doctorId) {
        $sql = "SELECT status, COUNT(*) AS total FROM appointments
                WHERE doctor_id = ?
                  AND MONTH(appt_date) = MONTH(CURDATE())
                  AND YEAR(appt_date)  = YEAR(CURDATE())
                GROUP BY status";
        $rows = $this->fetchAll($this->execute($sql, "i", [$doctorId]));
        $out  = ["pending"=>0,"confirmed"=>0,"completed"=>0,"cancelled"=>0,"total"=>0];
        foreach ($rows as $r) {
            $out[$r["status"]] = (int)$r["total"];
            $out["total"] += (int)$r["total"];
        }
        return $out;
    }

    public function patientStats($patientId) {
        $sql = "SELECT status, COUNT(*) AS total FROM appointments
                WHERE patient_id = ? GROUP BY status";
        $rows = $this->fetchAll($this->execute($sql, "i", [$patientId]));
        $out  = ["pending"=>0,"confirmed"=>0,"completed"=>0,"cancelled"=>0];
        foreach ($rows as $r) $out[$r["status"]] = (int)$r["total"];
        return $out;
    }

    public function nextForPatient($patientId) {
        $sql = "SELECT a.*, du.name AS doctor_name, s.name AS specialization
                FROM appointments a
                JOIN doctors d ON d.id = a.doctor_id
                JOIN users du ON du.id = d.user_id
                JOIN specializations s ON s.id = d.specialization_id
                WHERE a.patient_id = ?
                  AND a.appt_date >= CURDATE()
                  AND a.status IN ('pending','confirmed')
                ORDER BY a.appt_date ASC, a.appt_time ASC
                LIMIT 1";
        return $this->fetchOne($this->execute($sql, "i", [$patientId]));
    }
}
