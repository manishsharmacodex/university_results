<?php
include(__DIR__ . "/../server/connection.php");

$department_id = $_POST['department_id'] ?? '';

if (empty($department_id)) {
    echo "<option value=''>Select Course</option>";
    exit;
}

/* SAFE QUERY */
$stmt = $conn->prepare("
    SELECT id, course_name 
    FROM courses 
    WHERE department_id = ? 
    ORDER BY course_name ASC
");

$stmt->bind_param("i", $department_id);
$stmt->execute();

$result = $stmt->get_result();

echo "<option value=''>Select Course</option>";

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['id'] . "'>"
            . htmlspecialchars($row['course_name'], ENT_QUOTES, 'UTF-8') .
            "</option>";
    }

} else {
    echo "<option value='' disabled>No courses available</option>";
}

$stmt->close();
?>