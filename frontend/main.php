<?php
// DB Connection
include(__DIR__ . "/../backend/server/connection.php");

/* =========================================
   FETCH ADMISSION FORM SETTINGS
========================================= */
$result = mysqli_query($conn, "SELECT * FROM admission_form_settings WHERE id='1'");
$form_settings = $result ? mysqli_fetch_assoc($result) : null;

/* =========================================
   HANDLE ADMISSION SUBMISSION (AJAX)
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['admission_button'])) {

    $full_name = trim($_POST['full_name'] ?? '');
    $email_address = trim($_POST['email_address'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $course = trim($_POST['course'] ?? '');

    /* =========================
       VALIDATION
    ========================= */
    if ($full_name === '' || $email_address === '' || $phone_number === '' || $department === '' || $course === '') {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email address"]);
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
        echo json_encode(["status" => "error", "message" => "Email already registered"]);
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

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database insert failed"]);
        exit;
    }

    $stmt->bind_param("sssss", $full_name, $email_address, $phone_number, $department, $course);

    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Insert failed"]);
        exit;
    }

    $insert_id = $conn->insert_id;

    /* =========================
       ADMISSION NUMBER GENERATE
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
       SUCCESS RESPONSE
    ========================= */
    echo json_encode([
        "status" => "success",
        "message" => "Admission submitted successfully",
        "admission_no" => $admission_no
    ]);

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Alpha University - Top University In India</title>
    <link rel="stylesheet" type="text/css" href="./src/css/font.css">
    <link rel="stylesheet" type="text/css" href="./src/css/global.css">
    <link rel="stylesheet" type="text/css" href="./src/css/index.css">
</head>

<body>

    <!-- Navbar -->
    <?php
    require_once(__DIR__ . "/./src/components/navbar/navbar.php");
    ?>

    <!-- Slider -->
    <?php
    require_once(__DIR__ . "/./src/components/slider/slider.php");
    ?>

    <!-- Hero Section -->
    <?php
    require_once(__DIR__ . "/./src/components/hero/hero.php");
    ?>

    <!-- Stats Section -->
    <?php
    require_once(__DIR__ . "/./src/components/stats/stats.php");
    ?>

    <!-- Program Section -->
    <?php
    require_once(__DIR__ . "/./src/components/program/program.php");
    ?>

    <!-- Footer -->
    <?php
    require_once(__DIR__ . "/./src/components/footer/footer.php");
    ?>

    <!-- MODAL -->
    <div id="popupModal" class="modal">
        <div class="modal-content">
            <img src="./src/assets/success_icon.png" alt="Success Icon" class="success_icon">
            <h2>Application Submitted Successfully</h2>
            <p>Your Admission Number:</p>
            <h3 id="admissionNo"></h3>
            <button onclick="closeModal()">OK</button>
        </div>
    </div>



    <!-- JS File Connected -->
    <script type="text/javascript" src="./src/js/slider.js"></script>
    <!-- <script type="text/javascript" src="./src/js/fetchCourse.js"></script> -->
    <script type="text/javascript" src="./src/js/ajaxFormSubmission.js"></script>

</body>

</html>