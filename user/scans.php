<?php
/**
 * CYBER SHIELD - User My Scan History
 * File: user/scans.php
 * Features: Search, Filter by Result, Pagination, Strict user_id scoping
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/user-auth.php';
requireUser();

$currentUser = getCurrentUser();
$userId = $currentUser['id'];
$db = getDBConnection();

// Filters & Pagination Parameters
$search = trim($_GET['q'] ?? '');
$filterResult = trim($_GET['result'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

// Build Query with PDO prepared parameters
$conditions = ['user_id = :user_id'];
$params = [':user_id' => $userId];

if (!empty($search)) {
    $conditions[] = '(url LIKE :search OR domain LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

if (!empty($filterResult) && in_array(strtoupper($filterResult), ['SAFE', 'SUSPICIOUS', 'PHISHING'], true)) {
    $conditions[] = 'result = :filter_result';
    $params[':filter_result'] = strtoupper($filterResult);
}

$whereClause = implode(' AND ', $conditions);

// Get Total Count for Pagination
$countStmt = $db->prepare("SELECT COUNT(*) AS total FROM scans WHERE {$whereClause}");
$countStmt->execute($params);
$totalRecords = (int)($countStmt->fetch()['total'] ?? 0);
$totalPages = max(1, (int)ceil($totalRecords / $limit));

// Fetch Paginated Records
$dataSql = "
    SELECT * FROM scans 
    WHERE {$whereClause} 
    ORDER BY created_at DESC 
    LIMIT {$limit} OFFSET {$offset}
";
$dataStmt = $db->prepare($dataSql);
$dataStmt->execute($params);
$scans = $dataStmt->fetchAll();

$pageTitle = 'My Scan History';
$activeNav = 'scans';
$extraCss = 'dashboard.css';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="text-white fw-bold mb-1"><i class="fa-solid fa-clock-rotate-left text-cyan me-2"></i>My Scan History</h1>
            <p class="text-secondary small mb-0">Browse and inspect all URLs analyzed by your account. Strictly isolated to your user ID.</p>
        </div>
        <a href="<?= BASE_URL ?>/checker.php" class="btn btn-cyan">
            <i class="fa-solid fa-plus me-1"></i> Scan New URL
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="cyber-card p-3 mb-4">
        <form method="GET" action="scans.php" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary border-opacity-50 text-cyan">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input 
                        type="text" 
                        name="q" 
                        class="form-control bg-dark text-white border-secondary border-opacity-50" 
                        placeholder="Search by URL or domain..."
                        value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            </div>

            <div class="col-md-4">
                <select name="result" class="form-select bg-dark text-white border-secondary border-opacity-50">
                    <option value="">-- All Threat Classifications --</option>
                    <option value="SAFE" <?= $filterResult === 'SAFE' ? 'selected' : '' ?>>SAFE URLs Only</option>
                    <option value="SUSPICIOUS" <?= $filterResult === 'SUSPICIOUS' ? 'selected' : '' ?>>SUSPICIOUS URLs Only</option>
                    <option value="PHISHING" <?= $filterResult === 'PHISHING' ? 'selected' : '' ?>>PHISHING Threats Only</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-cyan w-100">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                <?php if (!empty($search) || !empty($filterResult)): ?>
                    <a href="scans.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Scans Table -->
    <div class="cyber-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-cyber mb-0">
                <thead>
                    <tr>
                        <th>Scan ID</th>
                        <th>Target URL</th>
                        <th>Host Domain</th>
                        <th>Protocol</th>
                        <th>Score</th>
                        <th>Classification</th>
                        <th>Scanned On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($scans)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-secondary">
                                <i class="fa-solid fa-inbox fs-2 mb-2 text-cyan d-block"></i>
                                No scan records found matching your filters.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($scans as $s): ?>
                            <tr>
                                <td class="font-monospace text-secondary small">#SEC-<?= str_pad((string)$s['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td class="font-monospace small text-truncate" style="max-width: 250px;">
                                    <a href="scan-details.php?id=<?= $s['id'] ?>" class="text-cyan text-decoration-none">
                                        <?= htmlspecialchars($s['url'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </td>
                                <td class="font-monospace small text-white"><?= htmlspecialchars($s['domain']) ?></td>
                                <td>
                                    <span class="badge <?= $s['has_https'] ? 'bg-success bg-opacity-25 text-success' : 'bg-danger bg-opacity-25 text-danger' ?> small">
                                        <?= strtoupper($s['protocol']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold <?= $s['risk_score'] > 59 ? 'text-danger' : ($s['risk_score'] > 29 ? 'text-warning' : 'text-success') ?>">
                                        <?= (int)$s['risk_score'] ?>/100
                                    </span>
                                </td>
                                <td><?= renderResultBadge($s['result']) ?></td>
                                <td class="text-secondary small"><?= date('M d, Y H:i', strtotime($s['created_at'])) ?></td>
                                <td>
                                    <a href="scan-details.php?id=<?= $s['id'] ?>" class="btn btn-outline-cyan btn-sm py-0 px-2" title="Inspect Analysis">
                                        <i class="fa-solid fa-eye"></i> Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
            <div class="p-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                <div class="text-secondary small">
                    Showing <?= min($totalRecords, $offset + 1) ?> - <?= min($totalRecords, $offset + count($scans)) ?> of <?= $totalRecords ?> scans
                </div>
                <nav>
                    <ul class="pagination pagination-cyber mb-0">
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($search) ?>&result=<?= urlencode($filterResult) ?>">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
