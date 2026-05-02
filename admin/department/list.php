<?php
include("../../config/auth.php");
include("../../server/connection.php");

$result = $conn->query("SELECT * FROM departments");
?>

<h2>Departments</h2>

<p><a href="../dashboard/index.php">Home</a> / Departments</p>

<a href="./add.php">Add New</a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Action</th>
        <th>Delete</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
            </td>
            <td>
                <a href="delete.php?id=<?= $row['id'] ?>" 
                   onclick="return confirm('Are you sure you want to delete this department?')">
                   Delete
                </a>
            </td>
        </tr>
    <?php } ?>
</table>