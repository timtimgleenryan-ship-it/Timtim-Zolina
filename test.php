<?php
echo "<h1>✅ XAMPP is Working!</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";

echo "<hr>";
echo "<h2>Available Files:</h2>";
echo "<ul>";
echo "<li><a href='login.php'>Login Page</a></li>";
echo "<li><a href='setup.php'>Setup Check</a></li>";
echo "<li><a href='fix_login.php'>Fix Login Issues</a></li>";
echo "<li><a href='install.php'>Installation Script</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<p>1. First run: <a href='fix_login.php'>fix_login.php</a></p>";
echo "<p>2. Then go to: <a href='login.php'>login.php</a></p>";
?>
