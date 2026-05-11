<?php
include("../../../server/connection.php");

$dept_id = $_GET['dept_id'];

$sql = "SELECT id, course_name FROM courses WHERE department_id='$dept_id'";
$result = $conn->query($sql);

$courses = [];

while($row = $result->fetch_assoc()){
    $courses[] = $row;
}

echo json_encode($courses);
?>