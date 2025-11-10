<?php
// Fix user passwords to correct values
require_once 'config/database.php';

echo "<!DOCTYPE html><html><head><title>Fix Passwords</title></head><body>";
echo "<h2>🔧 Fixing User Passwords</h2>";

try {
    $conn = getDBConnection();
    
    // Create correct password hashes
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $employee_password = password_hash('employee123', PASSWORD_DEFAULT);
    
    echo "<p>Generated new password hashes...</p>";
    
    // Update admin password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = 'admin@payroll.com'");
    $stmt->bind_param("s", $admin_password);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Admin password updated to: admin123</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to update admin password: " . $stmt->error . "</p>";
    }
    
    // Update employee password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = 'john@payroll.com'");
    $stmt->bind_param("s", $employee_password);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Employee password updated to: employee123</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to update employee password: " . $stmt->error . "</p>";
    }
    
    // Test the new passwords
    echo "<h3>Testing New Passwords:</h3>";
    
    // Test admin
    $result = $conn->query("SELECT password FROM users WHERE email = 'admin@payroll.com'");
    if ($result->num_rows > 0) {
        $stored_hash = $result->fetch_assoc()['password'];
        if (password_verify('admin123', $stored_hash)) {
            echo "<p style='color: green;'>✅ Admin login test: admin@payroll.com / admin123 - SUCCESS</p>";
        } else {
            echo "<p style='color: red;'>❌ Admin login test failed</p>";
        }
    }
    
    // Test employee
    $result = $conn->query("SELECT password FROM users WHERE email = 'john@payroll.com'");
    if ($result->num_rows > 0) {
        $stored_hash = $result->fetch_assoc()['password'];
        if (password_verify('employee123', $stored_hash)) {
            echo "<p style='color: green;'>✅ Employee login test: john@payroll.com / employee123 - SUCCESS</p>";
        } else {
            echo "<p style='color: red;'>❌ Employee login test failed</p>";
        }
    }
    
    $conn->close();
    
    echo "<hr>";
    echo "<h3>🎉 Passwords Fixed!</h3>";
    echo "<p><strong>You can now login with:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@payroll.com / admin123</li>";
    echo "<li><strong>Employee:</strong> john@payroll.com / employee123</li>";
    echo "</ul>";
    
    echo "<p><a href='login.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    echo "<p><a href='test_login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Login Again</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
