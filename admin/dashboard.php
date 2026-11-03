<?php
/**
 * CYBER SHIELD - Administrator SOC Command Dashboard
 * File: admin/dashboard.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin-auth.php';
requireAdmin();

$currentUser = getCurrentUser();
$db = getDBConnection();

// Fetch Dynamic Admin Metrics from MySQL
$usersCount = (int)($db->query("SELECT COUNT(*) AS c FROM users WHERE role = 'user'")->fetch()['c'] ?? 0);
$totalScans = (int)($db->query("SELECT COUNT(*) AS c FROM scans")->fetch()['c'] ?? 0);
$safeScans = (int)($db->query("SELECT COUNT(*) AS c FROM scans WHERE result = 'SAFE'")->fetch()['c'] ?? 0);
$suspScans = (int)($db->query("SELECT COUNT(*) AS c FROM scans WHERE result = 'SUSPICIOUS'")->fetch()['c'] ?? 0);
$phishScans = (int)($db->query("SELECT COUNT(*) AS c FROM scans WHERE result = 'PHISHING'")->fetch()['c'] ?? 0);
$todayScans = (int)($db->query("SELECT COUNT(*) AS c FROM scans WHERE DATE(created_at) = CURDATE()")->fetch()['c'] ?? 0);
$unreadFeedback = (int)($db->query("SELECT COUNT(*) AS c FROM feedback WHERE status = 'unread'")->fetch()['c'] ?? 0);

// Dynamic 7-Day Scan Trend
$dailyTrendStmt = $db->query("
    SELECT DATE_FORMAT(created_at, '%b %d') AS scan_date, COUNT(*) AS count 
    FROM scans 
    GROUP BY DATE(created_at) 
    ORDER BY created_at DESC 
    LIMIT 7
");
$dailyRaw = $dailyTrendStmt->fetchAll();
$dailyRaw = array_reverse($dailyRaw);
$chartLabels = array_column($dailyRaw, 'scan_date');
$chartValues = array_column($dailyRaw, 'count');

// Dynamic Risk Score Distribution (0-29, 30-59, 60-79, 80-100)
$scoreBucketsStmt = $db->query("
    SELECT 
        SUM(CASE WHEN risk_score <= 29 THEN 1 ELSE 0 END) as b1,
        SUM(CASE WHEN risk_score BETWEEN 30 AND 59 THEN 1 ELSE 0 END) as b2,
        SUM(CASE WHEN risk_score BETWEEN 60 AND 79 THEN 1 ELSE 0 END) as b3,
        SUM(CASE WHEN risk_score >= 80 THEN 1 ELSE 0 END) as b4
    FROM scans
");
$buckets = $scoreBucketsStmt->fetch() ?: ['b1' => 0, 'b2' => 0, 'b3' => 0, 'b4' => 0];

$pageTitle = 'SOC Admin Command Dashboard';
$activeNav = 'admin';
$extraCss = 'admin.css';
$extraJs = 'dashboard.js';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="admin-layout">
    <!-- Admin Navigation Sidebar -->
    <aside class="admin-sidebar">
        <div class="px-2 mb-4">
            <span class="badge bg-danger text-white small text-uppercase font-monospace mb-1">SOC ROOT LEVEL</span>
            <div class="text-white fw-bold fs-6">Admin Control Unit</div>
        </div>

        <ul class="admin-sidebar-nav">
            <li>
                <a href="dashboard.php" class="admin-sidebar-link active">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="users.php" class="admin-sidebar-link">
                    <i class="fa-solid fa-users"></i> Manage Users
                </a>
            </li>
            <li>
                <a href="scans.php" class="admin-sidebar-link">
                    <i class="fa-solid fa-radar"></i> Manage URL Scans
                </a>
            </li>
            <li>
                <a href="feedback.php" class="admin-sidebar-link">
                    <i class="fa-solid fa-inbox"></i> Feedback
                    <?php if ($unreadFeedback > 0): ?>
                        <span class="admin-badge-count"><?= $unreadFeedback ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="settings.php" class="admin-sidebar-link">
                    <i class="fa-solid fa-sliders"></i> System Settings
                </a>
            </li>
            <li>
                <a href="profile.php" class="admin-sidebar-link">
                    <i class="fa-solid fa-user-shield"></i> Admin Profile
                </a>
            </li>
            <li class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                <a href="logout.php" class="admin-sidebar-link text-danger">
                    <i class="fa-solid fa-right-from-bracket"></i> Admin Sign Out
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Workspace -->
    <main class="admin-main-content">
        <!-- Top Toolbar -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="text-white fw-bold mb-1">Security Operations Center (SOC)</h2>
                <p class="text-secondary small mb-0">System telemetry, threat classification breakdown, and registered user oversight.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>/checker.php" class="btn btn-outline-cyan btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> Scan Target
                </a>
                <a href="scans.php" class="btn btn-outline-danger btn-sm">
                    <i class="fa-solid fa-file-waveform me-1"></i> Full Telemetry
                </a>
            </div>
        </div>

        <!-- 6 Dynamic Counters -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-2">
                <div class="cyber-card p-3 text-center">
                    <span class="text-secondary small text-uppercase">Total Users</span>
                    <div class="fs-3 fw-bold text-info mt-1"><?= number_format($usersCount) ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2">
                <div class="cyber-card p-3 text-center">
                    <span class="text-secondary small text-uppercase">Total Scans</span>
                    <div class="fs-3 fw-bold text-cyan mt-1"><?= number_format($totalScans) ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2">
                <div class="cyber-card p-3 text-center">
                    <span class="text-secondary small text-uppercase">Safe URLs</span>
                    <div class="fs-3 fw-bold text-success mt-1"><?= number_format($safeScans) ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2">
                <div class="cyber-card p-3 text-center">
                    <span class="text-secondary small text-uppercase">Suspicious</span>
                    <div class="fs-3 fw-bold text-warning mt-1"><?= number_format($suspScans) ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2">
                <div class="cyber-card p-3 text-center">
                    <span class="text-secondary small text-uppercase">Phishing Detected</span>
                    <div class="fs-3 fw-bold text-danger mt-1"><?= number_format($phishScans) ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2">
                <div class="cyber-card p-3 text-center">
                    <span class="text-secondary small text-uppercase">Today's Scans</span>
                    <div class="fs-3 fw-bold text-white mt-1"><?= number_format($todayScans) ?></div>
                </div>
            </div>
        </div>

        <!-- Multi-Chart Matrix -->
        <div class="row g-4 mb-4">
            <!-- Chart 1: Daily Scans (Line Chart) -->
            <div class="col-lg-8">
                <div class="cyber-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-white fw-bold mb-0">
                            <i class="fa-solid fa-chart-line text-cyan me-2"></i>Daily Scan Frequency (Last 7 Days)
                        </h5>
                        <span class="badge bg-dark border border-secondary text-secondary small">MySQL Dynamic Grouping</span>
                    </div>
                    <div style="height: 260px;">
                        <canvas 
                            id="adminDailyScansChart"
                            data-labels='<?= json_encode($chartLabels) ?>'
                            data-values='<?= json_encode($chartValues) ?>'
                        ></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart 2: Threat Classification (Doughnut) -->
            <div class="col-lg-4">
                <div class="cyber-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-white fw-bold mb-0">
                            <i class="fa-solid fa-chart-pie text-cyan me-2"></i>Threat Breakdown
                        </h5>
                        <span class="badge bg-dark border border-secondary text-secondary small">Global</span>
                    </div>
                    <div style="height: 260px;">
                        <canvas 
                            id="userScanDoughnutChart"
                            data-safe="<?= $safeScans ?>"
                            data-suspicious="<?= $suspScans ?>"
                            data-phishing="<?= $phishScans ?>"
                        ></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Audit Scans -->
        <div class="cyber-card p-0 overflow-hidden">
            <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                <h5 class="text-white fw-bold mb-0">
                    <i class="fa-solid fa-shield-virus text-cyan me-2"></i>Recent System Threat Interceptions
                </h5>
                <a href="scans.php" class="btn btn-outline-cyan btn-sm">
                    Manage All Scans <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>

            <?php
            $recentAuditStmt = $db->query("
                SELECT s.*, u.name as user_name 
                FROM scans s 
                LEFT JOIN users u ON s.user_id = u.id 
                ORDER BY s.created_at DESC 
                LIMIT 6
            ");
            $auditScans = $recentAuditStmt->fetchAll();
            ?>

            <div class="table-responsive">
                <table class="table table-cyber mb-0">
                    <thead>
                        <tr>
                            <th>Scan ID</th>
                            <th>User</th>
                            <th>Target URL</th>
                            <th>Domain</th>
                            <th>Score</th>
                            <th>Result</th>
                            <th>Timestamp</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($auditScans as $sc): ?>
                            <tr>
                                <td class="font-monospace text-secondary small">#<?= $sc['id'] ?></td>
                                <td class="small text-white"><?= htmlspecialchars($sc['user_name'] ?? 'Guest/Public') ?></td>
                                <td class="font-monospace small text-truncate" style="max-width: 220px;">
                                    <?= htmlspecialchars($sc['url']) ?>
                                </td>
                                <td class="font-monospace small text-cyan"><?= htmlspecialchars($sc['domain']) ?></td>
                                <td>
                                    <span class="fw-bold <?= $sc['risk_score'] > 59 ? 'text-danger' : ($sc['risk_score'] > 29 ? 'text-warning' : 'text-success') ?>">
                                        <?= (int)$sc['risk_score'] ?>/100
                                    </span>
                                </td>
                                <td><?= renderResultBadge($sc['result']) ?></td>
                                <td class="text-secondary small"><?= date('M d, H:i', strtotime($sc['created_at'])) ?></td>
                                <td>
                                    <a href="scan-details.php?id=<?= $sc['id'] ?>" class="btn btn-outline-secondary btn-sm py-0 px-2">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
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
