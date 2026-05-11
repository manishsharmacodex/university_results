<?php
// DB Connection
include(__DIR__ . "/../../server/connection.php");
include("../../config/auth.php");


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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../css/sidebar.css">
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

            <a href="../../src/pages/add_student/add_students.php"
                class="<?= $activePage == 'add_students' ? 'active' : '' ?>" target="_BLANK">
                <i class="fa-solid fa-user-plus"></i>Add Student
            </a>

            <a href="../../src/pages/student_list/student_list.php"
                class="<?= $activePage == 'student_list' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i>Student List
            </a>

            <!-- SHOP MENU -->
            <div class="dropdown">

                <a href="javascript:void(0);" class="dropdown-btn">
                    <i class="fa-solid fa-shop"></i>
                    University Manage
                    <i class="fa-solid fa-caret-down dropdown-icon"></i>
                </a>

                <div class="dropdown-container">

                    <a href="../banner/list.php" class="<?= $activePage == 'banner' ? 'active' : '' ?>">
                        <i class="fa-solid fa-image"></i>Banner Update
                    </a>

                    <a href="../admission_form/list.php" class="<?= $activePage == 'admission_form' ? 'active' : '' ?>">
                        <i class="fa-solid fa-file-pen"></i>Admission Form Update
                    </a>

                </div>

            </div>

            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>

        <!-- MAIN -->
        <div class="main">

            <div class="breadcrum-header">
                <h1 class="dashboard-title">Dashboard Overview</h1>
                <p class="dashboard-desc">Welcome back, Admin</p>
            </div>

            <!-- CARDS -->
            <div class="cards">

                <div class="card blue">
                    <i class="fa-solid fa-user-graduate"></i>
                    <h3>Active Students</h3>
                    <p><?= $total_students ?></p>
                </div>

                <div class="card green">
                    <i class="fa-solid fa-file-signature"></i>
                    <h3>Admissions</h3>
                    <p><?= $total_admissions ?></p>
                </div>

                <div class="card orange">
                    <i class="fa-solid fa-book"></i>
                    <h3>Courses</h3>
                    <p><?= $total_courses ?></p>
                </div>

                <div class="card purple">
                    <i class="fa-solid fa-envelope"></i>
                    <h3>Queries</h3>
                    <p><?= $total_contact_us ?></p>
                </div>

                <div class="card pink">
                    <i class="fa-solid fa-building"></i>
                    <h3>Schools</h3>
                    <p><?= $total_departments ?></p>
                </div>

                <div class="card teal">
                    <i class="fa-solid fa-bank"></i>
                    <h3>Banks</h3>
                    <p><?= $total_banks ?></p>
                </div>

            </div>
        </div>
    </div>

    <script type="text/javascript" src="../js/dropdownToggle.js"></script>

</body>

</html>