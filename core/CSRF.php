<?php

class CSRF {

    public static function generateToken() {
        if (empty($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }
        return $_SESSION["csrf_token"];
    }

    public static function validateToken($token) {
        if (empty($_SESSION["csrf_token"]) || !is_string($token)) {
            return false;
        }
        return hash_equals($_SESSION["csrf_token"], $token);
    }

    public static function field() {
        $t = htmlspecialchars(self::generateToken(), ENT_QUOTES, "UTF-8");
        return '<input type="hidden" name="csrf_token" value="' . $t . '">';
    }
}
