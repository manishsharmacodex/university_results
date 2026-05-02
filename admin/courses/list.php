<?php
include("../../server/connection.php");

$result = $conn->query("
    SELECT courses.id, courses.course_name, departments.name AS department_name
    FROM courses
    JOIN departments ON courses.department_id = departments.id
");
?>

<h2>Courses</h2>

<p><a href="../dashboard/index.php">Home</a>/Courses</p>

<a href="add.php">Add New</a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Course Name</th>
        <th>Department</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['course_name'] ?></td>
            <td><?= $row['department_name'] ?></td>
        </tr>
    <?php } ?>
</table>