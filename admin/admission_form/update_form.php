<?php
// DB Connection
include(__DIR__ . "/../../server/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = 1;

    $form_title = $_POST['form_title'] ?? '';
    $form_description = $_POST['form_description'] ?? '';
    $button_text = $_POST['button_text'] ?? '';
    $background_color = $_POST['background_color'] ?? '#ffffff';
    $form_status = $_POST['form_status'] ?? '';

    // Basic validation (important)
    if (
        empty($form_title) ||
        empty($form_description) ||
        empty($button_text) ||
        !in_array($form_status, ['Open', 'Closed'])
    ) {
        echo "<script>alert('Invalid input data'); window.history.back();</script>";
        exit;
    }

    // Prepared statement (secure way)
    $stmt = $conn->prepare("
        UPDATE admission_form_settings 
        SET form_title = ?, 
            form_description = ?, 
            button_text = ?, 
            background_color = ?, 
            form_status = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "sssssi",
        $form_title,
        $form_description,
        $button_text,
        $background_color,
        $form_status,
        $id
    );

    if ($stmt->execute()) {
        echo "
        <script>
            alert('Admission Form Updated Successfully');
            window.location.href='list.php';
        </script>
        ";
    } else {
        echo "
        <script>
            alert('Update Failed');
            window.history.back();
        </script>
        ";
    }

    $stmt->close();
}
?>