<?php
// Debug login process
require_once 'config/database.php';

echo "<h2>Login Debug Tool</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    echo "<h3>Debug Information:</h3>";
    echo "<p><strong>Email entered:</strong> " . htmlspecialchars($email) . "</p>";
    echo "<p><strong>Password entered:</strong> " . str_repeat('*', strlen($password)) . " (length: " . strlen($password) . ")</p>";
    
    try {
        $conn = getDBConnection();
        
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo "<p style='color: red;'>❌ No user found with email: " . htmlspecialchars($email) . "</p>";
            
            // Show all users for debugging
            echo "<h4>Available users in database:</h4>";
            $all_users = $conn->query("SELECT id, name, email, role FROM users");
            if ($all_users->num_rows > 0) {
                echo "<ul>";
                while ($user = $all_users->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($user['email']) . " (" . $user['role'] . ")</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No users found in database!</p>";
            }
        } else {
            $user = $result->fetch_assoc();
            echo "<p style='color: green;'>✅ User found: " . htmlspecialchars($user['name']) . " (" . $user['role'] . ")</p>";
            
            // Test password
            echo "<p><strong>Stored password hash:</strong> " . substr($user['password'], 0, 30) . "...</p>";
            
            if (password_verify($password, $user['password'])) {
                echo "<p style='color: green;'>✅ Password verification successful!</p>";
                echo "<p style='color: green;'>Login should work. There might be a session issue.</p>";
            } else {
                echo "<p style='color: red;'>❌ Password verification failed!</p>";
                
                // Test with known passwords
                echo "<h4>Testing known passwords:</h4>";
                if (password_verify('admin123', $user['password'])) {
                    echo "<p>✅ 'admin123' works for this user</p>";
                } elseif (password_verify('employee123', $user['password'])) {
                    echo "<p>✅ 'employee123' works for this user</p>";
                } else {
                    echo "<p>❌ Neither 'admin123' nor 'employee123' work</p>";
                    echo "<p>The password hash might be corrupted. Try recreating users.</p>";
                }
            }
        }
        
        $conn->close();
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Debug - Payroll System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Test Login Credentials</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? 'admin@payroll.com'); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       value="<?php echo htmlspecialchars($_POST['password'] ?? 'admin123'); ?>" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Test Login</button>
                        </form>
                        
                        <hr>
                        <h6>Quick Actions:</h6>
                        <div class="d-grid gap-2">
                            <a href="create_users.php" class="btn btn-warning">Recreate Default Users</a>
                            <a href="login.php" class="btn btn-success">Go to Real Login Page</a>
                            <a href="setup.php" class="btn btn-info">System Setup Check</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
