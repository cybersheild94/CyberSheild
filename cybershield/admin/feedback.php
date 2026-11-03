<?php
/**
 * CYBER SHIELD - Administrator Feedback & Support Management
 * File: admin/feedback.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin-auth.php';
requireAdmin();

$db = getDBConnection();

// Handle Actions (mark as read, mark as unread, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (validateCSRFToken($csrf)) {
        $action = $_POST['action'] ?? '';
        $fId = (int)($_POST['feedback_id'] ?? 0);

        if ($fId > 0) {
            if ($action === 'mark_read') {
                $stmt = $db->prepare("UPDATE feedback SET status = 'read' WHERE id = ?");
                $stmt->execute([$fId]);
                setFlashMessage('success', 'Feedback marked as read.');
            } elseif ($action === 'mark_unread') {
                $stmt = $db->prepare("UPDATE feedback SET status = 'unread' WHERE id = ?");
                $stmt->execute([$fId]);
                setFlashMessage('info', 'Feedback marked as unread.');
            } elseif ($action === 'delete') {
                $stmt = $db->prepare("DELETE FROM feedback WHERE id = ?");
                $stmt->execute([$fId]);
                setFlashMessage('success', 'Feedback message deleted.');
            }
            header('Location: feedback.php');
            exit;
        }
    }
}

// Fetch all feedback
$feedbackStmt = $db->query("
    SELECT f.*, u.name AS user_registered_name 
    FROM feedback f 
    LEFT JOIN users u ON f.user_id = u.id 
    ORDER BY f.created_at DESC
");
$feedbackItems = $feedbackStmt->fetchAll();

$pageTitle = 'Manage Feedback';
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
            <li><a href="feedback.php" class="admin-sidebar-link active"><i class="fa-solid fa-inbox"></i> Feedback</a></li>
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
                <h2 class="text-white fw-bold mb-1">User Feedback & Inquiries</h2>
                <p class="text-secondary small mb-0">Messages submitted via the public contact portal and false positive inquiries.</p>
            </div>
            <span class="badge bg-dark border border-cyan text-cyan">Inbox: <?= count($feedbackItems) ?></span>
        </div>

        <div class="cyber-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-cyber mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sender</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($feedbackItems)): ?>
                            <tr><td colspan="8" class="text-center py-5 text-secondary">No inquiries or feedback received.</td></tr>
                        <?php else: ?>
                            <?php foreach ($feedbackItems as $f): ?>
                                <tr>
                                    <td class="font-monospace small text-secondary">#<?= $f['id'] ?></td>
                                    <td class="text-white fw-semibold small"><?= htmlspecialchars($f['name']) ?></td>
                                    <td class="font-monospace small text-cyan"><?= htmlspecialchars($f['email']) ?></td>
                                    <td class="small text-white fw-medium"><?= htmlspecialchars($f['subject']) ?></td>
                                    <td class="small text-secondary" style="max-width: 260px;">
                                        <div class="text-truncate" title="<?= htmlspecialchars($f['message']) ?>">
                                            <?= htmlspecialchars($f['message']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($f['status'] === 'unread'): ?>
                                            <span class="badge bg-danger bg-opacity-25 text-danger border border-danger small">Unread</span>
                                        <?php else: ?>
                                            <span class="badge bg-success bg-opacity-25 text-success border border-success small">Read</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-secondary small"><?= date('M d, H:i', strtotime($f['created_at'])) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <?php if ($f['status'] === 'unread'): ?>
                                                <form action="feedback.php" method="POST" class="d-inline">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="action" value="mark_read">
                                                    <input type="hidden" name="feedback_id" value="<?= $f['id'] ?>">
                                                    <button type="submit" class="btn btn-outline-success btn-sm py-0 px-2" title="Mark as Read">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form action="feedback.php" method="POST" class="d-inline">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="action" value="mark_unread">
                                                    <input type="hidden" name="feedback_id" value="<?= $f['id'] ?>">
                                                    <button type="submit" class="btn btn-outline-secondary btn-sm py-0 px-2" title="Mark Unread">
                                                        <i class="fa-solid fa-envelope"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <form action="feedback.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this feedback?');">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="feedback_id" value="<?= $f['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
