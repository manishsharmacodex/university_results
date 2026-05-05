<?php
include("../../config/auth.php");
include("../../server/connection.php");

// Get course ID
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid ID");
}

// Get departments
$dept = $conn->query("SELECT * FROM departments");

// Get current course data
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Course not found");
}

// Update
if (isset($_POST['update'])) {
    $department_id = $_POST['department_id'];
    $course_name = $_POST['course_name'];

    $update = $conn->prepare("UPDATE courses SET department_id = ?, course_name = ? WHERE id = ?");
    $update->bind_param("isi", $department_id, $course_name, $id);

    if ($update->execute()) {
        header("Location: list.php");
        exit;
    } else {
        echo "Error: " . $update->error;
    }
}
?>

<html>

<head>
    <title>Edit Courses</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
</head>

<body>
    <h3>Edit Course</h3>

    <form method="POST">
        <select name="department_id">
            <?php while ($d = $dept->fetch_assoc()) { ?>
                <option value="<?= $d['id'] ?>" <?= $d['id'] == $data['department_id'] ? 'selected' : '' ?>>
                    <?= $d['name'] ?>
                </option>
            <?php } ?>
        </select>

        <input type="text" name="course_name" value="<?= $data['course_name'] ?>" required>

        <button type="submit" name="update">Update</button>
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