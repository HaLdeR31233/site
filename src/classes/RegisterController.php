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
        $postData = Security::sanitizePostData();

        $this->logger->info("Registration attempt", ["email" => $postData["email"] ?? ""]);

        $email = Security::sanitizeEmail($postData["email"] ?? "");
        $password = Security::sanitizePassword($postData["password"] ?? "");
        $confirmPassword = Security::sanitizePassword($postData["confirm_password"] ?? "");
        $name = Security::sanitizeName($postData["name"] ?? "");

        $errors = [];

        if (empty($email)) {
            $errors[] = "Email обов'язковий";
        } elseif (!Security::validateEmail($email)) {
            $errors[] = "Невірний формат email";
        }

        if (empty($password)) {
            $errors[] = "Пароль обов'язковий";
        } elseif (!Security::validatePassword($password)) {
            $errors[] = "Пароль має містити мінімум 8 символів, букви та цифри";
        }

        if ($password !== $confirmPassword) {
            $errors[] = "Паролі не співпадають";
        }

        if (empty($name)) {
            $errors[] = "Ім'я обов'язкове";
        }

        if (!empty($errors)) {
            $_SESSION["register_errors"] = array_map([Security::class, 'escapeOutput'], $errors);
            $_SESSION["old_input"] = [
                "email" => Security::escapeOutput($email),
                "name" => Security::escapeOutput($name)
            ];
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

            $errorMessage = "Помилка реєстрації";
            if (strpos($e->getMessage(), "already exists") !== false) {
                $errorMessage = "Користувач з таким email вже існує";
            }

            $_SESSION["register_errors"] = [Security::escapeOutput($errorMessage)];
            $_SESSION["old_input"] = [
                "email" => Security::escapeOutput($email),
                "name" => Security::escapeOutput($name)
            ];
            $this->viewer->redirect("/register");
        }
    }
}
