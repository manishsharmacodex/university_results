<?php
include("../../server/connection.php");

$dept = $conn->query("SELECT * FROM departments");

if(isset($_POST['save'])){
    $department_id = $_POST['department_id'];
    $course_name = $_POST['course_name'];

    $sql = "INSERT INTO courses (department_id, course_name)
            VALUES ('$department_id', '$course_name')";
    $conn->query($sql);
}
?>

<form method="POST">
    <select name="department_id">
        <?php while($d = $dept->fetch_assoc()){ ?>
            <option value="<?= $d['id'] ?>">
                <?= $d['name'] ?>
            </option>
        <?php } ?>
    </select>

    <input type="text" name="course_name" placeholder="Course Name">
    <button name="save">Add Course</button>
</form>