import { VivaQuestion } from '../types';

export const VIVA_QUESTIONS: VivaQuestion[] = [
  {
    id: 1,
    category: 'PHP & Backend',
    question: 'Why was PHP 8+ selected over legacy PHP 7 for this BCA project?',
    answer: 'PHP 8+ introduces strict typing declarations (`declare(strict_types=1)`), union types, match expressions, JIT performance optimization, and robust error handling while maintaining native compatibility with standard college environments like XAMPP and WAMP without external runtime dependencies.',
    quickTip: 'Mention strict typing and native XAMPP compatibility.'
  },
  {
    id: 2,
    category: 'MySQL & Database',
    question: 'What is PDO and why is it preferred over mysqli or old mysql functions?',
    answer: 'PDO (PHP Data Objects) is a lightweight, consistent database abstraction interface. It supports prepared statements with named and positional parameters, effectively eliminating SQL Injection vulnerabilities, and can switch underlying database engines (e.g., MySQL, PostgreSQL, SQLite) without rewriting queries.',
    quickTip: 'Highlight security against SQL Injection via prepared statements.'
  },
  {
    id: 3,
    category: 'Security & Heuristics',
    question: 'How does session_regenerate_id(true) mitigate session hijacking and fixation?',
    answer: 'Session fixation occurs when an attacker forces a victim to use a known session identifier. By calling session_regenerate_id(true) upon successful authentication, PHP creates a brand-new cryptographically secure session ID and purges the old one from storage.',
    quickTip: 'Explain that the old session token is invalidated upon login.'
  },
  {
    id: 4,
    category: 'Security & Heuristics',
    question: 'How does Cyber Shield prevent Cross-Site Request Forgery (CSRF)?',
    answer: 'A cryptographically random token is generated using bin2hex(random_bytes(32)) and stored in the user session. All state-modifying requests (POST/PUT/DELETE) must send this token via form field or HTTP header, which the backend verifies using constant-time hash_equals().',
    quickTip: 'Remember: bin2hex(random_bytes(32)) and hash_equals().'
  },
  {
    id: 5,
    category: 'Security & Heuristics',
    question: 'Why is password_hash() with PASSWORD_BCRYPT preferred over MD5 or SHA256?',
    answer: 'MD5 and SHA256 are fast hash algorithms designed for message integrity, making them susceptible to rapid GPU brute-force cracking and rainbow table lookups. Bcrypt is an intentionally slow, salted, adaptive key-derivation function that incorporates random salting automatically.',
    quickTip: 'Mention automatic salting and GPU-resistant computational work factor.'
  },
  {
    id: 6,
    category: 'Security & Heuristics',
    question: 'What is the role of htmlspecialchars($str, ENT_QUOTES, "UTF-8") in defensive coding?',
    answer: 'It converts HTML control characters (&, <, >, ", \') into harmless HTML entity equivalents (&amp;, &lt;, &gt;, &quot;, &#039;), preventing Cross-Site Scripting (XSS) by neutralizing injected script execution.',
    quickTip: 'Key phrase: Converts characters into HTML entities to stop XSS.'
  },
  {
    id: 7,
    category: 'Security & Heuristics',
    question: 'What is a zero-hour phishing attack?',
    answer: 'A zero-hour phishing attack involves a newly minted domain created and deployed within minutes. Traditional blacklists (like Google Safe Browsing or PhishTank) have a reporting lag of 4-24 hours. Cyber Shield analyzes lexical heuristics in real-time without relying on external blacklists.',
    quickTip: 'Emphasize the latency gap of static blacklists.'
  },
  {
    id: 8,
    category: 'Security & Heuristics',
    question: 'How does the heuristic analyzer evaluate subdomains?',
    answer: 'Attackers craft deceptive hostnames such as paypal.com.account-verify.click to mislead mobile browsers. The analyzer extracts the true authoritative host (account-verify.click) and penalizes URLs with 3 or more subdomain levels (+20 risk).',
    quickTip: 'Subdomains deceive mobile address bars; 3+ levels = +20 risk.'
  },
  {
    id: 9,
    category: 'Security & Heuristics',
    question: 'Why is a numeric IP address in a URL considered high risk (+25)?',
    answer: 'Legitimate public web portals register domains with DNS hostnames and SSL certificates. Attackers use raw IP addresses (e.g. http://192.168.1.10/login) to bypass domain registrar identity verification and WHOIS tracking.',
    quickTip: 'Raw IPs bypass domain registry KYC and WHOIS checks.'
  },
  {
    id: 10,
    category: 'Security & Heuristics',
    question: 'What is typosquatting and how does Cyber Shield detect it?',
    answer: 'Typosquatting is the malicious registration of common misspellings or lookalikes of popular domains (e.g., paypa1.com, rnicrosoft.com). The engine checks for brand keyword matches outside official authoritative domains and flags high-risk combinations.',
    quickTip: 'Brand keyword present in non-official domain = Brand Impersonation (+25).'
  },
  {
    id: 11,
    category: 'MySQL & Database',
    question: 'Why choose the InnoDB engine instead of MyISAM in MySQL?',
    answer: 'InnoDB supports ACID transactions, foreign key constraints (CASCADE / SET NULL), row-level locking for concurrent writes, and automatic crash recovery, whereas MyISAM lacks foreign keys and only provides table-level locking.',
    quickTip: 'ACID transactions and real foreign key enforcement.'
  },
  {
    id: 12,
    category: 'MySQL & Database',
    question: 'Explain the relationship between the users and scans tables.',
    answer: 'It is a One-to-Many relationship. A registered user can perform multiple scans. The scans.user_id field references users.id with ON DELETE SET NULL, ensuring forensic records remain available for administrator audit even if an account is deleted.',
    quickTip: '1:N relationship with ON DELETE SET NULL constraint.'
  },
  {
    id: 13,
    category: 'Frontend & JS',
    question: 'What is the advantage of using the Fetch API / AJAX for scanning URLs?',
    answer: 'It enables asynchronous request processing: the user submits a URL, and the browser communicates with api/scan-url.php in the background without refreshing the page, providing instant visual feedback via a dynamic threat index gauge.',
    quickTip: 'Asynchronous updates without whole-page refresh.'
  },
  {
    id: 14,
    category: 'Security & Heuristics',
    question: 'Is Cyber Shield using a deep learning neural network or an expert heuristic rule engine?',
    answer: 'Cyber Shield utilizes an AI-inspired intelligent rule-based heuristic classification system. This ensures deterministic transparency (explainable AI), ultra-low latency (<25ms), and reliable viva defense without requiring heavy GPU inference infrastructure.',
    quickTip: 'Be honest and proud: Intelligent rule-based heuristics with explainability.'
  },
  {
    id: 15,
    category: 'Testing & Defense',
    question: 'How can this rule-based system be expanded into a Machine Learning pipeline in future work?',
    answer: 'The 10 heuristic features currently extracted (protocol, raw IP, length, keyword weights, subdomain count, special characters, TLD reputation) can serve as a numerical feature vector to train an XGBoost or Random Forest classifier on the Kaggle Phishing dataset.',
    quickTip: 'Mention converting current heuristics into a 10-dimensional feature vector.'
  },
  {
    id: 16,
    category: 'PHP & Backend',
    question: 'How is Role-Based Access Control (RBAC) enforced on the server?',
    answer: 'Through middleware functions requireUser() and requireAdmin(). The server checks session parameters ($_SESSION[\'user_role\']). If a normal user accesses an admin endpoint, the server responds with 403 Forbidden and redirects.',
    quickTip: 'Session verification via requireAdmin() guard.'
  },
  {
    id: 17,
    category: 'PHP & Backend',
    question: 'What prevents an administrator from accidentally deleting their own account in admin/users.php?',
    answer: 'A defensive guard check ($targetUserId === $currentAdmin[\'id\']) intercepts the POST action and halts execution with a flash error notification, preventing admin lockout.',
    quickTip: 'Server checks if target ID matches current session admin ID.'
  },
  {
    id: 18,
    category: 'Security & Heuristics',
    question: 'What is the risk score classification breakdown?',
    answer: '0 to 29 indicates SAFE (Green), 30 to 59 indicates SUSPICIOUS (Amber/Yellow), and 60 to 100 indicates PHISHING (Red/Critical).',
    quickTip: '0-29 Safe, 30-59 Suspicious, 60-100 Phishing.'
  },
  {
    id: 19,
    category: 'Security & Heuristics',
    question: 'What is the "@" symbol exploit in URL syntax?',
    answer: 'RFC 3986 allows user authentication before an @ sign (e.g., http://google.com@evil.com). Web browsers connect to evil.com while casual users assume they are visiting google.com. The engine flags any @ symbol with +25 risk.',
    quickTip: 'User authentication prefix trick to disguise true host.'
  },
  {
    id: 20,
    category: 'Security & Heuristics',
    question: 'Why are common URL shorteners flagged (+20 risk)?',
    answer: 'Shorteners like bit.ly or tinyurl.com mask the true destination domain, preventing client-side inspection before navigation. Cyber Shield flags shortened links so users inspect them with caution.',
    quickTip: 'Shorteners obscure destination domain identity.'
  }
];

export const VIVA_SCRIPT_2_MINUTES = `
"Respected Examiners,

I am pleased to present my BCA Major Final-Year Project: CYBER SHIELD - AI-Based Phishing URL Checker.

THE PROBLEM:
Phishing remains the primary vector for credential theft and financial cyber fraud. While traditional security relies on centralized blacklists, these blacklists suffer from critical latency windows of 4 to 24 hours during zero-hour attacks, where attackers deploy automated disposable domains.

OUR SOLUTION:
To solve this, I designed and developed CYBER SHIELD: an intelligent real-time phishing detection web application built natively with PHP 8+, MySQL, Bootstrap 5, JavaScript, and Chart.js.

HOW IT WORKS:
When an address is submitted, our heuristic engine parses the target in milliseconds across 10 lexical, structural, and brand impersonation vectors:
1. Protocol encryption (HTTPS vs unencrypted HTTP)
2. Host resolution (Raw numeric IP vs registered domain)
3. URL character length and path padding
4. Credential-harvesting keyword density (login, verify, banking, update)
5. Brand impersonation and typosquatting (PayPal, Netflix, Apple clones)
6. Subdomain hierarchy depth (detecting mobile viewport deception)
7. Character obfuscation (such as the '@' symbol trick and excessive hyphens)
8. Abused top-level domains (.xyz, .top, .click)
9. URL shorteners (masking destinations)
10. Whitelist reputation mitigations.

The engine computes a deterministic Threat Index from 0 to 100, classifying the link as SAFE, SUSPICIOUS, or PHISHING, and generates a detailed report explaining why the score was assigned.

ARCHITECTURE & SECURITY:
The project implements robust Role-Based Access Control (RBAC):
- Regular users have a dedicated dashboard, personal scan history, and profile management.
- SOC Administrators oversee global scan telemetry, manage users, review feedback, and dynamically configure heuristic weights in MySQL.
- Security controls include PDO prepared statements against SQL Injection, htmlspecialchars against XSS, cryptographically secure CSRF tokens, and Bcrypt password hashing.

The application runs smoothly on standard XAMPP or Apache servers and provides a complete foundation for future Machine Learning integration using Random Forest classifiers.

I welcome your questions and invite you to inspect the live scanner and codebase. Thank you."
`;
