<?php
/**
 * CYBER SHIELD - User Access Control Middleware
 * File: includes/user-auth.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function requireUser(): void {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? BASE_URL;
        setFlashMessage('warning', 'Please login to access this protected area.');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}
