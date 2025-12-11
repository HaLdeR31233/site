<?php

/**
 * Модель MyModel
 *
 * Відповідає за основну бізнес-логіку додатку DIM.RIA.
 * Забезпечує роботу з нерухомістю, включаючи створення, редагування,
 * видалення, пошук та отримання статистики.
 *
 * @package     DIM.RIA
 * @subpackage  Models
 * @author      Student
 * @version     1.0.0
 */

namespace App\Classes;

use App\Classes\Database;
use App\Classes\Property;
use App\Classes\Security;
use PDO;
use Exception;
use Monolog\Logger;
use Ramsey\Uuid\Uuid;

/**
 * Клас MyModel - основна модель додатку
 *
 * Реалізує бізнес-логіку для роботи з нерухомістю:
 * - Створення, редагування, видалення об'єктів нерухомості
 * - Пошук та фільтрація
 * - Статистика та аналітика
 * - Управління статусами об'єктів
 */
class MyModel
{
    private PDO $db;
    private Logger $logger;

    /**
     * Конструктор класу
     *
     * @param Logger $logger Логгер для запису подій
     * @throws Exception Якщо не вдалося підключитися до БД
     */
    public function __construct(Logger $logger)
    {
        $this->db = Database::connect();
        $this->logger = $logger;
        $this->logger->info("MyModel initialized");
    }

    /**
     * Створити новий об'єкт нерухомості
     *
     * @param array $data Дані для створення об'єкта
     * @return Property Створений об'єкт нерухомості
     * @throws Exception Якщо дані некоректні або створення не вдалося
     */
    public function createProperty(array $data): Property
    {
        // Санітізуємо вхідні дані
        $data = Security::sanitizeArray($data);
        $this->validatePropertyData($data);

        $property = new Property(
            null,
            $data['title'],
            $data['description'] ?? '',
            $data['address'],
            (float) $data['price'],
            (int) ($data['rooms'] ?? 1),
            (float) ($data['area'] ?? 0),
            $data['type'] ?? 'apartment',
            'available',
            $data['user_id'] ?? null
        );

        if ($property->save()) {
            $this->logger->info("Property created", [
                'property_id' => $property->getId(),
                'title' => $property->getTitle(),
                'user_id' => $property->getUserId()
            ]);
            return $property;
        }

        throw new Exception("Failed to create property");
    }

    /**
     * Оновити існуючий об'єкт нерухомості
     *
     * @param int $id ID об'єкта для оновлення
     * @param array $data Нові дані
     * @return Property Оновлений об'єкт
     * @throws Exception Якщо об'єкт не знайдено або оновлення не вдалося
     */
    public function updateProperty(int $id, array $data): Property
    {
        $property = Property::findById($id);
        if (!$property) {
            throw new Exception("Property not found with ID: {$id}");
        }

        // Санітізуємо вхідні дані
        $data = Security::sanitizeArray($data);
        $this->validatePropertyData($data, false); // false = не обов'язкові поля

        if (isset($data['title'])) $property->setTitle($data['title']);
        if (isset($data['description'])) $property->setDescription($data['description']);
        if (isset($data['address'])) $property->setAddress($data['address']);
        if (isset($data['price'])) $property->setPrice((float) $data['price']);
        if (isset($data['rooms'])) $property->setRooms((int) $data['rooms']);
        if (isset($data['area'])) $property->setArea((float) $data['area']);
        if (isset($data['type'])) $property->setType($data['type']);
        if (isset($data['status'])) $property->setStatus($data['status']);

        if ($property->save()) {
            $this->logger->info("Property updated", [
                'property_id' => $property->getId(),
                'title' => $property->getTitle()
            ]);
            return $property;
        }

        throw new Exception("Failed to update property");
    }

    /**
     * Видалити об'єкт нерухомості
     *
     * @param int $id ID об'єкта для видалення
     * @return bool Результат видалення
     * @throws Exception Якщо об'єкт не знайдено
     */
    public function deleteProperty(int $id): bool
    {
        $property = Property::findById($id);
        if (!$property) {
            throw new Exception("Property not found with ID: {$id}");
        }

        if ($property->delete()) {
            $this->logger->info("Property deleted", [
                'property_id' => $id,
                'title' => $property->getTitle()
            ]);
            return true;
        }

        return false;
    }

    /**
     * Отримати об'єкт нерухомості за ID
     *
     * @param int $id ID об'єкта
     * @return Property|null Об'єкт нерухомості або null
     */
    public function getProperty(int $id): ?Property
    {
        $property = Property::findById($id);
        if ($property) {
            $this->logger->debug("Property retrieved", ['property_id' => $id]);
        }
        return $property;
    }

    /**
     * Отримати всі об'єкти нерухомості з фільтрацією
     *
     * @param array $filters Фільтри для пошуку
     * @param int $limit Максимальна кількість результатів
     * @param int $offset Зсув для пагінації
     * @return array Масив об'єктів нерухомості
     */
    public function getProperties(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $properties = Property::getAll($filters);

        // Застосувати пагінацію
        $properties = array_slice($properties, $offset, $limit);

        $this->logger->info("Properties retrieved", [
            'count' => count($properties),
            'filters' => $filters,
            'limit' => $limit,
            'offset' => $offset
        ]);

        return $properties;
    }

    /**
     * Пошук об'єктів нерухомості
     *
     * @param string $query Пошуковий запит
     * @param array $filters Додаткові фільтри
     * @return array Результати пошуку
     */
    public function searchProperties(string $query, array $filters = []): array
    {
        // Санітізуємо пошуковий запит та фільтри
        $query = Security::sanitizeInput($query);
        $filters = Security::sanitizeArray($filters);

        $results = Property::search($query, $filters);

        $this->logger->info("Properties search performed", [
            'query' => $query,
            'filters' => $filters,
            'results_count' => count($results)
        ]);

        return $results;
    }

    /**
     * Отримати об'єкти нерухомості користувача
     *
     * @param int $userId ID користувача
     * @return array Об'єкти нерухомості користувача
     */
    public function getUserProperties(int $userId): array
    {
        $properties = Property::getByUserId($userId);

        $this->logger->info("User properties retrieved", [
            'user_id' => $userId,
            'count' => count($properties)
        ]);

        return $properties;
    }

    /**
     * Отримати статистику нерухомості
     *
     * @return array Статистичні дані
     */
    public function getStatistics(): array
    {
        $stats = Property::getStats();

        // Додати додаткову статистику
        $stats['total_types'] = $this->getPropertyTypesCount();
        $stats['recent_properties'] = $this->getRecentPropertiesCount(7); // за останні 7 днів

        $this->logger->info("Statistics generated", $stats);

        return $stats;
    }

    /**
     * Отримати кількість об'єктів за типами
     *
     * @return array Кількість об'єктів кожного типу
     */
    private function getPropertyTypesCount(): array
    {
        try {
            $sql = "SELECT type, COUNT(*) as count FROM properties GROUP BY type";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (Exception $e) {
            $this->logger->error("Failed to get property types count", ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Отримати кількість нових об'єктів за останні дні
     *
     * @param int $days Кількість днів
     * @return int Кількість нових об'єктів
     */
    private function getRecentPropertiesCount(int $days): int
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM properties WHERE created_at >= datetime('now', '-{$days} days')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (Exception $e) {
            $this->logger->error("Failed to get recent properties count", ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Змінити статус об'єкта на "орендовано"
     *
     * @param int $id ID об'єкта
     * @return bool Результат операції
     */
    public function markPropertyAsRented(int $id): bool
    {
        $property = Property::findById($id);
        if (!$property) {
            throw new Exception("Property not found with ID: {$id}");
        }

        if ($property->markAsRented()) {
            $this->logger->info("Property marked as rented", ['property_id' => $id]);
            return true;
        }

        return false;
    }

    /**
     * Змінити статус об'єкта на "доступно"
     *
     * @param int $id ID об'єкта
     * @return bool Результат операції
     */
    public function markPropertyAsAvailable(int $id): bool
    {
        $property = Property::findById($id);
        if (!$property) {
            throw new Exception("Property not found with ID: {$id}");
        }

        if ($property->markAsAvailable()) {
            $this->logger->info("Property marked as available", ['property_id' => $id]);
            return true;
        }

        return false;
    }

    /**
     * Генерувати звіт по нерухомості
     *
     * @param string $format Формат звіту (array, json, csv)
     * @return mixed Звіт у вказаному форматі
     */
    public function generateReport(string $format = 'array')
    {
        $properties = Property::getAll();
        $stats = $this->getStatistics();

        $report = [
            'generated_at' => date('Y-m-d H:i:s'),
            'total_properties' => count($properties),
            'statistics' => $stats,
            'properties' => array_map(function($property) {
                return $property->toArray();
            }, $properties)
        ];

        switch ($format) {
            case 'json':
                return json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            case 'csv':
                return $this->convertReportToCsv($report);
            default:
                return $report;
        }
    }

    /**
     * Конвертувати звіт у CSV формат
     *
     * @param array $report Звіт у вигляді масиву
     * @return string CSV рядок
     */
    private function convertReportToCsv(array $report): string
    {
        $csv = "ID,Title,Address,Price,Type,Status\n";

        foreach ($report['properties'] as $property) {
            $csv .= sprintf(
                "%d,\"%s\",\"%s\",%.2f,%s,%s\n",
                $property['id'],
                str_replace('"', '""', $property['title']),
                str_replace('"', '""', $property['address']),
                $property['price'],
                $property['type'],
                $property['status']
            );
        }

        return $csv;
    }

    /**
     * Валідація даних нерухомості
     *
     * @param array $data Дані для валідації
     * @param bool $requireAll Чи потрібні всі поля
     * @throws Exception Якщо дані некоректні
     */
    private function validatePropertyData(array $data, bool $requireAll = true): void
    {
        $errors = [];

        if ($requireAll || isset($data['title'])) {
            if (empty($data['title']) || strlen($data['title']) < 3) {
                $errors[] = "Назва має бути не менше 3 символів";
            }
        }

        if ($requireAll || isset($data['address'])) {
            if (empty($data['address'])) {
                $errors[] = "Адреса обов'язкова";
            }
        }

        if ($requireAll || isset($data['price'])) {
            if (!is_numeric($data['price']) || $data['price'] <= 0) {
                $errors[] = "Ціна має бути додатним числом";
            }
        }

        if (isset($data['rooms']) && (!is_numeric($data['rooms']) || $data['rooms'] < 0)) {
            $errors[] = "Кількість кімнат має бути невід'ємним числом";
        }

        if (isset($data['area']) && (!is_numeric($data['area']) || $data['area'] < 0)) {
            $errors[] = "Площа має бути невід'ємним числом";
        }

        $validTypes = ['apartment', 'house', 'office', 'land', 'commercial'];
        if (isset($data['type']) && !in_array($data['type'], $validTypes)) {
            $errors[] = "Невірний тип нерухомості";
        }

        $validStatuses = ['available', 'rented', 'sold'];
        if (isset($data['status']) && !in_array($data['status'], $validStatuses)) {
            $errors[] = "Невірний статус нерухомості";
        }

        if (!empty($errors)) {
            throw new Exception("Помилки валідації: " . implode(", ", $errors));
        }
    }

    /**
     * Отримати рекомендовані об'єкти для користувача
     *
     * @param int $userId ID користувача
     * @param int $limit Максимальна кількість рекомендацій
     * @return array Рекомендовані об'єкти
     */
    public function getRecommendedProperties(int $userId, int $limit = 5): array
    {
        try {
            // Логіка рекомендацій на основі переглянутих об'єктів
            // Спростимо - повернемо останні доступні об'єкти інших користувачів
            $sql = "
                SELECT * FROM properties
                WHERE user_id != ? AND status = 'available'
                ORDER BY created_at DESC
                LIMIT ?
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $limit]);

            $properties = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $properties[] = Property::createFromArray($row);
            }

            $this->logger->info("Recommended properties generated", [
                'user_id' => $userId,
                'count' => count($properties)
            ]);

            return $properties;
        } catch (Exception $e) {
            $this->logger->error("Failed to get recommended properties", [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return [];
        }
    }
}
