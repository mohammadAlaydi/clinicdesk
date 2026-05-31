<?php
require_once __DIR__ . "/BaseModel.php";

class AppointmentLogModel extends BaseModel {

    public function log($apptId, $userId, $oldStatus, $newStatus, $note = "") {
        $sql = "INSERT INTO appointment_logs
                (appointment_id, changed_by_user_id, old_status, new_status, note)
                VALUES (?,?,?,?,?)";
        return (bool)$this->execute($sql, "iisss", [
            $apptId, $userId, $oldStatus, $newStatus, $note
        ]);
    }

    public function getByAppointment($apptId) {
        $sql = "SELECT l.*, u.name AS user_name, u.role AS user_role
                FROM appointment_logs l
                JOIN users u ON u.id = l.changed_by_user_id
                WHERE l.appointment_id = ?
                ORDER BY l.changed_at DESC";
        return $this->fetchAll($this->execute($sql, "i", [$apptId]));
    }
}
