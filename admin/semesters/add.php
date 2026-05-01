<?php
include("../../server/connection.php");

if (isset($_POST['save'])) {
    $semester = $_POST['semester'];

    $sql = "INSERT INTO university_results.semesters (semester_name) VALUES ('$semester')";
    $conn->query($sql);
    echo "Semesters added";
}
?>

<form method="POST">
    <label>Semester Allotment</label>
    <!-- <input type="text" name="semester" placeholder="Enter Semester"> -->
    <select name="semester" required>
        <option value="Select Semester" selected>Select Semester</option>
        <option value="Semester 1">Semester 1</option>
        <option value="Semester 2">Semester 2</option>
        <option value="Semester 3">Semester 3</option>
        <option value="Semester 4">Semester 4</option>
        <option value="Semester 5">Semester 5</option>
        <option value="Semester 6">Semester 6</option>
        <option value="Semester 7">Semester 7</option>
        <option value="Semester 8">Semester 8</option>
    </select>
    <button name="save">Add</button>
</form>