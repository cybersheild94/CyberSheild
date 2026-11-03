<?php
/**
 * CYBER SHIELD - Password Reset Token Verification & Update
 * File: reset-password.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/security.php';

$token = sanitizeInput($_GET['token'] ?? $_POST['token'] ?? '');
$error = null;
$isValidToken = false;
$resetRecord = null;

$db = getDBConnection();

if (!empty($token)) {
    try {
        $stmt = $db->prepare("
            SELECT pr.*, u.email, u.name 
            FROM password_resets pr
            JOIN users u ON pr.user_id = u.id
            WHERE pr.token = ? AND pr.expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $resetRecord = $stmt->fetch();
        if ($resetRecord) {
            $isValidToken = true;
        } else {
            $error = 'This password reset token is invalid, used, or expired. Please request a new one.';
        }
    } catch (PDOException $e) {
        error_log("Token check error: " . $e->getMessage());
        $error = 'Database error verifying reset token.';
    }
} else {
    $error = 'No reset token was provided in the request.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isValidToken) {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($csrf)) {
        $error = 'CSRF validation failed. Please refresh.';
    } else {
        $newPass = (string)($_POST['password'] ?? '');
        $confirmPass = (string)($_POST['confirm_password'] ?? '');

        if (empty($newPass) || strlen($newPass) < 8) {
            $error = 'New password must contain at least 8 characters.';
        } elseif ($newPass !== $confirmPass) {
            $error = 'The passwords entered do not match.';
        } else {
            try {
                $hash = hashUserPassword($newPass);

                // Update user's password
                $upStmt = $db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                $upStmt->execute([$hash, $resetRecord['user_id']]);

                // Clear used token
                $delStmt = $db->prepare("DELETE FROM password_resets WHERE user_id = ?");
                $delStmt->execute([$resetRecord['user_id']]);

                setFlashMessage('success', 'Password changed successfully! You may now sign in with your new credentials.');
                header('Location: ' . BASE_URL . '/login.php');
                exit;
            } catch (PDOException $e) {
                error_log("Password update error: " . $e->getMessage());
                $error = 'Database exception during password update.';
            }
        }
    }
}

$pageTitle = 'Reset Account Password';
$activeNav = 'login';
$extraCss = 'auth.css';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-icon-badge">
            <i class="fa-solid fa-lock-open"></i>
        </div>

        <h2 class="auth-title">Set New Password</h2>
        <p class="auth-subtitle">Create a robust password for your account</p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small mb-3">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
            </div>
            <?php if (!$isValidToken): ?>
                <div class="text-center mt-3">
                    <a href="forgot-password.php" class="btn btn-outline-cyan btn-sm">Request New Reset Link</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($isValidToken): ?>
            <div class="mb-3 text-secondary small">
                Resetting password for: <strong class="text-white"><?= htmlspecialchars($resetRecord['email']) ?></strong>
            </div>

            <form action="reset-password.php" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="form-floating mb-3">
                    <input type="password" name="password" class="form-control" id="newPassword" placeholder="New Password" required>
                    <label for="newPassword">New Password (min 8 characters)</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" name="confirm_password" class="form-control" id="confirmNewPassword" placeholder="Confirm Password" required>
                    <label for="confirmNewPassword">Confirm New Password</label>
                </div>

                <button type="submit" class="btn btn-cyan w-100 py-2 mb-3">
                    <i class="fa-solid fa-check me-1"></i> Update Password & Sign In
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
