<?php
require_once __DIR__ . "/../config/database.php";

class Database {

    private static $instance = null;
    private $conn;

    private function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_errno) {
            throw new RuntimeException("Database connection failed.");
        }
        $this->conn->set_charset(DB_CHARSET);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function query($sql, $types = "", $params = []) {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        if ($types !== "" && count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }
        $ok = $stmt->execute();
        if (!$ok) {
            $stmt->close();
            return false;
        }

        $result = $stmt->get_result();
        if ($result === false) {

            $stmt->close();
            return true;
        }
        $stmt->close();
        return $result;
    }

    public function lastInsertId() {
        return $this->conn->insert_id;
    }
}
