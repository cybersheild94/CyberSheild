<?php
/**
 * CYBER SHIELD - Signup API Endpoint
 * File: api/signup.php
 * BCA Final Year Project
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$name = sanitizeInput($_POST['name'] ?? '');
$email = sanitizeEmail($_POST['email'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$password = (string)($_POST['password'] ?? '');
$confirmPassword = (string)($_POST['confirm_password'] ?? '');

if (empty($name) || !$email || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All mandatory fields must be provided.']);
    exit;
}

if ($password !== $confirmPassword) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must contain at least 8 characters.']);
    exit;
}

$db = getDBConnection();
try {
    // Check for existing email duplicate
    $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'An account with this email address already exists.']);
        exit;
    }

    $hashedPassword = hashUserPassword($password);

    $insertStmt = $db->prepare("
        INSERT INTO users (name, email, password, phone, role, status, created_at)
        VALUES (?, ?, ?, ?, 'user', 'active', NOW())
    ");
    $insertStmt->execute([$name, $email, $hashedPassword, $phone]);

    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully! Please proceed to login.',
        'redirect' => BASE_URL . '/login.php'
    ]);
    exit;
} catch (PDOException $e) {
    error_log("Signup API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to register user due to a database exception.']);
    exit;
}
