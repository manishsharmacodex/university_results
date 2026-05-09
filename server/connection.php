<?php

// Enable strict mysqli error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Load Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

try {

    // FIX: point to project root (NOT /server folder)
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    // Database configuration from ENV
    $server_name = $_ENV['DB_HOST'];
    $user_name   = $_ENV['DB_USER'];
    $password    = $_ENV['DB_PASSWORD'];
    $db_name     = $_ENV['DB_NAME'];

    // Create connection
    $conn = new mysqli(
        $server_name,
        $user_name,
        $password,
        $db_name
    );

    // Set charset
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {

    // Log actual error (never expose details to users)
    error_log($e->getMessage());

    die("Database connection failed. Please try again later.");
}

?>