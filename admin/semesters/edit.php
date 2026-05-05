<?php
include("../../config/auth.php");
include("../../server/connection.php");

$id = $_POST['id'] ?? null; // Read from POST

if (!$id) {
    die("Invalid ID");
}

$semester_name = strtoupper($_POST['semester_name'] ?? ''); // Always store uppercase

if ($semester_name) {
    // Check if semester already exists (excluding current one)
    $check_stmt = $conn->prepare("SELECT id FROM semesters WHERE semester_name=? AND id != ?");
    $check_stmt->bind_param("si", $semester_name, $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {
        // Update semester using prepared statement
        $stmt = $conn->prepare("UPDATE semesters SET semester_name=? WHERE id=?");
        $stmt->bind_param("si", $semester_name, $id);

        if ($stmt->execute()) {
            echo "<script>window.location.href='list.php';</script>";
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Semester already exists!";
    }
} else {
    die("Invalid data submitted");
}
?>