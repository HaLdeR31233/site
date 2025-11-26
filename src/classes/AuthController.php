<?php
namespace App\Classes;

use Monolog\Logger;

class AuthController
{
    private Viewer $viewer;
    private Logger $logger;

    public function __construct(Viewer $viewer, Logger $logger)
    {
        $this->viewer = $viewer;
        $this->logger = $logger;
    }

    public function handleAuth(): void
    {
        $action = $_GET["action"] ?? "";

        switch ($action) {
            case "logout":
                $this->logout();
                break;
            case "check":
                $this->checkAuth();
                break;
            default:
                $this->viewer->redirect("/login");
                break;
        }
    }

    private function logout(): void
    {
        $this->logger->info("User logout initiated");
        session_destroy();
        $this->viewer->redirect("/home");
    }

    private function checkAuth(): void
    {
        $isLoggedIn = isset($_SESSION["user"]);
        $user = $_SESSION["user"] ?? null;

        $this->logger->info("Auth check requested", ["logged_in" => $isLoggedIn]);

        $this->viewer->renderJson([
            "authenticated" => $isLoggedIn,
            "user" => $isLoggedIn ? $user : null
        ]);
    }
}
