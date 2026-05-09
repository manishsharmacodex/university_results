<?php
include("../../../server/connection.php");

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$email = $data['email'];
$phone = $data['phone'];

$stmt = $conn->prepare("UPDATE student_details SET email=?, phone=? WHERE id=?");
$stmt->bind_param("ssi", $email, $phone, $id);

echo $stmt->execute() ? "success" : "error";
?>