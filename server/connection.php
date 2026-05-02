<?php

error_reporting(0);

$server_name = "localhost";
$user_name = "root";
$password = "admin@23bca";
$db_name = "university_results";

$conn = mysqli_connect($server_name, $user_name, $password, $db_name);

if ($conn) {
    // echo "<script>
    //     alert('Connection Successully');
    // </script>";
} else {
    echo "Failed Connection" . mysqli_connect_error();
}

?>