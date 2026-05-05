<?php
include("../../config/auth.php");
include("../../server/connection.php");

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid ID");
}

// get data
$stmt = $conn->prepare("SELECT * FROM semesters WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Semester not found");
}

// update
if (isset($_POST['update'])) {
    $semester = $_POST['semester'];

    $update = $conn->prepare("UPDATE semesters SET semester_name = ? WHERE id = ?");
    $update->bind_param("si", $semester, $id);

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
    <title>Edit Semester</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
</head>

<body>
    <h3>Edit Semester</h3>

    <form method="POST">
        <select name="semester" required>
            <option value="SEMESTER 1" <?= $data['semester_name'] == "SEMESTER 1" ? 'selected' : '' ?>>SEMESTER 1</option>
            <option value="SEMESTER 2" <?= $data['semester_name'] == "SEMESTER 2" ? 'selected' : '' ?>>SEMESTER 2</option>
            <option value="SEMESTER 3" <?= $data['semester_name'] == "SEMESTER 3" ? 'selected' : '' ?>>SEMESTER 3</option>
            <option value="SEMESTER 4" <?= $data['semester_name'] == "SEMESTER 4" ? 'selected' : '' ?>>SEMESTER 4</option>
            <option value="SEMESTER 5" <?= $data['semester_name'] == "SEMESTER 5" ? 'selected' : '' ?>>SEMESTER 5</option>
            <option value="SEMESTER 6" <?= $data['semester_name'] == "SEMESTER 6" ? 'selected' : '' ?>>SEMESTER 6</option>
            <option value="SEMESTER 7" <?= $data['semester_name'] == "SEMESTER 7" ? 'selected' : '' ?>>SEMESTER 7</option>
            <option value="SEMESTER 8" <?= $data['semester_name'] == "SEMESTER 8" ? 'selected' : '' ?>>SEMESTER 8</option>
        </select>

        <button type="submit" name="update">Update</button>
    </form>
</body>

</html>