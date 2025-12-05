<?php
namespace App\Classes;

class Security
{
    /**
     * Sanitize input data to prevent XSS attacks
     *
     * @param string $data
     * @return string
     */
    public static function sanitizeInput(string $data): string
    {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize email input
     *
     * @param string $email
     * @return string
     */
    public static function sanitizeEmail(string $email): string
    {
        $sanitized = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        return htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize password (only trim, no other modifications)
     *
     * @param string $password
     * @return string
     */
    public static function sanitizePassword(string $password): string
    {
        return trim($password);
    }

    /**
     * Escape output for templates to prevent XSS
     *
     * @param string $data
     * @return string
     */
    public static function escapeOutput(string $data): string
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}
