<?php
require_once __DIR__ . "/helpers.php";

class Auth {

    public static function login($user) {
        session_regenerate_id(true);
        $_SESSION["user"] = [
            "id"          => (int)$user["id"],
            "name"        => $user["name"],
            "role"        => $user["role"],
            "email"       => $user["email"],
            "avatar"      => $user["avatar"] ?? null,
            "first_login" => (int)($user["first_login"] ?? 0),
        ];
    }

    public static function clearFirstLogin() {
        if (isset($_SESSION["user"])) {
            $_SESSION["user"]["first_login"] = 0;
        }
    }

    public static function isFirstLogin() {
        return !empty($_SESSION["user"]["first_login"]);
    }

    public static function logout() {
        $_SESSION = [];
        session_unset();
        session_destroy();
    }

    public static function check() {
        return isset($_SESSION["user"]);
    }

    public static function currentUser() {
        return $_SESSION["user"] ?? null;
    }

    public static function role() {
        return $_SESSION["user"]["role"] ?? "";
    }

    public static function id() {
        return $_SESSION["user"]["id"] ?? 0;
    }

    public static function requireRole(...$roles) {
        if (!self::check()) {
            redirect(BASE_URL . "index.php?page=auth&action=login");
        }
        if (!in_array(self::role(), $roles)) {
            redirect(BASE_URL . "index.php?page=errors&action=403");
        }

        if (self::isFirstLogin()) {
            $p = $_GET["page"]   ?? "";
            $a = $_GET["action"] ?? "";
            if (!($p == "auth" && ($a == "change_password" || $a == "logout"))) {
                redirect(BASE_URL . "index.php?page=auth&action=change_password");
            }
        }
    }
}
