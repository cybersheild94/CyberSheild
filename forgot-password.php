<?php
/**
 * CYBER SHIELD - Password Recovery Portal
 * File: forgot-password.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/security.php';

$message = null;
$demoResetLink = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($token)) {
        $error = 'Session expired. Please try again.';
    } else {
        $email = sanitizeEmail($_POST['email'] ?? '');

        if (!$email) {
            $error = 'Please enter a valid email address.';
        } else {
            $db = getDBConnection();
            try {
                $stmt = $db->prepare("SELECT id, name FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    // Generate cryptographically secure token
                    $resetToken = bin2hex(random_bytes(32));
                    $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour validity

                    // Invalidate any older tokens for this user
                    $delStmt = $db->prepare("DELETE FROM password_resets WHERE user_id = ?");
                    $delStmt->execute([$user['id']]);

                    // Store new token in password_resets table
                    $insertStmt = $db->prepare("
                        INSERT INTO password_resets (user_id, token, expires_at, created_at)
                        VALUES (?, ?, ?, NOW())
                    ");
                    $insertStmt->execute([$user['id'], $resetToken, $expiresAt]);

                    $demoResetLink = BASE_URL . '/reset-password.php?token=' . $resetToken;
                    $message = 'Password reset instructions have been created.';
                } else {
                    // Constant time response to prevent user enumeration
                    $message = 'If this email exists in our records, password reset instructions have been generated.';
                }
            } catch (PDOException $e) {
                error_log("Password reset error: " . $e->getMessage());
                $error = 'Database error generating token.';
            }
        }
    }
}

$pageTitle = 'Forgot Password';
$activeNav = 'login';
$extraCss = 'auth.css';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-icon-badge">
            <i class="fa-solid fa-key"></i>
        </div>

        <h2 class="auth-title">Password Recovery</h2>
        <p class="auth-subtitle">Enter your registered email to generate a secure reset link</p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small mb-3">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-success py-2 small mb-3">
                <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($demoResetLink): ?>
            <div class="p-3 bg-dark bg-opacity-75 rounded border border-cyan mb-4 small">
                <span class="text-cyan fw-bold d-block mb-1"><i class="fa-solid fa-flask me-1"></i> Academic / Viva Development Mode:</span>
                <span class="text-secondary d-block mb-2">Since local XAMPP/WAMP usually lacks configured SMTP mail server, Cyber Shield displays the generated one-time link below:</span>
                <a href="<?= htmlspecialchars($demoResetLink) ?>" class="text-break text-cyan fw-semibold">
                    <?= htmlspecialchars($demoResetLink) ?>
                </a>
            </div>
        <?php endif; ?>

        <form action="forgot-password.php" method="POST">
            <?= csrfField() ?>

            <div class="form-floating mb-3">
                <input 
                    type="email" 
                    name="email" 
                    class="form-control" 
                    id="resetEmail" 
                    placeholder="name@example.com" 
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required
                >
                <label for="resetEmail">Registered Email Address</label>
            </div>

            <button type="submit" class="btn btn-cyan w-100 py-2 mb-3">
                <i class="fa-solid fa-paper-plane me-1"></i> Generate Password Reset Link
            </button>

            <div class="text-center small text-secondary">
                Remembered your credentials? 
                <a href="login.php" class="auth-link fw-bold ms-1">Back to Login</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
