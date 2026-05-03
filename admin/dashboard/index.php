<?php
include("../../config/auth.php");
include("../../server/connection.php");

// Prevent browser cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>

<h1>Admin Dashboard</h1>

<ul>
    <li><a href="../department/list.php">Departments</a></li>
    <li><a href="../courses/list.php">Courses</a></li>
    <li><a href="../semesters/list.php">Semesters</a></li>
    <li><a href="../bank/list.php">Banks</a></li>
</ul>

<br><br>

<a href="../auth/logout.php"
    style="padding:10px 15px; background:red; color:white; text-decoration:none; border-radius:5px;">
    Logout
</a>