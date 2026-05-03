<?php
$password = "admin"; // <-- set your admin password here

$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Plain Password: " . $password . "<br>";
echo "Hashed Password: " . $hash;
?>