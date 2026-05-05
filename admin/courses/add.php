<?php
include("../../config/auth.php");
include("../../server/connection.php");

$dept = $conn->query("SELECT * FROM departments");

if (isset($_POST['save'])) {
    $department_id = $_POST['department_id'];
    $course_name = $_POST['course_name'];

    $stmt = $conn->prepare("INSERT INTO courses (department_id, course_name) VALUES (?, ?)");
    $stmt->bind_param("is", $department_id, $course_name);

    if ($stmt->execute()) {
        echo "Course added";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<html>

<head>
    <title>add Courses</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
</head>

<body>
    <p><a href="./list.php">Go Back</a>/Courses</p>

    <form method="POST">
        <select name="department_id">
            <?php while ($d = $dept->fetch_assoc()) { ?>
                <option value="<?= $d['id'] ?>">
                    <?= $d['name'] ?>
                </option>
            <?php } ?>
        </select>

        <input type="text" name="course_name" placeholder="Course Name">
        <button name="save">Add Course</button>
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