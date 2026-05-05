<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent cache
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Security headers (optional but good)
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Check login
if (empty($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>