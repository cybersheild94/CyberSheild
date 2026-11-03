<?php
/**
 * CYBER SHIELD - Global Threat Intelligence / Scan History Information
 * File: history.php
 * BCA Final Year Project
 */

declare(strict_types=1);

$pageTitle = 'Threat Intelligence Feed';
$activeNav = 'history';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$db = getDBConnection();

// Fetch latest public / anonymous scans with masked URLs for safety
$stmt = $db->query("
    SELECT id, domain, protocol, risk_score, result, created_at, url_length, has_https
    FROM scans
    ORDER BY created_at DESC
    LIMIT 20
");
$recentScans = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <div class="hero-badge">
            <i class="fa-solid fa-satellite-dish text-cyan"></i>
            <span>COMMUNITY DEFENSE INTELLIGENCE</span>
        </div>
        <h1 class="text-white fw-bold">Live Phishing Threat Feed</h1>
        <p class="text-secondary max-w-xl mx-auto">
            Aggregated real-time telemetry from all Cyber Shield scans. Demonstrating persistent MySQL storage and heuristic scoring.
        </p>
    </div>

    <div class="cyber-card p-0 overflow-hidden">
        <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
            <h5 class="text-white fw-bold mb-0">
                <i class="fa-solid fa-list-check text-cyan me-2"></i>Latest Scanned Targets
            </h5>
            <span class="badge bg-cyan text-dark font-monospace fw-bold">LIVE TELEMETRY</span>
        </div>

        <div class="table-responsive">
            <table class="table table-cyber mb-0">
                <thead>
                    <tr>
                        <th>Scan ID</th>
                        <th>Target Domain</th>
                        <th>Protocol</th>
                        <th>Threat Score</th>
                        <th>Classification</th>
                        <th>Length</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentScans)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-secondary">
                                No scan history available yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentScans as $s): ?>
                            <tr>
                                <td class="font-monospace text-secondary small">#SEC-<?= str_pad((string)$s['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td class="font-monospace text-cyan fw-semibold"><?= htmlspecialchars($s['domain']) ?></td>
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
                                <td class="text-secondary small"><?= (int)$s['url_length'] ?> chars</td>
                                <td class="text-secondary small"><?= date('M d, H:i', strtotime($s['created_at'])) ?></td>
                                <td>
                                    <a href="scan-details.php?id=<?= $s['id'] ?>" class="btn btn-outline-secondary btn-sm py-0 px-2">
                                        <i class="fa-solid fa-eye"></i> View
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
