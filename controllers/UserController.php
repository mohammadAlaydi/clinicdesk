<?php
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/Paginator.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__ . "/../models/SpecializationModel.php";

class UserController {

    public function index() {
        Auth::requireRole("admin");

        $um = new UserModel();
        $page    = max(1, (int)($_GET["p"] ?? 1));
        $role    = trim($_GET["role"]   ?? "");
        $search  = trim($_GET["search"] ?? "");

        $total  = $um->countAll($role, $search);
        $users  = $um->getAllPaginated($page, $role, $search);
        $pg     = new Paginator($total, ITEMS_PER_PAGE, $page);

        $pageTitle = "Users";
        require __DIR__ . "/../views/users/index.php";
    }

    public function create() {
        Auth::requireRole("admin");
        $sm = new SpecializationModel();
        $specializations = $sm->getAll();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleCreate();
            return;
        }

        $pageTitle = "Create User";
        require __DIR__ . "/../views/users/create.php";
    }

    private function handleCreate() {
        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            set_flash("danger", "Invalid CSRF.");
            redirect(url("users", "create"));
        }

        $name  = sanitize($_POST["name"]  ?? "");
        $email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
        $pass  = $_POST["password"] ?? "";
        $role  = $_POST["role"]  ?? "patient";
        $phone = sanitize($_POST["phone"] ?? "");

        if (!$name || !$email || !$pass || !in_array($role, ["admin","doctor","patient"], true)) {
            set_flash("danger", "Fill all required fields.");
            redirect(url("users", "create"));
        }
        if (strlen($pass) < 6) {
            set_flash("warning", "Password must be at least 6 chars.");
            redirect(url("users", "create"));
        }

        $um = new UserModel();
        if ($um->findByEmail($email)) {
            set_flash("danger", "Email already used.");
            redirect(url("users", "create"));
        }

        $newId = $um->create([
            "name" => $name,
            "email" => $email,
            "password" => password_hash($pass, PASSWORD_BCRYPT, ["cost" => 12]),
            "role" => $role,
            "phone" => $phone,
        ]);

        if (!$newId) {
            set_flash("danger", "Could not create user.");
            redirect(url("users", "create"));
        }

        if ($role === "doctor") {
            $dm = new DoctorModel();
            $days = $_POST["available_days"] ?? ["Sun","Mon","Tue","Wed","Thu"];
            if (!is_array($days)) $days = ["Sun","Mon","Tue","Wed","Thu"];

            $dm->create([
                "user_id"           => $newId,
                "specialization_id" => (int)($_POST["specialization_id"] ?? 1),
                "bio"               => sanitize($_POST["bio"] ?? ""),
                "consultation_fee"  => (float)($_POST["consultation_fee"] ?? 0),
                "available_days"    => implode(",", $days),
            ]);
        }

        set_flash("success", "User created.");
        redirect(url("users"));
    }

    public function edit() {
        Auth::requireRole("admin");
        $id = (int)($_GET["id"] ?? 0);
        $um = new UserModel();
        $user = $um->findById($id);
        if (!$user) {
            set_flash("danger", "User not found.");
            redirect(url("users"));
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
                set_flash("danger", "Invalid CSRF.");
                redirect(url("users", "edit", ["id" => $id]));
            }
            $um->update($id, [
                "name"  => sanitize($_POST["name"]  ?? $user["name"]),
                "phone" => sanitize($_POST["phone"] ?? $user["phone"]),
                "avatar"=> $user["avatar"],
            ]);
            set_flash("success", "User updated.");
            redirect(url("users"));
        }

        $pageTitle = "Edit User";
        require __DIR__ . "/../views/users/edit.php";
    }

    public function toggle() {
        Auth::requireRole("admin");
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            redirect(url("users"));
        }
        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            set_flash("danger", "Invalid CSRF.");
            redirect(url("users"));
        }
        $id = (int)($_POST["id"] ?? 0);

        if ($id === Auth::id()) {
            set_flash("warning", "Can't disable yourself.");
            redirect(url("users"));
        }
        $um = new UserModel();
        $um->toggleActive($id);
        set_flash("success", "Status changed.");
        redirect(url("users"));
    }

    public function profile() {
        Auth::requireRole("admin", "doctor", "patient");
        $um = new UserModel();
        $user = $um->findById(Auth::id());

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
                set_flash("danger", "Bad request.");
                redirect(url("users", "profile"));
            }
            $newAvatar = $user["avatar"];

            if (!empty($_FILES["avatar"]["name"])) {
                $r = $this->handleAvatarUpload($_FILES["avatar"]);
                if ($r["ok"]) {
                    $newAvatar = "avatars/" . $r["name"];
                } else {
                    set_flash("danger", $r["msg"]);
                    redirect(url("users", "profile"));
                }
            }

            $um->update(Auth::id(), [
                "name"   => sanitize($_POST["name"]  ?? $user["name"]),
                "phone"  => sanitize($_POST["phone"] ?? $user["phone"]),
                "avatar" => $newAvatar,
            ]);

            $_SESSION["user"]["avatar"] = $newAvatar;
            $_SESSION["user"]["name"]   = sanitize($_POST["name"] ?? $user["name"]);

            if (!empty($_POST["new_password"])) {
                $current = $_POST["current_password"] ?? "";
                if (!password_verify($current, $user["password"])) {
                    set_flash("danger", "Wrong current password.");
                    redirect(url("users", "profile"));
                }
                if (strlen($_POST["new_password"]) < 6) {
                    set_flash("warning", "New password too short.");
                    redirect(url("users", "profile"));
                }
                $um->updatePassword(Auth::id(), password_hash($_POST["new_password"], PASSWORD_BCRYPT, ["cost"=>12]));
            }

            set_flash("success", "Profile updated.");
            redirect(url("users", "profile"));
        }

        $pageTitle = "My Profile";
        require __DIR__ . "/../views/users/profile.php";
    }

    private function handleAvatarUpload($file) {
        if ($file["error"] !== UPLOAD_ERR_OK) {
            return ["ok" => false, "msg" => "Upload failed."];
        }
        if ($file["size"] > MAX_IMAGE_SIZE) {
            return ["ok" => false, "msg" => "Image too big (max 1MB)."];
        }

        $info = @getimagesize($file["tmp_name"]);
        if (!$info) {
            return ["ok" => false, "msg" => "Not a valid image."];
        }
        $allowed = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
        if (!in_array($info[2], $allowed, true)) {
            return ["ok" => false, "msg" => "Only JPG or PNG."];
        }
        $ext = $info[2] === IMAGETYPE_PNG ? "png" : "jpg";
        $name = "u" . Auth::id() . "_" . time() . "." . $ext;
        $dest = UPLOAD_AVATAR_PATH . $name;

        if (!move_uploaded_file($file["tmp_name"], $dest)) {
            return ["ok" => false, "msg" => "Could not save file."];
        }
        return ["ok" => true, "name" => $name];
    }
}
