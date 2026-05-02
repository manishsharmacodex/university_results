<?php
include("../../server/connection.php");

// Step 1: Get ID from URL
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid ID");
}

// Step 2: Fetch existing data
$stmt = $conn->prepare("SELECT * FROM university_results.departments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Department not found");
}

// Step 3: Update data
if (isset($_POST['update'])) {
    $name = $_POST['name'];

    $update = $conn->prepare("UPDATE university_results.departments SET name = ? WHERE id = ?");
    $update->bind_param("si", $name, $id);

    if ($update->execute()) {
        header("Location: list.php");
        exit;
    } else {
        echo "Error: " . $update->error;
    }
}
?>

<p>
    <a href="./list.php">Go Back</a> / Edit Department
</p>

<form method="POST">
    <input type="text" name="name" value="<?= $data['name'] ?>" required>
    <button type="submit" name="update">Update</button>
</form>

<script>
    document.querySelectorAll("input[type='text'], textarea").forEach(field => {
        field.addEventListener("input", function () {
            this.value = this.value.toUpperCase();
        });
    });
</script>