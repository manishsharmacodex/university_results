<?php
// DB Connection
include(__DIR__ . "/server/connection.php");

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
    <link rel="stylesheet" type="text/css" href="./css/font.css">

    <style>
        :root {
            --bg: #0b1220;
            --primary: #00d9ff;
            --muted: #b8c1d1;
            --radius: 14px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            width: 100%;
            /* height: 100vh; */
            background: radial-gradient(circle at top, #14213d, var(--bg));
            color: #fff;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* =========================================
           NAVBAR
        ========================================= */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 60px;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(14px);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
        }

        .navbar ul {
            display: flex;
            gap: 20px;
            list-style: none;
        }

        .navbar ul li {
            color: var(--muted);
            transition: 0.3s;
            cursor: pointer;
            font-size: 16px;
        }

        .navbar ul li:hover {
            color: var(--primary);
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
        }

        .nav-btn {
            padding: 10px 16px;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
        }

        .student-btn {
            background: var(--primary);
            color: #000;
        }

        .admin-btn {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .nav-btn:hover {
            transform: translateY(-2px);
        }

        /* =========================================
           SLIDER
        ========================================= */
        .slider {
            width: 100%;
            height: 450px;
            position: relative;
            overflow: hidden;
        }

        .slides {
            width: 100%;
            height: 100%;
        }

        .slide {
            width: 100%;
            height: 100%;
            display: none;
            position: relative;
        }

        .slide.active {
            display: block;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .caption {
            position: absolute;
            left: 60px;
            bottom: 60px;
        }

        .caption h2 {
            font-size: 50px;
            margin-bottom: 10px;
            background-color: #fff;
            color: #14213d;
            padding: 5px 20px;
        }

        .caption p {
            color: #ddd;
            font-size: 32px;
            background-color: #14213d;
            padding: 5px 20px;
        }

        .prev,
        .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 22px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
        }

        .prev:hover,
        .next:hover {
            background: var(--primary);
            color: #000;
        }

        .prev {
            left: 20px;
        }

        .next {
            right: 20px;
        }

        /* =========================================
           HERO SECTION
        ========================================= */
        .hero {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            padding: 80px 60px;
            align-items: flex-start;
        }

        .hero-text {
            max-width: 550px;
        }

        .hero-text h1 {
            font-size: 55px;
            line-height: 1.2;
        }

        .hero-text span {
            color: var(--primary);
        }

        .hero-text p {
            margin-top: 15px;
            color: var(--muted);
            line-height: 1.7;
        }

        .hero-buttons {
            margin-top: 20px;
        }

        .hero-buttons button {
            padding: 12px 18px;
            border-radius: 10px;
            border: none;
            margin-right: 10px;
            cursor: pointer;
        }

        .primary {
            background: var(--primary);
            color: #000;
        }

        .secondary {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        /* =========================================
           FORM
        ========================================= */
        .form-box {
            width: 400px;
            padding: 30px;
            border-radius: var(--radius);
            color: #000;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .form-box h3 {
            margin-bottom: 10px;
            font-size: 24px;
        }

        .form-box p {
            margin-bottom: 20px;
            color: #555;
        }

        .form-box input,
        .form-box select {
            width: 100%;
            padding: 13px;
            margin-bottom: 14px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
            outline: none;
        }

        .form-box input:focus,
        .form-box select:focus {
            border-color: var(--primary);
        }

        .button {
            width: 100%;
            padding: 13px;
            border: none;
            background: var(--primary);
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
        }

        .closed-message {
            background: red;
            color: white;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 600;
        }

        /* =========================================
           STATS
        ========================================= */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 40px 60px;
        }

        .stat {
            background: rgba(255, 255, 255, 0.06);
            padding: 25px;
            border-radius: var(--radius);
            text-align: center;
        }

        .stat h2 {
            color: var(--primary);
            margin-bottom: 8px;
        }

        .stat p {
            color: var(--muted);
        }

        /* =========================================
           PROGRAMS
        ========================================= */
        .section {
            padding: 60px;
            text-align: center;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background: rgba(255, 255, 255, 0.06);
            padding: 25px;
            border-radius: var(--radius);
        }

        .card h3 {
            color: var(--primary);
            margin-bottom: 10px;
        }

        .card p {
            color: var(--muted);
        }

        /* =========================================
           FOOTER
        ========================================= */
        .footer {
            background: #050a14;
            padding: 50px 60px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .footer h3 {
            color: var(--primary);
            margin-bottom: 10px;
        }

        .footer p,
        .footer a {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 6px;
            display: block;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            color: #777;
            font-size: 13px;
        }

        .no-banners {
            width: 100%;
            height: 450px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-size: 20px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.05);
        }

        /* ===============================
   MODAL BACKDROP (GLASS EFFECT)
================================= */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 15, 25, 0.75);
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeIn 0.25s ease-in-out;
            /* backdrop-filter: blur(8px); */
            /* -webkit-backdrop-filter: blur(8px); */
        }

        /* ===============================
   MODAL BOX
================================= */
        .modal-content {
            width: 100%;
            max-width: 500px;
            background: linear-gradient(145deg, #ffffff, #f3f6ff);
            border-radius: 18px;
            padding: 28px 24px;
            text-align: center;
            color: #111;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            transform: translateY(20px);
            animation: slideUp 0.3s ease forwards;
            position: relative;
        }

        .modal-content h2 {
            font-size: 20px;
            font-weight: 700;
            color: #0b1220;
            margin-bottom: 10px;
        }

        /* ===============================
   ADMISSION NUMBER
================================= */
        .modal-content h3 {
            color: #00bcd4;
            font-size: 22px;
            font-weight: 800;
            margin: 12px 0;
            letter-spacing: 1px;
        }

        .modal-content p {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }

        .modal-content button {
            margin-top: 15px;
            padding: 10px 18px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            background: #00d9ff;
            color: #000;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .modal-content button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 217, 255, 0.3);
        }

        .success_icon {
            width: 100px;
            height: 60px;
        }

        .disabled-form {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>

</head>

<body>

    <!-- =========================================
         NAVBAR
    ========================================= -->
    <div class="navbar">

        <a href="./index.php">
            <div class="logo">Alpha University</div>
        </a>

        <ul>
            <a href="./index.php">
                <li>Home</li>
            </a>

            <li>Programs</li>

            <li>Admissions</li>

            <a href="./src/pages/results/results.php" target="_blank">
                <li>Exam & Results</li>
            </a>

            <a href="./src/pages/school/school_information.php">
                <li>School Informations</li>
            </a>

            <a href="./src/pages/contact_us/contact.php">
                <li>Contact</li>
            </a>
        </ul>

        <div class="nav-buttons">

            <button class="nav-btn student-btn">
                Student Login
            </button>

            <a href="./admin/auth/login.php" target="_blank">
                <button class="nav-btn admin-btn">
                    Admin Login
                </button>
            </a>

        </div>

    </div>

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
    <div class="footer">

        <div class="footer-grid">

            <div>
                <h3>About</h3>
                <p>
                    Top private university in India focused on innovation.
                </p>
            </div>

            <div>
                <h3>Links</h3>
                <a href="#">Admissions</a>
                <a href="#">Programs</a>
                <a href="#">Results</a>
            </div>

            <div>
                <h3>Support</h3>
                <a href="#">Help Center</a>
                <a href="#">Privacy Policy</a>
            </div>

            <div>
                <h3>Contact</h3>
                <p>Email: contact@alphauniversity.com</p>
                <p>Phone: +91 99999 99999</p>
            </div>

        </div>

        <div class="footer-bottom">
            © 2026 Alpha University | All Rights Reserved
        </div>

    </div>

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


</body>

</html>

<script>
    class Slider {
        constructor({
            containerSelector = ".slides",
            slideSelector = ".slide",
            nextBtnSelector = ".next",
            prevBtnSelector = ".prev",
            intervalTime = 10000
        } = {}) {

            // Elements
            this.container = document.querySelector(containerSelector);
            this.slides = document.querySelectorAll(slideSelector);
            this.nextBtn = document.querySelector(nextBtnSelector);
            this.prevBtn = document.querySelector(prevBtnSelector);

            // State
            this.index = 0;
            this.intervalTime = intervalTime;
            this.interval = null;

            // Swipe
            this.startX = 0;
            this.endX = 0;

            if (!this.slides.length || !this.container) return;

            this.init();
        }

        init() {
            this.showSlide(this.index);
            this.startAuto();

            this.bindEvents();
        }

        /* -------------------------
           Core Functions
        --------------------------*/

        showSlide(i) {
            const total = this.slides.length;

            this.slides.forEach(slide => slide.classList.remove("active"));

            this.index = (i + total) % total;
            this.slides[this.index].classList.add("active");
        }

        nextSlide = () => {
            this.showSlide(this.index + 1);
        };

        prevSlide = () => {
            this.showSlide(this.index - 1);
        };

        /* -------------------------
           Auto Slide (safe)
        --------------------------*/

        startAuto() {
            this.stopAuto(); // prevent multiple intervals
            this.interval = setInterval(this.nextSlide, this.intervalTime);
        }

        stopAuto() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
        }

        /* -------------------------
           Events
        --------------------------*/

        bindEvents() {

            // Buttons
            this.nextBtn?.addEventListener("click", () => {
                this.nextSlide();
                this.restartAuto();
            });

            this.prevBtn?.addEventListener("click", () => {
                this.prevSlide();
                this.restartAuto();
            });

            // Pause on hover
            this.container.addEventListener("mouseenter", () => this.stopAuto());
            this.container.addEventListener("mouseleave", () => this.startAuto());

            // Keyboard support
            document.addEventListener("keydown", (e) => {
                if (e.key === "ArrowRight") {
                    this.nextSlide();
                    this.restartAuto();
                }
                if (e.key === "ArrowLeft") {
                    this.prevSlide();
                    this.restartAuto();
                }
            });

            // Touch support (mobile swipe)
            this.container.addEventListener("touchstart", (e) => {
                this.startX = e.touches[0].clientX;
            });

            this.container.addEventListener("touchend", (e) => {
                this.endX = e.changedTouches[0].clientX;
                this.handleSwipe();
            });
        }

        handleSwipe() {
            const diff = this.startX - this.endX;

            if (Math.abs(diff) > 50) {
                if (diff > 0) this.nextSlide();
                else this.prevSlide();

                this.restartAuto();
            }
        }

        restartAuto() {
            this.stopAuto();
            this.startAuto();
        }
    }

    /* -------------------------
       INIT SLIDER
    --------------------------*/

    document.addEventListener("DOMContentLoaded", () => {
        new Slider({
            intervalTime: 10000
        });
    });



    /* =========================================
FETCH COURSE ACCORDING DEPARTMENT
========================================= */

    const department = document.getElementById("department");
    const course = document.getElementById("course");

    let controller = null;

    if (department && course) {

        department.addEventListener("change", async function () {

            const departmentId = this.value;

            // Reset
            if (!departmentId) {
                course.innerHTML = "<option value=''>Select Course</option>";
                return;
            }

            // Cancel previous request
            if (controller) {
                controller.abort();
            }

            controller = new AbortController();

            try {

                // Loading state
                course.innerHTML = "<option disabled>Loading...</option>";

                const response = await fetch("./ajax/get_courses.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                        department_id: departmentId
                    }),
                    signal: controller.signal
                });

                if (!response.ok) {
                    throw new Error("Server error");
                }

                const data = await response.text();

                if (!data) {
                    throw new Error("Empty response");
                }

                course.innerHTML = data;

            } catch (error) {

                if (error.name === "AbortError") return;

                console.error(error);
                course.innerHTML = "<option>Error loading courses</option>";

            }

        });

    }


    /* =========================================
    AJAX FORM SUBMIT + POPUP
    ========================================= */

    const form = document.getElementById("admissionForm");

    if (form) {

        form.addEventListener("submit", async function (e) {
            e.preventDefault();

            let formData = new FormData(this);
            formData.append("admission_button", true);

            try {

                const response = await fetch(window.location.href, {
                    method: "POST",
                    body: formData
                });

                const text = await response.text();
                const data = JSON.parse(text);

                if (data.status === "success") {

                    document.getElementById("admissionNo").innerText = data.admission_no;
                    document.getElementById("popupModal").style.display = "flex";

                    form.reset();

                } else {
                    alert(data.message || "Error occurred");
                }

            } catch (error) {
                console.error("Submit Error:", error);
                alert("Something went wrong. Please try again.");
            }

        });

    }


    /* =========================================
    MODAL CLOSE
    ========================================= */

    function closeModal() {
        document.getElementById("popupModal").style.display = "none";
    }
</script>