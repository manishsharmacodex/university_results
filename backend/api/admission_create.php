<?php
header("Content-Type: application/json");

// DB connection
include(__DIR__ . "/../server/connection.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit;
}

/* =========================
   GET INPUT (JSON OR FORM)
========================= */
$data = json_decode(file_get_contents("php://input"), true);

$full_name = trim($data['full_name'] ?? $_POST['full_name'] ?? '');
$email_address = trim($data['email_address'] ?? $_POST['email_address'] ?? '');
$phone_number = trim($data['phone_number'] ?? $_POST['phone_number'] ?? '');
$department = trim($data['department'] ?? $_POST['department'] ?? '');
$course = trim($data['course'] ?? $_POST['course'] ?? '');

/* =========================
   VALIDATION
========================= */
if (!$full_name || !$email_address || !$phone_number || !$department || !$course) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email"]);
    exit;
}

if (!preg_match('/^[0-9]{10,15}$/', $phone_number)) {
    echo json_encode(["status" => "error", "message" => "Invalid phone number"]);
    exit;
}

/* =========================
   DUPLICATE CHECK
========================= */
$check = $conn->prepare("SELECT id FROM admission_list WHERE email_address = ?");
$check->bind_param("s", $email_address);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already exists"]);
    exit;
}

/* =========================
   INSERT DATA
========================= */
$stmt = $conn->prepare("
    INSERT INTO admission_list 
    (full_name, email_address, phone_number, department, course)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param("sssss", $full_name, $email_address, $phone_number, $department, $course);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Insert failed"]);
    exit;
}

$insert_id = $conn->insert_id;

/* =========================
   GENERATE ADMISSION NO
========================= */
$year = date("Y");

$dept = "DEP" . strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $department), 0, 3));
$crs = "CRS" . strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $course), 0, 3));

$admission_no = "AU-$year-$dept-$crs-" . str_pad($insert_id, 4, "0", STR_PAD_LEFT);

/* =========================
   UPDATE ADMISSION NO
========================= */
$update = $conn->prepare("UPDATE admission_list SET admission_no = ? WHERE id = ?");
$update->bind_param("si", $admission_no, $insert_id);
$update->execute();

/* =========================
   RESPONSE
========================= */
echo json_encode([
    "status" => "success",
    "message" => "Admission submitted successfully",
    "admission_no" => $admission_no
]);