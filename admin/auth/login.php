<?php
session_start();

// If already logged in → redirect to dashboard
if (isset($_SESSION['admin'])) {
    header("Location: ../dashboard/index.php");
    exit;
}

// Prevent browser cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include("../../server/connection.php");

// Function to generate captcha
function generateCaptcha()
{
    $_SESSION['num1'] = rand(1, 20);
    $_SESSION['num2'] = rand(1, 20);

    // Random operator
    $_SESSION['operator'] = rand(0, 1) ? '+' : '-';
}

// Generate CAPTCHA initially
if (!isset($_SESSION['num1']) || !isset($_SESSION['num2']) || !isset($_SESSION['operator'])) {
    generateCaptcha();
}

// Calculate answer
if ($_SESSION['operator'] === '+') {
    $captcha_answer = $_SESSION['num1'] + $_SESSION['num2'];
} else {
    $captcha_answer = $_SESSION['num1'] - $_SESSION['num2'];
}

$error = "";

// AJAX request for refreshing captcha
if (isset($_POST['refresh_captcha'])) {
    generateCaptcha();
    echo $_SESSION['num1'] . "|" . $_SESSION['operator'] . "|" . $_SESSION['num2'];
    exit;
}

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $captcha = $_POST['captcha'];

    // Recalculate answer again (important)
    if ($_SESSION['operator'] === '+') {
        $captcha_answer = $_SESSION['num1'] + $_SESSION['num2'];
    } else {
        $captcha_answer = $_SESSION['num1'] - $_SESSION['num2'];
    }

    // CAPTCHA check
    if ((int) $captcha !== $captcha_answer) {

        $error = "Wrong CAPTCHA answer";
        generateCaptcha();

    } else {

        $sql = "SELECT * FROM university_results.admin_user 
                WHERE user_name='$username' AND password='$password'";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {

            $_SESSION['admin'] = $username;

            unset($_SESSION['num1']);
            unset($_SESSION['num2']);
            unset($_SESSION['operator']);

            header("Location: ../dashboard/index.php");
            exit;

        } else {

            $error = "Invalid username or password";
            generateCaptcha();
        }
    }
}
?>

<html>

<head>
    <title>ERP Login</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">

    <script>
        function refreshCaptcha() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                if (this.status === 200) {
                    let data = this.responseText.split("|");
                    document.getElementById("num1").innerText = data[0];
                    document.getElementById("operator").innerText = data[1];
                    document.getElementById("num2").innerText = data[2];
                }
            };

            xhr.send("refresh_captcha=1");
        }
    </script>

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

        .captcha-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .captcha-text {
            font-weight: bold;
            color: #333;
        }

        .refresh-btn {
            padding: 6px 10px;
            background: #2a5298;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .refresh-btn:hover {
            background: #1e3c72;
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

            <!-- CAPTCHA -->
            <div class="captcha-container">
                <div class="captcha-text">
                    What is
                    <span id="num1"><?= $_SESSION['num1'] ?></span>
                    <span id="operator"><?= $_SESSION['operator'] ?></span>
                    <span id="num2"><?= $_SESSION['num2'] ?></span> ?
                </div>

                <button type="button" class="refresh-btn" onclick="refreshCaptcha()">
                    ↻ Refresh
                </button>
            </div>

            <input type="text" name="captcha" placeholder="Enter Answer" required>

            <input type="submit" name="login" value="LOGIN">

        </form>

    </div>

</body>
</html>


<script>
    if (window.history && window.history.pushState) {
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function () {
            window.location.href = "../dashboard/index.php";
        };
    }
</script>