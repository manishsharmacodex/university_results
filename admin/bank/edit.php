<?php
include("../../config/auth.php");
include("../../server/connection.php");

// Read ID and new bank_master_id from POST
$id = $_POST['id'] ?? null;
$bank_master_id = $_POST['bank_master_id'] ?? null;

if (!$id || !$bank_master_id) {
    die("Invalid data submitted");
}

// Prepare and execute update query
$stmt = $conn->prepare("UPDATE banks SET bank_master_id=? WHERE id=?");
$stmt->bind_param("ii", $bank_master_id, $id);

if ($stmt->execute()) {
    // Redirect back to list page (preserve page if sent)
    $page = $_GET['page'] ?? 1;
    echo "<script>window.location.href='list.php?page=$page';</script>";
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>