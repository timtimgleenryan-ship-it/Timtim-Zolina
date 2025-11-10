<?php
// Quick diagnosis and fix for login issues
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Fix - Login Issues</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .box { border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .button { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
    </style>
</head>
<body>
    <h1>🔧 Login Issue Quick Fix</h1>
    
    <?php
    echo "<div class='box'>";
    echo "<h2>Step 1: Testing MySQL Connection</h2>";
    
    // Test MySQL connection
    $mysql_ok = false;
    try {
        $conn = new mysqli('localhost', 'root', '');
        if ($conn->connect_error) {
            echo "<p class='error'>❌ MySQL Connection Failed: " . $conn->connect_error . "</p>";
            echo "<p><strong>Solution:</strong> Start XAMPP MySQL service in Control Panel</p>";
        } else {
            echo "<p class='success'>✅ MySQL Connection OK</p>";
            $mysql_ok = true;
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ MySQL Error: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    if ($mysql_ok) {
        echo "<div class='box'>";
        echo "<h2>Step 2: Checking Database</h2>";
        
        // Check if database exists
        $result = $conn->query("SHOW DATABASES LIKE 'payroll_system'");
        if ($result->num_rows == 0) {
            echo "<p class='warning'>⚠️ Database 'payroll_system' doesn't exist</p>";
            echo "<p class='info'>Creating database...</p>";
            if ($conn->query("CREATE DATABASE payroll_system")) {
                echo "<p class='success'>✅ Database created successfully</p>";
            } else {
                echo "<p class='error'>❌ Failed to create database: " . $conn->error . "</p>";
            }
        } else {
            echo "<p class='success'>✅ Database 'payroll_system' exists</p>";
        }
        
        // Select database
        $conn->select_db('payroll_system');
        echo "</div>";
        
        echo "<div class='box'>";
        echo "<h2>Step 3: Checking Tables</h2>";
        
        // Check users table
        $result = $conn->query("SHOW TABLES LIKE 'users'");
        if ($result->num_rows == 0) {
            echo "<p class='warning'>⚠️ Users table doesn't exist</p>";
            echo "<p class='info'>Creating users table...</p>";
            
            $sql = "CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'employee') NOT NULL DEFAULT 'employee',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            
            if ($conn->query($sql)) {
                echo "<p class='success'>✅ Users table created</p>";
            } else {
                echo "<p class='error'>❌ Failed to create users table: " . $conn->error . "</p>";
            }
        } else {
            echo "<p class='success'>✅ Users table exists</p>";
        }
        
        // Check payslips table
        $result = $conn->query("SHOW TABLES LIKE 'payslips'");
        if ($result->num_rows == 0) {
            echo "<p class='warning'>⚠️ Payslips table doesn't exist</p>";
            echo "<p class='info'>Creating payslips table...</p>";
            
            $sql = "CREATE TABLE payslips (
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
            )";
            
            if ($conn->query($sql)) {
                echo "<p class='success'>✅ Payslips table created</p>";
            } else {
                echo "<p class='error'>❌ Failed to create payslips table: " . $conn->error . "</p>";
            }
        } else {
            echo "<p class='success'>✅ Payslips table exists</p>";
        }
        echo "</div>";
        
        echo "<div class='box'>";
        echo "<h2>Step 4: Creating/Checking Users</h2>";
        
        // Check existing users
        $result = $conn->query("SELECT COUNT(*) as count FROM users");
        $user_count = $result->fetch_assoc()['count'];
        echo "<p class='info'>Current users in database: $user_count</p>";
        
        // Delete existing default users and recreate them
        $conn->query("DELETE FROM users WHERE email IN ('admin@payroll.com', 'john@payroll.com')");
        echo "<p class='info'>Cleared existing default users</p>";
        
        // Create new users with proper password hashing
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $employee_password = password_hash('employee123', PASSWORD_DEFAULT);
        
        echo "<p class='info'>Generated password hashes</p>";
        
        // Insert admin
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        
        $name = 'Administrator';
        $email = 'admin@payroll.com';
        $role = 'admin';
        $stmt->bind_param("ssss", $name, $email, $admin_password, $role);
        
        if ($stmt->execute()) {
            echo "<p class='success'>✅ Admin user created: admin@payroll.com</p>";
        } else {
            echo "<p class='error'>❌ Failed to create admin: " . $stmt->error . "</p>";
        }
        
        // Insert employee
        $name = 'John Doe';
        $email = 'john@payroll.com';
        $role = 'employee';
        $stmt->bind_param("ssss", $name, $email, $employee_password, $role);
        
        if ($stmt->execute()) {
            echo "<p class='success'>✅ Employee user created: john@payroll.com</p>";
        } else {
            echo "<p class='error'>❌ Failed to create employee: " . $stmt->error . "</p>";
        }
        echo "</div>";
        
        echo "<div class='box'>";
        echo "<h2>Step 5: Testing Login Credentials</h2>";
        
        // Test admin login
        $result = $conn->query("SELECT password FROM users WHERE email = 'admin@payroll.com'");
        if ($result->num_rows > 0) {
            $stored_hash = $result->fetch_assoc()['password'];
            if (password_verify('admin123', $stored_hash)) {
                echo "<p class='success'>✅ Admin password verification works</p>";
            } else {
                echo "<p class='error'>❌ Admin password verification failed</p>";
            }
        }
        
        // Test employee login
        $result = $conn->query("SELECT password FROM users WHERE email = 'john@payroll.com'");
        if ($result->num_rows > 0) {
            $stored_hash = $result->fetch_assoc()['password'];
            if (password_verify('employee123', $stored_hash)) {
                echo "<p class='success'>✅ Employee password verification works</p>";
            } else {
                echo "<p class='error'>❌ Employee password verification failed</p>";
            }
        }
        
        // Show all users
        echo "<h3>Users in Database:</h3>";
        $result = $conn->query("SELECT id, name, email, role FROM users");
        if ($result->num_rows > 0) {
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>ID: " . $row['id'] . " | " . htmlspecialchars($row['name']) . " | " . htmlspecialchars($row['email']) . " | " . $row['role'] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='error'>No users found!</p>";
        }
        echo "</div>";
        
        $conn->close();
    }
    ?>
    
    <div class='box'>
        <h2>🎉 Setup Complete!</h2>
        <p><strong>Login Credentials:</strong></p>
        <ul>
            <li><strong>Admin:</strong> admin@payroll.com / admin123</li>
            <li><strong>Employee:</strong> john@payroll.com / employee123</li>
        </ul>
        
        <p><strong>Next Steps:</strong></p>
        <a href="login.php" class="button">Go to Login Page</a>
        <a href="test.php" class="button">Test Basic Setup</a>
    </div>
    
    <div class='box'>
        <h3>If Login Still Fails:</h3>
        <ol>
            <li>Make sure XAMPP Apache and MySQL are both running</li>
            <li>Clear your browser cache</li>
            <li>Try using incognito/private browsing mode</li>
            <li>Check that you're using the exact credentials above</li>
        </ol>
    </div>
</body>
</html>
