<?php
require_once(__DIR__ . "/../../../../backend/config/config.php");
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/frontend/src/components/navbar/navbar.css">

<div class="navbar">

    <a href="<?= BASE_URL ?>index.php">
        <div class="logo">Alpha University</div>
    </a>

    <ul>

        <a href="<?= BASE_URL ?>index.php">
            <li>Home</li>
        </a>

        <li>Programs</li>

        <li>Admissions</li>

        <a href="<?= BASE_URL ?>/frontend/src/pages/results/results.php" target="_blank">
            <li>Exam & Results</li>
        </a>

        <a href="<?= BASE_URL ?>/frontend/src/pages/school/school_information.php">
            <li>School Informations</li>
        </a>

        <a href="<?= BASE_URL ?>/frontend/src/pages/contact_us/contact.php">
            <li>Contact</li>
        </a>

    </ul>

    <div class="nav-buttons">

        <button class="nav-btn student-btn">
            Student Login
        </button>

        <a href="<?= BASE_URL ?>/backend/admin/auth/login.php" target="_blank">
            <button class="nav-btn admin-btn">
                Admin Login
            </button>
        </a>

    </div>

</div>