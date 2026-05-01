<?php
session_start();
include("../../server/connection.php");

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM university_results.admin_user WHERE user_name='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: ../dashboard/index.php");
    } else {
        echo "Invalid login";
    }
}
?>
<html>

<head>
    <title>Login Page</title>
</head>

<body>
    <form method="POST">
        <input type="text" name="username" placeholder="Username"><br>
        <input type="password" name="password" placeholder="Password"><br>
        <input type="submit" name="login" value="Login">
    </form>
</body>

</html>