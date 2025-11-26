<?php
namespace App\Classes;

use Monolog\Logger;
use App\Classes\Database;
use Exception;

class LoginController
{
    private Viewer $viewer;
    private Logger $logger;

    public function __construct(Viewer $viewer, Logger $logger)
    {
        $this->viewer = $viewer;
        $this->logger = $logger;
    }

    public function showLoginForm(): void
    {
        $this->logger->info("Login form displayed");

        $data = [
            "title" => "Р’С…С–Рґ - DIM.RIA",
            "errors" => $_SESSION["login_errors"] ?? [],
            "old_input" => $_SESSION["old_input"] ?? []
        ];

        unset($_SESSION["login_errors"], $_SESSION["old_input"]);

        $this->viewer->render("login", $data);
    }

    public function login(): void
    {
        $this->logger->info("Login attempt", ["email" => $_POST["email"] ?? ""]);

        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";
        $remember = isset($_POST["remember"]);

        $errors = [];

        if (empty($email)) {
            $errors[] = "Email РѕР±РѕРІ'СЏР·РєРѕРІРёР№";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "РќРµРІС–СЂРЅРёР№ С„РѕСЂРјР°С‚ email";
        }

        if (empty($password)) {
            $errors[] = "РџР°СЂРѕР»СЊ РѕР±РѕРІ'СЏР·РєРѕРІРёР№";
        }

        if (!empty($errors)) {
            $_SESSION["login_errors"] = $errors;
            $_SESSION["old_input"] = ["email" => $email];
            $this->viewer->redirect("/login");
            return;
        }

        try {
            $user = Database::authenticateUser($email, $password);

            if ($user) {
                $_SESSION["user"] = $user;
                $this->logger->info("User logged in successfully", ["email" => $email, "user_id" => $user["id"]]);
                $this->viewer->redirect("/home");
            } else {
                $_SESSION["login_errors"] = ["РќРµРІС–СЂРЅРёР№ email Р°Р±Рѕ РїР°СЂРѕР»СЊ"];
                $_SESSION["old_input"] = ["email" => $email];
                $this->logger->warning("Failed login attempt", ["email" => $email]);
                $this->viewer->redirect("/login");
            }
        } catch (Exception $e) {
            $this->logger->error("Database error during login", ["error" => $e->getMessage()]);
            $_SESSION["login_errors"] = ["Помилка сервера. Спробуйте пізніше."];
            $_SESSION["old_input"] = ["email" => $email];
            $this->viewer->redirect("/login");
        }
    }

    public function logout(): void
    {
        $this->logger->info("User logged out");
        session_destroy();
        $this->viewer->redirect("/home");
    }
}
