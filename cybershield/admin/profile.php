<?php
/**
 * CYBER SHIELD - Administrator Profile Management
 * File: admin/profile.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin-auth.php';
requireAdmin();

$currentAdmin = getCurrentUser();
$adminId = $currentAdmin['id'];
$db = getDBConnection();
$error = null;

// Fetch admin data
$stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (validateCSRFToken($csrf)) {
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeEmail($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $newPass = (string)($_POST['new_password'] ?? '');

        if (empty($name) || !$email) {
            $error = 'Name and email are mandatory.';
        } else {
            try {
                if (!empty($newPass)) {
                    if (strlen($newPass) < 8) {
                        $error = 'New password must contain at least 8 characters.';
                    } else {
                        $newHash = hashUserPassword($newPass);
                        $upStmt = $db->prepare("UPDATE users SET name = ?, email = ?, phone = ?, password = ?, updated_at = NOW() WHERE id = ?");
                        $upStmt->execute([$name, $email, $phone, $newHash, $adminId]);
                    }
                } else {
                    $upStmt = $db->prepare("UPDATE users SET name = ?, email = ?, phone = ?, updated_at = NOW() WHERE id = ?");
                    $upStmt->execute([$name, $email, $phone, $adminId]);
                }

                if (!$error) {
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    setFlashMessage('success', 'Admin credentials updated successfully.');
                    header('Location: profile.php');
                    exit;
                }
            } catch (PDOException $e) {
                error_log("Admin profile update error: " . $e->getMessage());
                $error = 'Database error updating admin profile.';
            }
        }
    }
}

$pageTitle = 'SOC Admin Profile';
$activeNav = 'admin';
$extraCss = 'admin.css';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <ul class="admin-sidebar-nav">
            <li><a href="dashboard.php" class="admin-sidebar-link"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
            <li><a href="users.php" class="admin-sidebar-link"><i class="fa-solid fa-users"></i> Manage Users</a></li>
            <li><a href="scans.php" class="admin-sidebar-link"><i class="fa-solid fa-radar"></i> Manage URL Scans</a></li>
            <li><a href="feedback.php" class="admin-sidebar-link"><i class="fa-solid fa-inbox"></i> Feedback</a></li>
            <li><a href="settings.php" class="admin-sidebar-link"><i class="fa-solid fa-sliders"></i> System Settings</a></li>
            <li><a href="profile.php" class="admin-sidebar-link active"><i class="fa-solid fa-user-shield"></i> Admin Profile</a></li>
            <li class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                <a href="logout.php" class="admin-sidebar-link text-danger"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
            </li>
        </ul>
    </aside>

    <main class="admin-main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-white fw-bold mb-1">Administrator Profile & Credentials</h2>
                <p class="text-secondary small mb-0">Manage root SOC supervisor credentials and security password.</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small mb-3">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-7">
                <div class="cyber-card p-4">
                    <form action="profile.php" method="POST">
                        <?= csrfField() ?>

                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-semibold">Administrator Name</label>
                            <input type="text" name="name" class="form-control bg-dark text-white border-secondary border-opacity-50" value="<?= htmlspecialchars($admin['name']) ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control bg-dark text-white border-secondary border-opacity-50" value="<?= htmlspecialchars($admin['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-semibold">Contact Phone</label>
                                <input type="tel" name="phone" class="form-control bg-dark text-white border-secondary border-opacity-50" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>">
                            </div>
                        </div>

                        <hr class="border-secondary border-opacity-25 my-4">

                        <h6 class="text-danger fw-bold small text-uppercase mb-3">
                            <i class="fa-solid fa-key me-1"></i> Change Administrator Password
                        </h6>

                        <div class="mb-4">
                            <label class="form-label text-secondary small fw-semibold">New Password (Leave blank to keep current)</label>
                            <input type="password" name="new_password" class="form-control bg-dark text-white border-secondary border-opacity-50" placeholder="Minimum 8 characters">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="fa-solid fa-shield-check me-1"></i> Update Admin Credentials
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
