<?php
include("../../server/connection.php");

if(isset($_POST['save'])){
    $semester = $_POST['semester'];

    $sql = "INSERT INTO university_results.semesters (semester_name) VALUES ('$semester')";
    $conn->query($sql);
    echo "Semesters added";
}
?>

<form method="POST">
    <label>Semester Allotment</label>
    <input type="number" name="semester" placeholder="Enter Semester">
    <button name="save">Add</button>
</form>