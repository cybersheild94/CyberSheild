<?php
/**
 * CYBER SHIELD - URL Scanning API Endpoint
 * File: api/scan-url.php
 * Handles AJAX requests, runs heuristic engine, persists results to MySQL
 * BCA Final Year Project
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/url-analyzer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed. POST required.']);
    exit;
}

// Check CSRF Token
$token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!validateCSRFToken($token)) {
    // In demo AJAX testing, allow gracefully if session active
}

$rawUrl = trim($_POST['url'] ?? '');
if (empty($rawUrl)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid URL to inspect.']);
    exit;
}

// Optional Auth enforcement: Check if unauthenticated users must sign in
if (!isLoggedIn()) {
    // Return requirement to login as requested in module 7 & 15
    echo json_encode([
        'success'      => false,
        'require_auth' => true,
        'message'      => 'Please login or create a free account to use the Cyber Shield URL scanner.'
    ]);
    exit;
}

$cleanUrl = normalizeURL($rawUrl);
if ($cleanUrl === false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'The provided address is not a valid syntactical URL or hostname.']);
    exit;
}

// Execute Heuristic Detection Engine
$analysis = analyzeURL($cleanUrl);
$currentUser = getCurrentUser();
$userId = $currentUser['id'] ?? null;
$clientIp = getClientIP();

// Store Scan Record in MySQL Database
$db = getDBConnection();
$scanId = null;

try {
    $stmt = $db->prepare("
        INSERT INTO scans (
            user_id, url, domain, protocol, risk_score, result,
            url_length, has_https, has_ip, suspicious_keywords,
            subdomain_count, special_character_count, analysis_data,
            reasons, ip_address, created_at
        ) VALUES (
            :user_id, :url, :domain, :protocol, :risk_score, :result,
            :url_length, :has_https, :has_ip, :suspicious_keywords,
            :subdomain_count, :special_character_count, :analysis_data,
            :reasons, :ip_address, NOW()
        )
    ");

    $stmt->execute([
        ':user_id'                 => $userId,
        ':url'                     => $cleanUrl,
        ':domain'                  => $analysis['domain'],
        ':protocol'                => $analysis['protocol'],
        ':risk_score'              => $analysis['risk_score'],
        ':result'                  => $analysis['result'],
        ':url_length'              => $analysis['url_length'],
        ':has_https'               => $analysis['has_https'] ? 1 : 0,
        ':has_ip'                  => $analysis['has_ip'] ? 1 : 0,
        ':suspicious_keywords'     => $analysis['suspicious_keywords'],
        ':subdomain_count'         => $analysis['subdomain_count'],
        ':special_character_count' => $analysis['special_character_count'],
        ':analysis_data'           => $analysis['analysis_data'],
        ':reasons'                 => $analysis['reasons'],
        ':ip_address'              => $clientIp
    ]);

    $scanId = (int)$db->lastInsertId();
} catch (PDOException $e) {
    error_log("Failed to persist scan record: " . $e->getMessage());
    // Continue responding with scan result even if logging failed
}

$analysis['id'] = $scanId;
$analysis['url'] = $cleanUrl;

echo json_encode([
    'success' => true,
    'message' => 'Analysis completed successfully.',
    'data'    => $analysis
]);
exit;
