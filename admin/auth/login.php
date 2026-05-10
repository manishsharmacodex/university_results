<?php
session_start();

// Prevent browser cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// If already logged in → redirect to dashboard
if (isset($_SESSION['admin'])) {
    header("Location: ../dashboard/index.php");
    exit;
}

// DB Connection File include
include(__DIR__ . "/../../server/connection.php");


// Function to generate captcha
function generateCaptcha()
{
    $_SESSION['num1'] = rand(1, 20);
    $_SESSION['num2'] = rand(1, 20);
    $_SESSION['operator'] = rand(0, 1) ? '+' : '-';
}

// Generate CAPTCHA initially
if (!isset($_SESSION['num1'])) {
    generateCaptcha();
}

// Calculate answer
function getCaptchaAnswer()
{
    if ($_SESSION['operator'] === '+') {
        return $_SESSION['num1'] + $_SESSION['num2'];
    } else {
        return $_SESSION['num1'] - $_SESSION['num2'];
    }
}

// error message display on login form
$error = "";

// AJAX request for refreshing captcha
if (isset($_POST['refresh_captcha'])) {
    generateCaptcha();
    echo $_SESSION['num1'] . "|" . $_SESSION['operator'] . "|" . $_SESSION['num2'];
    exit;
}


// login logic
if (isset($_POST['login'])) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';

    if ($username === '' || $password === '') {
        $error = "All fields are required";
        generateCaptcha();

    } elseif (
        !isset($_SESSION['num1'], $_SESSION['num2'], $_SESSION['operator']) ||
        (int) $captcha !== getCaptchaAnswer()
    ) {
        $error = "Wrong CAPTCHA answer";
        generateCaptcha();

    } else {

        $stmt = $conn->prepare("SELECT user_name, password FROM university_results.admin_user WHERE user_name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                session_regenerate_id(true);
                $_SESSION['admin'] = $username;

                unset($_SESSION['num1'], $_SESSION['num2'], $_SESSION['operator']);

                header("Location: ../dashboard/index.php");
                exit;

            } else {
                $error = "Invalid username or password";
                generateCaptcha();
            }

        } else {
            $error = "Invalid username or password";
            generateCaptcha();
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
            /* text-transform: uppercase; */
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
    <!-- Login box -->
    <div class="login-box">

        <h2>ERP Admin Login</h2>

        <?php if ($error != "") { ?>
            <div class="error"><?= $error ?></div>
        <?php } ?>

        <form method="POST">

            <input type="text" name="username" placeholder="Username" autocomplete="off" required>
            <input type="password" name="password" placeholder="Password" autocomplete="off" required>

            <!-- CAPTCHA -->
            <div class="captcha-container">
                <div class="captcha-text">
                    What is
                    <span id="num1"><?= $_SESSION['num1'] ?? '' ?></span>
                    <span id="operator"><?= $_SESSION['operator'] ?? '' ?></span>
                    <span id="num2"><?= $_SESSION['num2'] ?? '' ?></span> ?
                </div>

                <button type="button" class="refresh-btn" onclick="refreshCaptcha()">↻ Refresh Captcha</button>
            </div>

            <input type="text" name="captcha" placeholder="Enter Answer" autocomplete="off" required>

            <input type="submit" name="login" value="LOGIN">

        </form>
    </div>

</body>

</html>

<script>
    // session history
    if (window.history && window.history.pushState) {
        window.history.pushState(null, document.title, window.location.href);

        window.onpopstate = function () {
            window.history.pushState(null, document.title, window.location.href);
        };
    }


    // function for refresh captcha code
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