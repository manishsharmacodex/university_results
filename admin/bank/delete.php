<?php
include("../../config/auth.php");
include("../../server/connection.php");

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid ID");
}

$stmt = $conn->prepare("DELETE FROM banks WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: list.php");
    exit;
} else {
    echo "Error deleting record: " . $stmt->error;
}
?>