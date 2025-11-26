<?php
namespace App\Classes;

use App\Classes\Database;
use Exception;

class User
{
    private ?int $id;
    private string $email;
    private string $name;
    private string $password;
    private string $created_at;

    public function __construct(?int $id = null, string $email = '', string $name = '', string $password = '', string $created_at = '')
    {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->password = $password;
        $this->created_at = $created_at;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    // Setters
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    // Static methods for database operations
    public static function authenticate(string $email, string $password): ?User
    {
        try {
            $userData = Database::authenticateUser($email, $password);

            if ($userData) {
                return new User(
                    $userData['id'],
                    $userData['email'],
                    $userData['name'],
                    '',
                    $userData['created_at'] ?? ''
                );
            }

            return null;
        } catch (Exception $e) {
            throw new Exception("Authentication failed: " . $e->getMessage());
        }
    }

    public static function register(string $email, string $password, string $name): User
    {
        try {
            $userId = Database::registerUser($email, $password, $name);

            return new User($userId, $email, $name, '', date('Y-m-d H:i:s'));
        } catch (Exception $e) {
            throw new Exception("Registration failed: " . $e->getMessage());
        }
    }

    public static function findById(int $id): ?User
    {
        try {
            $pdo = Database::connect();

            $sql = "SELECT id, email, name, created_at FROM users WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            $userData = $stmt->fetch();

            if ($userData) {
                return new User(
                    $userData['id'],
                    $userData['email'],
                    $userData['name'],
                    '',
                    $userData['created_at']
                );
            }

            return null;
        } catch (Exception $e) {
            throw new Exception("Failed to find user: " . $e->getMessage());
        }
    }

    public static function findByEmail(string $email): ?User
    {
        try {
            $pdo = Database::connect();

            $sql = "SELECT id, email, name, created_at FROM users WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);

            $userData = $stmt->fetch();

            if ($userData) {
                return new User(
                    $userData['id'],
                    $userData['email'],
                    $userData['name'],
                    '',
                    $userData['created_at']
                );
            }

            return null;
        } catch (Exception $e) {
            throw new Exception("Failed to find user: " . $e->getMessage());
        }
    }

    public static function getAll(): array
    {
        try {
            $pdo = Database::connect();

            $sql = "SELECT id, email, name, created_at FROM users ORDER BY created_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $users = [];
            while ($userData = $stmt->fetch()) {
                $users[] = new User(
                    $userData['id'],
                    $userData['email'],
                    $userData['name'],
                    '',
                    $userData['created_at']
                );
            }

            return $users;
        } catch (Exception $e) {
            throw new Exception("Failed to get users: " . $e->getMessage());
        }
    }

    // Instance methods
    public function save(): bool
    {
        try {
            $pdo = Database::connect();

            if ($this->id) {
                // Update existing user
                $sql = "UPDATE users SET email = ?, name = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                return $stmt->execute([$this->email, $this->name, $this->id]);
            } else {
                // Create new user
                $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (email, password, name) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$this->email, $hashedPassword, $this->name]);
                $this->id = (int) $pdo->lastInsertId();
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Failed to save user: " . $e->getMessage());
        }
    }

    public function delete(): bool
    {
        if (!$this->id) {
            return false;
        }

        try {
            $pdo = Database::connect();

            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$this->id]);
        } catch (Exception $e) {
            throw new Exception("Failed to delete user: " . $e->getMessage());
        }
    }

    public function changePassword(string $newPassword): bool
    {
        if (!$this->id) {
            return false;
        }

        try {
            $pdo = Database::connect();

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$hashedPassword, $this->id]);
        } catch (Exception $e) {
            throw new Exception("Failed to change password: " . $e->getMessage());
        }
    }

    // Convert to array (for JSON responses, etc.)
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'created_at' => $this->created_at
        ];
    }
}
