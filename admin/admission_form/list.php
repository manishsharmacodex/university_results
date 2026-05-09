<?php

include("../../server/connection.php");
include("../../config/auth.php");

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* UPDATE DATA */

if (isset($_POST['update_form'])) {

    $form_title = mysqli_real_escape_string($conn, $_POST['form_title']);

    $form_description = mysqli_real_escape_string(
        $conn,
        $_POST['form_description']
    );

    $button_text = mysqli_real_escape_string(
        $conn,
        $_POST['button_text']
    );

    $background_color = $_POST['background_color'];

    $form_status = $_POST['form_status'];

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
    }
}

/* FETCH DATA */

$data = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT * FROM admission_form_settings WHERE id='1'"
    )
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Admission Form Settings</title>

    <style>
        body {
            background: #f1f5f9;
            padding: 40px;
        }

        .container {

            max-width: 700px;
            margin: auto;

            background: #fff;

            padding: 30px;

            border-radius: 15px;

            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #1e3a8a;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: 600;
        }

        input,
        textarea,
        select {

            width: 100%;
            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 10px;
        }

        textarea {
            height: 120px;
            resize: none;
        }

        button {

            margin-top: 20px;

            background: #2563eb;
            color: white;

            border: none;

            padding: 12px 20px;

            border-radius: 10px;

            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }
    </style>

</head>

<body>

    <div class="container">

        <h2>Admission Form Management</h2>

        <form method="POST">

            <label>Form Title</label>

            <input type="text" name="form_title" value="<?= htmlspecialchars($data['form_title']) ?>" required>

            <label>Form Description</label>

            <textarea name="form_description" required><?= htmlspecialchars($data['form_description']) ?></textarea>

            <label>Button Text</label>

            <input type="text" name="button_text" value="<?= htmlspecialchars($data['button_text']) ?>" required>

            <label>Background Color</label>

            <input type="color" name="background_color" value="<?= $data['background_color'] ?>">

            <label>Admission Status</label>

            <select name="form_status">

                <option value="Open" <?= $data['form_status'] == 'Open' ? 'selected' : '' ?>>
                    Open
                </option>

                <option value="Closed" <?= $data['form_status'] == 'Closed' ? 'selected' : '' ?>>
                    Closed
                </option>

            </select>

            <button type="submit" name="update_form">

                Update Admission Form

            </button>

        </form>

    </div>

</body>

</html>