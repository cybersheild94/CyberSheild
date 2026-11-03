<?php
/**
 * CYBER SHIELD - User Password Change
 * File: user/change-password.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/user-auth.php';
requireUser();

$currentUser = getCurrentUser();
$userId = $currentUser['id'];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($token)) {
        $error = 'Session expired. Please try again.';
    } else {
        $currentPassword = (string)($_POST['current_password'] ?? '');
        $newPassword = (string)($_POST['new_password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'All password fields are required.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New password and confirmation password do not match.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'New password must be at least 8 characters long.';
        } else {
            $db = getDBConnection();
            try {
                // Fetch existing password hash
                $stmt = $db->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();

                if ($user && verifyUserPassword($currentPassword, $user['password'])) {
                    $newHash = hashUserPassword($newPassword);

                    $updateStmt = $db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                    $updateStmt->execute([$newHash, $userId]);

                    setFlashMessage('success', 'Your password was changed successfully.');
                    header('Location: profile.php');
                    exit;
                } else {
                    $error = 'The current password you entered is incorrect.';
                }
            } catch (PDOException $e) {
                error_log("Password update error: " . $e->getMessage());
                $error = 'Database error updating password.';
            }
        }
    }
}

$pageTitle = 'Change Password';
$activeNav = 'profile';
$extraCss = 'dashboard.css';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-white fw-bold mb-0">
                    <i class="fa-solid fa-key text-cyan me-2"></i>Security Credentials
                </h1>
                <a href="profile.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Profile
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2 small mb-3">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="cyber-card p-4 p-md-5">
                <form action="change-password.php" method="POST">
                    <?= csrfField() ?>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-semibold">Current Password</label>
                        <input type="password" name="current_password" class="form-control bg-dark text-white border-secondary border-opacity-50" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-semibold">New Password (min 8 characters)</label>
                        <input type="password" name="new_password" class="form-control bg-dark text-white border-secondary border-opacity-50" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-semibold">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control bg-dark text-white border-secondary border-opacity-50" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-cyan">
                            <i class="fa-solid fa-shield-check me-1"></i> Update Account Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
