<?php
session_start();
include("../server/connection.php");

$error = "";

if (isset($_POST["login"])) {

    $user = trim($_POST['userName']);
    $pwd = trim($_POST['password']);

    if (!empty($user) && !empty($pwd)) {

        // SECURE QUERY (Prepared Statement)
        $stmt = $conn->prepare("SELECT * FROM university_results.admin_user WHERE user_name = ? AND password = ?");
        $stmt->bind_param("ss", $user, $pwd);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {

            $row = $result->fetch_assoc();

            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_user'] = $row['user_name'];

            header("Location: ../admin_panel/admin_index.php");
            exit();

        } else {
            $error = "Invalid username or password";
        }

    } else {
        $error = "Please fill all fields";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ERP Admin Login</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    height: 100vh;
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Glass Card */
.login-container {
    width: 380px;
    padding: 35px;
    border-radius: 15px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(12px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    color: white;
}

/* Title */
.login-container h2 {
    text-align: center;
    margin-bottom: 25px;
    font-weight: 600;
}

/* Input */
.input-box {
    margin-bottom: 18px;
}

.input-box label {
    font-size: 14px;
    opacity: 0.8;
}

.input-box input {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border: none;
    border-radius: 6px;
    outline: none;
}

/* Button */
.login-btn {
    width: 100%;
    padding: 12px;
    background: #00c6ff;
    border: none;
    border-radius: 6px;
    color: black;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

.login-btn:hover {
    background: #00a2d3;
}

/* Error */
.error {
    background: rgba(255, 0, 0, 0.2);
    padding: 8px;
    margin-bottom: 15px;
    border-radius: 5px;
    text-align: center;
    font-size: 14px;
}

/* Footer */
.footer {
    text-align: center;
    margin-top: 15px;
    font-size: 12px;
    opacity: 0.7;
}
</style>

</head>

<body>

<div class="login-container">

    <h2>ERP Admin</h2>

    <form method="POST" autocomplete="off">

        <?php if (!empty($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <div class="input-box">
            <label>Username</label>
            <input type="text" name="userName" required>
        </div>

        <div class="input-box">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <input type="submit" name="login" value="Login" class="login-btn">

    </form>

    <div class="footer">
        ERP System © <?php echo date("Y"); ?>
    </div>

</div>

</body>
</html>