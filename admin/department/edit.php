<?php
include("../../config/auth.php");
include("../../server/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $name = $_POST['name'];

    $stmt = $conn->prepare("UPDATE departments SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $id);

    if ($stmt->execute()) {
        echo "<script>
            alert('Department updated successfully');
            window.location.href='list.php';
        </script>";
    }
}
?>