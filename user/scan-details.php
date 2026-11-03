<?php
/**
 * CYBER SHIELD - User Scan Details Bridge
 * File: user/scan-details.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/user-auth.php';
requireUser();

// Delegate to root scan-details with existing authentication parameters
require_once __DIR__ . '/../scan-details.php';
