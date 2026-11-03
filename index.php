<?php
/**
 * CYBER SHIELD - Public Home Landing Portal
 * File: index.php
 * BCA Final Year Project
 */

declare(strict_types=1);

$pageTitle = 'AI-Based Phishing URL Checker';
$activeNav = 'home';
$extraJs = 'checker.js';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$stats = getSystemStats();
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="hero-badge">
                    <i class="fa-solid fa-shield-virus"></i>
                    <span>BCA FINAL YEAR MAJOR PROJECT DEMO</span>
                </div>

                <h1 class="hero-title mb-3">
                    Stay One Step Ahead of <span class="hero-gradient-text">Phishing</span>
                </h1>

                <p class="hero-subtitle mx-auto mb-4">
                    Cyber Shield intelligently analyzes suspicious URLs and helps you identify potential phishing threats before they put your information at risk.
                </p>

                <!-- URL Scanner Box -->
                <div class="col-lg-9 mx-auto mb-4">
                    <form id="urlScanForm" class="scanner-container">
                        <?= csrfField() ?>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0 text-cyan ps-3">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input 
                                type="text" 
                                id="scanUrlInput" 
                                name="url" 
                                class="form-control scanner-input" 
                                placeholder="Enter a suspicious URL (e.g. http://secure-paypal-login.xyz)..." 
                                required
                            >
                            <button type="submit" id="scanSubmitBtn" class="btn btn-cyan px-4">
                                <span id="scanSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                                <span id="scanBtnText"><i class="fa-solid fa-bolt me-1"></i> Scan URL</span>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Scan Result Output Container -->
                    <div id="scanResultContainer" class="d-none text-start"></div>
                </div>

                <!-- Live Dynamic Threat Metrics from MySQL -->
                <div class="row g-3 justify-content-center mt-3">
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-number text-cyan"><?= number_format($stats['total_scans']) ?></div>
                            <div class="stat-label">URLs Scanned</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-number text-danger"><?= number_format($stats['threats_detected']) ?></div>
                            <div class="stat-label">Threats Detected</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-number text-info"><?= number_format($stats['registered_users']) ?></div>
                            <div class="stat-label">Registered Users</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-number text-success"><?= $stats['accuracy'] ?></div>
                            <div class="stat-label">Detection Accuracy</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 border-top border-secondary border-opacity-25">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-cyan text-uppercase fw-bold small tracking-wider">Detection Pipeline</span>
            <h2 class="text-white fw-bold mt-1">How Cyber Shield Analyzes Phishing</h2>
            <p class="text-secondary max-w-xl mx-auto">
                An intelligent multi-stage heuristic engine inspecting structural anomalies, brand impersonations, and credential entrapment markers in milliseconds.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-code-compare"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">1. Lexical URL Parsing</h5>
                    <p class="text-secondary small mb-0">
                        Deconstructs full protocol, hostname, path depth, subdomain levels, raw IP notation, and suspicious character obfuscation patterns.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-fingerprint"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">2. Heuristic Risk Scoring</h5>
                    <p class="text-secondary small mb-0">
                        Scores threat weights across 10 security indicators including brand impersonation, high-abuse TLDs, and credential-harvesting keywords.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">3. Classification & Report</h5>
                    <p class="text-secondary small mb-0">
                        Generates a clear 0-100 Threat Index, actionable safe/suspicious/phishing verdict, and forensic breakdown stored securely in MySQL.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Phishing Is Dangerous -->
<section class="py-5 bg-dark bg-opacity-25 border-top border-secondary border-opacity-25">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <span class="text-danger text-uppercase fw-bold small tracking-wider">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Threat Landscape
                </span>
                <h2 class="text-white fw-bold mt-2 mb-3">Why Phishing Is the #1 Cyber Attack Vector</h2>
                <p class="text-secondary">
                    Over 85% of modern enterprise data breaches originate from deceptive links in emails, SMS, or fake login portals. Attackers exploit human psychology rather than software vulnerabilities alone.
                </p>
                <div class="d-flex flex-column gap-3 mt-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-cyan fs-5"><i class="fa-solid fa-building-columns"></i></div>
                        <div>
                            <h6 class="text-white mb-1">Financial & Banking Credential Hijacking</h6>
                            <p class="text-secondary small mb-0">Deceptive clones mimic bank login pages to intercept two-factor codes and debit cards.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-cyan fs-5"><i class="fa-solid fa-envelope-open-text"></i></div>
                        <div>
                            <h6 class="text-white mb-1">Corporate Spear-Phishing</h6>
                            <p class="text-secondary small mb-0">Targeted spear-phishing messages deceiving employees into running remote access malware.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-cyan fs-5"><i class="fa-solid fa-id-card"></i></div>
                        <div>
                            <h6 class="text-white mb-1">Identity Theft & Account Takeover</h6>
                            <p class="text-secondary small mb-0">Hijacking social accounts and email gateways using obfuscated URL redirectors.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="cyber-card p-4">
                    <h5 class="text-white fw-bold mb-3 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-bullseye text-danger me-2"></i>Recent Detection Samples</span>
                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger small">Real-time DB</span>
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-cyber table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Target Host</th>
                                    <th>Score</th>
                                    <th>Verdict</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-monospace small text-truncate" style="max-width: 180px;">paypal.verify-auth.xyz</td>
                                    <td><span class="badge bg-danger">85/100</span></td>
                                    <td><?= renderResultBadge('PHISHING') ?></td>
                                </tr>
                                <tr>
                                    <td class="font-monospace small text-truncate" style="max-width: 180px;">192.168.1.45/bank</td>
                                    <td><span class="badge bg-danger">90/100</span></td>
                                    <td><?= renderResultBadge('PHISHING') ?></td>
                                </tr>
                                <tr>
                                    <td class="font-monospace small text-truncate" style="max-width: 180px;">login-mycompany.net</td>
                                    <td><span class="badge bg-warning text-dark">35/100</span></td>
                                    <td><?= renderResultBadge('SUSPICIOUS') ?></td>
                                </tr>
                                <tr>
                                    <td class="font-monospace small text-truncate" style="max-width: 180px;">github.com/torvalds</td>
                                    <td><span class="badge bg-success">05/100</span></td>
                                    <td><?= renderResultBadge('SAFE') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <a href="checker.php" class="btn btn-outline-cyan btn-sm w-100">
                            <i class="fa-solid fa-play me-1"></i> Launch Interactive Scanner
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5 text-center">
    <div class="container">
        <div class="cyber-card py-5 px-4 text-center mx-auto" style="max-width: 850px;">
            <div class="brand-shield-icon mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h2 class="text-white fw-bold mb-2">Protect Yourself Against Phishing Attacks</h2>
            <p class="text-secondary mb-4 mx-auto" style="max-width: 560px;">
                Create a free researcher account to analyze unlimited URLs, inspect risk breakdowns, save historical reports, and monitor cyber threats.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="signup.php" class="btn btn-cyan px-4 py-2">
                    <i class="fa-solid fa-user-plus me-1"></i> Create Free Account
                </a>
                <a href="awareness.php" class="btn btn-outline-cyan px-4 py-2">
                    <i class="fa-solid fa-book-open me-1"></i> Learn Security Tips
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Login Required Modal (When unauthenticated visitor scans URL) -->
<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content cyber-card p-4 border-cyan">
            <div class="text-center mb-3">
                <div class="auth-icon-badge">
                    <i class="fa-solid fa-lock text-cyan"></i>
                </div>
                <h4 class="text-white fw-bold" id="loginModalLabel">Authentication Required</h4>
                <p class="text-secondary small">
                    Please login or create an account to use the Cyber Shield URL scanner and save forensic telemetry to MySQL.
                </p>
            </div>
            <div class="d-grid gap-2">
                <a href="login.php" class="btn btn-cyan">
                    <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Login to Your Account
                </a>
                <a href="signup.php" class="btn btn-outline-cyan">
                    <i class="fa-solid fa-user-plus me-1"></i> Create Free Account
                </a>
                <button type="button" class="btn btn-link text-secondary btn-sm" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>


<style>
/* Cyber Shield - professional light theme */
:root {
    --cs-primary: #1976d2;
    --cs-primary-dark: #125ca8;
    --cs-accent: #12a6b5;
    --cs-ink: #203247;
    --cs-muted: #65758b;
    --cs-line: #e2eaf2;
    --cs-surface: #ffffff;
    --cs-soft: #f3f8fc;
}
body {
    background: #f5f8fc !important;
    color: var(--cs-ink) !important;
}
main, .hero-section, section {
    color: var(--cs-ink);
}
.hero-section {
    background: linear-gradient(135deg, #f8fbff 0%, #eaf5ff 55%, #f2fbfc 100%) !important;
    padding-top: 5rem;
    padding-bottom: 4rem;
}
.hero-title, h1, h2, h3, h4, h5, h6,
.text-white {
    color: #203247 !important;
}
.hero-subtitle, .text-secondary, p.text-secondary {
    color: #64758b !important;
}
.hero-badge {
    background: #e4f3fb !important;
    color: #176b9b !important;
    border: 1px solid #c9e5f3;
}
.hero-gradient-text {
    background: linear-gradient(90deg, #1676cf, #12a6b5);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent !important;
}
.scanner-container, .cyber-card, .feature-box, .stat-card,
.auth-card, .demo-account-hint, .modal-content {
    background: #ffffff !important;
    color: var(--cs-ink) !important;
    border: 1px solid var(--cs-line) !important;
    border-radius: 18px !important;
    box-shadow: 0 10px 30px rgba(34, 74, 115, .07) !important;
}
.scanner-container { padding: 10px; }
.scanner-input, .form-control, .form-select {
    background: #fbfdff !important;
    color: #203247 !important;
    border: 1px solid #d7e2ed !important;
}
.scanner-input::placeholder, .form-control::placeholder { color: #91a0b1 !important; }
.input-group-text { color: #1686a4 !important; }
.btn-cyan {
    background: #1976d2 !important;
    border-color: #1976d2 !important;
    color: #fff !important;
}
.btn-cyan:hover { background: #125ca8 !important; border-color: #125ca8 !important; }
.btn-outline-cyan {
    color: #1976d2 !important;
    border-color: #1976d2 !important;
}
.btn-outline-cyan:hover { background: #eaf4ff !important; color: #125ca8 !important; }
.text-cyan { color: #1286a0 !important; }
.stat-number { color: #1976d2 !important; }
.stat-label { color: #68798e !important; }
.feature-icon-wrapper, .brand-shield-icon, .auth-icon-badge {
    background: #eaf5ff !important;
    color: #1976d2 !important;
    border: 1px solid #d5e9fb !important;
}
section.border-top, .border-secondary {
    border-color: #e3ebf3 !important;
}
.bg-dark, .bg-dark.bg-opacity-25 {
    background: #f0f6fb !important;
}
.table-cyber {
    --bs-table-bg: #fff;
    --bs-table-color: #33465c;
    --bs-table-border-color: #e5edf4;
}
.table-cyber thead th { color: #52677e !important; background: #f3f7fb; }
.table-cyber td { color: #42566d !important; }
.modal-content { background: #fff !important; }
.text-danger { color: #d64c5c !important; }
.text-success { color: #21865b !important; }
.text-info { color: #247eae !important; }
a { text-decoration-thickness: 1px; }
@media (max-width: 767px) {
    .hero-section { padding-top: 3rem; padding-bottom: 2.5rem; }
    .scanner-container .input-group { flex-wrap: wrap; }
    .scanner-input { min-width: 0; }
    .scanner-container .btn { width: 100%; margin-top: 8px; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
