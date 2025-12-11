<?php

/**
 * Клас Security - методи захисту додатку
 *
 * Реалізує різні методи захисту від атак:
 * - Захист від XSS через фільтрацію вхідних даних
 * - Екранування HTML символів
 * - Валідація даних
 *
 * @package     DIM.RIA
 * @subpackage  Security
 * @author      Student
 * @version     1.0.0
 */

namespace App\Classes;

class Security
{
    /**
     * Фільтрує вхідні дані від XSS атак
     *
     * @param string $data Вхідні дані
     * @return string Очищені дані
     */
    public static function sanitizeInput(string $data): string
    {
        // Видаляємо HTML теги
        $data = strip_tags($data);

        // Конвертуємо спеціальні символи в HTML сутності
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Видаляємо зайві пробіли
        $data = trim($data);

        return $data;
    }

    /**
     * Санітізує email (видаляє теги, але зберігає @ і .)
     *
     * @param string $email Email для санітізації
     * @return string Санітізований email
     */
    public static function sanitizeEmail(string $email): string
    {
        // Видаляємо HTML теги
        $email = strip_tags($email);

        // Видаляємо зайві пробіли
        $email = trim($email);

        // Перевіряємо на XSS спроби
        if (!self::isSafeString($email)) {
            self::logXssAttempt($email, 'email_input');
            return '';
        }

        return $email;
    }

    /**
     * Санітізує пароль (тільки базова фільтрація)
     *
     * @param string $password Пароль для санітізації
     * @return string Санітізований пароль
     */
    public static function sanitizePassword(string $password): string
    {
        // Видаляємо зайві пробіли з кінців
        $password = trim($password);

        // Перевіряємо на XSS спроби в паролі
        if (!self::isSafeString($password)) {
            self::logXssAttempt($password, 'password_input');
            return '';
        }

        return $password;
    }

    /**
     * Санітізує ім'я користувача
     *
     * @param string $name Ім'я для санітізації
     * @return string Санітізоване ім'я
     */
    public static function sanitizeName(string $name): string
    {
        // Видаляємо HTML теги
        $name = strip_tags($name);

        // Конвертуємо спеціальні символи
        $name = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Видаляємо зайві пробіли
        $name = trim($name);

        // Перевіряємо на XSS
        if (!self::isSafeString($name)) {
            self::logXssAttempt($name, 'name_input');
            return '';
        }

        return $name;
    }

    /**
     * Екранує дані для виведення в HTML
     *
     * @param string $output Дані для екранування
     * @return string Екрановані дані
     */
    public static function escapeOutput(string $output): string
    {
        return htmlspecialchars($output, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Фільтрує масив даних (рекурсивно)
     *
     * @param array $data Масив даних
     * @return array Очищений масив
     */
    public static function sanitizeArray(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = self::sanitizeInput($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Фільтрує $_POST дані
     *
     * @return array Очищені POST дані
     */
    public static function sanitizePostData(): array
    {
        return self::sanitizeArray($_POST);
    }

    /**
     * Фільтрує $_GET дані
     *
     * @return array Очищені GET дані
     */
    public static function sanitizeGetData(): array
    {
        return self::sanitizeArray($_GET);
    }

    /**
     * Перевіряє email на коректність
     *
     * @param string $email Email для перевірки
     * @return bool Результат перевірки
     */
    public static function validateEmail(string $email): bool
    {
        $email = trim($email);
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Перевіряє пароль на міцність
     *
     * @param string $password Пароль для перевірки
     * @return bool Результат перевірки
     */
    public static function validatePassword(string $password): bool
    {
        // Мінімум 8 символів, містить букви та цифри
        return strlen($password) >= 8 &&
               preg_match('/[a-zA-Z]/', $password) &&
               preg_match('/[0-9]/', $password);
    }

    /**
     * Генерує CSRF токен
     *
     * @return string CSRF токен
     */
    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Перевіряє CSRF токен
     *
     * @param string $token Токен для перевірки
     * @return bool Результат перевірки
     */
    public static function validateCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) &&
               hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Екранує рядок для безпечного виведення в HTML
     * (як альтернатива до htmlspecialchars)
     *
     * @param string $string Рядок для екранування
     * @return string Екранований рядок
     */
    public static function escapeHtml(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Перевіряє, чи є рядок безпечним для виведення
     *
     * @param string $string Рядок для перевірки
     * @return bool Результат перевірки
     */
    public static function isSafeString(string $string): bool
    {
        // Перевіряємо на наявність підозрілих патернів
        $dangerousPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i'
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $string)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Логує спроби XSS атак
     *
     * @param string $data Підозрілі дані
     * @param string $source Джерело даних
     * @return void
     */
    public static function logXssAttempt(string $data, string $source): void
    {
        $logger = new \Monolog\Logger('security');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler(
            __DIR__ . '/../../logs/security.log',
            \Monolog\Logger::WARNING
        ));

        $logger->warning('XSS attempt detected', [
            'data' => substr($data, 0, 100), // обмежуємо довжину для логу
            'source' => $source,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Створює безпечний рядок для JavaScript
     *
     * @param string $string Рядок для екранування
     * @return string Екранований рядок
     */
    public static function escapeJs(string $string): string
    {
        return json_encode($string, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    /**
     * Створює безпечний рядок для URL
     *
     * @param string $string Рядок для екранування
     * @return string Екранований рядок
     */
    public static function escapeUrl(string $string): string
    {
        return urlencode($string);
    }
}
