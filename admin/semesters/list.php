<?php
include("../../server/connection.php");

$result = $conn->query("SELECT * FROM semesters");
?>

<h2>Semester</h2>

<p><a href="../dashboard/index.php">Home</a>/Semester</p>

<a href="add.php">Add New</a>

<table border="1">
<tr>
    <th>ID</th>
    <th>Name</th>
</tr>

<?php while($row = $result->fetch_assoc()){ ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['semester_name'] ?></td>
</tr>
<?php } ?>
</table>