<?php
include("../../config/auth.php");
include("../../server/connection.php");

// Fetch master banks
$master = $conn->query("SELECT * FROM bank_master");

if (isset($_POST['save'])) {
    $bank_master_id = $_POST['bank_master_id'];

    $stmt = $conn->prepare("INSERT INTO banks (bank_master_id) VALUES (?)");
    $stmt->bind_param("i", $bank_master_id);

    if ($stmt->execute()) {
        header("Location: list.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<h3>Add Bank</h3>

<form method="POST">
    <select name="bank_master_id" required>
        <option value="">Select Bank</option>
        <?php while ($row = $master->fetch_assoc()) { ?>
            <option value="<?= $row['id'] ?>">
                <?= $row['bank_name'] ?>
            </option>
        <?php } ?>
    </select>

    <br><br>
    <button name="save">Add Bank</button>
</form>