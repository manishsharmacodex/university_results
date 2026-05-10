<?php

// Enable strict mysqli error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Load Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

try {

    // Load .env from project root
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    // Validate required ENV variables
    if (
        empty($_ENV['DB_HOST']) ||
        empty($_ENV['DB_USER']) ||
        empty($_ENV['DB_NAME'])
    ) {
        throw new Exception("Missing database environment variables");
    }

    // Create MySQL connection
    $conn = new mysqli(
        $_ENV['DB_HOST'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD'] ?? '',
        $_ENV['DB_NAME']
    );

    // Set charset
    $conn->set_charset("utf8mb4");

} catch (Throwable $e) {

    // Log real error (do not expose to users)
    error_log("DB Connection Error: " . $e->getMessage());

    die("Database connection failed. Please try again later.");
}

?>