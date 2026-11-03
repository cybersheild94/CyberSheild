<?php
/**
 * CYBER SHIELD - Administrator Access Control Middleware
 * File: includes/admin-auth.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function requireAdmin(): void {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_admin_login'] = $_SERVER['REQUEST_URI'] ?? (BASE_URL . '/admin/dashboard.php');
        setFlashMessage('danger', 'Administrator privileges required. Please authenticate.');
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }

    if (!isAdmin()) {
        http_response_code(403);
        setFlashMessage('danger', 'Access Denied: You do not possess administrator rights to view this page.');
        header('Location: ' . BASE_URL . '/user/dashboard.php');
        exit;
    }
}
