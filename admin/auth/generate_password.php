<?php
$password = "admin"; // Set Your Passwords

$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Plain Password : " . $password . "\n";
echo "Hashed Password : " . $hash . "\n";
?>