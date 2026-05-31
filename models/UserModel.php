<?php
require_once __DIR__ . "/BaseModel.php";

class UserModel extends BaseModel {

    public function findById($id) {
        $res = $this->execute("SELECT * FROM users WHERE id=? LIMIT 1", "i", [$id]);
        return $this->fetchOne($res);
    }

    public function findByEmail($email) {
        $res = $this->execute("SELECT * FROM users WHERE email=? LIMIT 1", "s", [$email]);
        return $this->fetchOne($res);
    }

    public function create($data) {
        $sql = "INSERT INTO users (name,email,password,role,phone,is_active)
                VALUES (?,?,?,?,?,1)";
        $ok = $this->execute(
            $sql,
            "sssss",
            [$data["name"], $data["email"], $data["password"], $data["role"], $data["phone"] ?? null]
        );
        if (!$ok) return 0;
        return (int)$this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE users SET name=?, phone=?, avatar=? WHERE id=?";
        $r = $this->execute($sql, "sssi", [
            $data["name"],
            $data["phone"] ?? null,
            $data["avatar"] ?? null,
            $id
        ]);
        return (bool)$r;
    }

    public function updatePassword($id, $newHash) {
        $r = $this->execute("UPDATE users SET password=? WHERE id=?", "si", [$newHash, $id]);
        return (bool)$r;
    }

    public function clearFirstLogin($id) {
        $r = $this->execute("UPDATE users SET first_login = 0 WHERE id=?", "i", [$id]);
        return (bool)$r;
    }

    public function toggleActive($id) {
        $r = $this->execute("UPDATE users SET is_active = 1 - is_active WHERE id=?", "i", [$id]);
        return (bool)$r;
    }

    public function getAllPaginated($page, $role = "", $search = "") {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;

        $where  = [];
        $types  = "";
        $params = [];

        if ($role != "") {
            $where[] = "role = ?";
            $types .= "s";
            $params[] = $role;
        }
        if ($search != "") {
            $where[] = "(name LIKE ? OR email LIKE ?)";
            $types .= "ss";
            $like = "%" . $search . "%";
            $params[] = $like;
            $params[] = $like;
        }

        $sql = "SELECT id, name, email, role, phone, is_active, created_at FROM users";
        if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
        $types .= "ii";
        $params[] = $perPage;
        $params[] = $offset;

        $res = $this->execute($sql, $types, $params);
        return $this->fetchAll($res);
    }

    public function countAll($role = "", $search = "") {
        $where  = [];
        $types  = "";
        $params = [];
        if ($role != "") {
            $where[] = "role = ?";
            $types .= "s";
            $params[] = $role;
        }
        if ($search != "") {
            $where[] = "(name LIKE ? OR email LIKE ?)";
            $types .= "ss";
            $like = "%" . $search . "%";
            $params[] = $like;
            $params[] = $like;
        }
        $sql = "SELECT COUNT(*) AS c FROM users";
        if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);

        $res = $this->execute($sql, $types, $params);
        $row = $this->fetchOne($res);
        return (int)($row["c"] ?? 0);
    }

    public function countByRole() {
        $res = $this->execute("SELECT role, COUNT(*) AS total FROM users GROUP BY role");
        $rows = $this->fetchAll($res);
        $out = ["admin" => 0, "doctor" => 0, "patient" => 0];
        foreach ($rows as $r) {
            $out[$r["role"]] = (int)$r["total"];
        }
        return $out;
    }
}
