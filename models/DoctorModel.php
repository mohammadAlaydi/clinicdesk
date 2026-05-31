<?php
require_once __DIR__ . "/BaseModel.php";

class DoctorModel extends BaseModel {

    public function findById($id) {
        $sql = "SELECT d.*, u.name, u.email, u.phone, u.avatar, s.name AS specialization
                FROM doctors d
                JOIN users u ON u.id = d.user_id
                JOIN specializations s ON s.id = d.specialization_id
                WHERE d.id = ?
                LIMIT 1";
        return $this->fetchOne($this->execute($sql, "i", [$id]));
    }

    public function findByUserId($userId) {
        $sql = "SELECT d.*, u.name, u.email, u.phone, u.avatar, s.name AS specialization
                FROM doctors d
                JOIN users u ON u.id = d.user_id
                JOIN specializations s ON s.id = d.specialization_id
                WHERE d.user_id = ?
                LIMIT 1";
        return $this->fetchOne($this->execute($sql, "i", [$userId]));
    }

    public function getAll() {
        $sql = "SELECT d.id, u.name, s.name AS specialization, d.consultation_fee, d.available_days
                FROM doctors d
                JOIN users u ON u.id = d.user_id
                JOIN specializations s ON s.id = d.specialization_id
                WHERE u.is_active = 1
                ORDER BY u.name ASC";
        return $this->fetchAll($this->execute($sql));
    }

    public function getAllPaginated($page) {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;
        $sql = "SELECT d.id, d.consultation_fee, d.available_days, d.bio,
                       u.id AS user_id, u.name, u.email, u.phone, u.is_active,
                       s.name AS specialization
                FROM doctors d
                JOIN users u ON u.id = d.user_id
                JOIN specializations s ON s.id = d.specialization_id
                ORDER BY d.id DESC
                LIMIT ? OFFSET ?";
        return $this->fetchAll($this->execute($sql, "ii", [$perPage, $offset]));
    }

    public function countAll() {
        $row = $this->fetchOne($this->execute("SELECT COUNT(*) AS c FROM doctors"));
        return (int)($row["c"] ?? 0);
    }

    public function create($data) {
        $sql = "INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days)
                VALUES (?,?,?,?,?)";
        $ok = $this->execute($sql, "iisds", [
            (int)$data["user_id"],
            (int)$data["specialization_id"],
            $data["bio"] ?? null,
            (float)($data["consultation_fee"] ?? 0),
            $data["available_days"] ?? "Sun,Mon,Tue,Wed,Thu"
        ]);
        if (!$ok) return 0;
        return (int)$this->db->lastInsertId();
    }

    public function update($doctorId, $data) {
        $sql = "UPDATE doctors
                SET specialization_id=?, bio=?, consultation_fee=?, available_days=?
                WHERE id=?";
        $r = $this->execute($sql, "isdsi", [
            (int)$data["specialization_id"],
            $data["bio"] ?? null,
            (float)$data["consultation_fee"],
            $data["available_days"],
            $doctorId
        ]);
        return (bool)$r;
    }

    public function getAvailableDays($doctorId) {
        $row = $this->fetchOne($this->execute(
            "SELECT available_days FROM doctors WHERE id=?",
            "i", [$doctorId]
        ));
        if (!$row) return [];
        return explode(",", $row["available_days"]);
    }
}
