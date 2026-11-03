<?php
/**
 * CYBER SHIELD - Global Navbar Component
 * File: includes/navbar.php
 * BCA Final Year Project
 */

declare(strict_types=1);

$user = getCurrentUser();
$isAdmin = isAdmin();
?>
<nav class="navbar navbar-expand-lg navbar-dark cyber-navbar sticky-top">
    <div class="container-fluid px-lg-4">
        <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/index.php">
            <div class="brand-shield-icon me-2">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <span class="brand-text-cyan">CYBER</span>
            <span class="brand-text-white ms-1">SHIELD</span>
            <span class="badge cyber-version-badge ms-2">v1.0 AI-Heuristic</span>
        </a>

        <button class="navbar-toggler border-cyan" type="button" data-bs-toggle="collapse" data-bs-target="#cyberShieldNavbar" aria-controls="cyberShieldNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="cyberShieldNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'home' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php">
                        <i class="fa-solid fa-house me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'checker' ? 'active' : '' ?>" href="<?= BASE_URL ?>/checker.php">
                        <i class="fa-solid fa-crosshairs me-1"></i> URL Scanner
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'awareness' ? 'active' : '' ?>" href="<?= BASE_URL ?>/awareness.php">
                        <i class="fa-solid fa-graduation-cap me-1"></i> Awareness
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'about' ? 'active' : '' ?>" href="<?= BASE_URL ?>/about.php">
                        <i class="fa-solid fa-circle-info me-1"></i> About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'contact' ? 'active' : '' ?>" href="<?= BASE_URL ?>/contact.php">
                        <i class="fa-solid fa-envelope me-1"></i> Contact
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <?php if ($user): ?>
                    <?php if ($isAdmin): ?>
                        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline-danger btn-sm">
                            <i class="fa-solid fa-shield-virus me-1"></i> Admin Console
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/user/dashboard.php" class="btn btn-outline-info btn-sm">
                            <i class="fa-solid fa-chart-line me-1"></i> Dashboard
                        </a>
                        <a href="<?= BASE_URL ?>/user/scans.php" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-clock-rotate-left me-1"></i> My Scans
                        </a>
                    <?php endif; ?>

                    <div class="dropdown">
                        <button class="btn btn-cyber-profile dropdown-toggle btn-sm" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-circle-user text-cyan me-1"></i>
                            <span class="user-greeting"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end cyber-dropdown" aria-labelledby="userMenuDropdown">
                            <?php if ($isAdmin): ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/dashboard.php"><i class="fa-solid fa-gauge-high me-2 text-cyan"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/profile.php"><i class="fa-solid fa-user-gear me-2 text-cyan"></i>Admin Profile</a></li>
                                <li><hr class="dropdown-divider border-secondary"></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/admin/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/dashboard.php"><i class="fa-solid fa-chart-pie me-2 text-cyan"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/profile.php"><i class="fa-solid fa-user-gear me-2 text-cyan"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/change-password.php"><i class="fa-solid fa-key me-2 text-cyan"></i>Security</a></li>
                                <li><hr class="dropdown-divider border-secondary"></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php" class="btn btn-outline-cyan btn-sm">
                        <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Login
                    </a>
                    <a href="<?= BASE_URL ?>/signup.php" class="btn btn-cyan btn-sm">
                        <i class="fa-solid fa-user-plus me-1"></i> Get Started Free
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-lg-4 mt-3">
    <?= renderFlashMessage() ?>
</div>
