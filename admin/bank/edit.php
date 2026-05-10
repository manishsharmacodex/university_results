<?php

/* ================= DB CONNECTION ================= */
include(__DIR__ . "/../../server/connection.php");

include("../../config/auth.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ================= VALID REQUEST ================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: list.php");
    exit;
}

/* ================= GET DATA ================= */

$id = (int) ($_POST['id'] ?? 0);

$bank_master_id = (int) ($_POST['bank_master_id'] ?? 0);

$page = (int) ($_POST['page'] ?? 1);

/* ================= VALIDATION ================= */

if ($id <= 0 || $bank_master_id <= 0) {

    $_SESSION['message'] = [
        'text' => 'Invalid data submitted!',
        'type' => 'error'
    ];

    header("Location: list.php?page=" . $page);
    exit;
}

/* ================= UPDATE QUERY ================= */

$stmt = $conn->prepare("
    UPDATE banks
    SET bank_master_id = ?
    WHERE id = ?
");

if (!$stmt) {

    $_SESSION['message'] = [
        'text' => 'Database error!',
        'type' => 'error'
    ];

    header("Location: list.php?page=" . $page);
    exit;
}

/* ================= EXECUTE ================= */

$stmt->bind_param("ii", $bank_master_id, $id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        $_SESSION['message'] = [
            'text' => 'Bank updated successfully!',
            'type' => 'success'
        ];

    } else {

        $_SESSION['message'] = [
            'text' => 'No changes made!',
            'type' => 'error'
        ];
    }

} else {

    $_SESSION['message'] = [
        'text' => 'Error updating bank!',
        'type' => 'error'
    ];
}

$stmt->close();

header("Location: list.php?page=" . $page);
exit;

?>