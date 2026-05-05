<?php
include("../../config/auth.php");
include("../../server/connection.php");

if (isset($_POST['save'])) {
    $name = $_POST['name'];

    // Prepare statement
    $stmt = $conn->prepare("INSERT INTO university_results.departments (name) VALUES (?)");

    // Bind parameter
    $stmt->bind_param("s", $name);

    // Execute
    if ($stmt->execute()) {
        echo "Department added";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}
?>

<html>

<head>
    <title>Add Department</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
</head>

<body>
    <p><a href="./list.php">Go Back</a> / Add Department</p>

    <form method="POST">
        <input type="text" name="name" placeholder="Department Name" required>
        <button name="save">Add</button>
    </form>



    <script>
        document.querySelectorAll("input[type='text'], textarea").forEach(field => {
            field.addEventListener("input", function () {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
</body>

</html>