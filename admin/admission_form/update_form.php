<?php
include("../../server/connection.php");
include("../../config/auth.php");

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $form_title = mysqli_real_escape_string($conn, $_POST['form_title']);
    $form_description = mysqli_real_escape_string($conn, $_POST['form_description']);
    $button_text = mysqli_real_escape_string($conn, $_POST['button_text']);
    $background_color = mysqli_real_escape_string($conn, $_POST['background_color']);
    $form_status = mysqli_real_escape_string($conn, $_POST['form_status']);

    $update = mysqli_query($conn, "
        UPDATE admission_form_settings SET
            form_title='$form_title',
            form_description='$form_description',
            button_text='$button_text',
            background_color='$background_color',
            form_status='$form_status'
        WHERE id='1'
    ");

    if ($update) {
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
}
?>