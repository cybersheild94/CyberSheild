<?php
/**
 * CYBER SHIELD - Global Application Configuration
 * File: config/config.php
 * BCA Final Year Project
 */

declare(strict_types=1);

// Error reporting settings
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Set to '1' for debugging in development
ini_set('log_errors', '1');

// Timezone
date_default_timezone_set('Asia/Kolkata'); // Standard Indian Standard Time for BCA curriculum

// Base URLs and Paths
define('APP_NAME', 'CYBER SHIELD');
define('APP_VERSION', '1.0.0 (BCA Final Edition)');
define('APP_TAGLINE', 'AI-Inspired Phishing URL Analysis & Cyber Threat Defense');

// Automatically detect base URL for XAMPP, WAMP, or Apache virtual hosts
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

// Normalize root path for subdirectory installations like /cyber-shield/
$basePath = preg_replace('#/(user|admin|api|includes|config|assets)(/.*)?$#', '', $scriptDir);
$basePath = rtrim($basePath, '/');

define('BASE_URL', $protocol . $host . $basePath);
define('ROOT_PATH', dirname(__DIR__));

// Security & Session Settings
if (session_status() === PHP_SESSION_NONE) {
    // Session cookie parameters for security
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

// Risk thresholds
define('RISK_THRESHOLD_SAFE', 29);
define('RISK_THRESHOLD_SUSPICIOUS', 59);
define('RISK_THRESHOLD_PHISHING', 60);

// Load Database
require_once __DIR__ . '/database.php';
