<?php
// Quick fix for login issues
echo "<!DOCTYPE html><html><head><title>Fix Login Issue</title></head><body>";
echo "<h2>🔧 Fixing Login Issue</h2>";

// Step 1: Check MySQL connection
echo "<h3>Step 1: Testing MySQL Connection</h3>";
$mysql_conn = @new mysqli('localhost', 'root', '');
if ($mysql_conn->connect_error) {
    echo "<p style='color: red;'>❌ MySQL connection failed: " . $mysql_conn->connect_error . "</p>";
    echo "<p><strong>Solution:</strong> Start XAMPP MySQL service</p>";
    echo "</body></html>";
    exit();
} else {
    echo "<p style='color: green;'>✅ MySQL connection successful</p>";
}

// Step 2: Check/Create database
echo "<h3>Step 2: Checking Database</h3>";
$result = $mysql_conn->query("SHOW DATABASES LIKE 'payroll_system'");
if ($result->num_rows == 0) {
    echo "<p style='color: orange;'>⚠️ Database doesn't exist. Creating it...</p>";
    $mysql_conn->query("CREATE DATABASE payroll_system");
    echo "<p style='color: green;'>✅ Database created</p>";
} else {
    echo "<p style='color: green;'>✅ Database exists</p>";
}

// Select database
$mysql_conn->select_db('payroll_system');

// Step 3: Check/Create users table
echo "<h3>Step 3: Checking Users Table</h3>";
$result = $mysql_conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows == 0) {
    echo "<p style='color: orange;'>⚠️ Users table doesn't exist. Creating it...</p>";
    $sql = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'employee') NOT NULL DEFAULT 'employee',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($mysql_conn->query($sql)) {
        echo "<p style='color: green;'>✅ Users table created</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create users table: " . $mysql_conn->error . "</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Users table exists</p>";
}

// Step 4: Check/Create payslips table
echo "<h3>Step 4: Checking Payslips Table</h3>";
$result = $mysql_conn->query("SHOW TABLES LIKE 'payslips'");
if ($result->num_rows == 0) {
    echo "<p style='color: orange;'>⚠️ Payslips table doesn't exist. Creating it...</p>";
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
    
    if ($mysql_conn->query($sql)) {
        echo "<p style='color: green;'>✅ Payslips table created</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create payslips table: " . $mysql_conn->error . "</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Payslips table exists</p>";
}

// Step 5: Create default users
echo "<h3>Step 5: Creating Default Users</h3>";

// Clear existing default users
$mysql_conn->query("DELETE FROM users WHERE email IN ('admin@payroll.com', 'john@payroll.com')");

// Create password hashes
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$employee_password = password_hash('employee123', PASSWORD_DEFAULT);

// Insert admin user
$stmt = $mysql_conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");

// Admin
$name = 'Administrator';
$email = 'admin@payroll.com';
$role = 'admin';
$stmt->bind_param("ssss", $name, $email, $admin_password, $role);

if ($stmt->execute()) {
    echo "<p style='color: green;'>✅ Admin user created: admin@payroll.com / admin123</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to create admin user: " . $stmt->error . "</p>";
}

// Employee
$name = 'John Doe';
$email = 'john@payroll.com';
$role = 'employee';
$stmt->bind_param("ssss", $name, $email, $employee_password, $role);

if ($stmt->execute()) {
    echo "<p style='color: green;'>✅ Employee user created: john@payroll.com / employee123</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to create employee user: " . $stmt->error . "</p>";
}

// Step 6: Test login
echo "<h3>Step 6: Testing Login</h3>";
$result = $mysql_conn->query("SELECT email, role FROM users");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Users in database:</p>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['email']) . " (" . $row['role'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ No users found in database</p>";
}

// Test password verification
$test_result = $mysql_conn->query("SELECT password FROM users WHERE email = 'admin@payroll.com'");
if ($test_result->num_rows > 0) {
    $stored_hash = $test_result->fetch_assoc()['password'];
    if (password_verify('admin123', $stored_hash)) {
        echo "<p style='color: green;'>✅ Admin password verification works</p>";
    } else {
        echo "<p style='color: red;'>❌ Admin password verification failed</p>";
    }
}

$mysql_conn->close();

echo "<hr>";
echo "<h3>🎉 Setup Complete!</h3>";
echo "<p><strong>You can now login with:</strong></p>";
echo "<ul>";
echo "<li><strong>Admin:</strong> admin@payroll.com / admin123</li>";
echo "<li><strong>Employee:</strong> john@payroll.com / employee123</li>";
echo "</ul>";

echo "<p><a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";

echo "</body></html>";
?>
