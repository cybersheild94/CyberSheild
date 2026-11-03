<?php
/**
 * CYBER SHIELD - Administrator Scan Telemetry Oversight
 * File: admin/scans.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin-auth.php';
requireAdmin();

$db = getDBConnection();

// Delete scan action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (validateCSRFToken($csrf) && ($_POST['action'] ?? '') === 'delete') {
        $delId = (int)($_POST['scan_id'] ?? 0);
        if ($delId > 0) {
            $stmt = $db->prepare("DELETE FROM scans WHERE id = ?");
            $stmt->execute([$delId]);
            setFlashMessage('success', "Forensic scan record #SEC-{$delId} permanently purged.");
            header('Location: scans.php');
            exit;
        }
    }
}

// Search and filter parameters
$search = trim($_GET['q'] ?? '');
$resultFilter = trim($_GET['result'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 15;
$offset = ($page - 1) * $limit;

$conditions = ['1=1'];
$params = [];

if (!empty($search)) {
    $conditions[] = '(s.url LIKE :s OR s.domain LIKE :s OR u.name LIKE :s)';
    $params[':s'] = '%' . $search . '%';
}

if (!empty($resultFilter) && in_array(strtoupper($resultFilter), ['SAFE', 'SUSPICIOUS', 'PHISHING'], true)) {
    $conditions[] = 's.result = :r';
    $params[':r'] = strtoupper($resultFilter);
}

$where = implode(' AND ', $conditions);

// Count
$countStmt = $db->prepare("
    SELECT COUNT(*) AS total 
    FROM scans s 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE {$where}
");
$countStmt->execute($params);
$totalScans = (int)($countStmt->fetch()['total'] ?? 0);
$totalPages = max(1, (int)ceil($totalScans / $limit));

// Data
$dataStmt = $db->prepare("
    SELECT s.*, u.name AS user_name, u.email AS user_email 
    FROM scans s 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE {$where} 
    ORDER BY s.created_at DESC 
    LIMIT {$limit} OFFSET {$offset}
");
$dataStmt->execute($params);
$scans = $dataStmt->fetchAll();

$pageTitle = 'Manage URL Scans Telemetry';
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
            <li><a href="scans.php" class="admin-sidebar-link active"><i class="fa-solid fa-radar"></i> Manage URL Scans</a></li>
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
                <h2 class="text-white fw-bold mb-1">URL Threat Scans Telemetry</h2>
                <p class="text-secondary small mb-0">Full persistent log of all heuristic phishing analyses performed across the system.</p>
            </div>
            <span class="badge bg-dark border border-cyan text-cyan">Total Recorded: <?= $totalScans ?></span>
        </div>

        <!-- Filter Bar -->
        <div class="cyber-card p-3 mb-4">
            <form method="GET" action="scans.php" class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="q" class="form-control bg-dark text-white border-secondary border-opacity-50" placeholder="Search by URL, domain, or user name..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <select name="result" class="form-select bg-dark text-white border-secondary border-opacity-50">
                        <option value="">-- All Classifications --</option>
                        <option value="SAFE" <?= $resultFilter === 'SAFE' ? 'selected' : '' ?>>SAFE</option>
                        <option value="SUSPICIOUS" <?= $resultFilter === 'SUSPICIOUS' ? 'selected' : '' ?>>SUSPICIOUS</option>
                        <option value="PHISHING" <?= $resultFilter === 'PHISHING' ? 'selected' : '' ?>>PHISHING</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-cyan w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                    <?php if (!empty($search) || !empty($resultFilter)): ?>
                        <a href="scans.php" class="btn btn-outline-secondary"><i class="fa-solid fa-xmark"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="cyber-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-cyber mb-0">
                    <thead>
                        <tr>
                            <th>Scan ID</th>
                            <th>Target User</th>
                            <th>Analyzed URL</th>
                            <th>Domain</th>
                            <th>Score</th>
                            <th>Verdict</th>
                            <th>Client IP</th>
                            <th>Scanned On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($scans)): ?>
                            <tr><td colspan="9" class="text-center py-5 text-secondary">No scan records matching your filter parameters.</td></tr>
                        <?php else: ?>
                            <?php foreach ($scans as $sc): ?>
                                <tr>
                                    <td class="font-monospace small text-secondary">#<?= $sc['id'] ?></td>
                                    <td class="small text-white">
                                        <?= htmlspecialchars($sc['user_name'] ?? 'Public Guest') ?>
                                    </td>
                                    <td class="font-monospace small text-truncate" style="max-width: 220px;">
                                        <a href="scan-details.php?id=<?= $sc['id'] ?>" class="text-cyan text-decoration-none">
                                            <?= htmlspecialchars($sc['url']) ?>
                                        </a>
                                    </td>
                                    <td class="font-monospace small text-white"><?= htmlspecialchars($sc['domain']) ?></td>
                                    <td>
                                        <span class="fw-bold <?= $sc['risk_score'] > 59 ? 'text-danger' : ($sc['risk_score'] > 29 ? 'text-warning' : 'text-success') ?>">
                                            <?= (int)$sc['risk_score'] ?>/100
                                        </span>
                                    </td>
                                    <td><?= renderResultBadge($sc['result']) ?></td>
                                    <td class="font-monospace small text-secondary"><?= htmlspecialchars($sc['ip_address'] ?? '127.0.0.1') ?></td>
                                    <td class="text-secondary small"><?= date('M d, H:i', strtotime($sc['created_at'])) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <a href="scan-details.php?id=<?= $sc['id'] ?>" class="btn btn-outline-cyan btn-sm py-0 px-2" title="Inspect">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <form action="scans.php" method="POST" class="d-inline" onsubmit="return confirm('Purge this forensic scan record?');">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="scan_id" value="<?= $sc['id'] ?>">
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

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="p-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                    <div class="text-secondary small">
                        Showing page <?= $page ?> of <?= $totalPages ?> (<?= $totalScans ?> records)
                    </div>
                    <ul class="pagination pagination-cyber mb-0">
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($search) ?>&result=<?= urlencode($resultFilter) ?>">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
