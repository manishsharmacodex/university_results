<?php
include("../../config/auth.php");
include("../../server/connection.php");

// Fetch bank master list
$master = $conn->query("SELECT id, bank_name FROM bank_master");

if (isset($_POST['save'])) {

    $bank_master_id = $_POST['bank_master_id'];

    if (empty($bank_master_id)) {
        die("Please select a bank");
    }

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

<p><a href="./list.php">Go Back</a></p>

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

    <button type="submit" name="save">Add Bank</button>

</form>