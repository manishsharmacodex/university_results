<?php
session_start();
include("../../server/connection.php");

// Generate CAPTCHA only once
if (!isset($_SESSION['num1']) || !isset($_SESSION['num2'])) {
    $_SESSION['num1'] = rand(1, 20);
    $_SESSION['num2'] = rand(1, 20);
}

$captcha_answer = $_SESSION['num1'] + $_SESSION['num2'];

$error = "";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $captcha = $_POST['captcha'];

    // CAPTCHA check
    if ((int) $captcha !== $captcha_answer) {

        $error = "Wrong CAPTCHA answer";

        // regenerate CAPTCHA
        $_SESSION['num1'] = rand(1, 20);
        $_SESSION['num2'] = rand(1, 20);

    } else {

        $sql = "SELECT * FROM university_results.admin_user 
                WHERE user_name='$username' AND password='$password'";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {

            $_SESSION['admin'] = $username;

            unset($_SESSION['num1']);
            unset($_SESSION['num2']);

            header("Location: ../dashboard/index.php");
            exit;

        } else {

            $error = "Invalid username or password";

            // regenerate CAPTCHA
            $_SESSION['num1'] = rand(1, 20);
            $_SESSION['num2'] = rand(1, 20);
        }
    }
}
?>
<html>

<head>
    <title>ERP Login</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #081224, #0e1c35);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: #fff;
            padding: 40px;
            width: 360px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 20px;
            color: #2a5298;
        }

        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
        }

        .login-box input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background: #2a5298;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .login-box input[type="submit"]:hover {
            background: #1e3c72;
        }

        .captcha {
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="login-box">

        <h2>University ERP Login</h2>

        <?php if ($error != "") { ?>
            <div class="error"><?= $error ?></div>
        <?php } ?>

        <form method="POST">

            <input type="text" name="username" placeholder="Username" required>

            <input type="password" name="password" placeholder="Password" required>

            <div class="captcha">
                What is <?= $_SESSION['num1'] ?> + <?= $_SESSION['num2'] ?> ?
            </div>

            <input type="text" name="captcha" placeholder="Enter Answer" required>

            <input type="submit" name="login" value="LOGIN">

        </form>

    </div>

</body>

</html>