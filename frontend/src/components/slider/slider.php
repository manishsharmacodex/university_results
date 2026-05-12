<?php
require_once(__DIR__ . "/../../../../backend/config/config.php");
?>

<link rel="stylesheet" href="<?= BASE_URL ?>frontend/src/components/slider/slider.css">
<script src="<?= BASE_URL ?>frontend/src/components/slider/slider.js" defer></script>


<!-- SLIDER SECTION -->
<div class="slider">
    <div class="slides">

        <?php
        $banners = mysqli_query(
            $conn,
            "SELECT title, description, image FROM banners ORDER BY id DESC"
        );

        if ($banners && mysqli_num_rows($banners) > 0) {

            while ($row = mysqli_fetch_assoc($banners)) {

                $image = BASE_URL . "backend/admin/uploads/banners/" . $row['image'];
                ?>

                <div class="slide">

                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($row['title']) ?>">

                    <div class="caption">
                        <h2><?= htmlspecialchars($row['title']) ?></h2>
                        <p><?= htmlspecialchars($row['description']) ?></p>
                    </div>

                </div>

                <?php
            }

        } else {
            echo "<div class='no-banners'>No banners found</div>";
        }
        ?>

    </div>

    <button class="prev">&#10094;</button>
    <button class="next">&#10095;</button>
</div>