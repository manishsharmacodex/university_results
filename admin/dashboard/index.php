<?php
include("../../config/auth.php");
include("../../server/connection.php");

// ✅ Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// ✅ Check login
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>

<html>
<head>
    <title>Admin Panel - Dashboard Page</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>
    <h1>Admin Dashboard</h1>

    <ul>
        <li><a href="../department/list.php">Departments</a></li>
        <li><a href="../courses/list.php">Courses</a></li>
        <li><a href="../semesters/list.php">Semesters</a></li>
        <li><a href="../bank/list.php">Banks</a></li>
        <li><a href="../../student_details/add_students.php" target="_BLANK">Add New Student</a></li>

    </ul>

    <br><br>

    <a href="../auth/logout.php"
        style="padding:10px 15px; background:red; color:white; text-decoration:none; border-radius:5px;">
        Logout
    </a>
</body>
</html>