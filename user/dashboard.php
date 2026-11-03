<?php
/**
 * CYBER SHIELD - User Security Dashboard
 * File: user/dashboard.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/user-auth.php';
requireUser();

$currentUser = getCurrentUser();
$db = getDBConnection();

// Fetch metrics specifically for this user
$userId = $currentUser['id'];

$totalScansStmt = $db->prepare("SELECT COUNT(*) AS total FROM scans WHERE user_id = ?");
$totalScansStmt->execute([$userId]);
$totalScans = (int)($totalScansStmt->fetch()['total'] ?? 0);

$safeStmt = $db->prepare("SELECT COUNT(*) AS count FROM scans WHERE user_id = ? AND result = 'SAFE'");
$safeStmt->execute([$userId]);
$safeCount = (int)($safeStmt->fetch()['count'] ?? 0);

$suspStmt = $db->prepare("SELECT COUNT(*) AS count FROM scans WHERE user_id = ? AND result = 'SUSPICIOUS'");
$suspStmt->execute([$userId]);
$suspCount = (int)($suspStmt->fetch()['count'] ?? 0);

$phishStmt = $db->prepare("SELECT COUNT(*) AS count FROM scans WHERE user_id = ? AND result = 'PHISHING'");
$phishStmt->execute([$userId]);
$phishCount = (int)($phishStmt->fetch()['count'] ?? 0);

// Recent Scans
$recentStmt = $db->prepare("SELECT * FROM scans WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$recentStmt->execute([$userId]);
$recentScans = $recentStmt->fetchAll();

$pageTitle = 'My Security Dashboard';
$activeNav = 'dashboard';
$extraCss = 'dashboard.css';
$extraJs = 'dashboard.js';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4">
    <!-- Welcome Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <span class="badge bg-cyan text-dark font-monospace fw-bold mb-1">USER SEC-PORTAL</span>
            <h1 class="text-white fw-bold mb-1">Welcome, <?= htmlspecialchars($currentUser['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-secondary small mb-0">Overview of your personal URL scans, threat telemetry, and security activity.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/checker.php" class="btn btn-cyan">
                <i class="fa-solid fa-plus me-1"></i> Scan New URL
            </a>
            <a href="scans.php" class="btn btn-outline-cyan">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> Full History
            </a>
        </div>
    </div>

    <!-- Stat Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="user-stat-card">
                <div class="stat-icon stat-icon-cyan">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div>
                    <div class="stat-number text-cyan"><?= $totalScans ?></div>
                    <div class="stat-label">Total Scans</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="user-stat-card">
                <div class="stat-icon stat-icon-green">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
                <div>
                    <div class="stat-number text-success"><?= $safeCount ?></div>
                    <div class="stat-label">Safe URLs</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="user-stat-card">
                <div class="stat-icon stat-icon-amber">
                    <i class="fa-solid fa-shield-exclamation"></i>
                </div>
                <div>
                    <div class="stat-number text-warning"><?= $suspCount ?></div>
                    <div class="stat-label">Suspicious</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="user-stat-card">
                <div class="stat-icon stat-icon-red">
                    <i class="fa-solid fa-shield-virus"></i>
                </div>
                <div>
                    <div class="stat-number text-danger"><?= $phishCount ?></div>
                    <div class="stat-label">Phishing Threats</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart and Quick Scan Row -->
    <div class="row g-4 mb-4">
        <!-- Chart.js Doughnut: My Scan Analysis -->
        <div class="col-lg-5">
            <div class="cyber-card chart-card p-4">
                <h5 class="text-white fw-bold mb-3 d-flex align-items-center justify-content-between">
                    <span><i class="fa-solid fa-chart-pie text-cyan me-2"></i>Threat Classification</span>
                    <span class="badge bg-dark border border-secondary text-secondary small">Real-time</span>
                </h5>

                <div style="height: 250px; position: relative;">
                    <canvas 
                        id="userScanDoughnutChart"
                        data-safe="<?= $safeCount ?>"
                        data-suspicious="<?= $suspCount ?>"
                        data-phishing="<?= $phishCount ?>"
                    ></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Summary & Protection Advice -->
        <div class="col-lg-7">
            <div class="cyber-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <h5 class="text-white fw-bold mb-3">
                        <i class="fa-solid fa-bullhorn text-cyan me-2"></i>Proactive Cyber Protection Advice
                    </h5>
                    <div class="p-3 bg-dark bg-opacity-50 rounded border border-secondary border-opacity-25 mb-3">
                        <strong class="text-cyan d-block mb-1">
                            <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Recent Threat Pattern Identified
                        </strong>
                        <p class="text-secondary small mb-0">
                            Many malicious payloads are currently exploiting deceptive subdomains (e.g. <code>login.paypal.verify-account.click</code>). Cyber Shield flags any URL with 3+ subdomain levels as elevated risk.
                        </p>
                    </div>

                    <div class="row g-2 text-secondary small">
                        <div class="col-6">
                            <i class="fa-solid fa-check text-success me-1"></i> Scans logged to MySQL
                        </div>
                        <div class="col-6">
                            <i class="fa-solid fa-check text-success me-1"></i> Lexical heuristic audit
                        </div>
                        <div class="col-6">
                            <i class="fa-solid fa-check text-success me-1"></i> Brand spoofing alert
                        </div>
                        <div class="col-6">
                            <i class="fa-solid fa-check text-success me-1"></i> Exportable forensic record
                        </div>
                    </div>
                </div>

                <div class="pt-3 mt-3 border-top border-secondary border-opacity-25 d-flex gap-2">
                    <a href="<?= BASE_URL ?>/checker.php" class="btn btn-cyan btn-sm flex-fill">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Open Scanner
                    </a>
                    <a href="profile.php" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="fa-solid fa-user-gear me-1"></i> Account Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Scans Table -->
    <div class="cyber-card p-0 overflow-hidden">
        <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
            <h5 class="text-white fw-bold mb-0">
                <i class="fa-solid fa-clock-rotate-left text-cyan me-2"></i>My Recent URL Scans
            </h5>
            <a href="scans.php" class="btn btn-outline-cyan btn-sm">
                View All Scans <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-cyber mb-0">
                <thead>
                    <tr>
                        <th>URL</th>
                        <th>Host / Domain</th>
                        <th>Score</th>
                        <th>Result</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentScans)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary">
                                You haven't performed any scans yet.
                                <a href="<?= BASE_URL ?>/checker.php" class="text-cyan ms-1">Scan a URL now &rarr;</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentScans as $s): ?>
                            <tr>
                                <td class="font-monospace small text-truncate" style="max-width: 250px;">
                                    <a href="scan-details.php?id=<?= $s['id'] ?>" class="text-cyan text-decoration-none">
                                        <?= htmlspecialchars($s['url'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </td>
                                <td class="font-monospace small text-white"><?= htmlspecialchars($s['domain']) ?></td>
                                <td>
                                    <span class="fw-bold <?= $s['risk_score'] > 59 ? 'text-danger' : ($s['risk_score'] > 29 ? 'text-warning' : 'text-success') ?>">
                                        <?= (int)$s['risk_score'] ?>/100
                                    </span>
                                </td>
                                <td><?= renderResultBadge($s['result']) ?></td>
                                <td class="text-secondary small"><?= date('M d, Y - H:i', strtotime($s['created_at'])) ?></td>
                                <td>
                                    <a href="scan-details.php?id=<?= $s['id'] ?>" class="btn btn-outline-secondary btn-sm py-0 px-2">
                                        <i class="fa-solid fa-eye"></i> Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
