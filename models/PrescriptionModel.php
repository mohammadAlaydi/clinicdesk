<?php
require_once __DIR__ . "/BaseModel.php";

class PrescriptionModel extends BaseModel {

    public function findById($id) {
        return $this->fetchOne($this->execute(
            "SELECT * FROM prescriptions WHERE id=? LIMIT 1", "i", [$id]
        ));
    }

    public function findByAppointmentId($apptId) {
        return $this->fetchOne($this->execute(
            "SELECT * FROM prescriptions WHERE appointment_id=? LIMIT 1",
            "i", [$apptId]
        ));
    }

    public function create($data) {
        $sql = "INSERT INTO prescriptions (appointment_id, diagnosis, medications, notes, file_path)
                VALUES (?,?,?,?,?)";
        $ok = $this->execute($sql, "issss", [
            (int)$data["appointment_id"],
            $data["diagnosis"],
            $data["medications"],
            $data["notes"] ?? null,
            $data["file_path"] ?? null
        ]);
        return $ok ? (int)$this->db->lastInsertId() : 0;
    }

    public function getByPatient($patientId) {
        $sql = "SELECT pr.*, a.appt_date, a.appt_time, du.name AS doctor_name
                FROM prescriptions pr
                JOIN appointments a ON a.id = pr.appointment_id
                JOIN doctors d ON d.id = a.doctor_id
                JOIN users du ON du.id = d.user_id
                WHERE a.patient_id = ?
                ORDER BY pr.created_at DESC";
        return $this->fetchAll($this->execute($sql, "i", [$patientId]));
    }

    public function countByPatient($patientId) {
        $sql = "SELECT COUNT(*) AS c
                FROM prescriptions pr
                JOIN appointments a ON a.id = pr.appointment_id
                WHERE a.patient_id = ?";
        $row = $this->fetchOne($this->execute($sql, "i", [$patientId]));
        return (int)($row["c"] ?? 0);
    }
}
