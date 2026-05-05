<?php
include("../../config/auth.php");
include("../../server/connection.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: list.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$bank_master_id = (int)($_POST['bank_master_id'] ?? 0);
$page = (int)($_POST['page'] ?? 1);

// Validation
if (
    $id <= 0 ||
    $bank_master_id <= 0
) {
    $_SESSION['message'] = "Invalid data submitted!";
    header("Location: list.php?page=" . $page);
    exit;
}

$stmt = $conn->prepare("UPDATE banks SET bank_master_id = ? WHERE id = ?");

if (!$stmt) {
    $_SESSION['message'] = "Database error!";
    header("Location: list.php?page=" . $page);
    exit;
}

$stmt->bind_param("ii", $bank_master_id, $id);

if ($stmt->execute()) {
    $_SESSION['message'] = ($stmt->affected_rows > 0)
        ? "Bank updated successfully!"
        : "No changes made!";
} else {
    $_SESSION['message'] = "Error updating bank!";
}

$stmt->close();

header("Location: list.php?page=" . $page);
exit;
?>