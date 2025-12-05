<?php
namespace App\Classes;

use Monolog\Logger;
use App\Classes\Database;
use App\Classes\Security;
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

        $email = Security::sanitizeEmail($_POST["email"] ?? "");
        $password = Security::sanitizePassword($_POST["password"] ?? "");
        $remember = isset($_POST["remember"]);

        $errors = [];

        if (empty($email)) {
            $errors[] = Security::escapeOutput("Email обов'язковий");
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = Security::escapeOutput("Невірний формат email");
        }

        if (empty($password)) {
            $errors[] = Security::escapeOutput("Пароль обов'язковий");
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
                $_SESSION["login_errors"] = [Security::escapeOutput("Невірний email або пароль")];
                $_SESSION["old_input"] = ["email" => $email];
                $this->logger->warning("Failed login attempt", ["email" => $email]);
                $this->viewer->redirect("/login");
            }
        } catch (Exception $e) {
            $this->logger->error("Database error during login", ["error" => $e->getMessage()]);
            $_SESSION["login_errors"] = [Security::escapeOutput("Помилка сервера. Спробуйте пізніше.")];
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
