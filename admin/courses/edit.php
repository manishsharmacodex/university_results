<?php
include("../../config/auth.php");
include("../../server/connection.php");

$id = $_POST['id'] ?? null; // <-- read from POST

if (!$id) {
    die("Invalid ID");
}

$course_name = $_POST['course_name'] ?? '';
$department_id = $_POST['department_id'] ?? '';

if ($course_name && $department_id) {
    $stmt = $conn->prepare("UPDATE courses SET course_name=?, department_id=? WHERE id=?");
    $stmt->bind_param("sii", $course_name, $department_id, $id);

    if ($stmt->execute()) {
        echo "<script>window.location.href='list.php';</script>";
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    die("Invalid data submitted");
}
?>