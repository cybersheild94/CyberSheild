<?php
/**
 * CYBER SHIELD - User Session Termination
 * File: logout.php
 * BCA Final Year Project
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

logoutUser();

setFlashMessage('info', 'You have been securely signed out of Cyber Shield.');
header('Location: ' . BASE_URL . '/login.php');
exit;
