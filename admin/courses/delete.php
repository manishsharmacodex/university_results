<?php
include("../../config/auth.php");
include("../../server/connection.php");

// Get ID from URL
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid ID");
}

// Prepare delete query
$stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Redirect back to list after delete
    header("Location: list.php");
    exit;
} else {
    echo "Error deleting course: " . $stmt->error;
}
?>