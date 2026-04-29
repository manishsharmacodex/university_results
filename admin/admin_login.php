<?php
include("../server/connection.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" type="text/css" href="../css/index.css">
    <link rel="stylesheet" type="text/css" href="./admin_login.css">
    <link rel="stylesheet" href="../font.css">
</head>

<body>
    <h1 class="admin_title">Admin Login Page</h1>
    <form action="#" method="POST" class="admin-form" autocomplete="OFF">
        <div class="input-box">
            <label class="form-label-title">User Name</label>
            <input type="text" placeholder="enter the user name" name="userName" class="input-field">
        </div>

        <div class="input-box">
            <label class="form-label-title">Password</label>
            <input type="text" placeholder="enter the password" name="password" class="input-field">
        </div>

        <div class="form-btn">
            <input type="submit" value="LOGIN" name="login" class="login-btn">
        </div>
    </form>


    <script type="text/JavaScript" src=""></script>
</body>

</html>


<!-- code for login crendentails -->
<?php

if (isset($_POST["login"])) {

    $user = $_POST['userName'];
    $pwd = $_POST['password'];

    $query = "SELECT * FROM university_results.admin_user WHERE user_name = '$user' AND password = '$pwd'";

    $data = mysqli_query($conn, $query);
    if ($data) {
        echo "LOGIN SUCCESSFULLY";
        header("location:../admin_panel/admin_index.php");
    } else {
        echo "Failed To Login Please Try Again";
    }

}

?>