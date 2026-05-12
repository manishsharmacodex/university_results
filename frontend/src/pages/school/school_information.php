<?php
require_once(__DIR__ . "/../../../../backend/config/config.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Information - Alpha University</title>
    <!-- <link rel="stylesheet" type="text/css" href="../../css/font.css"> -->
    <link rel="stylesheet" href="<?= BASE_URL ?>frontend/src/css/font.css">
    <!-- <link rel="stylesheet" type="text/css" href="../../css/global.css"> -->
    <link rel="stylesheet" href="<?= BASE_URL ?>frontend/src/css/global.css">
    <!-- <link rel="stylesheet" type="text/css" href="../../css/school_information.css"> -->
    <link rel="stylesheet" href="<?= BASE_URL ?>frontend/src/css/school_information.css">
</head>

<body>

    <!-- NAVBAR -->
     <?php
        include("../../components/navbar/navbar.php");
     ?>

    <!-- PAGE -->
    <section class="container">

        <h1 class="title">Schools at Alpha University</h1>
        <p class="subtitle">Explore different schools offering world-class education and industry exposure</p>

        <div class="grid">

            <!-- CARD -->
            <div class="card">
                <h2>SET</h2>
                <span>Engineering & Technology</span>
                <p>Modern labs, AI/ML training, and industry-ready programs.</p>

                <div class="overlay">
                    <h2>SET</h2>
                    <p>Build future-ready tech skills with hands-on experience.</p>
                    <button>Apply Now</button>
                </div>
            </div>

            <div class="card">
                <h2>SOB</h2>
                <span>Business School</span>
                <p>Leadership, entrepreneurship, and global business skills.</p>

                <div class="overlay">
                    <h2>SOB</h2>
                    <p>Develop management skills for global careers.</p>
                    <button>Apply Now</button>
                </div>
            </div>

            <div class="card">
                <h2>SOL</h2>
                <span>Law</span>
                <p>Practical legal education with real case studies.</p>

                <div class="overlay">
                    <h2>SOL</h2>
                    <p>Learn law with courtroom exposure and internships.</p>
                    <button>Apply Now</button>
                </div>
            </div>

            <div class="card">
                <h2>SOA</h2>
                <span>Architecture</span>
                <p>Creative design thinking with modern tools.</p>

                <div class="overlay">
                    <h2>SOA</h2>
                    <p>Design sustainable and innovative structures.</p>
                    <button>Apply Now</button>
                </div>
            </div>

            <div class="card">
                <h2>BVHM</h2>
                <span>Hotel Management</span>
                <p>Global hospitality training with internships.</p>

                <div class="overlay">
                    <h2>BVHM</h2>
                    <p>Learn hospitality with real industry exposure.</p>
                    <button>Apply Now</button>
                </div>
            </div>

            <div class="card">
                <h2>SOD</h2>
                <span>Design School</span>
                <p>UI/UX, graphics, and product design training.</p>

                <div class="overlay">
                    <h2>SOD</h2>
                    <p>Create modern digital and creative experiences.</p>
                    <button>Apply Now</button>
                </div>
            </div>

        </div>

    </section>

    <!-- FOOTER -->
    <?php
        include("../../components/footer/footer.php");
     ?>

</body>

</html>