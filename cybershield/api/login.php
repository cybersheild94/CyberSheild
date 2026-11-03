<?php
/**
 * CYBER SHIELD - Asynchronous Login API Endpoint
 * File: api/login.php
 * BCA Final Year Project
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$email = sanitizeEmail($_POST['email'] ?? '');
$password = (string)($_POST['password'] ?? '');

if (!$email || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide both email and password.']);
    exit;
}

$db = getDBConnection();
try {
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && verifyUserPassword($password, $user['password'])) {
        if ($user['status'] !== 'active') {
            echo json_encode(['success' => false, 'message' => 'Your account is suspended or inactive. Please contact administrator.']);
            exit;
        }

        loginUser($user);

        $redirect = ($user['role'] === 'admin') ? BASE_URL . '/admin/dashboard.php' : BASE_URL . '/user/dashboard.php';
        echo json_encode([
            'success'  => true,
            'message'  => 'Login successful. Redirecting...',
            'redirect' => $redirect,
            'role'     => $user['role']
        ]);
        exit;
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }
} catch (PDOException $e) {
    error_log("Login API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An internal database error occurred.']);
    exit;
}
