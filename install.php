<?php
// Simple installation script for Payroll System
// This script helps set up the database and initial data

$error = '';
$success = '';
$step = $_GET['step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 1) {
        // Test database connection
        $host = $_POST['host'] ?? 'localhost';
        $username = $_POST['username'] ?? 'root';
        $password = $_POST['password'] ?? '';
        $database = $_POST['database'] ?? 'payroll_system';
        
        try {
            $conn = new mysqli($host, $username, $password);
            
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }
            
            // Create database if it doesn't exist
            $conn->query("CREATE DATABASE IF NOT EXISTS `$database`");
            $conn->select_db($database);
            
            // Create tables
            $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'employee') NOT NULL DEFAULT 'employee',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );
            
            CREATE TABLE IF NOT EXISTS payslips (
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT NOT NULL,
                month INT NOT NULL,
                year INT NOT NULL,
                daily_wage DECIMAL(10,2) NOT NULL,
                days_worked INT NOT NULL,
                gross_income DECIMAL(10,2) NOT NULL,
                tax_deduction DECIMAL(10,2) NOT NULL,
                sss_deduction DECIMAL(10,2) NOT NULL,
                philhealth_deduction DECIMAL(10,2) NOT NULL DEFAULT 250.00,
                pagibig_deduction DECIMAL(10,2) NOT NULL DEFAULT 200.00,
                total_deductions DECIMAL(10,2) NOT NULL,
                net_income DECIMAL(10,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_employee_month_year (employee_id, month, year)
            );
            ";
            
            if ($conn->multi_query($sql)) {
                // Wait for all queries to complete
                do {
                    if ($result = $conn->store_result()) {
                        $result->free();
                    }
                } while ($conn->next_result());
            }
            
            // Insert default admin user
            $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
            $employee_password = password_hash('employee123', PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT IGNORE INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            
            // Admin user
            $name = 'Administrator';
            $email = 'admin@payroll.com';
            $role = 'admin';
            $stmt->bind_param("ssss", $name, $email, $admin_password, $role);
            $stmt->execute();
            
            // Sample employee
            $name = 'John Doe';
            $email = 'john@payroll.com';
            $role = 'employee';
            $stmt->bind_param("ssss", $name, $email, $employee_password, $role);
            $stmt->execute();
            
            $conn->close();
            
            // Update config file
            $config_content = "<?php
// Database configuration
define('DB_HOST', '$host');
define('DB_USER', '$username');
define('DB_PASS', '$password');
define('DB_NAME', '$database');

// Create database connection
function getDBConnection() {
    try {
        \$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if (\$conn->connect_error) {
            throw new Exception(\"Connection failed: \" . \$conn->connect_error);
        }
        
        \$conn->set_charset(\"utf8\");
        return \$conn;
    } catch (Exception \$e) {
        die(\"Database connection error: \" . \$e->getMessage());
    }
}

// Test database connection
function testConnection() {
    \$conn = getDBConnection();
    if (\$conn) {
        \$conn->close();
        return true;
    }
    return false;
}
?>";
            
            file_put_contents('config/database.php', $config_content);
            
            $success = 'Installation completed successfully!';
            $step = 2;
            
        } catch (Exception $e) {
            $error = 'Installation failed: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Payroll System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2 class="card-title">Payroll System Installation</h2>
                            <p class="text-muted">Setup your payroll system database</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($step == 1): ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="host" class="form-label">Database Host</label>
                                    <input type="text" class="form-control" id="host" name="host" value="localhost" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Database Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="root" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Database Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                    <small class="text-muted">Leave empty if no password (default XAMPP setup)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="database" class="form-label">Database Name</label>
                                    <input type="text" class="form-control" id="database" name="database" value="payroll_system" required>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Install Database</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                </div>
                                
                                <h4 class="text-success mb-3">Installation Complete!</h4>
                                
                                <div class="alert alert-info text-start">
                                    <h6>Default Login Accounts:</h6>
                                    <p class="mb-2"><strong>Admin:</strong><br>
                                    Email: admin@payroll.com<br>
                                    Password: admin123</p>
                                    
                                    <p class="mb-0"><strong>Employee (Demo):</strong><br>
                                    Email: john@payroll.com<br>
                                    Password: employee123</p>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="login.php" class="btn btn-primary">Go to Login Page</a>
                                    <a href="index.php" class="btn btn-outline-secondary">Go to Application</a>
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted">
                                        You can delete this install.php file for security.
                                    </small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
