<?php
require_once(__DIR__ . "/../../../../backend/config/config.php");
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/frontend/src/components/hero/hero.css">

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<script type="text/javascript" src="<?= BASE_URL ?>/frontend/src/components/hero/fetchCourse.js"></script>

<script type="text/javascript" src="<?= BASE_URL ?>/frontend/src/components/hero/ajaxFormSubmission.js"></script>



<div class="hero">

    <!-- LEFT CONTENT -->
    <div class="hero-text">

        <h1>
            Shape Your Future at
            <span>Alpha University</span>
        </h1>

        <p>
            Industry-focused education, expert faculty,
            modern campus, and 95% placement record.
        </p>

        <div class="hero-buttons">

            <button class="primary">
                Apply Now
            </button>

            <a href="<?= BASE_URL ?>/frontend/src/pages/school/school_information.php">
                <button class="secondary">
                    Explore Programs
                </button>
            </a>

        </div>

    </div>

    <!-- RIGHT FORM -->
    <div>

        <?php
        $value = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Labore, odio architecto itaque quibusdam atque eos!";

        // Safe check for form settings
        $form_status = $form_settings['form_status'] ?? 'Open';
        $bg_color = $form_settings['background_color'] ?? '#ffffff';
        $title = $form_settings['form_title'] ?? 'Admission Form';
        $description = $form_settings['form_description'] ?? $value;
        $button_text = $form_settings['button_text'] ?? 'Submit';
        ?>

        <!-- CLOSED MESSAGE -->
        <?php if ($form_status === 'Closed') { ?>
            <div class="closed-message">
                Admissions Are Currently Closed
            </div>
        <?php } ?>

        <!-- FORM -->
        <form id="admissionForm" method="POST" class="<?= $form_status === 'Closed' ? 'disabled-form' : '' ?>"
            <?= $form_status === 'Closed' ? 'onsubmit="return false;"' : '' ?>>

            <div class="form-box" style="background: <?= htmlspecialchars($bg_color, ENT_QUOTES, 'UTF-8') ?>;">

                <h3><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h3>

                <p><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>

                <!-- FULL NAME -->
                <input type="text" name="full_name" placeholder="Enter Full Name">

                <!-- EMAIL -->
                <input type="email" name="email_address" placeholder="Email Address">

                <!-- PHONE -->
                <input type="tel" name="phone_number" placeholder="Phone Number" maxlength="10" pattern="[0-9]{10}">

                <!-- DEPARTMENT -->
                <select name="department" id="department">
                    <option value="" selected disabled>Select Department</option>

                    <?php
                    $department_query = mysqli_query($conn, "SELECT id, name FROM departments ORDER BY id DESC");

                    if ($department_query && mysqli_num_rows($department_query) > 0) {
                        while ($department = mysqli_fetch_assoc($department_query)) {
                            ?>
                            <option value="<?= $department['id'] ?>">
                                <?= htmlspecialchars($department['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php
                        }
                    } else {
                        ?>
                        <option value="" disabled>No departments available</option>
                        <?php
                    }
                    ?>
                </select>

                <!-- COURSE -->
                <select name="course" id="course">
                    <option value="" selected disabled>Select Course</option>
                </select>

                <!-- SUBMIT -->
                <input type="submit" value="<?= htmlspecialchars($button_text, ENT_QUOTES, 'UTF-8') ?>" class="button"
                    name="admission_button" <?= $form_status === 'Closed' ? 'disabled' : '' ?>>

            </div>

        </form>

    </div>
</div>