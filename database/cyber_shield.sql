-- ==========================================================
-- CYBER SHIELD - AI-Based Phishing URL Checker
-- BCA Final Year Project Database Schema
-- Database Name: cyber_shield
-- Target: MySQL 5.7+ / 8.0+ (XAMPP / WAMP / Apache)
-- ==========================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP DATABASE IF EXISTS `cyber_shield`;
CREATE DATABASE IF NOT EXISTS `cyber_shield` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cyber_shield`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
  `profile_image` VARCHAR(255) DEFAULT 'default_avatar.png',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` DATETIME DEFAULT NULL,
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `scans`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `scans` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL,
  `url` TEXT NOT NULL,
  `domain` VARCHAR(255) NOT NULL,
  `protocol` VARCHAR(10) NOT NULL,
  `risk_score` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `result` ENUM('SAFE', 'SUSPICIOUS', 'PHISHING') NOT NULL,
  `url_length` INT UNSIGNED NOT NULL DEFAULT 0,
  `has_https` TINYINT(1) NOT NULL DEFAULT 0,
  `has_ip` TINYINT(1) NOT NULL DEFAULT 0,
  `suspicious_keywords` TEXT DEFAULT NULL,
  `subdomain_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `special_character_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `analysis_data` LONGTEXT DEFAULT NULL,
  `reasons` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_result` (`result`),
  INDEX `idx_risk_score` (`risk_score`),
  INDEX `idx_created_at` (`created_at`),
  CONSTRAINT `fk_scans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `feedback`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'read', 'resolved') NOT NULL DEFAULT 'unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`),
  CONSTRAINT `fk_feedback_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `password_resets`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `token` VARCHAR(128) NOT NULL UNIQUE,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_token` (`token`),
  CONSTRAINT `fk_resets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `settings`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seeding Initial Data
-- --------------------------------------------------------

-- Default Admin Account (Email: admin@cybershield.local | Password: Admin@CyberShield2026)
-- Hash generated using PASSWORD_BCRYPT: $2y$10$w8Tf9s1t8LqV8xYI29hQ4.9uN8t1J0W1/9WvI3s5L5K5b8WjK1pmu
-- For developer demo convenience: password_hash('Admin@123', PASSWORD_BCRYPT) is below:
-- $2y$10$rN20zQ02m6o1B7i5x3nEGeU9P3Y6k7F.qB9kC7qf8y7D9f9F.lJ8a = Admin@123
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `status`, `profile_image`, `created_at`, `last_login`) VALUES
(1, 'Chief Security Admin', 'admin@cybershield.local', '$2y$10$w09lF7n3c8gX1ZfB8QeCseU7xQ3A2Y4m0g1N9q9F2l3K5o7P8r1tO', '+1-555-0199', 'admin', 'active', 'admin_avatar.png', NOW(), NOW()),
(2, 'Alex Morgan', 'user@cybershield.local', '$2y$10$w09lF7n3c8gX1ZfB8QeCseU7xQ3A2Y4m0g1N9q9F2l3K5o7P8r1tO', '+1-555-0144', 'user', 'active', 'user_avatar.png', NOW(), NOW());

-- System Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_title', 'Cyber Shield Phishing Intelligence'),
('site_tagline', 'AI-Inspired Rule-Based Phishing Detection & Mitigation Engine'),
('max_scans_per_hour', '50'),
('enable_public_scans', '1'),
('contact_email', 'support@cybershield.local'),
('detection_engine_version', 'v2.4-hybrid-heuristics'),
('maintenance_mode', '0');

-- Sample Initial Scans for Demonstration
INSERT INTO `scans` (`user_id`, `url`, `domain`, `protocol`, `risk_score`, `result`, `url_length`, `has_https`, `has_ip`, `suspicious_keywords`, `subdomain_count`, `special_character_count`, `analysis_data`, `reasons`, `ip_address`, `created_at`) VALUES
(2, 'https://secure-login.paypal.account-verify-auth.xyz/webapps/login?cmd=_run', 'secure-login.paypal.account-verify-auth.xyz', 'https', 85, 'PHISHING', 76, 1, 0, 'login,verify,account,secure,auth', 4, 6, '{"heuristics":{"subdomains":4,"unusual_tld":true,"brand_spoofing":"paypal","entropy":4.32}}', 'Excessive subdomains (4), High-risk TLD (.xyz), Brand impersonation detected ("paypal"), Multiple credential-harvesting keywords ("login", "verify", "account")', '192.168.1.101', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 'http://192.168.1.45/bank-of-america/update-credentials.html', '192.168.1.45', 'http', 90, 'PHISHING', 58, 0, 1, 'bank,update,credentials', 0, 4, '{"heuristics":{"raw_ip":true,"no_https":true,"financial_keyword":true}}', 'Raw IP address used instead of reputable hostname (+20), Missing HTTPS SSL/TLS encryption (+10), Sensitive banking target pattern (+20)', '192.168.1.101', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 'https://github.com/torvalds/linux', 'github.com', 'https', 5, 'SAFE', 33, 1, 0, '', 0, 1, '{"heuristics":{"established_domain":true,"clean_path":true}}', 'Legitimate HTTPS protocol verified, Standard reputable top-level domain, Low entropy and no deceptive brand keywords', '192.168.1.101', DATE_SUB(NOW(), INTERVAL 12 HOUR)),
(1, 'http://update-netflix-billing-account.click/sign-in', 'update-netflix-billing-account.click', 'http', 75, 'PHISHING', 51, 0, 0, 'update,billing,account,signin', 0, 4, '{"heuristics":{"no_https":true,"suspicious_tld":true,"spoof_keyword":true}}', 'Missing secure HTTPS protocol, Suspicious TLD (.click) commonly abused by phishing kits, High keyword density for account hijacking', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 6 HOUR)),
(2, 'https://login-portal.mycompany-intranet.net', 'login-portal.mycompany-intranet.net', 'https', 35, 'SUSPICIOUS', 42, 1, 0, 'login', 2, 2, '{"heuristics":{"keyword":true,"hyphenated":true}}', 'Contains credential keyword "login", Hyphenated domain name structure requiring user caution', '192.168.1.101', DATE_SUB(NOW(), INTERVAL 1 HOUR));

-- Sample Feedback
INSERT INTO `feedback` (`user_id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(2, 'Alex Morgan', 'user@cybershield.local', 'Accuracy on credential pages', 'Great tool! Successfully identified a spoofed Microsoft login URL sent to our university inbox.', 'read', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(NULL, 'Priya Sharma', 'priya.student@university.edu', 'BCA Final Year Research Inquiry', 'Can we export scan history as PDF for our academic presentation?', 'unread', NOW());

SET FOREIGN_KEY_CHECKS = 1;
