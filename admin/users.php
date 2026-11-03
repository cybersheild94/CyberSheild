<?php
/**
 * CYBER SHIELD - Admin User Management
 * File: admin/users.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin-auth.php';
requireAdmin();

$currentAdmin = getCurrentUser();
$db = getDBConnection();
$error = null;

// Handle Actions (Activate, Deactivate, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (validateCSRFToken($csrf)) {
        $action = $_POST['action'] ?? '';
        $targetUserId = (int)($_POST['user_id'] ?? 0);

        if ($targetUserId > 0) {
            // Prevent deleting or deactivating oneself!
            if ($targetUserId === $currentAdmin['id']) {
                setFlashMessage('danger', 'Security Restriction: You cannot deactivate or delete your own administrator session.');
                header('Location: users.php');
                exit;
            }

            if ($action === 'activate') {
                $stmt = $db->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                $stmt->execute([$targetUserId]);
                setFlashMessage('success', 'User account activated successfully.');
            } elseif ($action === 'deactivate') {
                $stmt = $db->prepare("UPDATE users SET status = 'suspended' WHERE id = ?");
                $stmt->execute([$targetUserId]);
                setFlashMessage('warning', 'User account suspended.');
            } elseif ($action === 'delete') {
                $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$targetUserId]);
                setFlashMessage('success', 'User account permanently deleted.');
            }
            header('Location: users.php');
            exit;
        }
    }
}

// Search & Role Filter
$search = trim($_GET['q'] ?? '');
$roleFilter = trim($_GET['role'] ?? '');

$conditions = ['1=1'];
$params = [];

if (!empty($search)) {
    $conditions[] = '(name LIKE :s OR email LIKE :s OR phone LIKE :s)';
    $params[':s'] = '%' . $search . '%';
}

if (!empty($roleFilter) && in_array($roleFilter, ['user', 'admin'], true)) {
    $conditions[] = 'role = :role';
    $params[':role'] = $roleFilter;
}

$where = implode(' AND ', $conditions);

$stmt = $db->prepare("SELECT * FROM users WHERE {$where} ORDER BY created_at DESC");
$stmt->execute($params);
$users = $stmt->fetchAll();

$pageTitle = 'Manage System Users';
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
                <h2 class="text-white fw-bold mb-1">User Account Management</h2>
                <p class="text-secondary small mb-0">Inspect user accounts, assign privilege levels, and manage account statuses.</p>
            </div>
            <span class="badge bg-dark border border-cyan text-cyan">Total: <?= count($users) ?> Accounts</span>
        </div>

        <!-- Filter Bar -->
        <div class="cyber-card p-3 mb-4">
            <form method="GET" action="users.php" class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="q" class="form-control bg-dark text-white border-secondary border-opacity-50" placeholder="Search by name, email, or phone..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select bg-dark text-white border-secondary border-opacity-50">
                        <option value="">-- All Roles --</option>
                        <option value="user" <?= $roleFilter === 'user' ? 'selected' : '' ?>>Users Only</option>
                        <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Administrators Only</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-cyan w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                    <?php if (!empty($search) || !empty($roleFilter)): ?>
                        <a href="users.php" class="btn btn-outline-secondary"><i class="fa-solid fa-xmark"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="cyber-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-cyber mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email Address</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="font-monospace small text-secondary">#<?= $u['id'] ?></td>
                                <td class="text-white fw-semibold"><?= htmlspecialchars($u['name']) ?></td>
                                <td class="font-monospace small text-cyan"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="small text-secondary"><?= htmlspecialchars($u['phone'] ?: 'N/A') ?></td>
                                <td>
                                    <span class="badge <?= $u['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?> text-uppercase small">
                                        <?= htmlspecialchars($u['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($u['status'] === 'active'): ?>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success small">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger small">Suspended</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary small"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                <td class="text-secondary small"><?= $u['last_login'] ? date('M d, H:i', strtotime($u['last_login'])) : 'Never' ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <a href="user-details.php?id=<?= $u['id'] ?>" class="btn btn-outline-info btn-sm py-0 px-2" title="User Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <?php if ($u['id'] !== $currentAdmin['id']): ?>
                                            <?php if ($u['status'] === 'active'): ?>
                                                <form action="users.php" method="POST" class="d-inline" onsubmit="return confirm('Suspend user access?');">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="action" value="deactivate">
                                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="btn btn-outline-warning btn-sm py-0 px-2" title="Suspend User">
                                                        <i class="fa-solid fa-user-slash"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form action="users.php" method="POST" class="d-inline">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="action" value="activate">
                                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="btn btn-outline-success btn-sm py-0 px-2" title="Activate User">
                                                        <i class="fa-solid fa-user-check"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <form action="users.php" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this user and decouple their scans?');">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Delete User">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
