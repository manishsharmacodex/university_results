<?php
include("../../config/auth.php");
include("../../server/connection.php");


// session for login
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

// DATA
$total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM student_details"))['total'] ?? 0;
$total_admissions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM admission_list"))['total'] ?? 0;
$total_courses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses"))['total'] ?? 0;
$total_contact_us = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM contact_us"))['total'] ?? 0;
$total_departments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM departments"))['total'] ?? 0;
$total_banks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM banks"))['total'] ?? 0;

$activePage = "dashboard"; // change per page
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(120deg, #eef2ff, #f8fafc);
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: #111827;
            color: white;
            padding: 20px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .sidebar a {
            display: flex;
            gap: 10px;
            align-items: center;
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px;
            margin: 6px 0;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: white;
            transform: translateX(5px);
        }

        /* MAIN */
        .main {
            flex: 1;
            padding: 30px;
        }

        .header {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        /* CARDS */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            padding: 20px;
            border-radius: 15px;
            color: white;
            position: relative;
            overflow: hidden;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .card i {
            font-size: 40px;
            position: absolute;
            right: 15px;
            top: 15px;
            opacity: 0.2;
        }

        .card h3 {
            font-size: 14px;
            letter-spacing: 1px;
        }

        .card p {
            font-size: 32px;
            font-weight: bold;
            margin-top: 10px;
        }

        /* COLORS */
        .blue {
            background: linear-gradient(135deg, #3b82f6, #1e3a8a);
        }

        .green {
            background: linear-gradient(135deg, #10b981, #065f46);
        }

        .orange {
            background: linear-gradient(135deg, #f59e0b, #7c2d12);
        }

        .purple {
            background: linear-gradient(135deg, #8b5cf6, #4c1d95);
        }

        .pink {
            background: linear-gradient(135deg, #ec4899, #831843);
        }

        .teal {
            background: linear-gradient(135deg, #14b8a6, #134e4a);
        }

        /* SLIDER */
        .slider {
            margin-top: 30px;
            height: 300px;
            overflow: hidden;
            border-radius: 15px;
            position: relative;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .slides {
            display: flex;
            height: 100%;
            transition: 0.8s;
        }

        .slides img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            flex-shrink: 0;
        }

        @media(max-width:768px) {
            .sidebar {
                width: 200px;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2><i class="fa-solid fa-user-shield"></i> Admin</h2>
            <a href="./index.php" class="<?= $activePage == 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i>Dashboard
            </a>

            <a href="../department/list.php" class="<?= $activePage == 'department' ? 'active' : '' ?>">
                <i class="fa-solid fa-building"></i>Department
            </a>

            <a href="../courses/list.php" class="<?= $activePage == 'courses' ? 'active' : '' ?>">
                <i class="fa-solid fa-book"></i>Courses
            </a>

            <a href="../semesters/list.php" class="<?= $activePage == 'semester' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar"></i>Semester
            </a>

            <a href="../bank/list.php" class="<?= $activePage == 'bank' ? 'active' : '' ?>">
                <i class="fa-solid fa-bank"></i>Bank
            </a>

            <a href="../../student_details/add_students.php"
                class="<?= $activePage == 'add_students' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-plus"></i>Add Student
            </a>

            <a href="../../student_details/student_list.php"
                class="<?= $activePage == 'student_list' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i>Student List
            </a>

            <a href="../auth/logout.php" style="background:#ef4444; color:white;">Logout</a>
        </div>

        <!-- MAIN -->
        <div class="main">

            <div class="header">
                <h1>Dashboard Overview</h1>
                <p>Welcome back, Admin</p>
            </div>

            <!-- CARDS -->
            <div class="cards">

                <div class="card blue">
                    <i class="fa-solid fa-user-graduate"></i>
                    <h3>Total Students</h3>
                    <p><?= $total_students ?></p>
                </div>

                <div class="card green">
                    <i class="fa-solid fa-file-signature"></i>
                    <h3>Total Admissions</h3>
                    <p><?= $total_admissions ?></p>
                </div>

                <div class="card orange">
                    <i class="fa-solid fa-book"></i>
                    <h3>Total Courses</h3>
                    <p><?= $total_courses ?></p>
                </div>

                <div class="card purple">
                    <i class="fa-solid fa-envelope"></i>
                    <h3>Total Queries</h3>
                    <p><?= $total_contact_us ?></p>
                </div>

                <div class="card pink">
                    <i class="fa-solid fa-building"></i>
                    <h3>Departments</h3>
                    <p><?= $total_departments ?></p>
                </div>

                <div class="card teal">
                    <i class="fa-solid fa-bank"></i>
                    <h3>Banks</h3>
                    <p><?= $total_banks ?></p>
                </div>

            </div>

            <!-- SLIDER -->
            <div class="slider">
                <div class="slides" id="slides">
                    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644">
                    <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d">
                    <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b">
                </div>
            </div>

        </div>
    </div>

    <script>
        let index = 0;
        const slides = document.getElementById("slides");
        const totalSlides = slides.children.length;

        setInterval(() => {
            index = (index + 1) % totalSlides;
            slides.style.transform = "translateX(" + (-index * 100) + "%)";
        }, 3000);
    </script>

</body>

</html>