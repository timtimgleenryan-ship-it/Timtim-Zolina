<?php
// Setup verification and quick fix script
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Payroll System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Payroll System Setup</h3>
                    </div>
                    <div class="card-body">
                        <h5>System Status Check</h5>
                        
                        <?php
                        $all_ok = true;
                        
                        // Check 1: PHP Version
                        echo "<div class='mb-3'>";
                        echo "<strong>1. PHP Version:</strong> ";
                        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
                            echo "<span class='status-ok'>✅ " . PHP_VERSION . " (OK)</span>";
                        } else {
                            echo "<span class='status-error'>❌ " . PHP_VERSION . " (Requires PHP 7.4+)</span>";
                            $all_ok = false;
                        }
                        echo "</div>";
                        
                        // Check 2: MySQLi Extension
                        echo "<div class='mb-3'>";
                        echo "<strong>2. MySQLi Extension:</strong> ";
                        if (extension_loaded('mysqli')) {
                            echo "<span class='status-ok'>✅ Available</span>";
                        } else {
                            echo "<span class='status-error'>❌ Not available</span>";
                            $all_ok = false;
                        }
                        echo "</div>";
                        
                        // Check 3: Config file
                        echo "<div class='mb-3'>";
                        echo "<strong>3. Configuration File:</strong> ";
                        if (file_exists('config/database.php')) {
                            echo "<span class='status-ok'>✅ Found</span>";
                        } else {
                            echo "<span class='status-error'>❌ Missing</span>";
                            $all_ok = false;
                        }
                        echo "</div>";
                        
                        // Check 4: MySQL Connection
                        echo "<div class='mb-3'>";
                        echo "<strong>4. MySQL Connection:</strong> ";
                        $mysql_conn = @new mysqli('localhost', 'root', '');
                        if ($mysql_conn->connect_error) {
                            echo "<span class='status-error'>❌ Failed: " . $mysql_conn->connect_error . "</span>";
                            echo "<br><small class='text-muted'>Make sure XAMPP MySQL service is running</small>";
                            $all_ok = false;
                        } else {
                            echo "<span class='status-ok'>✅ Connected</span>";
                            
                            // Check 5: Database exists
                            echo "</div><div class='mb-3'>";
                            echo "<strong>5. Payroll Database:</strong> ";
                            $result = $mysql_conn->query("SHOW DATABASES LIKE 'payroll_system'");
                            if ($result->num_rows > 0) {
                                echo "<span class='status-ok'>✅ Exists</span>";
                                
                                // Check tables
                                $mysql_conn->select_db('payroll_system');
                                $tables_check = true;
                                $required_tables = ['users', 'payslips'];
                                
                                echo "</div><div class='mb-3'>";
                                echo "<strong>6. Database Tables:</strong><br>";
                                foreach ($required_tables as $table) {
                                    $result = $mysql_conn->query("SHOW TABLES LIKE '$table'");
                                    if ($result->num_rows > 0) {
                                        echo "<span class='status-ok'>✅ $table</span><br>";
                                    } else {
                                        echo "<span class='status-error'>❌ $table</span><br>";
                                        $tables_check = false;
                                    }
                                }
                                
                                if (!$tables_check) {
                                    $all_ok = false;
                                }
                                
                            } else {
                                echo "<span class='status-error'>❌ Not found</span>";
                                $all_ok = false;
                            }
                            $mysql_conn->close();
                        }
                        echo "</div>";
                        
                        // Results and Actions
                        echo "<hr>";
                        if ($all_ok) {
                            echo "<div class='alert alert-success'>";
                            echo "<h5>🎉 Setup Complete!</h5>";
                            echo "<p>Your payroll system is ready to use.</p>";
                            echo "<div class='d-grid gap-2'>";
                            echo "<a href='login.php' class='btn btn-success'>Go to Login Page</a>";
                            echo "<a href='test_connection.php' class='btn btn-outline-info'>Run Detailed Test</a>";
                            echo "</div>";
                            echo "</div>";
                        } else {
                            echo "<div class='alert alert-warning'>";
                            echo "<h5>⚠️ Setup Required</h5>";
                            echo "<p>Some components need to be set up before you can use the system.</p>";
                            echo "<div class='d-grid gap-2'>";
                            echo "<a href='install.php' class='btn btn-primary'>Run Installation Script</a>";
                            echo "<a href='test_connection.php' class='btn btn-outline-info'>Detailed Diagnostics</a>";
                            echo "</div>";
                            echo "</div>";
                        }
                        ?>
                        
                        <hr>
                        <h6>Quick Actions:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="list-group">
                                    <a href="install.php" class="list-group-item list-group-item-action">
                                        🔧 Run Installation
                                    </a>
                                    <a href="test_connection.php" class="list-group-item list-group-item-action">
                                        🔍 Test Database Connection
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="list-group">
                                    <a href="login.php" class="list-group-item list-group-item-action">
                                        🔐 Go to Login
                                    </a>
                                    <a href="README.md" class="list-group-item list-group-item-action" target="_blank">
                                        📖 Read Documentation
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6>Default Login Accounts:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Admin Account</h6>
                                            <p class="card-text">
                                                <strong>Email:</strong> admin@payroll.com<br>
                                                <strong>Password:</strong> admin123
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Employee Account</h6>
                                            <p class="card-text">
                                                <strong>Email:</strong> john@payroll.com<br>
                                                <strong>Password:</strong> employee123
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
