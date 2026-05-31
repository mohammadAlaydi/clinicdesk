<?php
require_once __DIR__ . "/BaseModel.php";

class SpecializationModel extends BaseModel {

    public function getAll() {
        return $this->fetchAll($this->execute("SELECT * FROM specializations ORDER BY name ASC"));
    }

    public function create($name) {
        $ok = $this->execute("INSERT INTO specializations (name) VALUES (?)", "s", [$name]);
        return $ok ? (int)$this->db->lastInsertId() : 0;
    }

    public function delete($id) {
        $r = $this->execute("DELETE FROM specializations WHERE id=?", "i", [$id]);
        return (bool)$r;
    }

    public function isSafeToDelete($id) {
        $row = $this->fetchOne($this->execute(
            "SELECT COUNT(*) AS c FROM doctors WHERE specialization_id=?",
            "i", [$id]
        ));
        return ((int)($row["c"] ?? 0)) == 0;
    }
}
