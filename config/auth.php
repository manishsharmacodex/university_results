<?php
session_start();

// If admin is not logged in, redirect to login page
if (!isset($_SESSION['admin'])) {
    // header("Location: ../../admin/auth/login.php"); this is also work
    header("Location: /university_results/admin/auth/login.php");
    exit();
}
?>