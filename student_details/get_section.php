<?php
include("../server/connection.php");

$department = $_POST['department'];
$course = $_POST['course'];
$semester = $_POST['semester'];

function generateSection($conn, $department, $course, $semester)
{
    $sections = range('A', 'Z');

    $sql = "SELECT section, COUNT(*) as total 
            FROM student_details 
            WHERE department='$department' 
            AND course='$course' 
            AND semester='$semester'
            GROUP BY section";

    $result = $conn->query($sql);

    $sectionCounts = [];

    while ($row = $result->fetch_assoc()) {
        $sectionCounts[$row['section']] = $row['total'];
    }

    foreach ($sections as $sec) {
        if (!isset($sectionCounts[$sec]) || $sectionCounts[$sec] < 5) {
            return $sec;
        }
    }

    return "A";
}

echo generateSection($conn, $department, $course, $semester);
?>