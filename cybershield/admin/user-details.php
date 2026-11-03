<?php
/**
 * CYBER SHIELD - Admin User Detail Dossier
 * File: admin/user-details.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin-auth.php';
requireAdmin();

$userId = (int)($_GET['id'] ?? 0);
$db = getDBConnection();

$stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$userId]);
$targetUser = $stmt->fetch();

if (!$targetUser) {
    setFlashMessage('danger', 'User account not found.');
    header('Location: users.php');
    exit;
}

// User scans
$scansStmt = $db->prepare("SELECT * FROM scans WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
$scansStmt->execute([$userId]);
$userScans = $scansStmt->fetchAll();

$pageTitle = 'User Dossier: ' . $targetUser['name'];
$activeNav = 'admin';
$extraCss = 'admin.css';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <ul class="admin-sidebar-nav">
            <li><a href="dashboard.php" class="admin-sidebar-link"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
            <li><a href="users.php" class="admin-sidebar-link active"><i class="fa-solid fa-users"></i> Manage Users</a></li>
            <li><a href="scans.php" class="admin-sidebar-link"><i class="fa-solid fa-radar"></i> Manage URL Scans</a></li>
            <li><a href="feedback.php" class="admin-sidebar-link"><i class="fa-solid fa-inbox"></i> Feedback</a></li>
            <li><a href="settings.php" class="admin-sidebar-link"><i class="fa-solid fa-sliders"></i> System Settings</a></li>
            <li><a href="profile.php" class="admin-sidebar-link"><i class="fa-solid fa-user-shield"></i> Admin Profile</a></li>
            <li class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                <a href="logout.php" class="admin-sidebar-link text-danger"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
            </li>
        </ul>
    </aside>

    <main class="admin-main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="users.php" class="text-cyan text-decoration-none small"><i class="fa-solid fa-arrow-left me-1"></i> Back to Users</a>
                <h2 class="text-white fw-bold mb-0 mt-1">User Dossier #<?= $targetUser['id'] ?>: <?= htmlspecialchars($targetUser['name']) ?></h2>
            </div>
            <span class="badge bg-<?= $targetUser['status'] === 'active' ? 'success' : 'danger' ?> fs-6">
                <?= strtoupper($targetUser['status']) ?>
            </span>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="cyber-card p-4">
                    <h5 class="text-white fw-bold mb-3"><i class="fa-solid fa-id-card text-cyan me-2"></i>Account Details</h5>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2"><strong class="text-white">Email:</strong> <?= htmlspecialchars($targetUser['email']) ?></li>
                        <li class="mb-2"><strong class="text-white">Phone:</strong> <?= htmlspecialchars($targetUser['phone'] ?: 'N/A') ?></li>
                        <li class="mb-2"><strong class="text-white">Role:</strong> <span class="badge bg-primary text-uppercase"><?= htmlspecialchars($targetUser['role']) ?></span></li>
                        <li class="mb-2"><strong class="text-white">Created:</strong> <?= date('F j, Y - H:i', strtotime($targetUser['created_at'])) ?></li>
                        <li class="mb-2"><strong class="text-white">Last Login:</strong> <?= $targetUser['last_login'] ? date('F j, Y - H:i', strtotime($targetUser['last_login'])) : 'Never' ?></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="cyber-card p-0 overflow-hidden">
                    <div class="p-3 border-bottom border-secondary border-opacity-25">
                        <h5 class="text-white fw-bold mb-0"><i class="fa-solid fa-radar text-cyan me-2"></i>User Scans Log (<?= count($userScans) ?> records)</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-cyber mb-0">
                            <thead>
                                <tr>
                                    <th>URL</th>
                                    <th>Score</th>
                                    <th>Result</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($userScans)): ?>
                                    <tr><td colspan="5" class="text-center py-3 text-secondary">No scans recorded for this account.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($userScans as $sc): ?>
                                        <tr>
                                            <td class="font-monospace small text-truncate" style="max-width: 250px;"><?= htmlspecialchars($sc['url']) ?></td>
                                            <td class="fw-bold"><?= (int)$sc['risk_score'] ?>/100</td>
                                            <td><?= renderResultBadge($sc['result']) ?></td>
                                            <td class="small text-secondary"><?= date('M d, H:i', strtotime($sc['created_at'])) ?></td>
                                            <td><a href="scan-details.php?id=<?= $sc['id'] ?>" class="btn btn-outline-cyan btn-sm py-0 px-2">Inspect</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
