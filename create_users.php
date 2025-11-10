<?php
// Script to create/recreate default users
require_once 'config/database.php';

echo "<h2>Creating Default Users</h2>";

try {
    $conn = getDBConnection();
    
    // First, let's check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows == 0) {
        echo "<p style='color: red;'>❌ Users table doesn't exist. Please run install.php first.</p>";
        exit();
    }
    
    // Delete existing default users (if any)
    $conn->query("DELETE FROM users WHERE email IN ('admin@payroll.com', 'john@payroll.com')");
    echo "<p>🗑️ Cleared existing default users</p>";
    
    // Create password hashes
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $employee_password = password_hash('employee123', PASSWORD_DEFAULT);
    
    echo "<p>🔐 Generated password hashes:</p>";
    echo "<p>Admin hash: " . substr($admin_password, 0, 20) . "...</p>";
    echo "<p>Employee hash: " . substr($employee_password, 0, 20) . "...</p>";
    
    // Insert admin user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    
    $name = 'Administrator';
    $email = 'admin@payroll.com';
    $role = 'admin';
    $stmt->bind_param("ssss", $name, $email, $admin_password, $role);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Admin user created successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create admin user: " . $stmt->error . "</p>";
    }
    
    // Insert employee user
    $name = 'John Doe';
    $email = 'john@payroll.com';
    $role = 'employee';
    $stmt->bind_param("ssss", $name, $email, $employee_password, $role);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Employee user created successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create employee user: " . $stmt->error . "</p>";
    }
    
    // Verify users were created
    echo "<h3>Verification:</h3>";
    $result = $conn->query("SELECT id, name, email, role FROM users ORDER BY role, name");
    
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . $row['role'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test password verification
    echo "<h3>Password Test:</h3>";
    $test_result = $conn->query("SELECT password FROM users WHERE email = 'admin@payroll.com'");
    if ($test_result->num_rows > 0) {
        $stored_hash = $test_result->fetch_assoc()['password'];
        if (password_verify('admin123', $stored_hash)) {
            echo "<p style='color: green;'>✅ Admin password verification works</p>";
        } else {
            echo "<p style='color: red;'>❌ Admin password verification failed</p>";
        }
    }
    
    $test_result = $conn->query("SELECT password FROM users WHERE email = 'john@payroll.com'");
    if ($test_result->num_rows > 0) {
        $stored_hash = $test_result->fetch_assoc()['password'];
        if (password_verify('employee123', $stored_hash)) {
            echo "<p style='color: green;'>✅ Employee password verification works</p>";
        } else {
            echo "<p style='color: red;'>❌ Employee password verification failed</p>";
        }
    }
    
    $conn->close();
    
    echo "<hr>";
    echo "<h3>✅ Setup Complete!</h3>";
    echo "<p>You can now login with:</p>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@payroll.com / admin123</li>";
    echo "<li><strong>Employee:</strong> john@payroll.com / employee123</li>";
    echo "</ul>";
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL service is running</li>";
    echo "<li>Database 'payroll_system' exists</li>";
    echo "<li>Run install.php first if you haven't</li>";
    echo "</ul>";
}
?>
