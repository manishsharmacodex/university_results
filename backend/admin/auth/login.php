<?php
require_once(__DIR__ . "/../../config/config.php");
session_start();

// Prevent browser cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

// Redirect if already logged in
if (!empty($_SESSION['admin'])) {
    // header("Location: ../dashboard/index.php");
    // exit;

    header("Location: " . BASE_URL . "dashboard");
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

                // header("Location: ../dashboard/index.php");
                // exit;

                header("Location: " . BASE_URL . "dashboard");
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
    <!-- <link rel="stylesheet" type="text/css" href="./src/login.css"> -->
    <link rel="stylesheet" href="<?= BASE_URL ?>backend/admin/auth/src/login.css">
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

    <!-- <script type="text/javascript" src="./src/login.js"></script> -->
    <script src="<?= BASE_URL ?>backend/admin/auth/src/login.js"></script>

</body>

</html>