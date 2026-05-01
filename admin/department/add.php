<?php
include("../../server/connection.php");

if (isset($_POST['save'])) {
    $name = $_POST['name'];

    $sql = "INSERT INTO university_results.departments (name) VALUES ('$name')";
    $conn->query($sql);

    echo "Department added";
}
?>

<form method="POST">
    <input type="text" name="name" placeholder="Department Name">
    <button name="save">Add</button>
</form>