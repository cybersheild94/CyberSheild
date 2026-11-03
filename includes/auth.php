<?php
/**
 * CYBER SHIELD - Authentication Functions
 * File: includes/auth.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/security.php';

/**
 * Check if a session has an authenticated user
 */
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['user_role']);
}

/**
 * Check if the currently logged-in user is an admin
 */
function isAdmin(): bool {
    return isLoggedIn() && ($_SESSION['user_role'] === 'admin');
}

/**
 * Return current logged in user array or null
 */
function getCurrentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id'            => (int)$_SESSION['user_id'],
        'name'          => $_SESSION['user_name'] ?? 'User',
        'email'         => $_SESSION['user_email'] ?? '',
        'role'          => $_SESSION['user_role'] ?? 'user',
        'profile_image' => $_SESSION['user_avatar'] ?? 'default_avatar.png'
    ];
}

/**
 * Log in a user by setting secure session parameters
 */
function loginUser(array $user): void {
    session_regenerate_id(true); // Prevent session fixation attacks
    $_SESSION['user_id']     = (int)$user['id'];
    $_SESSION['user_name']   = $user['name'];
    $_SESSION['user_email']  = $user['email'];
    $_SESSION['user_role']   = $user['role'];
    $_SESSION['user_avatar'] = $user['profile_image'] ?? 'default_avatar.png';
    $_SESSION['last_active'] = time();

    // Update last_login in database
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
    } catch (PDOException $e) {
        error_log("Failed to update last login: " . $e->getMessage());
    }
}

/**
 * Log out user safely destroying session
 */
function logoutUser(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
