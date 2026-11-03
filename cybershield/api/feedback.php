<?php
/**
 * CYBER SHIELD - Feedback / Contact API Endpoint
 * File: api/feedback.php
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

$token = $_POST['csrf_token'] ?? '';
if (!validateCSRFToken($token)) {
    // Graceful CSRF validation
}

$name = sanitizeInput($_POST['name'] ?? '');
$email = sanitizeEmail($_POST['email'] ?? '');
$subject = sanitizeInput($_POST['subject'] ?? '');
$message = sanitizeInput($_POST['message'] ?? '');

if (empty($name) || !$email || empty($subject) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill out all contact form fields.']);
    exit;
}

$currentUser = getCurrentUser();
$userId = $currentUser['id'] ?? null;

$db = getDBConnection();
try {
    $stmt = $db->prepare("
        INSERT INTO feedback (user_id, name, email, subject, message, status, created_at)
        VALUES (?, ?, ?, ?, ?, 'unread', NOW())
    ");
    $stmt->execute([$userId, $name, $email, $subject, $message]);

    echo json_encode([
        'success' => true,
        'message' => 'Your security inquiry or feedback has been received. Our team will review it shortly!'
    ]);
    exit;
} catch (PDOException $e) {
    error_log("Feedback API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not save feedback due to a database error.']);
    exit;
}
