export interface ProjectFile {
  path: string;
  name: string;
  category: 'core' | 'config' | 'includes' | 'api' | 'user' | 'admin' | 'assets' | 'database' | 'docs';
  description: string;
  language: string;
  codeSnippet?: string;
}

export const PROJECT_FILES: ProjectFile[] = [
  {
    path: 'database/cyber_shield.sql',
    name: 'cyber_shield.sql',
    category: 'database',
    description: 'Complete MySQL schema with users, scans, feedback, password_resets, and settings tables plus seed records',
    language: 'sql'
  },
  {
    path: 'config/database.php',
    name: 'database.php',
    category: 'config',
    description: 'PDO database connection class with singleton pattern, UTF8mb4, and error handling',
    language: 'php'
  },
  {
    path: 'config/config.php',
    name: 'config.php',
    category: 'config',
    description: 'Application constants, timezone, dynamic root path detection, and session initialization',
    language: 'php'
  },
  {
    path: 'includes/security.php',
    name: 'security.php',
    category: 'includes',
    description: 'CSRF token generator & validator, XSS htmlspecialchars sanitizer, Bcrypt password hashing, and client IP resolver',
    language: 'php'
  },
  {
    path: 'includes/functions.php',
    name: 'functions.php',
    category: 'includes',
    description: 'Flash notifications, dynamic MySQL telemetry statistics, and threat badge renderers',
    language: 'php'
  },
  {
    path: 'includes/auth.php',
    name: 'auth.php',
    category: 'includes',
    description: 'Session authentication helpers, loginUser, logoutUser, and role guards',
    language: 'php'
  },
  {
    path: 'includes/user-auth.php',
    name: 'user-auth.php',
    category: 'includes',
    description: 'requireUser() access control middleware protecting member routes',
    language: 'php'
  },
  {
    path: 'includes/admin-auth.php',
    name: 'admin-auth.php',
    category: 'includes',
    description: 'requireAdmin() access control middleware enforcing administrative privileges',
    language: 'php'
  },
  {
    path: 'includes/url-analyzer.php',
    name: 'url-analyzer.php',
    category: 'includes',
    description: 'AI-inspired intelligent rule-based phishing detection engine with 10 heuristic scoring vectors',
    language: 'php'
  },
  {
    path: 'includes/header.php',
    name: 'header.php',
    category: 'includes',
    description: 'Global HTML5 header with Bootstrap 5, FontAwesome, Chart.js, and dark theme links',
    language: 'php'
  },
  {
    path: 'includes/navbar.php',
    name: 'navbar.php',
    category: 'includes',
    description: 'Role-aware navigation bar dynamically reflecting guest, user, and admin sessions',
    language: 'php'
  },
  {
    path: 'includes/footer.php',
    name: 'footer.php',
    category: 'includes',
    description: 'Global footer with academic viva disclaimers, system status indicator, and scripts',
    language: 'php'
  },
  {
    path: 'api/scan-url.php',
    name: 'scan-url.php',
    category: 'api',
    description: 'Asynchronous AJAX endpoint executing url-analyzer.php and persisting scan records to MySQL',
    language: 'php'
  },
  {
    path: 'api/login.php',
    name: 'login.php',
    category: 'api',
    description: 'Asynchronous login endpoint with JSON response and password_verify()',
    language: 'php'
  },
  {
    path: 'api/signup.php',
    name: 'signup.php',
    category: 'api',
    description: 'Asynchronous registration endpoint with email uniqueness validation and password hashing',
    language: 'php'
  },
  {
    path: 'api/feedback.php',
    name: 'feedback.php',
    category: 'api',
    description: 'Asynchronous contact form endpoint saving inquiries to the feedback table',
    language: 'php'
  },
  {
    path: 'index.php',
    name: 'index.php',
    category: 'core',
    description: 'Public landing portal with real-time scanner input, dynamic MySQL statistics, and threat cards',
    language: 'php'
  },
  {
    path: 'checker.php',
    name: 'checker.php',
    category: 'core',
    description: 'Main interactive URL phishing scanner with real-time heuristic gauge and test samples',
    language: 'php'
  },
  {
    path: 'history.php',
    name: 'history.php',
    category: 'core',
    description: 'Community threat intelligence feed displaying recent public scan telemetry',
    language: 'php'
  },
  {
    path: 'awareness.php',
    name: 'awareness.php',
    category: 'core',
    description: 'Cybersecurity educational guide covering phishing types, smishing, and domain spoofing',
    language: 'php'
  },
  {
    path: 'about.php',
    name: 'about.php',
    category: 'core',
    description: 'Comprehensive BCA final-year project report: Problem statement, pipeline, and future ML scope',
    language: 'php'
  },
  {
    path: 'contact.php',
    name: 'contact.php',
    category: 'core',
    description: 'Contact form with CSRF protection, user autofill, and MySQL persistence',
    language: 'php'
  },
  {
    path: 'scan-details.php',
    name: 'scan-details.php',
    category: 'core',
    description: 'Deep forensic scan report detailing lexical attributes, triggered heuristic flags, and client audit info',
    language: 'php'
  },
  {
    path: 'login.php',
    name: 'login.php',
    category: 'core',
    description: 'User sign-in portal with Bcrypt verification and pre-filled demo accounts for viva testing',
    language: 'php'
  },
  {
    path: 'signup.php',
    name: 'signup.php',
    category: 'core',
    description: 'User registration portal with client-side password strength meter and duplicate check',
    language: 'php'
  },
  {
    path: 'forgot-password.php',
    name: 'forgot-password.php',
    category: 'core',
    description: 'Password recovery generator with token creation and development reset link display',
    language: 'php'
  },
  {
    path: 'reset-password.php',
    name: 'reset-password.php',
    category: 'core',
    description: 'Password reset token consumer verifying expiration and hashing the new password',
    language: 'php'
  },
  {
    path: 'logout.php',
    name: 'logout.php',
    category: 'core',
    description: 'Session termination destroying authentication cookies and redirecting to login',
    language: 'php'
  },
  {
    path: 'user/dashboard.php',
    name: 'dashboard.php (User)',
    category: 'user',
    description: 'Member security dashboard featuring Chart.js threat distribution and recent scan history',
    language: 'php'
  },
  {
    path: 'user/profile.php',
    name: 'profile.php (User)',
    category: 'user',
    description: 'User profile management with avatar upload and account activity statistics',
    language: 'php'
  },
  {
    path: 'user/scans.php',
    name: 'scans.php (User)',
    category: 'user',
    description: 'Paginated, filterable, and searchable personal scan history isolated to user_id',
    language: 'php'
  },
  {
    path: 'user/change-password.php',
    name: 'change-password.php (User)',
    category: 'user',
    description: 'Password update interface with current password verification via password_verify()',
    language: 'php'
  },
  {
    path: 'admin/login.php',
    name: 'login.php (Admin)',
    category: 'admin',
    description: 'Dedicated SOC administrator authentication portal with role-based restriction',
    language: 'php'
  },
  {
    path: 'admin/dashboard.php',
    name: 'dashboard.php (Admin)',
    category: 'admin',
    description: 'SOC Command Center with 6 dynamic metric cards and 4 real-time Chart.js visualizers',
    language: 'php'
  },
  {
    path: 'admin/users.php',
    name: 'users.php (Admin)',
    category: 'admin',
    description: 'User management table with activation, suspension, deletion, and self-lockout prevention',
    language: 'php'
  },
  {
    path: 'admin/scans.php',
    name: 'scans.php (Admin)',
    category: 'admin',
    description: 'Global URL scan telemetry table with search, classification filter, and record purging',
    language: 'php'
  },
  {
    path: 'admin/feedback.php',
    name: 'feedback.php (Admin)',
    category: 'admin',
    description: 'Feedback inbox for user contact inquiries with read/unread toggle and delete actions',
    language: 'php'
  },
  {
    path: 'admin/settings.php',
    name: 'settings.php (Admin)',
    category: 'admin',
    description: 'Dynamic platform configuration interface editing MySQL settings table directly',
    language: 'php'
  },
  {
    path: 'admin/profile.php',
    name: 'profile.php (Admin)',
    category: 'admin',
    description: 'Administrator credential management with password update options',
    language: 'php'
  },
  {
    path: 'assets/css/style.css',
    name: 'style.css',
    category: 'assets',
    description: 'Main stylesheet featuring dark navy cyberpunk aesthetics, glassmorphism, and cyber grid',
    language: 'css'
  },
  {
    path: 'assets/js/checker.js',
    name: 'checker.js',
    category: 'assets',
    description: 'Frontend AJAX controller querying scan-url.php and rendering animated risk gauges',
    language: 'javascript'
  },
  {
    path: 'README.md',
    name: 'README.md',
    category: 'docs',
    description: 'Comprehensive BCA Project Report specification, XAMPP setup, and 40+ viva questions with 2-minute speech',
    language: 'markdown'
  }
];
