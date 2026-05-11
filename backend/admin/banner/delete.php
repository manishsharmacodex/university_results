<?php
include("../../server/connection.php");
include("../../config/auth.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: list.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['message'] = "Invalid ID!";
    header("Location: list.php");
    exit;
}

/* GET IMAGE */
$stmt = $conn->prepare("SELECT image FROM banners WHERE id = ?");
if (!$stmt) {
    $_SESSION['message'] = "Database error!";
    header("Location: list.php");
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

/* DELETE BANNER */
$stmt = $conn->prepare("DELETE FROM banners WHERE id = ?");
if (!$stmt) {
    $_SESSION['message'] = "Database error!";
    header("Location: list.php");
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    /* DELETE FILE */
    if (!empty($row['image'])) {
        $filePath = "../uploads/banners/" . $row['image'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $_SESSION['message'] = ($stmt->affected_rows > 0)
        ? "Banner deleted successfully!"
        : "No record found!";

} else {
    $_SESSION['message'] = "Delete failed!";
}

$stmt->close();

header("Location: list.php");
exit;
?>