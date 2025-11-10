<?php
require_once 'includes/auth.php';

$auth = new Auth();

// If logged in, redirect to appropriate dashboard
if ($auth->isLoggedIn()) {
    $auth->redirectBasedOnRole();
} else {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit();
}
?>
