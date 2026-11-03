<?php
/**
 * CYBER SHIELD - Administrator Authentication Portal
 * File: admin/login.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

// If already authenticated as admin, redirect to admin dashboard
if (isAdmin()) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($token)) {
        $error = 'Security session validation failed.';
    } else {
        $email = sanitizeEmail($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if (!$email || empty($password)) {
            $error = 'Please enter both administrator email and password.';
        } else {
            $db = getDBConnection();
            try {
                $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
                $stmt->execute([$email]);
                $admin = $stmt->fetch();

                if ($admin && verifyUserPassword($password, $admin['password'])) {
                    if ($admin['status'] !== 'active') {
                        $error = 'This administrator account is deactivated.';
                    } else {
                        loginUser($admin);
                        setFlashMessage('success', 'Authenticated with Administrator Privileges.');
                        header('Location: ' . BASE_URL . '/admin/dashboard.php');
                        exit;
                    }
                } else {
                    $error = 'Invalid administrator credentials or unauthorized role.';
                }
            } catch (PDOException $e) {
                error_log("Admin login DB error: " . $e->getMessage());
                $error = 'Database connection error during authentication.';
            }
        }
    }
}

$pageTitle = 'Administrator Secure Sign In';
$activeNav = 'admin';
$extraCss = 'auth.css';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="auth-wrapper">
    <div class="auth-card border-danger">
        <div class="auth-icon-badge" style="border-color: #ef4444; color: #ef4444; background: rgba(239, 68, 68, 0.15);">
            <i class="fa-solid fa-shield-virus"></i>
        </div>

        <h2 class="auth-title text-danger">SOC Admin Console</h2>
        <p class="auth-subtitle">Restricted to authorized cybersecurity system administrators</p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small mb-3">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <?= csrfField() ?>

            <div class="form-floating mb-3">
                <input 
                    type="email" 
                    name="email" 
                    class="form-control border-danger border-opacity-50" 
                    id="adminEmail" 
                    placeholder="admin@cybershield.local" 
                    value="<?= htmlspecialchars($_POST['email'] ?? 'admin@cybershield.local') ?>" 
                    required
                >
                <label for="adminEmail">Administrator Email</label>
            </div>

            <div class="form-floating mb-4">
                <input 
                    type="password" 
                    name="password" 
                    class="form-control border-danger border-opacity-50" 
                    id="adminPassword" 
                    placeholder="Password" 
                    value="Admin@CyberShield2026"
                    required
                >
                <label for="adminPassword">Master Password</label>
            </div>

            <button type="submit" class="btn btn-danger w-100 py-2 mb-3">
                <i class="fa-solid fa-unlock-keyhole me-1"></i> Authenticate SOC Admin
            </button>

            <div class="text-center small text-secondary">
                <a href="<?= BASE_URL ?>/index.php" class="auth-link text-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Return to Public Site
                </a>
            </div>
        </form>

        <div class="demo-account-hint border-danger border-opacity-25 mt-4">
            <span class="text-danger fw-bold"><i class="fa-solid fa-terminal me-1"></i> Demo Admin Account:</span><br>
            <code>admin@cybershield.local</code> | <code>Admin@CyberShield2026</code>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
