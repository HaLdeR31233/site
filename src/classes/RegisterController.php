<?php
namespace App\Classes;

use Monolog\Logger;
use App\Classes\Database;
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

        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";
        $name = trim($_POST["name"] ?? "");

        $errors = [];

        if (empty($email)) {
            $errors[] = "Email обов'язковий";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Невірний формат email";
        }

        if (empty($password)) {
            $errors[] = "Пароль обов'язковий";
        } elseif (strlen($password) < 6) {
            $errors[] = "Пароль має бути не менше 6 символів";
        }

        if ($password !== $confirmPassword) {
            $errors[] = "Паролі не співпадають";
        }

        if (empty($name)) {
            $errors[] = "Ім'я обов'язкове";
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

            $errorMessage = "Помилка реєстрації";
            if (strpos($e->getMessage(), "вже існує") !== false) {
                $errorMessage = "Користувач з таким email вже існує";
            }

            $_SESSION["register_errors"] = [$errorMessage];
            $_SESSION["old_input"] = ["email" => $email, "name" => $name];
            $this->viewer->redirect("/register");
        }
    }
}
