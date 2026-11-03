<?php
/**
 * CYBER SHIELD - Cybersecurity Awareness & Education
 * File: awareness.php
 * BCA Final Year Project
 */

declare(strict_types=1);

$pageTitle = 'Cybersecurity Phishing Awareness';
$activeNav = 'awareness';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <div class="hero-badge">
            <i class="fa-solid fa-graduation-cap text-cyan"></i>
            <span>SECURITY KNOWLEDGE BASE</span>
        </div>
        <h1 class="text-white fw-bold">Phishing Defense & Threat Awareness</h1>
        <p class="text-secondary max-w-xl mx-auto">
            Understanding the psychological tactics, deceptive vectors, and lexical indicators behind modern social engineering campaigns.
        </p>
    </div>

    <!-- What is Phishing Hero Card -->
    <div class="cyber-card p-4 p-md-5 mb-5 border-cyan">
        <div class="row align-items-center gy-4">
            <div class="col-lg-8">
                <span class="text-cyan text-uppercase fw-bold small tracking-wider">Fundamental Concept</span>
                <h3 class="text-white fw-bold mt-1 mb-3">What Exactly Is Phishing?</h3>
                <p class="text-secondary mb-3">
                    Phishing is a social engineering cyber attack where threat actors masquerade as trusted entities (such as banks, cloud providers, streaming platforms, or university IT portals) to manipulate victims into revealing sensitive credentials, credit card details, or downloading malware.
                </p>
                <p class="text-secondary mb-0">
                    Unlike software exploit attacks that target operating system bugs, phishing directly exploits human behavioral traits: <strong class="text-white">urgency</strong>, <strong class="text-white">fear of account loss</strong>, <strong class="text-white">curiosity</strong>, or <strong class="text-white">authority</strong>.
                </p>
            </div>
            <div class="col-lg-4 text-center">
                <div class="p-4 bg-dark bg-opacity-75 rounded-4 border border-cyan-dim">
                    <i class="fa-solid fa-shield-virus text-cyan" style="font-size: 4rem;"></i>
                    <div class="mt-3 text-white fw-bold">Human Firewall</div>
                    <div class="text-secondary small">Your vigilance is the ultimate line of defense.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Types of Phishing Attacks -->
    <div class="mb-5">
        <div class="text-center mb-4">
            <h3 class="text-white fw-bold">Primary Vectors of Phishing Attacks</h3>
            <p class="text-secondary small">Deceptive techniques classified by transmission vector and targeting level</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon-wrapper text-danger">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">1. Email Phishing</h5>
                    <p class="text-secondary small mb-2">
                        Mass emails disguised as transactional notices from PayPal, Netflix, or Microsoft urging "urgent password reset" or "billing failure update".
                    </p>
                    <span class="badge bg-danger bg-opacity-25 text-danger small">High Volume Mass Attack</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon-wrapper text-warning">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">2. SMS Phishing (Smishing)</h5>
                    <p class="text-secondary small mb-2">
                        Text messages pretending to be postal delivery updates, bank OTP confirmations, or lottery winnings with embedded shortlinks.
                    </p>
                    <span class="badge bg-warning bg-opacity-25 text-warning small">Rapid Click-Through Rate</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon-wrapper text-info">
                        <i class="fa-solid fa-globe"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">3. Website Clone Phishing</h5>
                    <p class="text-secondary small mb-2">
                        Pixel-perfect visual clones of official web portals hosted on deceptive domains (e.g. <code>paypa1.com</code> or <code>login-microsoft.top</code>).
                    </p>
                    <span class="badge bg-info bg-opacity-25 text-info small">Target of Cyber Shield</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon-wrapper text-primary">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">4. Spear Phishing</h5>
                    <p class="text-secondary small mb-2">
                        Highly customized attacks targeting specific employees or students using OSINT (Open Source Intelligence) gathered from LinkedIn or social profiles.
                    </p>
                    <span class="badge bg-primary bg-opacity-25 text-primary small">Targeted / High Impact</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon-wrapper text-secondary">
                        <i class="fa-solid fa-clone"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">5. Clone Phishing</h5>
                    <p class="text-secondary small mb-2">
                        Attackers intercept an authentic previous email exchange, duplicate it entirely, and swap the original attachment/link with a malicious version.
                    </p>
                    <span class="badge bg-secondary bg-opacity-25 text-light small">Evasion Technique</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon-wrapper text-cyan">
                        <i class="fa-solid fa-share-nodes"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">6. Social Media Phishing</h5>
                    <p class="text-secondary small mb-2">
                        Compromised friend accounts or fake customer support handles DMing users with "Is this your video?" or counterfeit cryptocurrency giveaways.
                    </p>
                    <span class="badge bg-cyan text-dark small">Social Engineering</span>
                </div>
            </div>
        </div>
    </div>

    <!-- How to Detect a Phishing Website (Checklist) -->
    <div class="cyber-card p-4 mb-5">
        <h4 class="text-white fw-bold mb-4 d-flex align-items-center">
            <i class="fa-solid fa-clipboard-check text-cyan me-2"></i>
            How to Spot a Phishing URL in 5 Seconds
        </h4>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="p-3 bg-dark bg-opacity-50 rounded border border-danger border-opacity-50 mb-3">
                    <h6 class="text-danger fw-bold"><i class="fa-solid fa-xmark me-2"></i>Look at the True Domain (Host)</h6>
                    <p class="text-secondary small mb-0">
                        Phishers put authentic names in subdomains to trick mobile viewports: <br>
                        <code>https://paypal.com.account-verify.click</code> &rarr; Actual host is <strong class="text-danger">account-verify.click</strong>, NOT PayPal!
                    </p>
                </div>
                <div class="p-3 bg-dark bg-opacity-50 rounded border border-danger border-opacity-50 mb-3">
                    <h6 class="text-danger fw-bold"><i class="fa-solid fa-xmark me-2"></i>Watch for Typosquatting</h6>
                    <p class="text-secondary small mb-0">
                        Subtle character substitutions like replacing lowercase 'l' with numeral '1', or 'rn' with 'm' (e.g. <code>rnicrosoft.com</code> instead of <code>microsoft.com</code>).
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-3 bg-dark bg-opacity-50 rounded border border-danger border-opacity-50 mb-3">
                    <h6 class="text-danger fw-bold"><i class="fa-solid fa-xmark me-2"></i>Missing or Suspicious HTTPS</h6>
                    <p class="text-secondary small mb-0">
                        Legitimate portals dealing with banking or credentials never use unencrypted HTTP or raw numeric IP addresses like <code>http://192.168.1.10/login</code>.
                    </p>
                </div>
                <div class="p-3 bg-dark bg-opacity-50 rounded border border-danger border-opacity-50 mb-3">
                    <h6 class="text-danger fw-bold"><i class="fa-solid fa-xmark me-2"></i>Free Suspicious Domain Extensions</h6>
                    <p class="text-secondary small mb-0">
                        Unusual TLDs such as <code>.xyz</code>, <code>.top</code>, <code>.work</code>, or <code>.buzz</code> registered anonymously hours before an attack campaign.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Golden Rules of Cyber Safety -->
    <div class="p-4 rounded-4" style="background: linear-gradient(135deg, rgba(0, 243, 255, 0.08), rgba(0, 102, 255, 0.08)); border: 1px solid rgba(0, 243, 255, 0.25);">
        <h4 class="text-white fw-bold mb-3"><i class="fa-solid fa-award text-cyan me-2"></i>The 4 Golden Rules of Web Safety</h4>
        <div class="row g-3 text-secondary small">
            <div class="col-md-3">
                <strong class="text-white d-block mb-1">1. Never Click Urgency Links</strong>
                <span>Always navigate directly by typing the official address in your address bar.</span>
            </div>
            <div class="col-md-3">
                <strong class="text-white d-block mb-1">2. Enable Multi-Factor Auth (MFA)</strong>
                <span>Hardware keys or authenticator apps block 99% of automated credential replay attacks.</span>
            </div>
            <div class="col-md-3">
                <strong class="text-white d-block mb-1">3. Check URL in Cyber Shield</strong>
                <span>Run suspicious links through our scanner before typing credentials.</span>
            </div>
            <div class="col-md-3">
                <strong class="text-white d-block mb-1">4. Report Phishing Campaigns</strong>
                <span>Notify your college network administrator or company SOC team immediately.</span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
