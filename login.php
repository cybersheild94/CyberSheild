<?php
/**
 * CYBER SHIELD - User Authentication Portal
 * File: login.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/security.php';

// If already logged in, redirect away
if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? BASE_URL . '/admin/dashboard.php' : BASE_URL . '/user/dashboard.php'));
    exit;
}

$error = null;
$selectedRole = ($_SERVER['REQUEST_METHOD'] === 'POST')
    ? (string)($_POST['login_role'] ?? 'user')
    : (string)($_GET['role'] ?? 'user');

if (!in_array($selectedRole, ['admin', 'user'], true)) {
    $selectedRole = 'user';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($token)) {
        $error = 'Security validation failed. Please refresh and try again.';
    } else {
        $email = sanitizeEmail($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if (!$email || $password === '') {
            $error = 'Please provide both email address and password.';
        } else {
            try {
                $db = getDBConnection();
                $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if (!$user || !verifyUserPassword($password, $user['password'])) {
                    $error = 'Invalid email address or password.';
                } elseif (($user['status'] ?? '') !== 'active') {
                    $error = 'Your account has been deactivated or suspended.';
                } elseif (($user['role'] ?? 'user') !== $selectedRole) {
                    $error = $selectedRole === 'admin'
                        ? 'This account does not have administrator access.'
                        : 'Please select Admin Login for this administrator account.';
                } else {
                    loginUser($user);
                    setFlashMessage('success', 'Welcome back, ' . $user['name'] . '!');

                    // Keep both account types in this file and route by verified database role.
                    unset($_SESSION['redirect_after_login']);
                    $destination = ($user['role'] === 'admin')
                        ? BASE_URL . '/admin/dashboard.php'
                        : BASE_URL . '/user/dashboard.php';

                    header('Location: ' . $destination);
                    exit;
                }
            } catch (PDOException $e) {
                error_log("Login DB Error: " . $e->getMessage());
                $error = 'A database connection error occurred. Please verify the MySQL service.';
            }
        }
    }
}

$pageTitle = 'Secure Login | Cyber Shield';
$activeNav = 'login';
$extraCss = 'auth.css';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<style>
:root {
    --login-blue: #1976d2;
    --login-cyan: #16a2b5;
    --login-ink: #203247;
    --login-muted: #687b90;
    --login-line: #dfe8f1;
}
body {
    background: linear-gradient(135deg, #f6faff 0%, #edf6ff 55%, #f1fbfc 100%) !important;
    color: var(--login-ink) !important;
}
.auth-wrapper {
    min-height: 78vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 42px 16px;
}
.auth-card {
    width: 100%;
    max-width: 470px;
    padding: 34px !important;
    background: rgba(255,255,255,.98) !important;
    border: 1px solid var(--login-line) !important;
    border-radius: 22px !important;
    box-shadow: 0 18px 50px rgba(39, 82, 125, .11) !important;
}
.auth-icon-badge {
    width: 58px;
    height: 58px;
    display: grid;
    place-items: center;
    margin: 0 auto 16px;
    border-radius: 17px;
    color: var(--login-blue) !important;
    background: #e9f4ff !important;
    border: 1px solid #d7eaff;
    font-size: 23px;
}
.auth-title { color: var(--login-ink) !important; font-weight: 750; text-align: center; }
.auth-subtitle { color: var(--login-muted) !important; text-align: center; margin-bottom: 24px; }
.role-switch {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    padding: 6px;
    background: #f0f5fa;
    border: 1px solid #e2eaf2;
    border-radius: 13px;
    margin-bottom: 22px;
}
.role-option {
    border: 0;
    border-radius: 9px;
    padding: 11px 8px;
    background: transparent;
    color: #62758a;
    font-weight: 650;
    transition: .2s ease;
}
.role-option.active {
    color: #1767b1;
    background: #fff;
    box-shadow: 0 3px 12px rgba(39,82,125,.10);
}
.auth-card .form-control {
    min-height: 52px;
    color: #203247 !important;
    background: #fbfdff !important;
    border: 1px solid #d8e3ed !important;
    border-radius: 10px !important;
}
.auth-card .form-control:focus {
    border-color: #73b4ed !important;
    box-shadow: 0 0 0 .2rem rgba(25,118,210,.12) !important;
}
.auth-card label { color: #718196 !important; }
.auth-card .text-secondary { color: #687b90 !important; }
.auth-link { color: #1976d2 !important; }
.btn-cyan {
    background: #1976d2 !important;
    border-color: #1976d2 !important;
    color: white !important;
    border-radius: 10px !important;
    font-weight: 650;
}
.btn-cyan:hover { background: #125ca8 !important; }
.demo-account-hint {
    margin-top: 22px;
    padding: 13px 15px;
    border-radius: 12px;
    background: #f4f8fc !important;
    border: 1px solid #e2eaf2 !important;
    color: #65778c !important;
    font-size: 12px;
    line-height: 1.8;
    overflow-wrap: anywhere;
}
.demo-account-hint code { color: #1767b1; }
@media(max-width: 480px) {
    .auth-card { padding: 25px 20px !important; }
}
</style>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-icon-badge"><i class="fa-solid fa-shield-halved"></i></div>
        <h2 class="auth-title">Welcome to Cyber Shield</h2>
        <p class="auth-subtitle">Sign in to continue to your security dashboard</p>

        <div class="role-switch" role="group" aria-label="Choose login type">
            <button type="button" class="role-option <?= $selectedRole === 'user' ? 'active' : '' ?>"
                    data-role="user" aria-pressed="<?= $selectedRole === 'user' ? 'true' : 'false' ?>">
                <i class="fa-solid fa-user me-2"></i>User Login
            </button>
            <button type="button" class="role-option <?= $selectedRole === 'admin' ? 'active' : '' ?>"
                    data-role="admin" aria-pressed="<?= $selectedRole === 'admin' ? 'true' : 'false' ?>">
                <i class="fa-solid fa-user-shield me-2"></i>Admin Login
            </button>
        </div>

        <div class="text-center small mb-3" style="color:#718196" id="roleDescription">
            <?= $selectedRole === 'admin' ? 'Administrator access to manage the Cyber Shield platform.' : 'Sign in to scan URLs and view your security reports.' ?>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small mb-3" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="login_role" id="loginRole" value="<?= htmlspecialchars($selectedRole, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control" id="loginEmail"
                       placeholder="name@example.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required autocomplete="username">
                <label for="loginEmail">Email Address</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="loginPassword"
                       placeholder="Password" required autocomplete="current-password">
                <label for="loginPassword">Password</label>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 small">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                    <label class="form-check-label" for="rememberMe" style="color:#687b90">Remember session</label>
                </div>
                <a href="forgot-password.php" class="auth-link">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-cyan w-100 py-3 mb-3">
                <i class="fa-solid fa-right-to-bracket me-2"></i><span id="submitLabel"><?= $selectedRole === 'admin' ? 'Sign In as Admin' : 'Sign In as User' ?></span>
            </button>

            <div class="text-center small" style="color:#687b90">
                New to Cyber Shield? <a href="signup.php" class="auth-link fw-bold ms-1">Create an account</a>
            </div>
        </form>

        <div class="demo-account-hint">
            <div class="fw-bold mb-1" style="color:#1767b1"><i class="fa-solid fa-circle-info me-1"></i> Demo account details</div>
            <div><strong>User:</strong> user@cybershield.local</div>
            <div><strong>Admin:</strong> admin@cybershield.local</div>
            <div class="mt-1">Use the password configured for each account in your database. For security, passwords are not pre-filled.</div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.role-option').forEach(function(button) {
    button.addEventListener('click', function() {
        const role = this.dataset.role;
        document.getElementById('loginRole').value = role;
        document.querySelectorAll('.role-option').forEach(function(item) {
            const active = item.dataset.role === role;
            item.classList.toggle('active', active);
            item.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
        document.getElementById('roleDescription').textContent = role === 'admin'
            ? 'Administrator access to manage the Cyber Shield platform.'
            : 'Sign in to scan URLs and view your security reports.';
        document.getElementById('submitLabel').textContent = role === 'admin'
            ? 'Sign In as Admin'
            : 'Sign In as User';
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
