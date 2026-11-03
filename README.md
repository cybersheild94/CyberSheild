# CYBER SHIELD: AI-Based Phishing URL Checker
### A Major Final-Year Bachelor of Computer Applications (BCA) Project

---

## 1. PROJECT OVERVIEW & ABSTRACT
**CYBER SHIELD** is a production-grade, full-stack cybersecurity web application engineered to detect and classify fraudulent phishing URLs before sensitive user credentials or financial assets are compromised. 

Traditional anti-phishing defense models rely on centralized static blacklists (such as Google Safe Browsing or PhishTank). While effective for known threats, blacklists suffer from critical latency windows during "zero-hour" attacks, where adversaries launch automated domain-generation algorithms (DGA) with lifespans under 4 hours. 

CYBER SHIELD addresses this vulnerability through an **AI-inspired, multi-stage heuristic feature classification engine**. Built natively on **PHP 8+** and **MySQL**, the system extracts lexical, structural, statistical, and brand-spoofing vectors directly from an incoming target URL. It scores the target across 10 deterministic risk parameters and outputs a **0–100 Threat Index** with clear classifications (**SAFE**, **SUSPICIOUS**, or **PHISHING**) in under 25 milliseconds.

The project features a modern **Cybersecurity SaaS dark interface** (Dark Navy `#070b14`, Electric Cyan `#00f3ff`, Glassmorphism, and Chart.js dynamic visualizations), complete role-based authentication (Bcrypt hashing, CSRF prevention, PDO prepared statements), an interactive User Security Portal, and an authoritative SOC Administrator Console.

---

## 2. PROBLEM STATEMENT
Phishing attacks account for more than 85% of corporate cyber breaches and consumer credential compromises. Modern threat actors leverage mobile device screen limitations, where truncated address bars hide extensive subdomain chains (e.g., `paypal.com.account-verification-auth.xyz`). Users lack intuitive, instant tools to verify link authenticity before entering authentication credentials.

Furthermore, traditional college final-year projects often provide either static mockups or brittle scripts. CYBER SHIELD provides an authentic, fully functional application suitable for academic demonstration, university viva examination, and real-world deployment on standard LAMP/WAMP stacks.

---

## 3. PROJECT OBJECTIVES
1. **Zero-Latency Threat Detection:** Analyze arbitrary web addresses locally without dependency on rate-limited third-party APIs.
2. **Transparent Explainability:** Avoid "black-box" decisions by detailing precisely *why* a URL was flagged (e.g., missing SSL, brand impersonation, excessive subdomains).
3. **Robust Security Architecture:** Implement defensive programming against OWASP Top 10 vulnerabilities (SQL Injection, XSS, CSRF, session hijacking).
4. **Relational Data Persistence:** Maintain complete forensic audit trails in MySQL for users and administrators.
5. **Interactive Visualization:** Provide dynamic Chart.js dashboards tracking threat landscapes over time.

---

## 4. SYSTEM ARCHITECTURE & DATA FLOW

```
[ Visitor / Authenticated User / SOC Admin ]
                    │
                    ▼ HTTP/HTTPS
        [ Apache Web Server / Nginx ]
                    │
                    ▼
          [ PHP 8+ Application ]
     ┌──────────────┴──────────────┐
     ▼                             ▼
[ Public / UI Layer ]     [ REST API: /api/scan-url.php ]
- Bootstrap 5 / Dark CSS         │
- Vanilla JS & Fetch             ▼
- Chart.js Gauges         [ url-analyzer.php Engine ]
                                 │
                 ┌───────────────┼───────────────┐
                 ▼               ▼               ▼
           [ Lexical ]     [ Structural ]   [ Brand Spoof ]
           - Protocol      - Subdomains     - Lexical Distance
           - Length        - Raw IP check   - Typosquatting
           - Obfuscation   - Suspicious TLD - Credential Terms
                 └───────────────┬───────────────┘
                                 ▼
                     [ 0-100 Threat Index ]
                     [ Classification Engine ]
                                 │
                                 ▼
                    [ MySQL Database (PDO) ]
                    - users, scans, feedback, settings
```

---

## 5. DATABASE SCHEMA (`cyber_shield`)

The relational database consists of 5 normalized InnoDB tables with foreign key constraints:

### 1. `users`
| Column | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED AUTO_INCREMENT | Primary Key |
| `name` | VARCHAR(100) | User full name |
| `email` | VARCHAR(150) UNIQUE | Unique login identifier |
| `password` | VARCHAR(255) | Bcrypt hashed password (`$2y$10$...`) |
| `phone` | VARCHAR(20) | Optional contact phone |
| `role` | ENUM('user', 'admin') | Role-based authorization flag |
| `status` | ENUM('active', 'inactive', 'suspended') | Account access status |
| `profile_image`| VARCHAR(255) | Stored profile picture |
| `created_at` | TIMESTAMP | Registration timestamp |
| `updated_at` | TIMESTAMP | Last profile modification |
| `last_login` | DATETIME | Recent session login |

### 2. `scans`
| Column | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED AUTO_INCREMENT | Primary Key |
| `user_id` | INT UNSIGNED NULL | Foreign Key `users.id` (ON DELETE SET NULL) |
| `url` | TEXT | Target URL analyzed |
| `domain` | VARCHAR(255) | Extracted hostname / domain |
| `protocol` | VARCHAR(10) | `http` or `https` |
| `risk_score` | TINYINT UNSIGNED | Calculated threat index (0–100) |
| `result` | ENUM('SAFE', 'SUSPICIOUS', 'PHISHING') | Threat verdict |
| `url_length` | INT UNSIGNED | Length in characters |
| `has_https` | TINYINT(1) | SSL encryption flag |
| `has_ip` | TINYINT(1) | Raw numeric IP address indicator |
| `suspicious_keywords` | TEXT | Matched credential harvest terms |
| `subdomain_count` | INT UNSIGNED | Hierarchy depth |
| `special_character_count` | INT UNSIGNED | Obfuscation symbol count |
| `analysis_data` | LONGTEXT | JSON payload of raw heuristic flags |
| `reasons` | TEXT | Human-readable explanation of risk |
| `ip_address` | VARCHAR(45) | Client IPv4/IPv6 address of scanner |
| `created_at` | TIMESTAMP | Analysis timestamp |

### 3. `feedback`
Stores inquiries and false-positive reports submitted from `contact.php`.

### 4. `password_resets`
Stores cryptographically random reset tokens (`bin2hex(random_bytes(32))`) with 1-hour expirations.

### 5. `settings`
Key-value store for global platform configurations (engine version, max scans/hour, maintenance mode).

---

## 6. FOLDER STRUCTURE

```text
cyber-shield/
│
├── index.php                 # Public Landing Portal with Live Scanner & Dynamic Counters
├── checker.php               # Interactive URL Phishing Scanner
├── history.php               # Global Threat Intelligence Feed
├── awareness.php             # Cybersecurity Educational Guide & Phishing Anatomy
├── about.php                 # Project Architecture, Objectives & Scope
├── contact.php               # Contact Form with CSRF & DB Storage
├── scan-details.php          # Detailed Forensic Scan Report
│
├── login.php                 # User Sign-In Portal (Bcrypt verification)
├── signup.php                # User Registration Portal
├── logout.php                # Secure Session Cleaner
├── forgot-password.php       # Password Reset Request Generator
├── reset-password.php        # Password Reset Token Consumer
│
├── user/                     # Authenticated User Protected Area
│   ├── dashboard.php         # User Stats, Chart.js Visualizations & Recent Scans
│   ├── profile.php           # User Profile Management & Avatar Upload
│   ├── scans.php             # Paginated & Filterable User Scan History
│   ├── scan-details.php      # User Scoped Scan Inspector
│   ├── change-password.php   # Current Password Verification & Hash Update
│   └── logout.php            # User Logout Bridge
│
├── admin/                    # SOC Administrator Protected Area (role = 'admin')
│   ├── login.php             # Administrator Dedicated Sign In
│   ├── dashboard.php         # Global SOC Command Center & 4 Chart.js Charts
│   ├── users.php             # User CRUD, Activation & Suspension
│   ├── user-details.php      # User Dossier & Associated Audit Logs
│   ├── scans.php             # Global URL Scans Telemetry & Purge
│   ├── scan-details.php      # Admin Detailed Scan Inspector
│   ├── feedback.php          # Inquiries Inbox (Read/Unread/Delete)
│   ├── settings.php          # Dynamic Platform Settings & Weights
│   ├── profile.php           # Master Admin Profile & Password
│   └── logout.php            # Administrator Logout Bridge
│
├── api/                      # Asynchronous REST JSON Endpoints
│   ├── scan-url.php          # Heuristic Execution & MySQL Persistence
│   ├── login.php             # AJAX Sign In Handler
│   ├── signup.php            # AJAX Registration Handler
│   └── feedback.php          # AJAX Feedback Handler
│
├── config/                   # Core Configurations
│   ├── database.php          # PDO MySQL Connection Singleton
│   └── config.php            # Global Constants, Error Handling & Sessions
│
├── includes/                 # Reusable Business Logic & Partials
│   ├── header.php            # HTML5 Head, Fonts, Bootstrap 5 & Chart.js
│   ├── navbar.php            # Role-Aware Navigation Bar
│   ├── footer.php            # Academic Disclaimers & Script Inclusions
│   ├── functions.php         # Formatting, Flash Alerts & Dynamic Stats
│   ├── auth.php              # Session Auth Functions & Current User
│   ├── user-auth.php         # requireUser() Access Control Guard
│   ├── admin-auth.php        # requireAdmin() Access Control Guard
│   ├── security.php          # CSRF Tokens, XSS Sanitization & Bcrypt
│   └── url-analyzer.php      # AI-Inspired Heuristic Classification Engine
│
├── assets/                   # Frontend Static Assets
│   ├── css/
│   │   ├── style.css         # Dark Navy Cyberpunk Theme & Components
│   │   ├── auth.css          # Auth Form Cards & Strength Meter
│   │   ├── dashboard.css     # Metric Cards & Layout
│   │   └── admin.css         # SOC Sidebar & Admin Grid
│   │
│   ├── js/
│   │   ├── script.js         # Bootstrap Initializers & Auto-Dismiss Alerts
│   │   ├── checker.js        # Async AJAX Scanner with Gauge Renderer
│   │   ├── auth.js           # Password Complexity & Validation
│   │   └── dashboard.js      # Dynamic Chart.js Initializers
│   │
│   └── images/
│       └── shield-icon.svg   # Vector Shield Branding
│
├── database/
│   └── cyber_shield.sql      # Complete Schema, Tables & Demo Seed Records
│
└── README.md                 # Project Documentation & Viva Examination Guide
```

---

## 7. HEURISTIC RISK SCORING ALGORITHM

The core classification function is `analyzeURL($url)` located in `includes/url-analyzer.php`. It calculates an aggregate risk score (0–100) using weighted threat vectors:

```
Total Risk Score = Σ (Vector Weights)
Score Range: 0 (Maximum Confidence Safe) to 100 (Critical Malicious Threat)
```

| Threat Vector | Condition / Detection Heuristic | Score Impact |
|---|---|---|
| **Protocol / SSL** | Missing HTTPS (plain `http://`) | +10 |
| **Numeric Host (IP)** | Host is IPv4 (e.g. `192.168.1.1`) instead of registered FQDN | +25 |
| **URL Length** | Length > 75 characters (obfuscation through payload padding) | +15 |
| **URL Length** | Length between 55 and 74 characters | +8 |
| **Phishing Keywords** | Presence of terms: `login`, `verify`, `account`, `password`, `banking`, `wallet`, `secure`, `update` | +8 to +35 (capped) |
| **Brand Impersonation**| Targets like `paypal`, `apple`, `google`, `netflix` in subdomains/path on non-official host | +25 |
| **Subdomain Hierarchy**| $\ge 3$ subdomain levels (e.g., `a.b.c.target.com`) | +20 |
| **Subdomain Hierarchy**| Exactly 2 subdomain levels | +10 |
| **Special Characters** | Contains `@` symbol (browser credential mask trick) | +25 |
| **Domain Obscuration** | High hyphen count ($\ge 3$ hyphens in domain name) | +15 |
| **Redirect Slashes** | Secondary double slashes in path (`//`) | +20 |
| **URL Shorteners** | Known shortener domain (`bit.ly`, `tinyurl.com`, `t.co`, etc.) | +20 |
| **Suspicious TLDs** | Abused extensions (`.xyz`, `.top`, `.click`, `.buzz`, `.tk`, etc.) | +15 |
| **Reputable Whitelist**| Reputable verified domains (`google.com`, `github.com`, `gov.in`, etc.) | -30 (Mitigation) |

### Final Verdict Boundaries:
* **0 to 29:** `SAFE` (Green) - Minimal or zero anomalous indicators.
* **30 to 59:** `SUSPICIOUS` (Amber) - Multiple anomalous flags; caution required.
* **60 to 100:** `PHISHING` (Red) - Critical credential harvesting or deceptive pattern detected.

---

## 8. STEP-BY-STEP INSTALLATION GUIDE (XAMPP / WAMP)

### Prerequisites:
* **PHP:** Version 8.0 or higher (with `pdo_mysql` extension enabled).
* **MySQL:** Version 5.7+ or MariaDB 10.4+.
* **Web Server:** Apache (standard in XAMPP/WAMP).

### Setup Instructions:
1. **Copy Files to Web Directory:**
   * In **XAMPP (Windows):** Copy the `cyber-shield` folder to `C:\xampp\htdocs\cyber-shield`.
   * In **WAMP (Windows):** Copy to `C:\wamp64\www\cyber-shield`.
   * In **Linux / Apache:** Copy to `/var/www/html/cyber-shield`.
2. **Start Services:**
   * Open the XAMPP Control Panel and start **Apache** and **MySQL**.
3. **Import Database:**
   * Open your browser and navigate to `http://localhost/phpmyadmin`.
   * Click the **Import** tab.
   * Click **Choose File** and select `cyber-shield/database/cyber_shield.sql`.
   * Click **Go**. The database `cyber_shield` and its 5 tables will be created and populated.
4. **Configure Database Credentials (If Changed):**
   * By default, XAMPP uses host `127.0.0.1`, user `root`, and an empty password `""`.
   * If your MySQL server has a password, update `config/database.php`:
     ```php
     private string $username = 'root';
     private string $password = 'your_mysql_password';
     ```
5. **Access Application:**
   * Open `http://localhost/cyber-shield` in your browser.

---

## 9. DEFAULT CREDENTIALS FOR TESTING & VIVA DEMO

| Role | Email Address | Password | Destination Portal |
|---|---|---|---|
| **Administrator** | `admin@cybershield.local` | `Admin@CyberShield2026` | `/admin/dashboard.php` |
| **User** | `user@cybershield.local` | `Admin@CyberShield2026` | `/user/dashboard.php` |

*(Note: In a live production rollout, credentials would be altered and environment variables used. These demo accounts are pre-seeded in `database/cyber_shield.sql` using standard Bcrypt hashes for evaluation).*

---

## 10. 40+ BCA VIVA EXAMINATION QUESTIONS & ANSWERS

### CATEGORY A: PHP & BACKEND ARCHITECTURE
1. **Q: Why was PHP 8+ selected over legacy PHP 7 or Python for this BCA project?**
   *A:* PHP 8+ offers significant performance enhancements (JIT compilation), strict typing declarations (`declare(strict_types=1)`), match expressions, nullsafe operators, and runs natively across standard XAMPP/WAMP environments without heavy runtime dependency managers.
2. **Q: What is PDO and why is it preferred over `mysqli`?**
   *A:* PDO (PHP Data Objects) is a database abstraction layer providing uniform access to multiple RDBMS engines (MySQL, PostgreSQL, SQLite). It natively supports prepared statements with named parameters, protecting against SQL injection.
3. **Q: How does `session_regenerate_id(true)` protect users during login?**
   *A:* It generates a brand-new session ID while deleting the old session token upon authentication, thwarting Session Fixation attacks where an attacker pre-seeds a known session cookie.
4. **Q: How does the application handle CSRF (Cross-Site Request Forgery)?**
   *A:* Every form and AJAX POST request includes a cryptographically secure random token (`bin2hex(random_bytes(32))`) stored in `$_SESSION['csrf_token']`. The server validates it using `hash_equals()` before processing mutations.
5. **Q: Why use `password_hash()` with `PASSWORD_BCRYPT` instead of `md5()` or `sha256()`?**
   *A:* MD5 and SHA256 are fast hash functions vulnerable to rainbow tables and brute-force attacks. Bcrypt is a slow, salted, adaptive key-derivation function that deliberately slows down attackers.
6. **Q: What does `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')` accomplish?**
   *A:* It converts special characters (like `<`, `>`, `&`, `"`, `'`) to HTML entities, neutralizing Reflected and Stored Cross-Site Scripting (XSS) by preventing arbitrary browser JavaScript execution.
7. **Q: How does the password reset system work without an active mail server in XAMPP?**
   *A:* It generates a secure one-time token in `password_resets` with a 1-hour expiration. For academic viva demonstration, the generated reset link is displayed directly in development mode, while the architecture is structured to attach `mail()` or PHPMailer via SMTP.
8. **Q: What is the difference between `require_once` and `include_once`?**
   *A:* `require_once` generates a fatal error (`E_COMPILE_ERROR`) and stops script execution if the file is missing, which is critical for configurations and security helpers. `include_once` generates only a warning (`E_WARNING`).
9. **Q: What is strict typing in PHP (`declare(strict_types=1)`)?**
   *A:* It forces PHP to enforce exact scalar type hints (strings, integers, booleans, arrays) on function arguments and return types, preventing unexpected type coercion errors.
10. **Q: How is role-based access control (RBAC) enforced?**
    *A:* Using authorization helper middleware (`requireUser()` in `includes/user-auth.php` and `requireAdmin()` in `includes/admin-auth.php`). Any unauthorized attempt by a regular user to access `/admin/*` results in a 403 Forbidden redirect.

### CATEGORY B: MYSQL & DATABASE DESIGN
11. **Q: Why is InnoDB chosen as the MySQL storage engine over MyISAM?**
    *A:* InnoDB provides ACID transaction compliance, row-level locking, and real foreign key constraint validation with cascade rules (`ON DELETE SET NULL` / `ON DELETE CASCADE`), unlike MyISAM.
12. **Q: Explain the database relationship between `users` and `scans`.**
    *A:* It is a One-to-Many relationship (1 user can have multiple scan records). `scans.user_id` is a foreign key referencing `users.id`.
13. **Q: Why is `user_id` allowed to be `NULL` in the `scans` table?**
    *A:* To support public anonymous scanning while preserving scan telemetry, and to keep forensic records intact even if a user account is deleted (`ON DELETE SET NULL`).
14. **Q: Why are database indexes added on `email`, `result`, and `risk_score`?**
    *A:* Indexes create B-Tree lookup tables that transform $O(n)$ full table scans into $O(\log n)$ logarithmic lookups, drastically speeding up queries as records grow.
15. **Q: What charset and collation are used in MySQL and why?**
    *A:* `utf8mb4` with `utf8mb4_unicode_ci`. Standard `utf8` in MySQL only supports up to 3 bytes; `utf8mb4` supports full 4-byte Unicode (including international characters and emojis).
16. **Q: How does `ON DUPLICATE KEY UPDATE` work in `settings.php`?**
    *A:* If a record with the same unique `setting_key` already exists, MySQL performs an `UPDATE` rather than throwing a duplicate key error, implementing an idempotent "upsert" pattern.

### CATEGORY C: FRONTEND & JAVASCRIPT
17. **Q: What is AJAX and how is it used in Cyber Shield?**
    *A:* Asynchronous JavaScript and XML (implemented via the modern `Fetch API`). It sends the target URL to `api/scan-url.php` in the background, parses JSON results, and dynamically updates the DOM without reloading the page.
18. **Q: Why use Chart.js for data visualization?**
    *A:* Chart.js renders lightweight HTML5 canvas graphs that are responsive, animated, and capable of displaying data directly from MySQL (daily scans, threat percentages, and user growth).
19. **Q: What is Glassmorphism in CSS?**
    *A:* A UI trend featuring semi-transparent backgrounds with backdrop blur (`backdrop-filter: blur(16px)`), subtle borders, and glowing accents simulating frosted glass.
20. **Q: How does the client-side password strength meter work?**
    *A:* It uses regular expressions in JavaScript to evaluate character length, uppercase letters, digits, and special characters, dynamically updating a color-coded strength bar before form submission.

### CATEGORY D: CYBERSECURITY & PHISHING HEURISTICS
21. **Q: What is a zero-hour phishing attack?**
    *A:* A newly registered malicious website that has not yet been analyzed or reported to global blacklists, allowing attackers a window of opportunity to steal credentials.
22. **Q: How does Cyber Shield detect deceptive subdomains?**
    *A:* Attackers often craft URLs like `https://paypal.com.account-verify.click`. The engine parses the hostname, counts subdomains, and evaluates the true authoritative domain (`account-verify.click`) rather than the leading brand prefix.
23. **Q: Why does a numeric IP address in a URL indicate high risk?**
    *A:* Legitimate consumer websites use registered domain names with valid SSL certificates. Phishers use raw IP addresses (`http://192.168.1.10/login`) to bypass domain registrar scrutiny and WHOIS identity verification.
24. **Q: What is typosquatting?**
    *A:* Registering domains that closely resemble popular brand names with common typographical errors (e.g., `paypa1.com` or `rnicrosoft.com`) to mislead users.
25. **Q: Why does missing HTTPS contribute to the risk score?**
    *A:* Credential transmission over plain HTTP is unencrypted and vulnerable to eavesdropping and man-in-the-middle (MITM) attacks. Legitimate authentication portals mandate HTTPS.
26. **Q: Why are URL shorteners considered suspicious in security contexts?**
    *A:* Shorteners (like `bit.ly`) obscure the ultimate destination URL, preventing users from seeing the true target host before clicking.
27. **Q: What is the `@` symbol trick in phishing URLs?**
    *A:* In URI specifications, text before an `@` symbol is treated as user authentication info, while text after the `@` is the real host. For example: `http://google.com@evil-site.com` actually navigates to `evil-site.com`.
28. **Q: How does the engine distinguish between safe and phishing sites?**
    *A:* By calculating an aggregate weighted score across 10 heuristics. Scores from 0–29 are classified as `SAFE`, 30–59 as `SUSPICIOUS`, and 60–100 as `PHISHING`.
29. **Q: Is Cyber Shield using a trained Deep Learning model?**
    *A:* No. Cyber Shield utilizes an **AI-inspired intelligent rule-based heuristic classifier**. This is explicitly designed for transparent explainability, low server overhead, and deterministic viva defense.
30. **Q: How can machine learning be integrated into this architecture in the future?**
    *A:* The 10 heuristic parameters currently extracted by `url-analyzer.php` can serve as a numerical feature vector input to a trained Random Forest or XGBoost model exported to ONNX or wrapped in a Python microservice.

### CATEGORY E: TESTING & PROJECT DEFENSE
31. **Q: What forms of software testing were performed on this project?**
    *A:* Unit testing on heuristic scoring rules, integration testing between AJAX and PHP endpoints, boundary-value testing on URL lengths and inputs, and security penetration testing against SQL injection and XSS.
32. **Q: How were SQL injection vulnerabilities tested?**
    *A:* By injecting payloads like `' OR '1'='1` and `'; DROP TABLE users; --` into login fields and search bars, confirming that PDO prepared statements parameterized them safely as literal strings.
33. **Q: How was XSS tested?**
    *A:* By submitting `<script>alert('xss')</script>` into contact form inputs and verifying that `sanitizeInput()` rendered them harmlessly via HTML entity encoding.
34. **Q: What are the current limitations of Cyber Shield?**
    *A:* It analyzes lexical and structural characteristics of the URL itself. It does not fetch and render the live DOM, execute JavaScript payloads, or inspect SSL certificate transparency logs.
35. **Q: What is the purpose of the Threat Intelligence Feed (`history.php`)?**
    *A:* It acts as a community detection log, displaying real-time scan telemetry stored in MySQL to demonstrate data persistence and multi-user capabilities.
36. **Q: What happens if an admin tries to delete their own account in the admin panel?**
    *A:* The system includes a safeguards check: `if ($targetUserId === $currentAdmin['id'])`, which blocks the operation and displays a security notification to prevent accidental admin lockout.
37. **Q: How does the application prevent brute-force login attacks?**
    *A:* By implementing session regeneration, rate limiting options in system settings, and Bcrypt password verification.
38. **Q: What is Smishing?**
    *A:* SMS-based phishing where attackers send deceptive text messages with urgent links (e.g., fake courier delivery fees or bank account lockouts).
39. **Q: Why is mobile phishing more dangerous than desktop phishing?**
    *A:* Mobile browsers truncate long URLs due to narrow viewports, hiding malicious domains placed after long subdomain chains.
40. **Q: What makes this project suitable for a BCA final year evaluation?**
    *A:* It demonstrates comprehensive full-stack competencies: relational database design, clean procedural and OOP PHP 8+, secure authentication, AJAX asynchronous communication, data visualization, cybersecurity threat modeling, and defensive coding standards.

---

## 11. 2-MINUTE VIVA PRESENTATION SCRIPT (FOR BCA STUDENTS)

> *"Good morning/afternoon, Respected Examiners.*
>
> *I am pleased to present my BCA Major Final-Year Project titled **CYBER SHIELD: AI-Based Phishing URL Checker**.*
>
> *Phishing remains the single most common entry point for cyber attacks worldwide. While traditional solutions rely on static blacklists, these blacklists fail against zero-hour attacks because modern phishing domains are created and discarded in just a few hours.*
>
> *To solve this problem, I built **CYBER SHIELD**, a real-time, rule-based phishing detection platform developed using **PHP 8+, MySQL, Bootstrap 5, JavaScript, and Chart.js**.*
>
> *When a user inputs a suspicious link, our heuristic engine parses the URL within milliseconds, extracting 10 lexical and structural risk vectors—including HTTPS protocol validation, raw IP host detection, URL length metrics, credential harvesting keywords, excessive subdomain depth, and brand impersonation patterns.*
>
> *The engine computes a deterministic Threat Index from 0 to 100 and classifies the target as **SAFE**, **SUSPICIOUS**, or **PHISHING**, providing the user with a transparent breakdown of why the link was flagged.*
>
> *The system features full **Role-Based Access Control**: registered users can access personal dashboards, track their scan history, and view forensic reports. Administrators can oversee global telemetry, manage accounts, monitor unread feedback, and dynamically configure heuristic threshold weights stored in MySQL.*
>
> *On the security front, the codebase implements **PDO prepared statements** to eliminate SQL Injection, **htmlspecialchars** against Cross-Site Scripting, **CSRF protection tokens**, and **Bcrypt password hashing**.*
>
> *CYBER SHIELD is fully responsive, runs effortlessly on standard XAMPP or Apache servers, and bridges academic software engineering with modern cybersecurity defense.*
>
> *I invite you to test our live scanner and review our system architecture. Thank you."*

---

## 12. LICENSE & ACADEMIC INTEGRITY
Developed as an academic demonstration for the Bachelor of Computer Applications (BCA) curriculum. Free for educational reference, research evaluation, and viva defense.
