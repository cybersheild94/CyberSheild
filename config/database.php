<?php
/**
 * CYBER SHIELD - Database Connection (PDO)
 * File: config/database.php
 * BCA Final Year Project
 */

declare(strict_types=1);

class Database {
    private string $host = '127.0.0.1';
    private string $db_name = 'cyber_shield2';
    private string $username = 'root';
    private string $password = ''; // Default XAMPP password is empty
    private string $charset = 'utf8mb4';
    private int $port = 3306;
    private ?PDO $conn = null;

    public function __construct() {
        // Read environment overrides if configured in .env or Apache vhosts
        $this->host = getenv('DB_HOST') ?: $this->host;
        $this->db_name = getenv('DB_NAME') ?: $this->db_name;
        $this->username = getenv('DB_USER') ?: $this->username;
        $this->password = getenv('DB_PASS') !== false ? (string)getenv('DB_PASS') : $this->password;
        $this->port = (int)(getenv('DB_PORT') ?: $this->port);
    }

    public function getConnection(): ?PDO {
        if ($this->conn !== null) {
            return $this->conn;
        }

        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset} COLLATE utf8mb4_unicode_ci"
        ];

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            return $this->conn;
        } catch (PDOException $e) {
            // In development, log error safely without leaking raw DB credentials to users
            error_log("Database Connection Error: " . $e->getMessage());
            
            // Check if fallback SQLite is available for zero-config portable testing
            $sqlitePath = __DIR__ . '/../database/cyber_shield.sqlite';
            if (file_exists($sqlitePath) || (defined('ENABLE_SQLITE_FALLBACK') && ENABLE_SQLITE_FALLBACK)) {
                try {
                    $this->conn = new PDO("sqlite:" . $sqlitePath, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]);
                    return $this->conn;
                } catch (PDOException $sqliteEx) {
                    error_log("SQLite Fallback Failed: " . $sqliteEx->getMessage());
                }
            }
            
            // Helpful message for students during viva/setup
            die(json_encode([
                'success' => false,
                'message' => 'Database connection failed. Please ensure MySQL is running in XAMPP/WAMP and the `cyber_shield` database is imported.'
            ]));
        }
    }
}

// Global helper function for obtaining PDO instance
function getDBConnection(): PDO {
    static $dbInstance = null;
    if ($dbInstance === null) {
        $database = new Database();
        $dbInstance = $database->getConnection();
    }
    return $dbInstance;
}
