<?php
include("../../server/connection.php");
include("../../config/auth.php");

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* FETCH DATA */
$result = mysqli_query($conn, "SELECT * FROM admission_form_settings WHERE id='1'");
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Form Settings</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            color: #fff;
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

        <form method="POST" action="./update_form.php">

            <label>Form Title</label>
            <input type="text" name="form_title" value="<?= htmlspecialchars($data['form_title']) ?>" required>

            <label>Form Description</label>
            <textarea name="form_description" required><?= htmlspecialchars($data['form_description']) ?></textarea>

            <label>Button Text</label>
            <input type="text" name="button_text" value="<?= htmlspecialchars($data['button_text']) ?>" required>

            <label>Background Color</label>
            <input type="color" name="background_color" value="<?= htmlspecialchars($data['background_color']) ?>">

            <label>Admission Status</label>
            <select name="form_status">
                <option value="Open" <?= $data['form_status'] == 'Open' ? 'selected' : '' ?>>Open</option>
                <option value="Closed" <?= $data['form_status'] == 'Closed' ? 'selected' : '' ?>>Closed</option>
            </select>

            <button type="submit">
                Update Admission Form
            </button>

        </form>

    </div>

</body>

</html>