<?php
include("../../config/auth.php");
include("../../server/connection.php");

$id = $_GET['id'] ?? null;

if (!$id) die("Invalid ID");

// Get current record
$stmt = $conn->prepare("SELECT * FROM banks WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) die("Not found");

// Get master list
$master = $conn->query("SELECT * FROM bank_master");

if (isset($_POST['update'])) {
    $bank_master_id = $_POST['bank_master_id'];

    $update = $conn->prepare("UPDATE banks SET bank_master_id=? WHERE id=?");
    $update->bind_param("ii", $bank_master_id, $id);

    if ($update->execute()) {
        header("Location: list.php");
        exit;
    } else {
        echo "Error: " . $update->error;
    }
}
?>

<h3>Edit Bank</h3>

<p><a href="./list.php">Go Back</a>/Update Bank</p>

<form method="POST">
    <select name="bank_master_id" required>
        <?php while ($row = $master->fetch_assoc()) { ?>
            <option value="<?= $row['id'] ?>"
                <?= $row['id'] == $data['bank_master_id'] ? 'selected' : '' ?>>
                <?= $row['bank_name'] ?>
            </option>
        <?php } ?>
    </select>

    <br><br>
    <button name="update">Update</button>
</form>