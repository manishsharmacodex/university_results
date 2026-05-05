<?php
include("../../config/auth.php");
include("../../server/connection.php");

$result = $conn->query("SELECT * FROM semesters");
?>

<html>

<head>
    <title>List Semester</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
</head>

<body>
    <h2>Semester</h2>

    <p><a href="../dashboard/index.php">Home</a> / Semester</p>

    <a href="add.php">Add New</a>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['semester_name'] ?></td>

                <!-- EDIT -->
                <td>
                    <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
                </td>

                <!-- DELETE -->
                <td>
                    <a href="delete.php?id=<?= $row['id'] ?>"
                        onclick="return confirm('Are you sure you want to delete this semester?')">
                        Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>

</html>