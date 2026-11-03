<?php
/**
 * CYBER SHIELD - User Profile Management
 * File: user/profile.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/user-auth.php';
requireUser();

$currentUser = getCurrentUser();
$userId = $currentUser['id'];
$db = getDBConnection();
$error = null;
$success = null;

// Fetch latest user details from DB
$userStmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

if (!$user) {
    die("User record not found.");
}

// Fetch scan count
$countStmt = $db->prepare("SELECT COUNT(*) AS total FROM scans WHERE user_id = ?");
$countStmt->execute([$userId]);
$totalScans = (int)($countStmt->fetch()['total'] ?? 0);

// Handle Profile Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($token)) {
        $error = 'Security session validation failed.';
    } else {
        $name = sanitizeInput($_POST['name'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $email = sanitizeEmail($_POST['email'] ?? '');

        if (empty($name) || !$email) {
            $error = 'Name and email are required fields.';
        } else {
            // Check email uniqueness if modified
            if ($email !== $user['email']) {
                $emailCheck = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
                $emailCheck->execute([$email, $userId]);
                if ($emailCheck->fetch()) {
                    $error = 'The requested email address is already in use by another account.';
                }
            }

            if (!$error) {
                // Profile image upload handling (optional)
                $profileImage = $user['profile_image'];
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    $fileTmp = $_FILES['profile_image']['tmp_name'];
                    $fileSize = $_FILES['profile_image']['size'];
                    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($fileInfo, $fileTmp);
                    finfo_close($fileInfo);

                    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
                    if (in_array($mimeType, $allowedMimes, true) && $fileSize < 2 * 1024 * 1024) {
                        $ext = match($mimeType) {
                            'image/png'  => 'png',
                            'image/webp' => 'webp',
                            default      => 'jpg'
                        };
                        $newFilename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                        $uploadDir = __DIR__ . '/../assets/images/uploads/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        if (move_uploaded_file($fileTmp, $uploadDir . $newFilename)) {
                            $profileImage = $newFilename;
                        }
                    } else {
                        $error = 'Invalid image file. Max 2MB JPG, PNG, or WebP allowed.';
                    }
                }

                if (!$error) {
                    try {
                        $updateStmt = $db->prepare("
                            UPDATE users 
                            SET name = ?, phone = ?, email = ?, profile_image = ?, updated_at = NOW() 
                            WHERE id = ?
                        ");
                        $updateStmt->execute([$name, $phone, $email, $profileImage, $userId]);

                        // Update session variables
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_avatar'] = $profileImage;

                        setFlashMessage('success', 'Your profile details have been successfully updated.');
                        header('Location: profile.php');
                        exit;
                    } catch (PDOException $e) {
                        error_log("Profile update error: " . $e->getMessage());
                        $error = 'Database error while saving profile updates.';
                    }
                }
            }
        }
    }
}

$pageTitle = 'My Profile';
$activeNav = 'profile';
$extraCss = 'dashboard.css';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-white fw-bold mb-0">
                    <i class="fa-solid fa-user-gear text-cyan me-2"></i>Account Profile
                </h1>
                <a href="change-password.php" class="btn btn-outline-cyan btn-sm">
                    <i class="fa-solid fa-key me-1"></i> Change Password
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2 small mb-3">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="cyber-card p-4 p-md-5 mb-4">
                <div class="row align-items-center mb-4 pb-4 border-bottom border-secondary border-opacity-25 gy-3">
                    <div class="col-sm-auto text-center">
                        <div class="brand-shield-icon mx-auto" style="width: 72px; height: 72px; font-size: 2rem;">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                    </div>
                    <div class="col-sm">
                        <h3 class="text-white fw-bold mb-1"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <div class="text-secondary small">
                            <span class="badge bg-cyan text-dark me-2 text-uppercase"><?= htmlspecialchars($user['role']) ?></span>
                            <span>Registered on <?= date('F d, Y', strtotime($user['created_at'])) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Account Stats Grid -->
                <div class="row g-3 mb-4 text-center">
                    <div class="col-4">
                        <div class="p-3 bg-dark bg-opacity-50 rounded border border-secondary border-opacity-25">
                            <div class="text-secondary small">Total Scans</div>
                            <div class="fs-4 fw-bold text-cyan"><?= $totalScans ?></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-dark bg-opacity-50 rounded border border-secondary border-opacity-25">
                            <div class="text-secondary small">Last Login</div>
                            <div class="small fw-semibold text-white mt-1">
                                <?= $user['last_login'] ? date('M d, H:i', strtotime($user['last_login'])) : 'First Session' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-dark bg-opacity-50 rounded border border-secondary border-opacity-25">
                            <div class="text-secondary small">Account Status</div>
                            <div class="text-success fw-bold text-capitalize mt-1">
                                <i class="fa-solid fa-circle-check me-1"></i><?= htmlspecialchars($user['status']) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Edit Form -->
                <form action="profile.php" method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-semibold">Full Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            class="form-control bg-dark text-white border-secondary border-opacity-50" 
                            value="<?= htmlspecialchars($user['name']) ?>" 
                            required
                        >
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-semibold">Email Address</label>
                            <input 
                                type="email" 
                                name="email" 
                                class="form-control bg-dark text-white border-secondary border-opacity-50" 
                                value="<?= htmlspecialchars($user['email']) ?>" 
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-semibold">Phone Number</label>
                            <input 
                                type="tel" 
                                name="phone" 
                                class="form-control bg-dark text-white border-secondary border-opacity-50" 
                                value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                            >
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-semibold">Optional Profile Avatar</label>
                        <input type="file" name="profile_image" class="form-control bg-dark text-white border-secondary border-opacity-50" accept="image/png, image/jpeg, image/webp">
                        <span class="text-secondary small">Accepted formats: JPG, PNG, WebP (Max 2MB)</span>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-cyan px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
