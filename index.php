<?php

include("./server/connection.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sushant University - Homepage</title>
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

        a {
            text-decoration: none;
            color: inherit;
        }

        body {
            background: radial-gradient(circle at top, #14213d, var(--bg));
            color: #fff;
            overflow-x: hidden;
        }

        /* ================= NAVBAR ================= */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 60px;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(14px);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-weight: 700;
            font-size: 20px;
            color: var(--primary);
        }

        .navbar ul {
            display: flex;
            gap: 22px;
            list-style: none;
        }

        .navbar ul li {
            color: var(--muted);
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }

        .navbar ul li:hover {
            color: var(--primary);
        }

        /* ================= NAV BUTTONS (NEW) ================= */
        .nav-buttons {
            display: flex;
            gap: 10px;
        }

        .nav-btn {
            padding: 8px 14px;
            border-radius: 25px;
            border: 1px solid transparent;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: 0.3s;
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
            box-shadow: 0 5px 15px rgba(0, 217, 255, 0.2);
        }

        /* ================= HERO ================= */
        .hero {
            display: flex;
            justify-content: space-between;
            padding: 80px 60px;
            gap: 40px;
        }

        .hero-text {
            max-width: 550px;
        }

        .hero-text h1 {
            font-size: 52px;
        }

        .hero-text span {
            color: var(--primary);
        }

        .hero-text p {
            margin-top: 15px;
            color: var(--muted);
        }

        .hero-buttons button {
            margin-top: 20px;
            margin-right: 10px;
            padding: 10px 18px;
            border-radius: 10px;
            border: none;
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

        /* ================= FORM ================= */
        .form-box {
            background: #ffffff;
            color: #000;
            padding: 30px;
            border-radius: var(--radius);
            width: 380px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .form-box h3 {
            margin-bottom: 15px;
        }

        .form-box input,
        .form-box select {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
            background: #fff;
            color: #000;
            font-size: 14px;
        }

        .form-box .button {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }

        /* ================= STATS ================= */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 40px 60px;
        }

        .stat {
            background: rgba(255, 255, 255, 0.06);
            padding: 20px;
            border-radius: var(--radius);
            text-align: center;
        }

        .stat h2 {
            color: var(--primary);
        }

        .stat p {
            color: var(--muted);
        }

        /* ================= SECTIONS ================= */
        .section {
            padding: 60px;
            text-align: center;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.06);
            padding: 20px;
            border-radius: var(--radius);
        }

        .card h3 {
            color: var(--primary);
        }

        .card p {
            color: var(--muted);
        }

        /* ================= FOOTER ================= */
        .footer {
            padding: 50px 60px;
            background: #050a14;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .footer h3 {
            color: var(--primary);
        }

        .footer p,
        .footer a {
            color: var(--muted);
            font-size: 13px;
            display: block;
            margin-bottom: 5px;
            text-decoration: none;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
            font-size: 12px;
            color: #777;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 900px) {

            .hero,
            .stats,
            .cards,
            .footer-grid {
                grid-template-columns: 1fr;
                flex-direction: column;
            }

            .hero {
                flex-direction: column;
            }

            .hero-text h1 {
                font-size: 36px;
            }

            .navbar {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <div class="navbar">
        <div class="logo">🎓 Sushant University</div>

        <ul>
            <a href="./index.php">
                <li>Home</li>
            </a>
            <li>Programs</li>
            <li>Admissions</li>
            <a href="./results_check/results.php">
                <li>Exam & Results</li>
            </a>
            <a href="./school/school_information.php">
                <li>School Informations</li>
            </a>
            <a href="./src/pages/contact.php"><li>Contact</li></a>
        </ul>

        <!-- ✅ NEW BUTTONS -->
        <div class="nav-buttons">
            <button class="nav-btn student-btn">Student Login</button>
            <a href="./admin/admin_login.php" target="_BLANK"><button class="nav-btn admin-btn">Admin Login</button></a>
        </div>
    </div>

    <!-- HERO -->
    <div class="hero">

        <div class="hero-text">
            <h1>Shape Your Future at <span>Sushant University</span></h1>
            <p>Industry-focused education, expert faculty, modern campus, and 95% placement record.</p>

            <div class="hero-buttons">
                <button class="primary">Apply Now</button>
                <button class="secondary">Explore Programs</button>
            </div>
        </div>

        <!-- FORM -->
        <form action="#" method="post">

            <div class="form-box">
                <h3>Admission Form 2026</h3>

                <input type="text" name="full_name" placeholder="Full Name">
                <input type="email" name="email_address" placeholder="Email Address">
                <input type="text" name="phone_number" placeholder="Phone Number">

                <select name="course">
                    <option value="Select Course">Select Course</option>
                    <option value="BCA">BCA</option>
                    <option value="MCA">MCA</option>
                    <option value="B.TECH">B.TECH</option>
                    <option value="M.TECH">M.TECH</option>
                    <option value="LLB">LLB</option>
                    <option value="B.ARCH">B.ARCH</option>
                </select>

                <!-- <button>Submit Application</button> -->
                <input type="submit" value="Submit Application" class="button" name="addmission_button">
            </div>

    </div>

    </form>

    <!-- STATS -->
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

    <!-- PROGRAMS -->
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

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-grid">

            <div>
                <h3>About</h3>
                <p>Top private university in India focused on innovation.</p>
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
                <p>Email: info@university.com</p>
                <p>Phone: +91 99999 99999</p>
            </div>

        </div>

        <div class="footer-bottom">
            © 2026 Sushant University | All Rights Reserved
        </div>
    </div>

</body>

</html>



<!-- insert admission data into database -->
<?php

if (isset($_POST['addmission_button'])) {

    $full_name = $_POST['full_name'];
    $email_address = $_POST['email_address'];
    $phone_number = $_POST['phone_number'];
    $course = $_POST['course'];


    $query = "INSERT INTO university_results.admission_list (full_name,email_address,phone_number,course) VALUES('$full_name','$email_address','$phone_number','$course')";

    $data = mysqli_query($conn, $query);

    if ($data) {
        echo "<script>
                alert('Your Form have been Submited Our Team will call back within 24 hours  - Thank You');
            </script>";
    } else {
        echo "<script>
                alert('sorry please try again');
            </script>";
    }
}
?>