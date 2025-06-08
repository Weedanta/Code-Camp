<?php
// config/database.php - Database configuration and connection

class Database {
    private $host;
    private $database_name;
    private $username;
    private $password;
    private $charset;
    public $conn;

    public function __construct() {
        // Database configuration
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->database_name = $_ENV['DB_NAME'] ?? 'code_camp';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? 'rahasia';
        $this->charset = 'utf8mb4';
    }

    // Get database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->database_name . ";charset=" . $this->charset;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $this->charset . " COLLATE utf8mb4_unicode_ci"
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            // Set timezone
            $this->conn->exec("SET time_zone = '+07:00'");
            
        } catch(PDOException $exception) {
            // Log the error
            error_log("Database connection error: " . $exception->getMessage());
            
            // In production, don't expose database errors
            if ($_ENV['APP_ENV'] === 'production') {
                throw new Exception("Database connection failed");
            } else {
                throw new Exception("Database connection error: " . $exception->getMessage());
            }
        }

        return $this->conn;
    }

    // Test database connection
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                return [
                    'success' => true, 
                    'message' => 'Database connection successful'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false, 
                'message' => $e->getMessage()
            ];
        }
        
        return [
            'success' => false, 
            'message' => 'Unknown connection error'
        ];
    }

    // Get database info
    public function getDatabaseInfo() {
        try {
            $conn = $this->getConnection();
            
            // Get MySQL version
            $stmt = $conn->query("SELECT VERSION() as version");
            $version = $stmt->fetch()['version'];
            
            // Get database size
            $stmt = $conn->prepare("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = ?
            ");
            $stmt->execute([$this->database_name]);
            $size = $stmt->fetch()['size_mb'];
            
            // Get table count
            $stmt = $conn->prepare("
                SELECT COUNT(*) as table_count 
                FROM information_schema.tables 
                WHERE table_schema = ?
            ");
            $stmt->execute([$this->database_name]);
            $tableCount = $stmt->fetch()['table_count'];
            
            return [
                'host' => $this->host,
                'database' => $this->database_name,
                'version' => $version,
                'size_mb' => $size,
                'table_count' => $tableCount,
                'charset' => $this->charset
            ];
            
        } catch (Exception $e) {
            error_log("Error getting database info: " . $e->getMessage());
            return null;
        }
    }

    // Create database backup
    public function backup($outputPath) {
        try {
            $conn = $this->getConnection();
            
            // Get all tables
            $stmt = $conn->prepare("SHOW TABLES");
            $stmt->execute();
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $sql = "-- Database Backup\n";
            $sql .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Database: " . $this->database_name . "\n\n";
            
            $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
            
            foreach ($tables as $table) {
                // Get table structure
                $stmt = $conn->prepare("SHOW CREATE TABLE `$table`");
                $stmt->execute();
                $createTable = $stmt->fetch();
                
                $sql .= "-- Structure for table `$table`\n";
                $sql .= "DROP TABLE IF EXISTS `$table`;\n";
                $sql .= $createTable['Create Table'] . ";\n\n";
                
                // Get table data
                $stmt = $conn->prepare("SELECT * FROM `$table`");
                $stmt->execute();
                $rows = $stmt->fetchAll();
                
                if (!empty($rows)) {
                    $sql .= "-- Data for table `$table`\n";
                    
                    foreach ($rows as $row) {
                        $values = array_map(function($value) use ($conn) {
                            return $value === null ? 'NULL' : $conn->quote($value);
                        }, array_values($row));
                        
                        $sql .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    
                    $sql .= "\n";
                }
            }
            
            $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
            
            // Write to file
            $result = file_put_contents($outputPath, $sql);
            
            return [
                'success' => $result !== false,
                'message' => $result !== false ? 'Backup created successfully' : 'Failed to create backup',
                'file_size' => $result !== false ? filesize($outputPath) : 0
            ];
            
        } catch (Exception $e) {
            error_log("Database backup error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ];
        }
    }

    // Optimize database tables
    public function optimize() {
        try {
            $conn = $this->getConnection();
            
            // Get all tables
            $stmt = $conn->prepare("SHOW TABLES");
            $stmt->execute();
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $optimized = 0;
            $errors = [];
            
            foreach ($tables as $table) {
                try {
                    $stmt = $conn->prepare("OPTIMIZE TABLE `$table`");
                    $stmt->execute();
                    $optimized++;
                } catch (Exception $e) {
                    $errors[] = "Failed to optimize table `$table`: " . $e->getMessage();
                }
            }
            
            return [
                'success' => empty($errors),
                'message' => "Optimized $optimized tables" . (empty($errors) ? '' : '. Errors: ' . implode(', ', $errors)),
                'optimized_count' => $optimized,
                'errors' => $errors
            ];
            
        } catch (Exception $e) {
            error_log("Database optimization error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Optimization failed: ' . $e->getMessage()
            ];
        }
    }

    // Check database health
    public function checkHealth() {
        try {
            $conn = $this->getConnection();
            $issues = [];
            
            // Check connection
            if (!$conn) {
                $issues[] = 'Database connection failed';
                return ['healthy' => false, 'issues' => $issues];
            }
            
            // Check if required tables exist
            $requiredTables = [
                'users', 'admins', 'bootcamps', 'categories', 
                'orders', 'order_items', 'reviews', 'settings',
                'admin_activity_logs'
            ];
            
            $stmt = $conn->prepare("SHOW TABLES");
            $stmt->execute();
            $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($requiredTables as $table) {
                if (!in_array($table, $existingTables)) {
                    $issues[] = "Required table '$table' is missing";
                }
            }
            
            // Check for corrupted tables
            foreach ($existingTables as $table) {
                $stmt = $conn->prepare("CHECK TABLE `$table`");
                $stmt->execute();
                $result = $stmt->fetch();
                
                if ($result['Msg_text'] !== 'OK') {
                    $issues[] = "Table `$table` may be corrupted: " . $result['Msg_text'];
                }
            }
            
            // Check disk space (approximate)
            $stmt = $conn->prepare("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = ?
            ");
            $stmt->execute([$this->database_name]);
            $dbSize = $stmt->fetch()['size_mb'];
            
            if ($dbSize > 1000) { // More than 1GB
                $issues[] = "Database size is large ($dbSize MB) - consider optimization";
            }
            
            return [
                'healthy' => empty($issues),
                'issues' => $issues,
                'size_mb' => $dbSize
            ];
            
        } catch (Exception $e) {
            error_log("Database health check error: " . $e->getMessage());
            return [
                'healthy' => false,
                'issues' => ['Health check failed: ' . $e->getMessage()]
            ];
        }
    }

    // Execute migration/setup scripts
    public function runMigration($sqlFile) {
        try {
            if (!file_exists($sqlFile)) {
                throw new Exception("Migration file not found: $sqlFile");
            }
            
            $sql = file_get_contents($sqlFile);
            $conn = $this->getConnection();
            
            // Split SQL into individual statements
            $statements = array_filter(
                array_map('trim', explode(';', $sql)),
                function($stmt) {
                    return !empty($stmt) && !preg_match('/^--/', $stmt);
                }
            );
            
            $conn->beginTransaction();
            
            foreach ($statements as $statement) {
                if (!empty(trim($statement))) {
                    $conn->exec($statement);
                }
            }
            
            $conn->commit();
            
            return [
                'success' => true,
                'message' => 'Migration completed successfully',
                'statements_executed' => count($statements)
            ];
            
        } catch (Exception $e) {
            if (isset($conn)) {
                $conn->rollback();
            }
            
            error_log("Migration error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Migration failed: ' . $e->getMessage()
            ];
        }
    }

    // Clean up old logs
    public function cleanLogs($daysToKeep = 30) {
        try {
            $conn = $this->getConnection();
            
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-$daysToKeep days"));
            
            // Clean admin activity logs
            $stmt = $conn->prepare("DELETE FROM admin_activity_logs WHERE created_at < ?");
            $stmt->execute([$cutoffDate]);
            $deletedActivityLogs = $stmt->rowCount();
            
            // You can add more log tables here as needed
            
            return [
                'success' => true,
                'message' => "Cleaned logs older than $daysToKeep days",
                'deleted_activity_logs' => $deletedActivityLogs
            ];
            
        } catch (Exception $e) {
            error_log("Log cleanup error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Log cleanup failed: ' . $e->getMessage()
            ];
        }
    }
}
?>