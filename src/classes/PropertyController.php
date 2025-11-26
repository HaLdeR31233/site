<?php
namespace App\Classes;

use Monolog\Logger;
use App\Classes\Property;
use App\Classes\Security;
use Exception;

class PropertyController
{
    private Viewer $viewer;
    private Logger $logger;

    public function __construct(Viewer $viewer, Logger $logger)
    {
        $this->viewer = $viewer;
        $this->logger = $logger;
    }

    public function index(): void
    {
        $this->logger->info("Properties list displayed");

        try {
            $filters = [
                'type' => $_GET['type'] ?? '',
                'status' => $_GET['status'] ?? '',
                'min_price' => $_GET['min_price'] ?? '',
                'max_price' => $_GET['max_price'] ?? '',
                'rooms' => $_GET['rooms'] ?? ''
            ];

            $properties = Property::getAll(array_filter($filters));

            $data = [
                "title" => "Нерухомість - DIM.RIA",
                "properties" => $properties,
                "filters" => $filters
            ];

            $this->viewer->render("properties/index", $data);
        } catch (Exception $e) {
            $this->logger->error("Failed to load properties", ["error" => $e->getMessage()]);
            $this->viewer->render("error", ["message" => "Помилка завантаження нерухомості"]);
        }
    }

    public function show(int $id): void
    {
        $this->logger->info("Property details displayed", ["property_id" => $id]);

        try {
            $property = Property::findById($id);

            if (!$property) {
                $this->viewer->render("error", ["message" => "Нерухомість не знайдена"]);
                return;
            }

            $data = [
                "title" => Security::escapeOutput($property->getTitle()) . " - DIM.RIA",
                "property" => $property
            ];

            $this->viewer->render("properties/show", $data);
        } catch (Exception $e) {
            $this->logger->error("Failed to load property", ["property_id" => $id, "error" => $e->getMessage()]);
            $this->viewer->render("error", ["message" => "Помилка завантаження нерухомості"]);
        }
    }

    public function create(): void
    {
        $this->logger->info("Create property form displayed");

        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $this->viewer->redirect("/login");
            return;
        }

        $data = [
            "title" => "Додати нерухомість - DIM.RIA",
            "errors" => $_SESSION["property_errors"] ?? [],
            "old_input" => $_SESSION["property_input"] ?? []
        ];

        unset($_SESSION["property_errors"], $_SESSION["property_input"]);

        $this->viewer->render("properties/create", $data);
    }

    public function store(): void
    {
        $this->logger->info("Property creation attempt");

        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $this->viewer->redirect("/login");
            return;
        }

        $userId = $_SESSION['user']['id'];

        // Sanitize input
        $title = Security::sanitizeInput($_POST["title"] ?? "");
        $description = Security::sanitizeInput($_POST["description"] ?? "");
        $address = Security::sanitizeInput($_POST["address"] ?? "");
        $price = (float) ($_POST["price"] ?? 0);
        $rooms = (int) ($_POST["rooms"] ?? 1);
        $area = (float) ($_POST["area"] ?? 0);
        $type = Security::sanitizeInput($_POST["type"] ?? "apartment");

        $errors = [];

        if (empty($title)) {
            $errors[] = Security::escapeOutput("Назва обов'язкова");
        }

        if (empty($address)) {
            $errors[] = Security::escapeOutput("Адреса обов'язкова");
        }

        if ($price <= 0) {
            $errors[] = Security::escapeOutput("Ціна має бути більше 0");
        }

        if ($rooms < 1) {
            $errors[] = Security::escapeOutput("Кількість кімнат має бути не менше 1");
        }

        if (!empty($errors)) {
            $_SESSION["property_errors"] = $errors;
            $_SESSION["property_input"] = [
                "title" => $title,
                "description" => $description,
                "address" => $address,
                "price" => $price,
                "rooms" => $rooms,
                "area" => $area,
                "type" => $type
            ];
            $this->viewer->redirect("/properties/create");
            return;
        }

        try {
            $property = new Property(
                null,
                $title,
                $description,
                $address,
                $price,
                $rooms,
                $area,
                $type,
                'available',
                $userId
            );

            if ($property->save()) {
                $this->logger->info("Property created successfully", [
                    "property_id" => $property->getId(),
                    "user_id" => $userId,
                    "title" => $title
                ]);
                $this->viewer->redirect("/properties/" . $property->getId());
            } else {
                throw new Exception("Failed to save property");
            }
        } catch (Exception $e) {
            $this->logger->error("Property creation failed", ["error" => $e->getMessage()]);
            $_SESSION["property_errors"] = [Security::escapeOutput("Помилка створення нерухомості")];
            $_SESSION["property_input"] = [
                "title" => $title,
                "description" => $description,
                "address" => $address,
                "price" => $price,
                "rooms" => $rooms,
                "area" => $area,
                "type" => $type
            ];
            $this->viewer->redirect("/properties/create");
        }
    }

    public function edit(int $id): void
    {
        $this->logger->info("Edit property form displayed", ["property_id" => $id]);

        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $this->viewer->redirect("/login");
            return;
        }

        try {
            $property = Property::findById($id);

            if (!$property) {
                $this->viewer->render("error", ["message" => "Нерухомість не знайдена"]);
                return;
            }

            // Check if user owns this property
            if ($property->getUserId() !== $_SESSION['user']['id']) {
                $this->viewer->render("error", ["message" => "Немає доступу до цієї нерухомості"]);
                return;
            }

            $data = [
                "title" => "Редагувати нерухомість - DIM.RIA",
                "property" => $property,
                "errors" => $_SESSION["property_errors"] ?? [],
                "old_input" => $_SESSION["property_input"] ?? []
            ];

            unset($_SESSION["property_errors"], $_SESSION["property_input"]);

            $this->viewer->render("properties/edit", $data);
        } catch (Exception $e) {
            $this->logger->error("Failed to load property for editing", ["property_id" => $id, "error" => $e->getMessage()]);
            $this->viewer->render("error", ["message" => "Помилка завантаження нерухомості"]);
        }
    }

    public function update(int $id): void
    {
        $this->logger->info("Property update attempt", ["property_id" => $id]);

        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $this->viewer->redirect("/login");
            return;
        }

        try {
            $property = Property::findById($id);

            if (!$property) {
                $this->viewer->render("error", ["message" => "Нерухомість не знайдена"]);
                return;
            }

            // Check if user owns this property
            if ($property->getUserId() !== $_SESSION['user']['id']) {
                $this->viewer->render("error", ["message" => "Немає доступу до цієї нерухомості"]);
                return;
            }

            // Sanitize input
            $title = Security::sanitizeInput($_POST["title"] ?? "");
            $description = Security::sanitizeInput($_POST["description"] ?? "");
            $address = Security::sanitizeInput($_POST["address"] ?? "");
            $price = (float) ($_POST["price"] ?? 0);
            $rooms = (int) ($_POST["rooms"] ?? 1);
            $area = (float) ($_POST["area"] ?? 0);
            $type = Security::sanitizeInput($_POST["type"] ?? "apartment");
            $status = Security::sanitizeInput($_POST["status"] ?? "available");

            $errors = [];

            if (empty($title)) {
                $errors[] = Security::escapeOutput("Назва обов'язкова");
            }

            if (empty($address)) {
                $errors[] = Security::escapeOutput("Адреса обов'язкова");
            }

            if ($price <= 0) {
                $errors[] = Security::escapeOutput("Ціна має бути більше 0");
            }

            if (!empty($errors)) {
                $_SESSION["property_errors"] = $errors;
                $_SESSION["property_input"] = [
                    "title" => $title,
                    "description" => $description,
                    "address" => $address,
                    "price" => $price,
                    "rooms" => $rooms,
                    "area" => $area,
                    "type" => $type,
                    "status" => $status
                ];
                $this->viewer->redirect("/properties/{$id}/edit");
                return;
            }

            $property->setTitle($title);
            $property->setDescription($description);
            $property->setAddress($address);
            $property->setPrice($price);
            $property->setRooms($rooms);
            $property->setArea($area);
            $property->setType($type);
            $property->setStatus($status);

            if ($property->save()) {
                $this->logger->info("Property updated successfully", ["property_id" => $id]);
                $this->viewer->redirect("/properties/{$id}");
            } else {
                throw new Exception("Failed to update property");
            }
        } catch (Exception $e) {
            $this->logger->error("Property update failed", ["property_id" => $id, "error" => $e->getMessage()]);
            $_SESSION["property_errors"] = [Security::escapeOutput("Помилка оновлення нерухомості")];
            $this->viewer->redirect("/properties/{$id}/edit");
        }
    }

    public function delete(int $id): void
    {
        $this->logger->info("Property deletion attempt", ["property_id" => $id]);

        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $this->viewer->redirect("/login");
            return;
        }

        try {
            $property = Property::findById($id);

            if (!$property) {
                $this->viewer->render("error", ["message" => "Нерухомість не знайдена"]);
                return;
            }

            // Check if user owns this property
            if ($property->getUserId() !== $_SESSION['user']['id']) {
                $this->viewer->render("error", ["message" => "Немає доступу до цієї нерухомості"]);
                return;
            }

            if ($property->delete()) {
                $this->logger->info("Property deleted successfully", ["property_id" => $id]);
                $this->viewer->redirect("/properties");
            } else {
                throw new Exception("Failed to delete property");
            }
        } catch (Exception $e) {
            $this->logger->error("Property deletion failed", ["property_id" => $id, "error" => $e->getMessage()]);
            $this->viewer->render("error", ["message" => "Помилка видалення нерухомості"]);
        }
    }

    public function search(): void
    {
        $this->logger->info("Property search performed", ["query" => $_GET['q'] ?? '']);

        $query = Security::sanitizeInput($_GET['q'] ?? '');

        if (empty($query)) {
            $this->viewer->redirect("/properties");
            return;
        }

        try {
            $filters = [
                'type' => $_GET['type'] ?? '',
                'status' => $_GET['status'] ?? '',
                'min_price' => $_GET['min_price'] ?? '',
                'max_price' => $_GET['max_price'] ?? ''
            ];

            $properties = Property::search($query, array_filter($filters));

            $data = [
                "title" => "Результати пошуку - DIM.RIA",
                "properties" => $properties,
                "query" => $query,
                "filters" => $filters
            ];

            $this->viewer->render("properties/search", $data);
        } catch (Exception $e) {
            $this->logger->error("Property search failed", ["query" => $query, "error" => $e->getMessage()]);
            $this->viewer->render("error", ["message" => "Помилка пошуку"]);
        }
    }

    public function myProperties(): void
    {
        $this->logger->info("User properties displayed");

        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $this->viewer->redirect("/login");
            return;
        }

        try {
            $properties = Property::getByUserId($_SESSION['user']['id']);

            $data = [
                "title" => "Мої оголошення - DIM.RIA",
                "properties" => $properties
            ];

            $this->viewer->render("properties/my", $data);
        } catch (Exception $e) {
            $this->logger->error("Failed to load user properties", ["error" => $e->getMessage()]);
            $this->viewer->render("error", ["message" => "Помилка завантаження оголошень"]);
        }
    }
}
