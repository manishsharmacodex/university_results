<?php
include("../../config/auth.php");
include("../../server/connection.php");

$result = $conn->query("
    SELECT banks.id, bank_master.bank_name
    FROM banks
    JOIN bank_master ON banks.bank_master_id = bank_master.id
    ORDER BY banks.id ASC
");
?>

<h2>Banks</h2>

<p><a href="../dashboard/index.php">Home</a>/Banks List</p>

<a href="add.php">Add New</a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Bank Name</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['bank_name'] ?></td>

            <td><a href="edit.php?id=<?= $row['id'] ?>">Edit</a></td>
            <td>
                <a href="delete.php?id=<?= $row['id'] ?>"
                    onclick="return confirm('Are you sure you want to delete this bank?');">
                    Delete
                </a>
            </td>
        </tr>
    <?php } ?>
</table>