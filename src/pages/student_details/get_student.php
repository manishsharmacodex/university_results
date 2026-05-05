<?php
include("../server/connection.php");

if(isset($_GET['id'])){

$id = $_GET['id'];

$sql = "
SELECT s.*, 
       d.name AS department_name,
       c.course_name,
       bm.bank_name AS bank_name
FROM student_details s
LEFT JOIN departments d ON s.department = d.id
LEFT JOIN courses c ON s.course = c.id
LEFT JOIN banks b ON s.bank_name = b.id
LEFT JOIN bank_master bm ON b.bank_master_id = bm.id
WHERE s.id = '$id'
";

$result = $conn->query($sql);

if($result->num_rows > 0){
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "Student not found"]);
}

}
?>