<?php
namespace App\Classes;

use Monolog\Logger;
use App\Classes\Database;
use App\Classes\Security;
use Exception;

class RegisterController
{
    private Viewer $viewer;
    private Logger $logger;

    public function __construct(Viewer $viewer, Logger $logger)
    {
        $this->viewer = $viewer;
        $this->logger = $logger;
    }

    public function showRegisterForm(): void
    {
        $this->logger->info("Register form displayed");

        $data = [
            "title" => "Реєстрація - DIM.RIA",
            "errors" => $_SESSION["register_errors"] ?? [],
            "old_input" => $_SESSION["old_input"] ?? []
        ];

        unset($_SESSION["register_errors"], $_SESSION["old_input"]);

        $this->viewer->render("register", $data);
    }

    public function register(): void
    {
        $this->logger->info("Registration attempt", ["email" => $_POST["email"] ?? ""]);

        $email = Security::sanitizeEmail($_POST["email"] ?? "");
        $password = Security::sanitizePassword($_POST["password"] ?? "");
        $confirmPassword = Security::sanitizePassword($_POST["confirm_password"] ?? "");
        $name = Security::sanitizeInput($_POST["name"] ?? "");

        $errors = [];

        if (empty($email)) {
            $errors[] = Security::escapeOutput("Email обов'язковий");
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = Security::escapeOutput("Невірний формат email");
        }

        if (empty($password)) {
            $errors[] = Security::escapeOutput("Пароль обов'язковий");
        } elseif (strlen($password) < 6) {
            $errors[] = Security::escapeOutput("Пароль має бути не менше 6 символів");
        }

        if ($password !== $confirmPassword) {
            $errors[] = Security::escapeOutput("Паролі не співпадають");
        }

        if (empty($name)) {
            $errors[] = Security::escapeOutput("Ім'я обов'язкове");
        }

        if (!empty($errors)) {
            $_SESSION["register_errors"] = $errors;
            $_SESSION["old_input"] = ["email" => $email, "name" => $name];
            $this->viewer->redirect("/register");
            return;
        }

        try {
            $userId = Database::registerUser($email, $password, $name);

            $this->logger->info("User registered successfully", [
                "email" => $email,
                "user_id" => $userId,
                "name" => $name
            ]);

            $_SESSION["user"] = [
                "id" => $userId,
                "email" => $email,
                "name" => $name
            ];

            $this->viewer->redirect("/home");

        } catch (Exception $e) {
            $this->logger->error("Registration error", ["error" => $e->getMessage()]);

            $errorMessage = Security::escapeOutput("Помилка реєстрації");
            if (strpos($e->getMessage(), "already exists") !== false) {
                $errorMessage = Security::escapeOutput("Користувач з таким email вже існує");
            }

            $_SESSION["register_errors"] = [$errorMessage];
            $_SESSION["old_input"] = ["email" => $email, "name" => $name];
            $this->viewer->redirect("/register");
        }
    }
}
