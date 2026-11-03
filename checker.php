<?php
/**
 * CYBER SHIELD - Main Interactive URL Scanner
 * File: checker.php
 * BCA Final Year Project
 */

declare(strict_types=1);

$pageTitle = 'Live URL Phishing Scanner';
$activeNav = 'checker';
$extraJs = 'checker.js';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$isAuth = isLoggedIn();
$currentUser = getCurrentUser();
?>

<div class="container py-5">
    <div class="text-center mb-4">
        <div class="hero-badge">
            <i class="fa-solid fa-radar text-cyan"></i>
            <span>REAL-TIME THREAT RADAR</span>
        </div>
        <h1 class="text-white fw-bold">Intelligent Phishing URL Checker</h1>
        <p class="text-secondary max-w-xl mx-auto">
            Input any suspicious link, shortlink, or IP address to run our multi-vector heuristic phishing classification engine.
        </p>
    </div>

    <?php if (!$isAuth): ?>
        <!-- Guest State: Must login / signup prompt -->
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="cyber-card text-center p-5 border-cyan">
                    <div class="auth-icon-badge">
                        <i class="fa-solid fa-user-lock"></i>
                    </div>
                    <h3 class="text-white fw-bold mb-2">Account Required</h3>
                    <p class="text-secondary mb-4">
                        Please login or create an account to use the URL scanner. All your scan analyses are saved to your private forensic dashboard.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="login.php" class="btn btn-cyan px-4">
                            <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Login
                        </a>
                        <a href="signup.php" class="btn btn-outline-cyan px-4">
                            <i class="fa-solid fa-user-plus me-1"></i> Create Free Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Authenticated Scanner Workspace -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="cyber-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary small">
                            <i class="fa-solid fa-user-shield text-cyan me-1"></i> Authenticated as 
                            <strong class="text-white"><?= htmlspecialchars($currentUser['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </span>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success small">
                            <i class="fa-solid fa-circle-check me-1"></i> Engine Ready
                        </span>
                    </div>

                    <form id="urlScanForm" class="scanner-container">
                        <?= csrfField() ?>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0 text-cyan ps-3">
                                <i class="fa-solid fa-shield-virus"></i>
                            </span>
                            <input 
                                type="text" 
                                id="scanUrlInput" 
                                name="url" 
                                class="form-control scanner-input" 
                                placeholder="Paste suspicious URL (e.g., https://apple-id-verify.support-portal.click/signin)..." 
                                required
                            >
                            <button type="submit" id="scanSubmitBtn" class="btn btn-cyan px-4">
                                <span id="scanSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                                <span id="scanBtnText"><i class="fa-solid fa-magnifying-glass me-1"></i> Analyze URL</span>
                            </button>
                        </div>
                    </form>

                    <!-- Quick Sample URL Test Shortcuts for Viva demonstration -->
                    <div class="mt-3 pt-2 d-flex flex-wrap align-items-center gap-2 small text-secondary">
                        <span class="me-1"><i class="fa-solid fa-flask text-warning"></i> Demo Test Samples:</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="document.getElementById('scanUrlInput').value='https://google.com';">
                            Safe: Google
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="document.getElementById('scanUrlInput').value='http://192.168.0.1/login.php?update_credentials=true';">
                            Phishing: Raw IP
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="document.getElementById('scanUrlInput').value='https://login.paypal.verify-auth.account.xyz/signin';">
                            Phishing: Spoof Subdomain
                        </button>
                    </div>
                </div>

                <!-- Live Results Area -->
                <div id="scanResultContainer" class="d-none"></div>

                <!-- Recent Scan History for This User -->
                <div class="mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-white fw-bold mb-0">
                            <i class="fa-solid fa-clock-rotate-left text-cyan me-2"></i>My Recent Scans
                        </h5>
                        <a href="user/scans.php" class="btn btn-outline-cyan btn-sm">
                            View All Scans <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <?php
                    $db = getDBConnection();
                    $stmt = $db->prepare("SELECT * FROM scans WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
                    $stmt->execute([$currentUser['id']]);
                    $recentScans = $stmt->fetchAll();
                    ?>

                    <?php if (empty($recentScans)): ?>
                        <div class="cyber-card text-center py-4 text-secondary">
                            <i class="fa-solid fa-magnifying-glass-chart fs-3 mb-2 text-cyan"></i>
                            <p class="mb-0 small">You haven't scanned any URLs yet. Enter a URL above to perform your first scan!</p>
                        </div>
                    <?php else: ?>
                        <div class="cyber-card p-0 overflow-hidden">
                            <div class="table-responsive">
                                <table class="table table-cyber mb-0">
                                    <thead>
                                        <tr>
                                            <th>Target URL</th>
                                            <th>Score</th>
                                            <th>Classification</th>
                                            <th>Timestamp</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentScans as $sc): ?>
                                            <tr>
                                                <td class="font-monospace small text-truncate" style="max-width: 280px;">
                                                    <a href="scan-details.php?id=<?= $sc['id'] ?>" class="text-cyan text-decoration-none">
                                                        <?= htmlspecialchars($sc['url'], ENT_QUOTES, 'UTF-8') ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="fw-bold <?= $sc['risk_score'] > 59 ? 'text-danger' : ($sc['risk_score'] > 29 ? 'text-warning' : 'text-success') ?>">
                                                        <?= (int)$sc['risk_score'] ?>/100
                                                    </span>
                                                </td>
                                                <td><?= renderResultBadge($sc['result']) ?></td>
                                                <td class="text-secondary small"><?= date('M d, H:i', strtotime($sc['created_at'])) ?></td>
                                                <td>
                                                    <a href="scan-details.php?id=<?= $sc['id'] ?>" class="btn btn-outline-secondary btn-sm py-0 px-2">
                                                        <i class="fa-solid fa-eye"></i> Details
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
