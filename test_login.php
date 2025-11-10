<?php
// Test login functionality step by step
require_once 'config/database.php';

echo "<!DOCTYPE html><html><head><title>Login Test</title></head><body>";
echo "<h2>🔍 Login Debug Test</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0;'>";
    echo "<h3>Testing Login for: " . htmlspecialchars($email) . "</h3>";
    
    try {
        $conn = getDBConnection();
        
        // Step 1: Check if user exists
        echo "<p><strong>Step 1:</strong> Looking for user in database...</p>";
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo "<p style='color: red;'>❌ User not found with email: " . htmlspecialchars($email) . "</p>";
        } else {
            $user = $result->fetch_assoc();
            echo "<p style='color: green;'>✅ User found: " . htmlspecialchars($user['name']) . " (Role: " . $user['role'] . ")</p>";
            
            // Step 2: Test password
            echo "<p><strong>Step 2:</strong> Testing password...</p>";
            echo "<p>Password entered: " . str_repeat('*', strlen($password)) . " (length: " . strlen($password) . ")</p>";
            echo "<p>Stored hash: " . substr($user['password'], 0, 30) . "...</p>";
            
            if (password_verify($password, $user['password'])) {
                echo "<p style='color: green;'>✅ Password verification SUCCESS!</p>";
                
                // Step 3: Test session creation (simulate login)
                echo "<p><strong>Step 3:</strong> Testing session creation...</p>";
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                
                echo "<p style='color: green;'>✅ Session created successfully!</p>";
                echo "<p><strong>Session data:</strong></p>";
                echo "<ul>";
                echo "<li>User ID: " . $_SESSION['user_id'] . "</li>";
                echo "<li>Name: " . htmlspecialchars($_SESSION['user_name']) . "</li>";
                echo "<li>Email: " . htmlspecialchars($_SESSION['user_email']) . "</li>";
                echo "<li>Role: " . $_SESSION['user_role'] . "</li>";
                echo "</ul>";
                
                echo "<p style='color: green; font-size: 18px;'><strong>🎉 LOGIN SHOULD WORK!</strong></p>";
                echo "<p>The login process is working correctly. The issue might be:</p>";
                echo "<ul>";
                echo "<li>Browser cache - try clearing cache or incognito mode</li>";
                echo "<li>Session conflicts - try closing all browser tabs</li>";
                echo "<li>Redirect issues in the auth.php file</li>";
                echo "</ul>";
                
            } else {
                echo "<p style='color: red;'>❌ Password verification FAILED!</p>";
                
                // Test with common passwords
                echo "<p><strong>Testing common passwords:</strong></p>";
                $test_passwords = ['admin123', 'employee123', 'password', '123456'];
                foreach ($test_passwords as $test_pass) {
                    if (password_verify($test_pass, $user['password'])) {
                        echo "<p style='color: green;'>✅ Password '$test_pass' works for this user!</p>";
                        break;
                    }
                }
                
                // Show password hash details
                echo "<p><strong>Password Hash Analysis:</strong></p>";
                echo "<p>Hash algorithm: " . (strpos($user['password'], '$2y$') === 0 ? 'bcrypt (correct)' : 'unknown/incorrect') . "</p>";
                echo "<p>Hash length: " . strlen($user['password']) . " characters</p>";
            }
        }
        
        $conn->close();
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
} else {
    // Show all users in database
    try {
        $conn = getDBConnection();
        echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0;'>";
        echo "<h3>Users in Database:</h3>";
        $result = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY role, name");
        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . $row['role'] . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>No users found in database!</p>";
        }
        echo "</div>";
        $conn->close();
    } catch (Exception $e) {
        echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
    }
}
?>

<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0;'>
    <h3>Test Login Form</h3>
    <form method="POST">
        <p>
            <label>Email:</label><br>
            <input type="email" name="email" value="admin@payroll.com" style="width: 300px; padding: 5px;" required>
        </p>
        <p>
            <label>Password:</label><br>
            <input type="password" name="password" value="admin123" style="width: 300px; padding: 5px;" required>
        </p>
        <p>
            <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px;">Test Login</button>
        </p>
    </form>
    
    <p><strong>Quick Tests:</strong></p>
    <form method="POST" style="display: inline;">
        <input type="hidden" name="email" value="admin@payroll.com">
        <input type="hidden" name="password" value="admin123">
        <button type="submit" style="background: #28a745; color: white; padding: 5px 10px; border: none; border-radius: 3px;">Test Admin</button>
    </form>
    
    <form method="POST" style="display: inline;">
        <input type="hidden" name="email" value="john@payroll.com">
        <input type="hidden" name="password" value="employee123">
        <button type="submit" style="background: #17a2b8; color: white; padding: 5px 10px; border: none; border-radius: 3px;">Test Employee</button>
    </form>
</div>

<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0;'>
    <h3>Next Steps:</h3>
    <p><a href="login.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Go to Real Login Page</a></p>
</div>

</body></html>
