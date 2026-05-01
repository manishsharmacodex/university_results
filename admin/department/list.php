<?php
include("../../server/connection.php");

$result = $conn->query("SELECT * FROM departments");
?>

<h2>Departments</h2>

<a href="add.php">Add New</a>

<table border="1">
<tr>
    <th>ID</th>
    <th>Name</th>
</tr>

<?php while($row = $result->fetch_assoc()){ ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['name'] ?></td>
</tr>
<?php } ?>
</table>