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
        $postData = Security::sanitizePostData();

        $this->logger->info("Login attempt", ["email" => $postData["email"] ?? ""]);

        $email = Security::sanitizeEmail($postData["email"] ?? "");
        $password = Security::sanitizePassword($postData["password"] ?? "");

        $errors = [];
    
        if (empty($email)) {
            $errors[] = "Email обов'язковий";
        } elseif (!Security::validateEmail($email)) {
            $errors[] = "Невірний формат email";
        }

        if (empty($password)) {
            $errors[] = "Пароль обов'язковий";
        }

        if (!empty($errors)) {
            $_SESSION["login_errors"] = array_map([Security::class, 'escapeOutput'], $errors);
            $_SESSION["old_input"] = ["email" => Security::escapeOutput($email)];
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
                $_SESSION["login_errors"] = ["Невірний email або пароль"];
                $_SESSION["old_input"] = ["email" => Security::escapeOutput($email)];
                $this->logger->warning("Failed login attempt", ["email" => $email]);
                $this->viewer->redirect("/login");
            }
        } catch (Exception $e) {
            $this->logger->error("Database error during login", ["error" => $e->getMessage()]);
            $_SESSION["login_errors"] = ["Помилка сервера. Спробуйте пізніше."];
            $_SESSION["old_input"] = ["email" => Security::escapeOutput($email)];
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
