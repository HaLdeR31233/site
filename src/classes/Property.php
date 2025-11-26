<?php
namespace App\Classes;

use App\Classes\Database;
use PDO;
use Exception;

class Property
{
    private ?int $id;
    private string $title;
    private string $description;
    private string $address;
    private float $price;
    private int $rooms;
    private float $area;
    private string $type; // apartment, house, office, etc.
    private string $status; // available, rented, sold
    private ?int $user_id;
    private string $created_at;
    private string $updated_at;

    public function __construct(
        ?int $id = null,
        string $title = '',
        string $description = '',
        string $address = '',
        float $price = 0.0,
        int $rooms = 0,
        float $area = 0.0,
        string $type = 'apartment',
        string $status = 'available',
        ?int $user_id = null,
        string $created_at = '',
        string $updated_at = ''
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->address = $address;
        $this->price = $price;
        $this->rooms = $rooms;
        $this->area = $area;
        $this->type = $type;
        $this->status = $status;
        $this->user_id = $user_id;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getAddress(): string { return $this->address; }
    public function getPrice(): float { return $this->price; }
    public function getRooms(): int { return $this->rooms; }
    public function getArea(): float { return $this->area; }
    public function getType(): string { return $this->type; }
    public function getStatus(): string { return $this->status; }
    public function getUserId(): ?int { return $this->user_id; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function getUpdatedAt(): string { return $this->updated_at; }

    // Setters
    public function setTitle(string $title): void { $this->title = $title; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setAddress(string $address): void { $this->address = $address; }
    public function setPrice(float $price): void { $this->price = $price; }
    public function setRooms(int $rooms): void { $this->rooms = $rooms; }
    public function setArea(float $area): void { $this->area = $area; }
    public function setType(string $type): void { $this->type = $type; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setUserId(?int $user_id): void { $this->user_id = $user_id; }

    // Static methods for database operations
    public static function createTable(): void
    {
        $pdo = Database::connect();

        $sql = "
            CREATE TABLE IF NOT EXISTS properties (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                address VARCHAR(500) NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                rooms INTEGER DEFAULT 1,
                area DECIMAL(8,2) DEFAULT 0.00,
                type VARCHAR(50) DEFAULT 'apartment',
                status VARCHAR(20) DEFAULT 'available',
                user_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            )
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    public static function findById(int $id): ?Property
    {
        try {
            $pdo = Database::connect();

            $sql = "SELECT * FROM properties WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            $propertyData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($propertyData) {
                return self::createFromArray($propertyData);
            }

            return null;
        } catch (Exception $e) {
            throw new Exception("Failed to find property: " . $e->getMessage());
        }
    }

    public static function getAll(array $filters = []): array
    {
        try {
            $pdo = Database::connect();

            $sql = "SELECT * FROM properties WHERE 1=1";
            $params = [];

            if (!empty($filters['type'])) {
                $sql .= " AND type = ?";
                $params[] = $filters['type'];
            }

            if (!empty($filters['status'])) {
                $sql .= " AND status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['min_price'])) {
                $sql .= " AND price >= ?";
                $params[] = $filters['min_price'];
            }

            if (!empty($filters['max_price'])) {
                $sql .= " AND price <= ?";
                $params[] = $filters['max_price'];
            }

            if (!empty($filters['rooms'])) {
                $sql .= " AND rooms >= ?";
                $params[] = $filters['rooms'];
            }

            $sql .= " ORDER BY created_at DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $properties = [];
            while ($propertyData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $properties[] = self::createFromArray($propertyData);
            }

            return $properties;
        } catch (Exception $e) {
            throw new Exception("Failed to get properties: " . $e->getMessage());
        }
    }

    public static function getByUserId(int $userId): array
    {
        try {
            $pdo = Database::connect();

            $sql = "SELECT * FROM properties WHERE user_id = ? ORDER BY created_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId]);

            $properties = [];
            while ($propertyData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $properties[] = self::createFromArray($propertyData);
            }

            return $properties;
        } catch (Exception $e) {
            throw new Exception("Failed to get user properties: " . $e->getMessage());
        }
    }

    public static function search(string $query, array $filters = []): array
    {
        try {
            $pdo = Database::connect();

            $sql = "SELECT * FROM properties WHERE (title LIKE ? OR description LIKE ? OR address LIKE ?)";
            $params = ["%$query%", "%$query%", "%$query%"];

            if (!empty($filters['type'])) {
                $sql .= " AND type = ?";
                $params[] = $filters['type'];
            }

            if (!empty($filters['status'])) {
                $sql .= " AND status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['min_price'])) {
                $sql .= " AND price >= ?";
                $params[] = $filters['min_price'];
            }

            if (!empty($filters['max_price'])) {
                $sql .= " AND price <= ?";
                $params[] = $filters['max_price'];
            }

            $sql .= " ORDER BY created_at DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $properties = [];
            while ($propertyData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $properties[] = self::createFromArray($propertyData);
            }

            return $properties;
        } catch (Exception $e) {
            throw new Exception("Failed to search properties: " . $e->getMessage());
        }
    }

    public static function getAvailable(): array
    {
        return self::getAll(['status' => 'available']);
    }

    public static function getByType(string $type): array
    {
        return self::getAll(['type' => $type]);
    }

    public static function getStats(): array
    {
        try {
            $pdo = Database::connect();

            $sql = "
                SELECT
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'available' THEN 1 END) as available,
                    COUNT(CASE WHEN status = 'rented' THEN 1 END) as rented,
                    AVG(price) as avg_price,
                    MIN(price) as min_price,
                    MAX(price) as max_price
                FROM properties
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Failed to get property stats: " . $e->getMessage());
        }
    }

    // Instance methods
    public function save(): bool
    {
        try {
            $pdo = Database::connect();

            if ($this->id) {
                // Update existing property
                $sql = "UPDATE properties SET title = ?, description = ?, address = ?, price = ?, rooms = ?, area = ?, type = ?, status = ?, user_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                return $stmt->execute([
                    $this->title,
                    $this->description,
                    $this->address,
                    $this->price,
                    $this->rooms,
                    $this->area,
                    $this->type,
                    $this->status,
                    $this->user_id,
                    $this->id
                ]);
            } else {
                // Create new property
                $sql = "INSERT INTO properties (title, description, address, price, rooms, area, type, status, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $this->title,
                    $this->description,
                    $this->address,
                    $this->price,
                    $this->rooms,
                    $this->area,
                    $this->type,
                    $this->status,
                    $this->user_id
                ]);
                $this->id = (int) $pdo->lastInsertId();
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Failed to save property: " . $e->getMessage());
        }
    }

    public function delete(): bool
    {
        if (!$this->id) {
            return false;
        }

        try {
            $pdo = Database::connect();

            $sql = "DELETE FROM properties WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$this->id]);
        } catch (Exception $e) {
            throw new Exception("Failed to delete property: " . $e->getMessage());
        }
    }

    public function markAsRented(): bool
    {
        $this->status = 'rented';
        return $this->save();
    }

    public function markAsAvailable(): bool
    {
        $this->status = 'available';
        return $this->save();
    }

    // Helper method to create Property object from array
    private static function createFromArray(array $data): Property
    {
        return new Property(
            $data['id'] ?? null,
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['address'] ?? '',
            (float) ($data['price'] ?? 0),
            (int) ($data['rooms'] ?? 0),
            (float) ($data['area'] ?? 0),
            $data['type'] ?? 'apartment',
            $data['status'] ?? 'available',
            $data['user_id'] ?? null,
            $data['created_at'] ?? '',
            $data['updated_at'] ?? ''
        );
    }

    // Convert to array (for JSON responses, etc.)
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'address' => $this->address,
            'price' => $this->price,
            'rooms' => $this->rooms,
            'area' => $this->area,
            'type' => $this->type,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    // Get formatted price
    public function getFormattedPrice(): string
    {
        return number_format($this->price, 0, '.', ' ') . ' ₴';
    }

    // Get property type in Ukrainian
    public function getTypeInUkrainian(): string
    {
        $types = [
            'apartment' => 'Квартира',
            'house' => 'Будинок',
            'office' => 'Офіс',
            'land' => 'Земля',
            'commercial' => 'Комерційна нерухомість'
        ];

        return $types[$this->type] ?? $this->type;
    }

    // Get status in Ukrainian
    public function getStatusInUkrainian(): string
    {
        $statuses = [
            'available' => 'Доступно',
            'rented' => 'Орендовано',
            'sold' => 'Продано'
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}
