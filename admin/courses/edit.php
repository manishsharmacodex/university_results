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
$course_name = trim($_POST['course_name'] ?? '');
$department_id = (int)($_POST['department_id'] ?? 0);

// Validation
if (
    $id <= 0 ||
    $department_id <= 0 ||
    $course_name === '' ||
    strlen($course_name) < 2 ||
    strlen($course_name) > 100
) {
    $_SESSION['message'] = "Invalid data submitted!";
    header("Location: list.php");
    exit;
}

// Convert to uppercase (same as your original logic)
$course_name = strtoupper($course_name);

$stmt = $conn->prepare("UPDATE courses SET course_name = ?, department_id = ? WHERE id = ?");

if (!$stmt) {
    $_SESSION['message'] = "Database error!";
    header("Location: list.php");
    exit;
}

$stmt->bind_param("sii", $course_name, $department_id, $id);

if ($stmt->execute()) {
    $_SESSION['message'] = ($stmt->affected_rows > 0)
        ? "Course updated successfully!"
        : "No changes made!";
} else {
    $_SESSION['message'] = "Error updating course!";
}

$stmt->close();

header("Location: list.php");
exit;
?>