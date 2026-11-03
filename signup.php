<?php
/**
 * CYBER SHIELD - User Registration Portal
 * File: signup.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/security.php';

// If already logged in, redirect away
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/user/dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($token)) {
        $error = 'Security session expired. Please submit the form again.';
    } else {
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeEmail($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');
        $terms = isset($_POST['terms']);

        if (empty($name) || !$email || empty($password)) {
            $error = 'Please fill out all mandatory registration fields.';
        } elseif (!$terms) {
            $error = 'You must acknowledge the Terms of Service & Security Policy.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match. Please verify.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must contain at least 8 characters for security.';
        } else {
            $db = getDBConnection();
            try {
                // Check if email already registered
                $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                $checkStmt->execute([$email]);
                if ($checkStmt->fetch()) {
                    $error = 'This email address is already registered. Please login or use forgot password.';
                } else {
                    $hashedPassword = hashUserPassword($password);

                    $insertStmt = $db->prepare("
                        INSERT INTO users (name, email, password, phone, role, status, profile_image, created_at)
                        VALUES (?, ?, ?, ?, 'user', 'active', 'default_avatar.png', NOW())
                    ");
                    $insertStmt->execute([$name, $email, $hashedPassword, $phone]);

                    setFlashMessage('success', 'Account created successfully. Please login.');
                    header('Location: ' . BASE_URL . '/login.php');
                    exit;
                }
            } catch (PDOException $e) {
                error_log("Signup error: " . $e->getMessage());
                $error = 'Unable to complete registration due to database exception.';
            }
        }
    }
}

$pageTitle = 'Create Free Security Account';
$activeNav = 'signup';
$extraCss = 'auth.css';
$extraJs = 'auth.js';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="auth-wrapper">
    <div class="auth-card auth-card-wide">
        <div class="auth-icon-badge">
            <i class="fa-solid fa-user-plus"></i>
        </div>

        <h2 class="auth-title">Create Security Account</h2>
        <p class="auth-subtitle">Join Cyber Shield to analyze phishing URLs and safeguard credentials</p>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show cyber-alert py-2 small mb-3" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="signup.php" method="POST">
            <?= csrfField() ?>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input 
                            type="text" 
                            name="name" 
                            class="form-control" 
                            id="signupName" 
                            placeholder="Full Name" 
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                            required
                        >
                        <label for="signupName">Full Name</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <input 
                            type="tel" 
                            name="phone" 
                            class="form-control" 
                            id="signupPhone" 
                            placeholder="+1 (555) 000-0000" 
                            value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                        >
                        <label for="signupPhone">Phone Number (Optional)</label>
                    </div>
                </div>
            </div>

            <div class="form-floating mb-3">
                <input 
                    type="email" 
                    name="email" 
                    class="form-control" 
                    id="signupEmail" 
                    placeholder="name@example.com" 
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                    required
                >
                <label for="signupEmail">Email Address</label>
            </div>

            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input 
                            type="password" 
                            name="password" 
                            class="form-control" 
                            id="signupPassword" 
                            placeholder="Password" 
                            required
                        >
                        <label for="signupPassword">Password</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <input 
                            type="password" 
                            name="confirm_password" 
                            class="form-control" 
                            id="signupConfirmPassword" 
                            placeholder="Confirm Password" 
                            required
                        >
                        <label for="signupConfirmPassword">Confirm Password</label>
                    </div>
                </div>
            </div>

            <!-- Client-side Password Strength Indicator -->
            <div class="mb-3">
                <div class="password-strength-bar">
                    <div id="passwordStrengthFill" class="password-strength-fill"></div>
                </div>
                <div id="passwordStrengthText" class="text-secondary small mt-1">Minimum 8 characters with letters, numbers, and symbols</div>
            </div>

            <div class="form-check mb-4 small">
                <input class="form-check-input bg-dark border-secondary" type="checkbox" name="terms" id="termsAgree" required>
                <label class="form-check-label text-secondary" for="termsAgree">
                    I accept the <a href="about.php" class="auth-link">Terms of Service</a> and confirm I will use Cyber Shield solely for authorized defensive URL scanning.
                </label>
            </div>

            <button type="submit" class="btn btn-cyan w-100 py-2 mb-3">
                <i class="fa-solid fa-shield me-1"></i> Register Free Account
            </button>

            <div class="text-center small text-secondary">
                Already registered? 
                <a href="login.php" class="auth-link fw-bold ms-1">Sign In Here</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
