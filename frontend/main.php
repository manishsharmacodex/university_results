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
    <link rel="stylesheet" type="text/css" href="./css/index.css">
    <link rel="stylesheet" type="text/css" href="./src/css/global.css">

</head>

<body>

    <!-- =========================================
         NAVBAR
    ========================================= -->
    <?php
    require_once(__DIR__ . "/./src/components/navbar/navbar.php");
    ?>

    <!-- =========================================
         SLIDER
    ========================================= -->
    <div class="slider">

        <div class="slides">
            <?php
            $banners = mysqli_query($conn, "SELECT title, description, image FROM banners ORDER BY id DESC");

            $first = true;

            if ($banners && mysqli_num_rows($banners) > 0) {

                while ($row = mysqli_fetch_assoc($banners)) {

                    $image = "./admin/uploads/banners/" . $row['image'];
                    $serverImage = __DIR__ . "/admin/uploads/banners/" . $row['image'];
                    ?>

                    <div class="slide <?= $first ? 'active' : '' ?>">

                        <?php if (file_exists($serverImage)) { ?>
                            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                        <?php } ?>

                        <div class="caption">
                            <h2><?= htmlspecialchars($row['title']) ?></h2>
                            <p><?= htmlspecialchars($row['description']) ?></p>
                        </div>

                    </div>

                    <?php
                    $first = false;
                }

            } else {
                echo "<div class='no-banners'>No banners found</div>";
            }
            ?>
        </div>

        <button class="prev">&#10094;</button>
        <button class="next">&#10095;</button>

    </div>

    <!-- =========================================
         HERO SECTION
    ========================================= -->
    <div class="hero">

        <!-- LEFT CONTENT -->
        <div class="hero-text">

            <h1>
                Shape Your Future at
                <span>Alpha University</span>
            </h1>

            <p>
                Industry-focused education, expert faculty,
                modern campus, and 95% placement record.
            </p>

            <div class="hero-buttons">

                <button class="primary">
                    Apply Now
                </button>

                <a href="./src/pages/school/school_information.php">
                    <button class="secondary">
                        Explore Programs
                    </button>
                </a>

            </div>

        </div>

        <!-- RIGHT FORM -->
        <div>

            <?php
            $value = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Labore, odio architecto itaque quibusdam atque eos!";

            // Safe check for form settings
            $form_status = $form_settings['form_status'] ?? 'Open';
            $bg_color = $form_settings['background_color'] ?? '#ffffff';
            $title = $form_settings['form_title'] ?? 'Admission Form';
            $description = $form_settings['form_description'] ?? $value;
            $button_text = $form_settings['button_text'] ?? 'Submit';
            ?>

            <!-- CLOSED MESSAGE -->
            <?php if ($form_status === 'Closed') { ?>
                <div class="closed-message">
                    Admissions Are Currently Closed
                </div>
            <?php } ?>

            <!-- FORM -->
            <form id="admissionForm" method="POST" class="<?= $form_status === 'Closed' ? 'disabled-form' : '' ?>"
                <?= $form_status === 'Closed' ? 'onsubmit="return false;"' : '' ?>>

                <div class="form-box" style="background: <?= htmlspecialchars($bg_color, ENT_QUOTES, 'UTF-8') ?>;">

                    <h3><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h3>

                    <p><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>

                    <!-- FULL NAME -->
                    <input type="text" name="full_name" placeholder="Enter Full Name">

                    <!-- EMAIL -->
                    <input type="email" name="email_address" placeholder="Email Address">

                    <!-- PHONE -->
                    <input type="tel" name="phone_number" placeholder="Phone Number" maxlength="10" pattern="[0-9]{10}">

                    <!-- DEPARTMENT -->
                    <select name="department" id="department">
                        <option value="" selected disabled>Select Department</option>

                        <?php
                        $department_query = mysqli_query($conn, "SELECT id, name FROM departments ORDER BY id DESC");

                        if ($department_query && mysqli_num_rows($department_query) > 0) {
                            while ($department = mysqli_fetch_assoc($department_query)) {
                                ?>
                                <option value="<?= $department['id'] ?>">
                                    <?= htmlspecialchars($department['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                                <?php
                            }
                        } else {
                            ?>
                            <option value="" disabled>No departments available</option>
                            <?php
                        }
                        ?>
                    </select>

                    <!-- COURSE -->
                    <select name="course" id="course">
                        <option value="" selected disabled>Select Course</option>
                    </select>

                    <!-- SUBMIT -->
                    <input type="submit" value="<?= htmlspecialchars($button_text, ENT_QUOTES, 'UTF-8') ?>"
                        class="button" name="admission_button" <?= $form_status === 'Closed' ? 'disabled' : '' ?>>

                </div>

            </form>

        </div>
    </div>

    <!-- =========================================
         STATS
    ========================================= -->
    <div class="stats">

        <div class="stat">
            <h2>10K+</h2>
            <p>Students</p>
        </div>

        <div class="stat">
            <h2>200+</h2>
            <p>Faculty</p>
        </div>

        <div class="stat">
            <h2>95%</h2>
            <p>Placements</p>
        </div>

        <div class="stat">
            <h2>50+</h2>
            <p>Courses</p>
        </div>

    </div>

    <!-- =========================================
         PROGRAMS
    ========================================= -->
    <div class="section">

        <h2>Our Popular Programs</h2>

        <div class="cards">

            <div class="card">
                <h3>B.Tech</h3>
                <p>Engineering & AI/ML programs</p>
            </div>

            <div class="card">
                <h3>MBA</h3>
                <p>Leadership & business skills</p>
            </div>

            <div class="card">
                <h3>Law</h3>
                <p>Modern legal education</p>
            </div>

        </div>

    </div>

    <!-- =========================================
         FOOTER
    ========================================= -->
    <?php
    // include($_SERVER['DOCUMENT_ROOT'] . "/university_results/root/footer/footer.php");
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
    <script type="text/javascript" src="./js/slider.js"></script>
    <script type="text/javascript" src="./js/fetchCourse.js"></script>
    <script type="text/javascript" src="./js/ajaxFormSubmission.js"></script>

</body>

</html>