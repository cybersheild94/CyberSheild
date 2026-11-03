<?php
/**
 * CYBER SHIELD - Administrator System Settings
 * File: admin/settings.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin-auth.php';
requireAdmin();

$db = getDBConnection();

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (validateCSRFToken($csrf)) {
        $allowedKeys = [
            'site_title', 'site_tagline', 'max_scans_per_hour',
            'enable_public_scans', 'contact_email',
            'detection_engine_version', 'maintenance_mode'
        ];

        foreach ($allowedKeys as $key) {
            $val = sanitizeInput($_POST[$key] ?? '');
            $stmt = $db->prepare("
                INSERT INTO settings (setting_key, setting_value, updated_at)
                VALUES (?, ?, NOW())
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()
            ");
            $stmt->execute([$key, $val]);
        }

        setFlashMessage('success', 'System configurations updated successfully in MySQL `settings` table.');
        header('Location: settings.php');
        exit;
    }
}

// Fetch all current settings
$stmt = $db->query("SELECT setting_key, setting_value FROM settings");
$rawSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

$pageTitle = 'System Settings';
$activeNav = 'admin';
$extraCss = 'admin.css';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <ul class="admin-sidebar-nav">
            <li><a href="dashboard.php" class="admin-sidebar-link"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
            <li><a href="users.php" class="admin-sidebar-link"><i class="fa-solid fa-users"></i> Manage Users</a></li>
            <li><a href="scans.php" class="admin-sidebar-link"><i class="fa-solid fa-radar"></i> Manage URL Scans</a></li>
            <li><a href="feedback.php" class="admin-sidebar-link"><i class="fa-solid fa-inbox"></i> Feedback</a></li>
            <li><a href="settings.php" class="admin-sidebar-link active"><i class="fa-solid fa-sliders"></i> System Settings</a></li>
            <li><a href="profile.php" class="admin-sidebar-link"><i class="fa-solid fa-user-shield"></i> Admin Profile</a></li>
            <li class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                <a href="logout.php" class="admin-sidebar-link text-danger"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
            </li>
        </ul>
    </aside>

    <main class="admin-main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-white fw-bold mb-1">System Configuration & Thresholds</h2>
                <p class="text-secondary small mb-0">Tune heuristic classifier weights, throttle rate limits, and site variables.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="cyber-card p-4">
                    <form action="settings.php" method="POST">
                        <?= csrfField() ?>

                        <h5 class="text-white fw-bold mb-3"><i class="fa-solid fa-globe text-cyan me-2"></i>Global Platform Identity</h5>

                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-semibold">Platform Application Name</label>
                            <input type="text" name="site_title" class="form-control bg-dark text-white border-secondary border-opacity-50" value="<?= htmlspecialchars($rawSettings['site_title'] ?? 'Cyber Shield Phishing Intelligence') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-semibold">Platform Tagline / Slogan</label>
                            <input type="text" name="site_tagline" class="form-control bg-dark text-white border-secondary border-opacity-50" value="<?= htmlspecialchars($rawSettings['site_tagline'] ?? 'AI-Inspired Rule-Based Phishing Detection & Mitigation Engine') ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-secondary small fw-semibold">Support Contact Email</label>
                            <input type="email" name="contact_email" class="form-control bg-dark text-white border-secondary border-opacity-50" value="<?= htmlspecialchars($rawSettings['contact_email'] ?? 'support@cybershield.local') ?>" required>
                        </div>

                        <hr class="border-secondary border-opacity-25 my-4">

                        <h5 class="text-white fw-bold mb-3"><i class="fa-solid fa-shield-halved text-cyan me-2"></i>Engine Heuristic Controls</h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-semibold">Engine Algorithm Tag</label>
                                <input type="text" name="detection_engine_version" class="form-control bg-dark text-white border-secondary border-opacity-50" value="<?= htmlspecialchars($rawSettings['detection_engine_version'] ?? 'v2.4-hybrid-heuristics') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-semibold">Hourly Scan Rate Limit Per IP</label>
                                <input type="number" name="max_scans_per_hour" class="form-control bg-dark text-white border-secondary border-opacity-50" value="<?= htmlspecialchars($rawSettings['max_scans_per_hour'] ?? '50') ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-semibold">Public Scanning Policy</label>
                                <select name="enable_public_scans" class="form-select bg-dark text-white border-secondary border-opacity-50">
                                    <option value="1" <?= ($rawSettings['enable_public_scans'] ?? '1') === '1' ? 'selected' : '' ?>>Enabled (Guests may scan)</option>
                                    <option value="0" <?= ($rawSettings['enable_public_scans'] ?? '1') === '0' ? 'selected' : '' ?>>Strict (Require Login / Registration)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-semibold">Maintenance Mode</label>
                                <select name="maintenance_mode" class="form-select bg-dark text-white border-secondary border-opacity-50">
                                    <option value="0" <?= ($rawSettings['maintenance_mode'] ?? '0') === '0' ? 'selected' : '' ?>>Operational (Online)</option>
                                    <option value="1" <?= ($rawSettings['maintenance_mode'] ?? '0') === '1' ? 'selected' : '' ?>>Maintenance Active</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-cyan px-4">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cyber-card p-4">
                    <h5 class="text-white fw-bold mb-3"><i class="fa-solid fa-circle-info text-cyan me-2"></i>Database Notes</h5>
                    <p class="text-secondary small">
                        All configuration keys are dynamically read from the MySQL <code>settings</code> table. Modifying parameters immediately influences detection policies without restarting the Apache web server.
                    </p>
                    <div class="p-3 bg-dark rounded border border-secondary border-opacity-25 small text-secondary">
                        <span class="text-white fw-bold d-block mb-1">Heuristic Cutoffs:</span>
                        <span class="d-block text-success">&bull; 0 - 29: SAFE</span>
                        <span class="d-block text-warning">&bull; 30 - 59: SUSPICIOUS</span>
                        <span class="d-block text-danger">&bull; 60 - 100: PHISHING</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
