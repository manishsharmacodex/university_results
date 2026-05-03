<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent cache
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Force login
if (!isset($_SESSION['admin'])) {
    // header("Location: ../../admin/auth/login.php"); this is also work
    header("Location: ../auth/login.php");
    exit;
}
?>