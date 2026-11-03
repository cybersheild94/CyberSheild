<?php
/**
 * CYBER SHIELD - Comprehensive Scan Forensic Details
 * File: scan-details.php
 * BCA Final Year Project
 */

declare(strict_types=1);

$pageTitle = 'Forensic Scan Report';
$activeNav = 'checker';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$scanId = (int)($_GET['id'] ?? 0);
if ($scanId <= 0) {
    setFlashMessage('warning', 'Invalid scan identifier specified.');
    header('Location: ' . BASE_URL . '/checker.php');
    exit;
}

$db = getDBConnection();
$currentUser = getCurrentUser();

$stmt = $db->prepare("
    SELECT s.*, u.name AS user_name, u.email AS user_email
    FROM scans s
    LEFT JOIN users u ON s.user_id = u.id
    WHERE s.id = ?
");
$stmt->execute([$scanId]);
$scan = $stmt->fetch();

if (!$scan) {
    echo '<div class="container py-5"><div class="alert alert-danger">Forensic record not found in Cyber Shield database.</div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Authorization check: User can only view their own scan unless they are admin
if ($scan['user_id'] && (!$currentUser || ($currentUser['id'] != $scan['user_id'] && !isAdmin()))) {
    echo '<div class="container py-5"><div class="alert alert-danger"><i class="fa-solid fa-lock me-2"></i>Access Denied: You do not have permission to view this forensic scan report.</div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$reasonsList = array_filter(array_map('trim', explode('|', $scan['reasons'] ?? '')));
$analysisData = json_decode($scan['analysis_data'] ?? '{}', true);
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-cyan text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="checker.php" class="text-cyan text-decoration-none">Scanner</a></li>
            <li class="breadcrumb-item active text-secondary" aria-current="page">Forensic Record #SEC-<?= $scan['id'] ?></li>
        </ol>
    </nav>

    <!-- Header Banner -->
    <div class="cyber-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-dark border border-cyan text-cyan mb-2 font-monospace">REPORT #SEC-<?= str_pad((string)$scan['id'], 6, '0', STR_PAD_LEFT) ?></span>
                <h2 class="text-white fw-bold mb-1">Deep Forensic URL Inspection</h2>
                <div class="text-secondary small">
                    <span><i class="fa-solid fa-calendar me-1"></i> <?= date('F j, Y - H:i:s T', strtotime($scan['created_at'])) ?></span>
                    <span class="ms-3"><i class="fa-solid fa-location-dot me-1"></i> Scan Client IP: <?= htmlspecialchars($scan['ip_address'] ?? 'N/A') ?></span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="small text-secondary text-uppercase fw-bold">Threat Score</div>
                    <div class="fs-2 fw-bold <?= $scan['risk_score'] > 59 ? 'text-danger' : ($scan['risk_score'] > 29 ? 'text-warning' : 'text-success') ?>">
                        <?= (int)$scan['risk_score'] ?>/100
                    </div>
                </div>
                <div><?= renderResultBadge($scan['result']) ?></div>
            </div>
        </div>

        <div class="mt-4 p-3 bg-dark bg-opacity-75 rounded border border-secondary border-opacity-25 font-monospace text-break">
            <span class="text-secondary small d-block mb-1">TARGET URL INSPECTED:</span>
            <span class="text-cyan fs-5"><?= htmlspecialchars($scan['url'], ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>

    <!-- Multi-column Heuristic Breakdown -->
    <div class="row g-4 mb-4">
        <!-- Structural Vector Grid -->
        <div class="col-lg-8">
            <div class="cyber-card h-100 p-4">
                <h5 class="text-white fw-bold mb-3 d-flex align-items-center">
                    <i class="fa-solid fa-layer-group text-cyan me-2"></i>
                    Structural & Lexical Characteristics
                </h5>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 bg-dark bg-opacity-50 rounded border border-secondary border-opacity-25">
                            <span class="text-secondary small d-block">Domain / Host</span>
                            <strong class="text-white font-monospace"><?= htmlspecialchars($scan['domain']) ?></strong>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-dark bg-opacity-50 rounded border border-secondary border-opacity-25">
                            <span class="text-secondary small d-block">Protocol Transport</span>
                            <strong class="<?= $scan['has_https'] ? 'text-success' : 'text-danger' ?>">
                                <i class="fa-solid <?= $scan['has_https'] ? 'fa-lock' : 'fa-lock-open' ?> me-1"></i>
                                <?= strtoupper($scan['protocol']) ?> (<?= $scan['has_https'] ? 'Secure SSL' : 'Insecure HTTP' ?>)
                            </strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-dark bg-opacity-50 rounded border border-secondary border-opacity-25">
                            <span class="text-secondary small d-block">Host Resolution Type</span>
                            <strong class="<?= $scan['has_ip'] ? 'text-danger' : 'text-info' ?>">
                                <?= $scan['has_ip'] ? 'Raw IP Address (Hostile)' : 'Standard Hostname' ?>
                            </strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-dark bg-opacity-50 rounded border border-secondary border-opacity-25">
                            <span class="text-secondary small d-block">Total URL Length</span>
                            <strong class="text-white font-monospace"><?= (int)$scan['url_length'] ?> characters</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-dark bg-opacity-50 rounded border border-secondary border-opacity-25">
                            <span class="text-secondary small d-block">Subdomain Hierarchy</span>
                            <strong class="<?= $scan['subdomain_count'] >= 2 ? 'text-warning' : 'text-white' ?>">
                                <?= (int)$scan['subdomain_count'] ?> subdomains
                            </strong>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-dark bg-opacity-50 rounded border border-secondary border-opacity-25">
                            <span class="text-secondary small d-block">Suspicious Keywords Detected</span>
                            <strong class="<?= !empty($scan['suspicious_keywords']) ? 'text-danger' : 'text-secondary' ?>">
                                <?= !empty($scan['suspicious_keywords']) ? htmlspecialchars($scan['suspicious_keywords']) : 'None detected' ?>
                            </strong>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-dark bg-opacity-50 rounded border border-secondary border-opacity-25">
                            <span class="text-secondary small d-block">Obfuscation Characters</span>
                            <strong class="text-white font-monospace"><?= (int)$scan['special_character_count'] ?> special symbols</strong>
                        </div>
                    </div>
                </div>

                <!-- Analysis Reasons -->
                <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                    <h6 class="text-cyan fw-bold small text-uppercase mb-3">
                        <i class="fa-solid fa-list-check me-1"></i> Specific Heuristic Flags Triggered:
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <?php if (empty($reasonsList)): ?>
                            <li class="text-secondary small">No adverse risk indicators identified for this target.</li>
                        <?php else: ?>
                            <?php foreach ($reasonsList as $reason): ?>
                                <li class="d-flex align-items-start gap-2 mb-2">
                                    <i class="fa-solid <?= $scan['result'] === 'SAFE' ? 'fa-check text-success' : 'fa-triangle-exclamation text-warning' ?> mt-1"></i>
                                    <span class="text-slate-200 small"><?= htmlspecialchars($reason) ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Metadata & User Identity Column -->
        <div class="col-lg-4">
            <div class="cyber-card h-100 p-4 d-flex flex-column justify-content-between">
                <div>
                    <h5 class="text-white fw-bold mb-3 d-flex align-items-center">
                        <i class="fa-solid fa-fingerprint text-cyan me-2"></i>
                        Audit Trail Information
                    </h5>

                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-3">
                            <span class="d-block text-white fw-semibold">Scanned By:</span>
                            <span><?= htmlspecialchars($scan['user_name'] ?? 'Public / Guest') ?></span>
                            <?php if (!empty($scan['user_email'])): ?>
                                <span class="d-block text-secondary small">(<?= htmlspecialchars($scan['user_email']) ?>)</span>
                            <?php endif; ?>
                        </li>
                        <li class="mb-3">
                            <span class="d-block text-white fw-semibold">Detection Engine:</span>
                            <span class="font-monospace text-cyan"><?= htmlspecialchars($analysisData['engine'] ?? 'CyberShield-Heuristics-v2') ?></span>
                        </li>
                        <li class="mb-3">
                            <span class="d-block text-white fw-semibold">Database Persistence:</span>
                            <span class="text-success"><i class="fa-solid fa-database me-1"></i> MySQL Table `scans` (Row ID: <?= $scan['id'] ?>)</span>
                        </li>
                    </ul>
                </div>

                <div class="pt-3 border-top border-secondary border-opacity-25">
                    <a href="checker.php" class="btn btn-outline-cyan w-100 btn-sm mb-2">
                        <i class="fa-solid fa-plus me-1"></i> Scan Another URL
                    </a>
                    <?php if (isLoggedIn()): ?>
                        <a href="user/scans.php" class="btn btn-dark w-100 btn-sm">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back to Scan History
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
