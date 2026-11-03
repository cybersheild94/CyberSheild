<?php
/**
 * CYBER SHIELD - About Project & Technical Architecture
 * File: about.php
 * BCA Final Year Project
 */

declare(strict_types=1);

$pageTitle = 'About Project & Architecture';
$activeNav = 'about';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <div class="hero-badge">
            <i class="fa-solid fa-graduation-cap text-cyan"></i>
            <span>BCA FINAL YEAR MAJOR PROJECT SPECIFICATION</span>
        </div>
        <h1 class="text-white fw-bold">About Cyber Shield</h1>
        <p class="text-secondary max-w-xl mx-auto">
            A comprehensive project report overview: Problem definition, heuristic classification pipeline, technological stack, security controls, and future scope.
        </p>
    </div>

    <!-- Executive Summary Card -->
    <div class="cyber-card p-4 mb-4">
        <h3 class="text-white fw-bold mb-3 d-flex align-items-center">
            <i class="fa-solid fa-circle-nodes text-cyan me-2"></i>Project Abstract
        </h3>
        <p class="text-secondary">
            Phishing remains one of the most prolific and devastating attack vectors in cyber space. Traditional detection mechanisms relying solely on static blacklists often fail against modern zero-hour phishing campaigns, where attackers generate disposable domains that remain active for only a few hours.
        </p>
        <p class="text-secondary mb-0">
            <strong class="text-white">CYBER SHIELD</strong> addresses this limitation by deploying an intelligent rule-based heuristic classification engine. By extracting and scoring lexical, structural, and semantic attributes of an incoming URL in real time, Cyber Shield calculates a deterministic 0–100 Threat Index without waiting for external blacklist updates.
        </p>
    </div>

    <div class="row g-4 mb-4">
        <!-- Problem Statement & Objectives -->
        <div class="col-lg-6">
            <div class="cyber-card h-100 p-4">
                <h4 class="text-white fw-bold mb-3 text-cyan">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>Problem Statement
                </h4>
                <p class="text-secondary small">
                    Conventional web security tools suffer from significant latency: by the time an anti-phishing database discovers and blacklists a fake credential portal, hundreds of users have already submitted sensitive financial or personal data. Furthermore, users browsing on mobile devices frequently cannot inspect long, obfuscated URLs due to truncated address bars.
                </p>
                <h5 class="text-white fw-bold mt-4 mb-2 small text-uppercase text-cyan">Project Objectives:</h5>
                <ul class="text-secondary small mb-0 ps-3">
                    <li class="mb-2">Build a zero-latency real-time URL inspection tool.</li>
                    <li class="mb-2">Implement multi-layer lexical heuristics evaluating HTTPS, length, subdomains, and obfuscation.</li>
                    <li class="mb-2">Provide an accessible, transparent explanation ("Why was this URL flagged?") for user education.</li>
                    <li class="mb-2">Design a secure, role-based PHP & MySQL platform with complete audit logging.</li>
                </ul>
            </div>
        </div>

        <!-- Proposed Solution & Architecture -->
        <div class="col-lg-6">
            <div class="cyber-card h-100 p-4">
                <h4 class="text-white fw-bold mb-3 text-cyan">
                    <i class="fa-solid fa-microchip me-2"></i>Proposed Solution
                </h4>
                <p class="text-secondary small">
                    Cyber Shield replaces slow external lookups with an on-the-fly heuristic decision engine. When a URL is submitted via async AJAX, the backend decomposes the target into 10 independent risk vectors:
                </p>
                <div class="bg-dark bg-opacity-50 p-3 rounded border border-secondary border-opacity-25 small mb-3">
                    <code class="text-cyan">
                        URL &rarr; Parse Protocol & Host &rarr; Lexical Breakdown &rarr; Keyword Density Analysis &rarr; Subdomain Hierarchy Depth &rarr; Brand Impersonation Matching &rarr; Aggregate Threat Index (0–100) &rarr; Safe / Suspicious / Phishing Classification
                    </code>
                </div>
                <p class="text-secondary small mb-0">
                    The entire pipeline executes in under 25 milliseconds, persisting full telemetry for auditability and compliance.
                </p>
            </div>
        </div>
    </div>

    <!-- Technology Stack Matrix -->
    <div class="cyber-card p-4 mb-4">
        <h4 class="text-white fw-bold mb-3"><i class="fa-solid fa-layer-group text-cyan me-2"></i>Technology Stack</h4>
        <div class="row g-3 text-center">
            <div class="col-md-3 col-6">
                <div class="p-3 bg-dark rounded border border-secondary border-opacity-25">
                    <i class="fa-brands fa-php text-primary fs-3 mb-2"></i>
                    <h6 class="text-white fw-bold mb-1">PHP 8+ Backend</h6>
                    <span class="text-secondary small">Procedural & OOP, PDO Prepared Statements, Session Auth</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 bg-dark rounded border border-secondary border-opacity-25">
                    <i class="fa-solid fa-database text-warning fs-3 mb-2"></i>
                    <h6 class="text-white fw-bold mb-1">MySQL 5.7 / 8.0</h6>
                    <span class="text-secondary small">Relational schema, foreign keys, UTF8MB4, indexes</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 bg-dark rounded border border-secondary border-opacity-25">
                    <i class="fa-brands fa-bootstrap text-info fs-3 mb-2"></i>
                    <h6 class="text-white fw-bold mb-1">Bootstrap 5 + CSS3</h6>
                    <span class="text-secondary small">Dark theme, glassmorphism, flexbox, mobile grid</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 bg-dark rounded border border-secondary border-opacity-25">
                    <i class="fa-brands fa-js text-warning fs-3 mb-2"></i>
                    <h6 class="text-white fw-bold mb-1">JavaScript & Fetch</h6>
                    <span class="text-secondary small">Asynchronous AJAX API, DOM mutation, Chart.js</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Implemented Functionality vs Future Scope -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="cyber-card h-100 p-4 border-success">
                <h5 class="text-success fw-bold mb-3">
                    <i class="fa-solid fa-circle-check me-2"></i>Currently Implemented (Phase 1)
                </h5>
                <ul class="text-secondary small mb-0 ps-3">
                    <li class="mb-2"><strong class="text-white">Heuristic Phishing Engine:</strong> Multi-weight scoring of HTTPS, IP addresses, subdomains, length, keywords, and spoofed brands.</li>
                    <li class="mb-2"><strong class="text-white">Role-Based Authentication:</strong> Secure User & Admin separation with Bcrypt password hashing.</li>
                    <li class="mb-2"><strong class="text-white">Database Audit Logging:</strong> Persistent storage of all scanned targets with user associations.</li>
                    <li class="mb-2"><strong class="text-white">Dynamic Dashboards:</strong> Chart.js visualizations for both individual users and system admins.</li>
                    <li class="mb-2"><strong class="text-white">Security Protections:</strong> PDO prepared statements, CSRF protection, and XSS sanitization.</li>
                </ul>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="cyber-card h-100 p-4 border-cyan">
                <h5 class="text-cyan fw-bold mb-3">
                    <i class="fa-solid fa-rocket me-2"></i>Future Scope & Enhancements (Phase 2)
                </h5>
                <ul class="text-secondary small mb-0 ps-3">
                    <li class="mb-2"><strong class="text-white">Trained Machine Learning Model:</strong> Transitioning from rule-based heuristics to Random Forest / XGBoost classifiers trained on the Kaggle Phishing Websites dataset.</li>
                    <li class="mb-2"><strong class="text-white">Third-Party Threat Feeds:</strong> Live API integration with Google Safe Browsing API v4 and VirusTotal API.</li>
                    <li class="mb-2"><strong class="text-white">Live WHOIS & Domain Age:</strong> Direct socket query for domain registration date (flagging domains < 14 days old).</li>
                    <li class="mb-2"><strong class="text-white">Browser Extension:</strong> Chrome & Firefox WebExtension plugin alerting users before form submission.</li>
                    <li class="mb-2"><strong class="text-white">Email Header Parser:</strong> Uploading raw .EML files to analyze SPF, DKIM, DMARC, and embedded links.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
