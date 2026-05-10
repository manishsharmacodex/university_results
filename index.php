<?php
include("./server/connection.php");

/* =========================================
   FETCH ADMISSION FORM SETTINGS
========================================= */
$form_settings = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT * FROM admission_form_settings WHERE id='1'"
    )
);

/* =========================================
   INSERT ADMISSION DATA
========================================= */
if (isset($_POST['admission_button'])) {

    $full_name = trim($_POST['full_name']);
    $email_address = trim($_POST['email_address']);
    $phone_number = trim($_POST['phone_number']);
    $department = trim($_POST['department']);
    $course = trim($_POST['course']);

    // Validation
    if (
        empty($full_name) ||
        empty($email_address) ||
        empty($phone_number) ||
        empty($department) ||
        empty($course)
    ) {

        echo "<script>alert('All fields are required');</script>";
        exit;
    }

    // Email validation
    if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {

        echo "<script>alert('Invalid email address');</script>";
        exit;
    }

    // Prepared Statement
    $stmt = $conn->prepare("
        INSERT INTO admission_list
        (
            full_name,
            email_address,
            phone_number,
            department,
            course
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssss",
        $full_name,
        $email_address,
        $phone_number,
        $department,
        $course
    );

    $data = $stmt->execute();

    if ($data) {

        echo "
        <script>
            alert(
                'Your form has been submitted successfully. Our team will contact you within 24 hours.'
            );

            window.location.href='index.php';
        </script>
        ";

    } else {

        echo "
        <script>
            alert('Sorry! Please try again.');
        </script>
        ";
    }
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
            $banners = mysqli_query(
                $conn,
                "SELECT * FROM banners ORDER BY id DESC"
            );

            $first = true;

            if (mysqli_num_rows($banners) > 0) {

                while ($row = mysqli_fetch_assoc($banners)) {

                    $image = "./admin/uploads/banners/" . $row['image'];
                    ?>

                    <div class="slide <?= $first ? 'active' : '' ?>">

                        <?php if (file_exists($image)) { ?>

                            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($row['title']) ?>">

                        <?php } ?>

                        <div class="caption">

                            <h2>
                                <?= htmlspecialchars($row['title']) ?>
                            </h2>

                            <p>
                                <?= htmlspecialchars($row['description']) ?>
                            </p>

                        </div>

                    </div>

                    <?php
                    $first = false;
                }

            } else {

                echo "<p>No banners found.</p>";
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

            <?php if ($form_settings['form_status'] == 'Closed') { ?>

                <div class="closed-message">
                    Admissions Are Currently Closed
                </div>

            <?php } ?>

            <form action="" method="POST" <?= $form_settings['form_status'] == 'Closed'
                ? 'style="pointer-events: none; opacity: 0.6;"'
                : '' ?>>

                <div class="form-box" style="background: <?= htmlspecialchars($form_settings['background_color']) ?>;">

                    <h3><?= htmlspecialchars($form_settings['form_title']) ?></h3>

                    <p><?= htmlspecialchars($form_settings['form_description']) ?></p>

                    <input type="text" name="full_name" placeholder="Full Name" required>

                    <input type="email" name="email_address" placeholder="Email Address" required>

                    <input type="text" name="phone_number" maxlength="10" placeholder="Phone Number" required>

                    <!-- DEPARTMENT -->
                    <select name="department" id="department" required>
                        <option value="" selected disabled>Select Department</option>

                        <?php
                        $department_query = mysqli_query($conn, "SELECT * FROM departments ORDER BY id DESC");

                        while ($department = mysqli_fetch_assoc($department_query)) {
                            ?>
                            <option value="<?= $department['id'] ?>">
                                <?= htmlspecialchars($department['name']) ?>
                            </option>
                        <?php } ?>
                    </select>

                    <!-- COURSE -->
                    <select name="course" id="course" required>
                        <option value="" selected disabled>Select Course</option>
                    </select>

                    <input type="submit" value="<?= htmlspecialchars($form_settings['button_text']) ?>" class="button"
                        name="admission_button">

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


    <script>

        /* =========================================
   SLIDER (FINAL VERSION)
========================================= */

        const slides = document.querySelectorAll(".slide");
        const nextBtn = document.querySelector(".next");
        const prevBtn = document.querySelector(".prev");
        const sliderContainer = document.querySelector(".slides");

        let index = 0;
        let sliderInterval;

        // Exit if no slides found
        if (slides.length > 0) {

            function showSlide(i) {
                slides.forEach(slide => slide.classList.remove("active"));
                slides[i].classList.add("active");
            }

            function nextSlide() {
                index = (index + 1) % slides.length;
                showSlide(index);
            }

            function prevSlide() {
                index = (index - 1 + slides.length) % slides.length;
                showSlide(index);
            }

            // Auto slide
            function startSlider() {
                sliderInterval = setInterval(nextSlide, 10000);
            }

            function stopSlider() {
                clearInterval(sliderInterval);
            }

            startSlider();

            // Buttons (safe check)
            if (nextBtn) {
                nextBtn.addEventListener("click", nextSlide);
            }

            if (prevBtn) {
                prevBtn.addEventListener("click", prevSlide);
            }

            // Pause on hover (optional but useful)
            if (sliderContainer) {
                sliderContainer.addEventListener("mouseenter", stopSlider);
                sliderContainer.addEventListener("mouseleave", startSlider);
            }

            // Initial slide
            showSlide(index);
        }


        /* =========================================
   FETCH COURSE ACCORDING DEPARTMENT
========================================= */

        const department = document.getElementById("department");
        const course = document.getElementById("course");

        if (department && course) {

            department.addEventListener("change", function () {

                const department_id = this.value;

                // Reset if no selection
                if (department_id === "") {
                    course.innerHTML = "<option value=''>Select Course</option>";
                    return;
                }

                const xhr = new XMLHttpRequest();

                xhr.open("POST", "./ajax/get_courses.php", true);

                xhr.setRequestHeader(
                    "Content-Type",
                    "application/x-www-form-urlencoded"
                );

                xhr.onload = function () {

                    if (xhr.status === 200) {
                        course.innerHTML = xhr.responseText;
                    } else {
                        course.innerHTML = "<option>Error loading courses</option>";
                    }

                };

                course.innerHTML = "<option>Loading...</option>";

                xhr.send("department_id=" + encodeURIComponent(department_id));

            });

        }
    </script>

</body>

</html>