<?php
namespace App\Classes;

use PDO;
use PDOException;
use Exception;

class Database
{
    private static ?PDO $connection = null;

     *
     * @return PDO
     * @throws Exception
     */
    public static function connect(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $dbType = $_ENV['DB_TYPE'] ?? 'sqlite';

        try {
            if ($dbType === 'sqlite') {
                $dbPath = __DIR__ . '/../../database.sqlite';
                self::$connection = new PDO("sqlite:{$dbPath}");

                self::createUsersTable();
            } elseif ($dbType === 'mysql') {
                $host = $_ENV['DB_HOST'] ?? 'localhost';
                $dbname = $_ENV['DB_NAME'] ?? 'composer_app';
                $username = $_ENV['DB_USER'] ?? 'root';
                $password = $_ENV['DB_PASS'] ?? '';

                self::$connection = new PDO(
                    "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]
                );
            } else {
                throw new Exception("Непідтримуваний тип бази даних: {$dbType}");
            }

            return self::$connection;

        } catch (PDOException $e) {
            throw new Exception("Помилка підключення до бази даних: " . $e->getMessage());
        }
    }

    private static function createUsersTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                name VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ";

        $stmt = self::$connection->prepare($sql);
        $stmt->execute();
    }

     *
     * @param string $email
     * @param string $password
     * @return array|null Масив з даними користувача або null, якщо не знайдено
     * @throws Exception
    public static function authenticateUser(string $email, string $password): ?array
    {
        try {
            $pdo = self::connect();

            $sql = "SELECT id, email, name, password FROM users WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);

            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            }

            return null;

        } catch (PDOException $e) {
            throw new Exception("Помилка авторизації: " . $e->getMessage());
        }
    }

     *
     * @param string $email
     * @param string $password
     * @param string $name
     * @return int ID створеного користувача
     * @throws Exception
    public static function registerUser(string $email, string $password, string $name): int
    {
        try {
            $pdo = self::connect();

            $checkSql = "SELECT id FROM users WHERE email = ?";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([$email]);

            if ($checkStmt->fetch()) {
                throw new Exception("Користувач з таким email вже існує");
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (email, password, name) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email, $hashedPassword, $name]);

            return (int) $pdo->lastInsertId();

        } catch (PDOException $e) {
            throw new Exception("Помилка реєстрації: " . $e->getMessage());
        }
    }

    public static function disconnect(): void
    {
        self::$connection = null;
    }
}
