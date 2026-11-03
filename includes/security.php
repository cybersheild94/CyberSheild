<?php
/**
 * CYBER SHIELD - Security Helpers
 * File: includes/security.php
 * Covers: CSRF tokens, XSS mitigation, input sanitization, and SQL safety
 */

declare(strict_types=1);

/**
 * Generate CSRF Token for forms and AJAX requests
 */
function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF Token
 */
function validateCSRFToken(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output hidden CSRF HTML input tag
 */
function csrfField(): string {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Clean & Sanitize String input against XSS
 */
function sanitizeInput(mixed $data): string {
    if ($data === null) {
        return '';
    }
    $trimmed = trim((string)$data);
    return htmlspecialchars($trimmed, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Sanitize & Validate Email
 */
function sanitizeEmail(string $email): string|false {
    $clean = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    return filter_var($clean, FILTER_VALIDATE_EMAIL) ? $clean : false;
}

/**
 * Normalize and Validate URL format
 */
function normalizeURL(string $url): string|false {
    $url = trim($url);
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    return $url;
}

/**
 * Secure Password Hash using Argon2ID or BCRYPT
 */
function hashUserPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Secure Password Verify
 */
function verifyUserPassword(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

/**
 * Get client IP address securely considering proxies
 */
function getClientIP(): string {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
}
