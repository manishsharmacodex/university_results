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

/* ================= GET ID ================= */

$id = isset($_POST['id'])
    ? (int) $_POST['id']
    : 0;

/* ================= VALIDATE ID ================= */

if ($id <= 0) {

    $_SESSION['message'] = [
        'text' => 'Invalid ID!',
        'type' => 'error'
    ];

    header("Location: list.php");
    exit;
}

/* ================= DELETE QUERY ================= */

$stmt = $conn->prepare("
    DELETE FROM banks
    WHERE id = ?
");

if (!$stmt) {

    $_SESSION['message'] = [
        'text' => 'Database error!',
        'type' => 'error'
    ];

    header("Location: list.php");
    exit;
}

/* ================= EXECUTE ================= */

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        $_SESSION['message'] = [
            'text' => 'Bank deleted successfully!',
            'type' => 'success'
        ];

    } else {

        $_SESSION['message'] = [
            'text' => 'No record found!',
            'type' => 'error'
        ];
    }

} else {

    $_SESSION['message'] = [
        'text' => 'Delete failed!',
        'type' => 'error'
    ];
}

$stmt->close();

header("Location: list.php");
exit;

?>