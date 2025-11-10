<?php
// Simple database connection test
echo "<h2>Database Connection Test</h2>";

// Test basic MySQL connection
echo "<h3>1. Testing MySQL Connection</h3>";
$conn = new mysqli('localhost', 'root', '');
if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ MySQL Connection Failed: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>✅ MySQL Connection Successful</p>";
    
    // Check if database exists
    echo "<h3>2. Checking Database</h3>";
    $result = $conn->query("SHOW DATABASES LIKE 'payroll_system'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Database 'payroll_system' exists</p>";
        
        // Test connection to payroll_system database
        $conn->select_db('payroll_system');
        
        // Check if tables exist
        echo "<h3>3. Checking Tables</h3>";
        $tables = ['users', 'payslips'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                echo "<p style='color: green;'>✅ Table '$table' exists</p>";
            } else {
                echo "<p style='color: red;'>❌ Table '$table' does not exist</p>";
            }
        }
        
        // Check if admin user exists
        echo "<h3>4. Checking Default Users</h3>";
        $result = $conn->query("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Admin user exists</p>";
        } else {
            echo "<p style='color: red;'>❌ Admin user does not exist</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Database 'payroll_system' does not exist</p>";
        echo "<p><strong>Solution:</strong> Run the installation script: <a href='install.php'>install.php</a></p>";
    }
}

echo "<h3>5. Testing Config File</h3>";
if (file_exists('config/database.php')) {
    echo "<p style='color: green;'>✅ Config file exists</p>";
    
    // Test including the config file
    try {
        require_once 'config/database.php';
        echo "<p style='color: green;'>✅ Config file loaded successfully</p>";
        
        // Test the getDBConnection function
        try {
            $test_conn = getDBConnection();
            echo "<p style='color: green;'>✅ getDBConnection() works</p>";
            $test_conn->close();
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ getDBConnection() failed: " . $e->getMessage() . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error loading config file: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Config file does not exist</p>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ul>";
echo "<li>If database doesn't exist: <a href='install.php'>Run Installation Script</a></li>";
echo "<li>If everything is OK: <a href='login.php'>Go to Login Page</a></li>";
echo "<li>If still having issues: Check XAMPP MySQL service is running</li>";
echo "</ul>";
?>
