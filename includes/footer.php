<?php
/**
 * CYBER SHIELD - Global Footer Include
 * File: includes/footer.php
 * BCA Final Year Project
 */

declare(strict_types=1);
?>
<footer class="cyber-footer mt-auto py-5 border-top border-dark-subtle">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="brand-shield-icon me-2">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <span class="brand-text-cyan fs-5">CYBER</span>
                    <span class="brand-text-white fs-5 ms-1">SHIELD</span>
                </div>
                <p class="text-secondary small mb-3">
                    CYBER SHIELD is an academic research & defense web platform engineered for BCA final-year project demonstration. It combines multi-layer lexical URL heuristics and statistical risk scoring to identify fraudulent phishing domains before credential compromises occur.
                </p>
                <div class="d-flex gap-3 text-secondary">
                    <span class="badge bg-dark-subtle text-cyan border border-cyan-dim"><i class="fa-solid fa-code me-1"></i> PHP 8+ / MySQL</span>
                    <span class="badge bg-dark-subtle text-info border border-info-dim"><i class="fa-solid fa-bolt me-1"></i> AJAX Async</span>
                    <span class="badge bg-dark-subtle text-warning border border-warning-dim"><i class="fa-solid fa-shield me-1"></i> Heuristic AI</span>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="text-uppercase text-cyan fw-bold mb-3 small tracking-wider">Navigation</h6>
                <ul class="list-unstyled text-secondary small">
                    <li class="mb-2"><a href="<?= BASE_URL ?>/index.php" class="footer-link">Home Portal</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/checker.php" class="footer-link">URL Scanner</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/awareness.php" class="footer-link">Threat Awareness</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/about.php" class="footer-link">Project Abstract</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/contact.php" class="footer-link">Contact & Viva</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 col-6">
                <h6 class="text-uppercase text-cyan fw-bold mb-3 small tracking-wider">Academic Details</h6>
                <ul class="list-unstyled text-secondary small">
                    <li class="mb-2"><span class="text-white">Degree:</span> Bachelor of Computer Applications</li>
                    <li class="mb-2"><span class="text-white">Subject:</span> Major Final Year Project</li>
                    <li class="mb-2"><span class="text-white">Architecture:</span> MVC / Procedural Clean PHP</li>
                    <li class="mb-2"><span class="text-white">Environment:</span> XAMPP / WAMP / Apache</li>
                    <li class="mb-2"><span class="text-white">Auth:</span> Bcrypt & CSRF Protected</li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase text-cyan fw-bold mb-3 small tracking-wider">Security Disclaimer</h6>
                <p class="text-secondary small">
                    This scanner employs client/server heuristic algorithms simulating AI feature classification. Always verify high-risk transactions directly with verified organization contacts.
                </p>
                <div class="cyber-status-pill">
                    <span class="status-indicator"></span>
                    <span class="text-light small">Detection Heuristics Active</span>
                </div>
            </div>
        </div>

        <hr class="my-4 border-secondary opacity-25">

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center text-secondary small">
            <div>
                &copy; <?= date('Y') ?> Cyber Shield Phishing Detection Engine. BCA Final Year Project.
            </div>
            <div class="mt-2 mt-sm-0">
                <span>Designed for Academic Presentation & Viva Examination</span>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- Global JS Assets -->
<script src="<?= BASE_URL ?>/assets/js/script.js"></script>
<?php if (isset($extraJs)): ?>
    <script src="<?= BASE_URL ?>/assets/js/<?= htmlspecialchars($extraJs) ?>"></script>
<?php endif; ?>
</body>
</html>
