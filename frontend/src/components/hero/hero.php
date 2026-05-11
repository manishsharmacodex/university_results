<?php
require_once(__DIR__ . "/../../../../backend/config/config.php");
?>

<link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>/frontend/src/components/hero/hero.css">

<!-- HERO SECTION -->
<div class="hero">

    <!-- LEFT CONTENT -->
    <?php
    require_once(__DIR__ . "/../heroText/heroText.php");
    ?>

    <!-- RIGHT CONTENT -->
    <?php
    require_once(__DIR__ . "/../admissionForm/admissionForm.php");
    ?>


</div>