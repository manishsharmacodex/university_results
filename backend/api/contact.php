<?php
include(__DIR__ . "../server/connection.php");

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$response = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $data = json_decode(file_get_contents("php://input"), true);

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $subject = trim($data['subject'] ?? '');
    $message = trim($data['message'] ?? '');

    if ($subject === "Other") {
        $custom = trim($data['custom_subject'] ?? '');
        $subject = $custom !== '' ? $custom : "Other";
    }

    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        echo json_encode([
            "success" => false,
            "message" => "All fields are required"
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email format"
        ]);
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO university_results.contact_us 
        (name, email, subject, message) 
        VALUES (?, ?, ?, ?)
    ");

    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => $conn->error
        ]);
        exit;
    }

    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Message sent successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Database error"
        ]);
    }

    $stmt->close();
}