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

$id = (int)($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');

if ($id <= 0 || $name === '' || strlen($name) < 2 || strlen($name) > 100) {
    $_SESSION['message'] = "Invalid data submitted!";
    header("Location: list.php");
    exit;
}

$stmt = $conn->prepare("UPDATE departments SET name=? WHERE id=?");

if (!$stmt) {
    $_SESSION['message'] = "Database error!";
    header("Location: list.php");
    exit;
}

$stmt->bind_param("si", $name, $id);

if ($stmt->execute()) {
    $_SESSION['message'] = ($stmt->affected_rows > 0)
        ? "Department updated successfully!"
        : "No changes made!";
} else {
    $_SESSION['message'] = "Error updating department!";
}

$stmt->close();

header("Location: list.php");
exit;
?>