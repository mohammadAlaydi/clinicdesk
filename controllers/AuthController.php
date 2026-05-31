<?php
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../models/UserModel.php";

class AuthController {

    public function login() {

        if (Auth::check()) {
            redirect(url("dashboard"));
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleLogin();
            return;
        }

        $pageTitle = "Login";
        require __DIR__ . "/../views/auth/login.php";
    }

    private function handleLogin() {

        $token = $_POST["csrf_token"] ?? "";
        if (!CSRF::validateToken($token)) {
            set_flash("danger", "Bad request.");
            redirect(url("auth", "login"));
        }

        $email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
        $pass  = $_POST["password"] ?? "";

        if (!$email || !$pass) {
            set_flash("danger", "Enter email and password.");
            redirect(url("auth", "login"));
        }

        $um   = new UserModel();
        $user = $um->findByEmail($email);

        if (!$user) {
            set_flash("danger", "Wrong email or password.");
            redirect(url("auth", "login"));
        }

        if ((int)$user["is_active"] !== 1) {
            set_flash("warning", "Account is disabled.");
            redirect(url("auth", "login"));
        }

        if (!password_verify($pass, $user["password"])) {
            set_flash("danger", "Wrong email or password.");
            redirect(url("auth", "login"));
        }

        Auth::login($user);
        redirect(url("dashboard"));
    }

    public function logout() {

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            redirect(url("dashboard"));
        }
        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            set_flash("danger", "Bad request.");
            redirect(url("dashboard"));
        }
        Auth::logout();
        redirect(url("auth", "login"));
    }

    public function changePassword() {
        if (!Auth::check()) {
            redirect(url("auth", "login"));
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleChangePassword();
            return;
        }

        $pageTitle = "Change Password";
        require __DIR__ . "/../views/auth/change_password.php";
    }

    private function handleChangePassword() {
        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            set_flash("danger", "Bad request.");
            redirect(url("auth", "change_password"));
        }

        $current = $_POST["current_password"] ?? "";
        $new     = $_POST["new_password"]     ?? "";
        $confirm = $_POST["confirm_password"] ?? "";

        $um   = new UserModel();
        $user = $um->findById(Auth::id());
        if (!$user) {
            Auth::logout();
            redirect(url("auth", "login"));
        }

        if (!password_verify($current, $user["password"])) {
            set_flash("danger", "Wrong current password.");
            redirect(url("auth", "change_password"));
        }

        if (strlen($new) < 8
            || !preg_match('/[a-z]/', $new)
            || !preg_match('/[A-Z]/', $new)
            || !preg_match('/[0-9]/', $new)) {
            set_flash("warning", "Use 8+ chars with lower, UPPER and a number.");
            redirect(url("auth", "change_password"));
        }
        if ($new !== $confirm) {
            set_flash("warning", "Passwords don't match.");
            redirect(url("auth", "change_password"));
        }

        $um->updatePassword(Auth::id(), password_hash($new, PASSWORD_BCRYPT, ["cost"=>12]));
        $um->clearFirstLogin(Auth::id());
        Auth::clearFirstLogin();

        set_flash("success", "Password changed.");
        redirect(url("dashboard"));
    }
}
