<?php
require_once __DIR__ . "/../core/Database.php";

abstract class BaseModel {

    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function execute($sql, $types = "", $params = []) {
        return $this->db->query($sql, $types, $params);
    }

    protected function fetchAll($result) {
        if ($result === false || $result === true) return [];
        $rows = [];
        while ($r = $result->fetch_assoc()) {
            $rows[] = $r;
        }
        return $rows;
    }

    protected function fetchOne($result) {
        if ($result === false || $result === true) return null;
        $row = $result->fetch_assoc();
        return $row ? $row : null;
    }
}
