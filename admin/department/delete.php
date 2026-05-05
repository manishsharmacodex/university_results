<?php
include("../../config/auth.php");
include("../../server/connection.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: list.php");
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    $_SESSION['message'] = "Invalid ID!";
    header("Location: list.php");
    exit;
}

$stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
if (!$stmt) {
    $_SESSION['message'] = "Database error!";
    header("Location: list.php");
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Department deleted successfully!";
    } else {
        $_SESSION['message'] = "No record found!";
    }
} else {
    $_SESSION['message'] = "Delete failed!";
}

$stmt->close();

header("Location: list.php");
exit;
?>