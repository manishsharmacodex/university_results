<?php
require_once(__DIR__ . "/../../../../backend/config/config.php");
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/frontend/src/components/navbar/navbar.css">


<!-- =========================================
            Navbar
    ========================================= -->
<div class="navbar">

    <a href="<?= BASE_URL ?>/index.php">
        <div class="logo">Alpha University</div>
    </a>

    <ul>
        <li><a href="<?= BASE_URL ?>/index.php">Home</a></li>

        <li>Programs</li>

        <li>Admissions</li>

        <li>
            <a href="<?= BASE_URL ?>/frontend/src/pages/results/results.php" target="_blank">
                Exam & Results
            </a>
        </li>

        <li>
            <a href="<?= BASE_URL ?>/frontend/src/pages/school/school_information.php">
                School Informations
            </a>
        </li>

        <li>
            <a href="<?= BASE_URL ?>/frontend/src/pages/contact_us/contact.php">
                Contact
            </a>
        </li>
    </ul>

    <div class="nav-buttons">

        <button class="nav-btn student-btn">
            Student Login
        </button>

        <a class="nav-btn admin-btn" href="<?= BASE_URL ?>/backend/admin/auth/login.php" target="_blank">
            Admin Login
        </a>

    </div>

</div>