<?php
// DB Connection
include(__DIR__ . "/../backend/server/connection.php");

/* =========================================
   FETCH ADMISSION FORM SETTINGS
========================================= */
$result = mysqli_query($conn, "SELECT * FROM admission_form_settings WHERE id='1'");
$form_settings = $result ? mysqli_fetch_assoc($result) : null;

require_once(__DIR__ . "/../backend/config/config.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Alpha University - Top University In India</title>
    <!-- <link rel="stylesheet" type="text/css" href="./src/css/font.css"> -->
    <link rel="stylesheet" href="<?= BASE_URL ?>frontend/src/css/font.css">
    <!-- <link rel="stylesheet" type="text/css" href="./src/css/global.css"> -->
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>frontend/src/css/global.css"">
    <!-- <link rel=" stylesheet" type="text/css" href="./src/css/index.css"> -->
</head>

<body>

    <!-- Navbar -->
    <?php
    require_once(__DIR__ . "/src/components/navbar/navbar.php");
    ?>

    <!-- Slider -->
    <?php
    require_once(__DIR__ . "/src/components/slider/slider.php");
    ?>

    <!-- Hero Section -->
    <?php
    require_once(__DIR__ . "/src/components/hero/hero.php");
    ?>

    <!-- Stats Section -->
    <?php
    require_once(__DIR__ . "/src/components/stats/stats.php");
    ?>

    <!-- Program Section -->
    <?php
    require_once(__DIR__ . "/src/components/program/program.php");
    ?>

    <!-- Footer -->
    <?php
    require_once(__DIR__ . "/src/components/footer/footer.php");
    ?>

</body>

</html>