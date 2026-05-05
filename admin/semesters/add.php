<?php
include("../../config/auth.php");
include("../../server/connection.php");

if (isset($_POST['save'])) {
    $semester = $_POST['semester'];

    $sql = "INSERT INTO university_results.semesters (semester_name) VALUES ('$semester')";
    $conn->query($sql);
    echo "Semesters added";
}
?>

<html>

<head>
    <title>Add Semester</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
</head>

<body>
    <p><a href="./list.php">GO Back</a>/Semester</p>

    <form method="POST">
        <label>Semester Allotment</label>
        <!-- <input type="text" name="semester" placeholder="Enter Semester"> -->
        <select name="semester" required>
            <option value="Select Semester" selected>SELECT SEMESTER</option>
            <option value="SEMESTER 1">SEMESTER 1</option>
            <option value="SEMESTER 2">SEMESTER 2</option>
            <option value="SEMESTER 3">SEMESTER 3</option>
            <option value="SEMESTER 4">SEMESTER 4</option>
            <option value="SEMESTER 5">SEMESTER 5</option>
            <option value="SEMESTER 6">SEMESTER 6</option>
            <option value="SEMESTER 7">SEMESTER 7</option>
            <option value="SEMESTER 8">SEMESTER 8</option>
        </select>
        <button name="save">Add</button>
    </form>
</body>

</html>