<?php
namespace App\Classes;

use Monolog\Logger;

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

        // Clear session data
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

        // Simple authentication (in real app, check against database)
        if ($email === "admin@example.com" && $password === "password") {
            $_SESSION["user"] = ["email" => $email, "name" => "Admin"];
            $this->logger->info("User logged in successfully", ["email" => $email]);
            $this->viewer->redirect("/home");
        } else {
            $_SESSION["login_errors"] = ["РќРµРІС–СЂРЅРёР№ email Р°Р±Рѕ РїР°СЂРѕР»СЊ"];
            $_SESSION["old_input"] = ["email" => $email];
            $this->logger->warning("Failed login attempt", ["email" => $email]);
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
