<?php
include("../../config/auth.php");
include("../../server/connection.php");

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

// DATA
$total_students = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM student_details"))['total'] ?? 0;
$total_admissions = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM admission_list"))['total'] ?? 0;
$total_courses = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM courses"))['total'] ?? 0;
$total_contact_us = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM contact_us"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", sans-serif;
        }

        body {
            background: #f4f6fb;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
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

        .sidebar a:hover {
            background: #2563eb;
            color: white;
        }

        .logout {
            background: #ef4444 !important;
            color: white !important;
            margin-top: 20px;
            justify-content: center;
        }

        /* MAIN */
        .main {
            flex: 1;
            padding: 30px;
        }

        .main h1 {
            margin-bottom: 20px;
        }

        /* CARDS */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            padding: 20px;
            border-radius: 12px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .card i {
            font-size: 35px;
            opacity: 0.3;
            position: absolute;
            right: 15px;
            top: 15px;
        }

        .card h3 {
            font-size: 16px;
        }

        .card p {
            font-size: 30px;
            font-weight: bold;
            margin-top: 10px;
        }

        .blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .green { background: linear-gradient(135deg, #10b981, #059669); }
        .orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }

        /* ================= SLIDER ================= */
        .slider {
            margin-top: 30px;
            width: 100%;
            height: 300px;
            overflow: hidden;
            border-radius: 15px;
            position: relative;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .slides {
            display: flex;
            width: 200%;
            height: 100%;
            transition: 0.8s;
        }

        .slides img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        /* Responsive */
        @media(max-width: 768px) {
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

        <a href="./index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="../department/list.php"><i class="fa-solid fa-building"></i> Departments</a>
        <a href="../courses/list.php"><i class="fa-solid fa-book"></i> Courses</a>
        <a href="../semesters/list.php"><i class="fa-solid fa-calendar"></i> Semesters</a>
        <a href="../bank/list.php"><i class="fa-solid fa-bank"></i> Banks</a>
        <a href="../../student_details/add_students.php"><i class="fa-solid fa-user-plus"></i> Add Student</a>
        <a href="../../student_details/student_list.php"><i class="fa-solid fa-users"></i> Student List</a>

        <a class="logout" href="../auth/logout.php">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>

    <!-- MAIN -->
    <div class="main">
        <h1>Dashboard Overview</h1>

        <!-- CARDS -->
        <div class="cards">

            <div class="card blue">
                <i class="fa-solid fa-user-graduate"></i>
                <h3>Total Students</h3>
                <p><?php echo $total_students; ?></p>
            </div>

            <div class="card green">
                <i class="fa-solid fa-file-signature"></i>
                <h3>Total Admissions</h3>
                <p><?php echo $total_admissions; ?></p>
            </div>

            <div class="card orange">
                <i class="fa-solid fa-book"></i>
                <h3>Total Courses</h3>
                <p><?php echo $total_courses; ?></p>
            </div>

            <div class="card purple">
                <i class="fa-solid fa-envelope"></i>
                <h3>Total Queries</h3>
                <p><?php echo $total_contact_us; ?></p>
            </div>

        </div>

        <!-- ================= SLIDER ================= -->
        <div class="slider">
            <div class="slides" id="slides">
                <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644" alt="slide1">
                <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d" alt="slide2">
            </div>
        </div>
    </div>
</div>

<!-- SLIDER SCRIPT -->
<script>
    let index = 0;
    const slides = document.getElementById("slides");

    setInterval(() => {
        index = (index + 1) % 2;
        slides.style.transform = "translateX(" + (-index * 100) + "%)";
    }, 10000);
</script>

</body>
</html>