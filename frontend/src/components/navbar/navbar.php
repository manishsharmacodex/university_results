<?php
require_once __DIR__ . "/../../../../backend/config/config.php";
?>

<link rel="stylesheet" href="<?= BASE_URL ?>frontend/src/components/navbar/navbar.css">


<!-- NAVBAR SECTION -->
<div class="navbar">

    <!-- <a href="<?= BASE_URL ?>index.php">
        <div class="logo">Alpha University</div>
    </a> -->

    <a href="<?= BASE_URL ?>">
        <div class="logo">Alpha University</div>
    </a>

    <ul>
        <!-- <li><a href="<?= BASE_URL ?>index.php">Home</a></li> -->
        <li><a href="<?= BASE_URL ?>">Home</a></li>

        <li>Programs</li>

        <li>Admissions</li>

        <li>
            <!-- <a href="<?= BASE_URL ?>frontend/src/pages/results/results.php" target="_blank" rel="noopener noreferrer">
                Exam & Results
            </a> -->

            <a href="<?= BASE_URL ?>results" target="_blank">Exam & Results</a>
        </li>

        <li>
            <!-- <a href="<?= BASE_URL ?>frontend/src/pages/school/school_information.php">
                School Informations
            </a> -->

            <a href="<?= BASE_URL ?>school-information">School Informations</a>
        </li>

        <li>
            <!-- <a href="<?= BASE_URL ?>frontend/src/pages/contact_us/contact.php">
                Contact
            </a> -->

            <a href="<?= BASE_URL ?>contact">Contact</a>
        </li>
    </ul>

    <div class="nav-buttons">

        <a class="nav-btn student-btn" href="#" rel="noopener noreferrer">
            Student Login
        </a>

        <!-- <a class="nav-btn admin-btn" href="<?= BASE_URL ?>backend/admin/auth/login.php" target="_blank"
            rel="noopener noreferrer">
            Admin Login
        </a> -->

        <a class="nav-btn admin-btn" href="<?= BASE_URL ?>login" target="_blank" rel="noopener noreferrer">
            Admin Login
        </a>

    </div>

</div>