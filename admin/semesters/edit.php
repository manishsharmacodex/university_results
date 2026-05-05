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
$semester_name = trim($_POST['semester_name'] ?? '');

// Validation
if (
    $id <= 0 ||
    $semester_name === ''
) {
    $_SESSION['message'] = "Invalid data submitted!";
    header("Location: list.php");
    exit;
}

// Convert to uppercase (your original logic)
$semester_name = strtoupper($semester_name);

// Duplicate check (excluding current record)
$check_stmt = $conn->prepare("SELECT id FROM semesters WHERE semester_name = ? AND id != ?");
$check_stmt->bind_param("si", $semester_name, $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $_SESSION['message'] = "Semester already exists!";
    header("Location: list.php");
    exit;
}

$stmt = $conn->prepare("UPDATE semesters SET semester_name = ? WHERE id = ?");

if (!$stmt) {
    $_SESSION['message'] = "Database error!";
    header("Location: list.php");
    exit;
}

$stmt->bind_param("si", $semester_name, $id);

if ($stmt->execute()) {
    $_SESSION['message'] = ($stmt->affected_rows > 0)
        ? "Semester updated successfully!"
        : "No changes made!";
} else {
    $_SESSION['message'] = "Error updating semester!";
}

$stmt->close();

header("Location: list.php");
exit;
?>