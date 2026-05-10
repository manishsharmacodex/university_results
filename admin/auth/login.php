<?php
session_start();

// Prevent browser cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

// Redirect if already logged in
if (!empty($_SESSION['admin'])) {
    header("Location: ../dashboard/index.php");
    exit;
}

include(__DIR__ . "/../../server/connection.php");

$error = "";

/* ---------------- CAPTCHA FUNCTIONS ---------------- */

function generateCaptcha()
{
    $_SESSION['num1'] = rand(1, 20);
    $_SESSION['num2'] = rand(1, 20);
    $_SESSION['operator'] = rand(0, 1) ? '+' : '-';
}

function getCaptchaAnswer()
{
    return ($_SESSION['operator'] === '+')
        ? $_SESSION['num1'] + $_SESSION['num2']
        : $_SESSION['num1'] - $_SESSION['num2'];
}

function resetCaptcha()
{
    generateCaptcha();
}

/* Common error handler (FIXED: moved outside login block) */
function setError(&$error, $msg)
{
    $error = $msg;
    resetCaptcha();
}

/* Ensure CAPTCHA exists */
if (!isset($_SESSION['num1'], $_SESSION['num2'], $_SESSION['operator'])) {
    generateCaptcha();
}

/* ---------------- AJAX CAPTCHA REFRESH ---------------- */

if (isset($_POST['refresh_captcha'])) {
    generateCaptcha();
    echo $_SESSION['num1'] . "|" . $_SESSION['operator'] . "|" . $_SESSION['num2'];
    exit;
}

/* ---------------- LOGIN HANDLER ---------------- */

if (isset($_POST['login'])) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';

    if ($username === '' || $password === '' || $captcha === '') {

        setError($error, "All fields are required");

    } elseif ((int) $captcha !== getCaptchaAnswer()) {

        setError($error, "Wrong CAPTCHA answer");

    } else {

        $stmt = $conn->prepare("
            SELECT user_name, password
            FROM university_results.admin_user
            WHERE user_name = ?
            LIMIT 1
        ");

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {

            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                session_regenerate_id(true);
                $_SESSION['admin'] = $row['user_name'];

                unset($_SESSION['num1'], $_SESSION['num2'], $_SESSION['operator']);

                header("Location: ../dashboard/index.php");
                exit;

            } else {
                setError($error, "Invalid username or password");
            }

        } else {
            setError($error, "Invalid username or password");
        }

        $stmt->close();
    }
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Admin Login</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #081224, #0e1c35);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: #fff;
            padding: 40px;
            width: 400px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 20px;
            color: #2a5298;
            font-weight: 600;
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
        }

        .refresh-btn {
            padding: 6px 10px;
            background: #2a5298;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="login-box">

        <h2>ERP Admin Login</h2>

        <?php if ($error != "") { ?>
            <div class="error"><?= $error ?></div>
        <?php } ?>

        <form method="POST">

            <input type="text" name="username" placeholder="Username" autocomplete="off">
            <input type="password" name="password" placeholder="Password" autocomplete="off">

            <!-- CAPTCHA -->
            <div class="captcha-container">
                <div class="captcha-text">
                    What is
                    <span id="num1"><?= $_SESSION['num1'] ?></span>
                    <span id="operator"><?= $_SESSION['operator'] ?></span>
                    <span id="num2"><?= $_SESSION['num2'] ?></span> ?
                </div>

                <button type="button" class="refresh-btn" onclick="refreshCaptcha()">↻ Refresh Captcha</button>
            </div>

            <input type="text" name="captcha" placeholder="Enter Answer" autocomplete="off">

            <input type="submit" name="login" value="LOGIN">

        </form>
    </div>

</body>

</html>

<script>
    // windows history prevent to back button
    if (window.history && window.history.pushState) {
        window.history.pushState(null, document.title, window.location.href);

        window.onpopstate = function () {
            window.history.pushState(null, document.title, window.location.href);
        };
    }


    // refresh functions
    function refreshCaptcha() {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (this.status === 200) {
                let data = this.responseText.trim().split("|");

                if (data.length === 3) {
                    document.getElementById("num1").innerText = data[0];
                    document.getElementById("operator").innerText = data[1];
                    document.getElementById("num2").innerText = data[2];
                }
            }
        };

        xhr.send("refresh_captcha=1");
    }
</script>