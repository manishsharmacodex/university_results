<?php

// DB Connection
include(__DIR__ . "/../../../../backend/server/connection.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Safe input handling
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg = trim($_POST['message'] ?? '');

    $subject = trim($_POST['subject'] ?? '');

    // Handle custom subject safely (FIXED)
    if ($subject === "Other") {
        $custom = trim($_POST['custom_subject'] ?? '');

        if ($custom !== '') {
            $subject = $custom;
        } else {
            $subject = "Other";
        }
    }

    // Basic validation
    if ($name === '' || $email === '' || $subject === '' || $msg === '') {
        $message = "❌ All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email format.";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO university_results.contact_us 
            (name, email, subject, message) 
            VALUES (?, ?, ?, ?)
        ");

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssss", $name, $email, $subject, $msg);

        if ($stmt->execute()) {
            $message = "✅ Thank you, $name! Your message has been sent successfully.";
        } else {
            $message = "❌ Something went wrong. Please try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - Alpha University</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
    <link rel="stylesheet" type="text/css" href="../../css/global.css">
    <link rel="stylesheet" type="text/css" href="../../css/contact.css">
    <style>
        
    </style>
</head>

<body>

    <!-- NAVBAR -->
     <?php
        include("../../components/navbar/navbar.php");
     ?>

    <!-- HERO -->
    <div class="hero">
        <h1>We're Here to <span>Help You</span></h1>
        <p>Contact us for admissions, results, or any university support.</p>
    </div>

    <!-- MAIN -->
    <div class="container">

        <!-- LEFT -->
        <div class="info-box">
            <h2>Contact Information</h2>
            <p class="info-item"><i class="fa-solid fa-location-dot"></i> Gurugram, Haryana, India</p>

            <p class="info-item"><i class="fa-solid fa-phone"></i> +91 9876543210</p>

            <p class="info-item"><i class="fa-solid fa-envelope"></i> support@alphauniversity.com</p>

            <p class="info-item"><i class="fa-solid fa-clock"></i> Mon - Sat (9AM - 5PM)</p>
        </div>

        <!-- FORM -->
        <div class="form-box">

            <h2>Send Message</h2>

            <?php if ($message) { ?>
                <div class="<?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                    <?= $message ?>
                </div>
            <?php } ?>

            <form method="POST">

                <div class="field">
                    <input type="text" name="name" placeholder="Enter Full Name">
                </div>

                <div class="field">
                    <input type="email" name="email" placeholder="Enter Email Address">
                </div>

                <div class="field">
                    <select name="subject" onchange="toggleCustomSubject(this.value)">
                        <option value="">Select Subject</option>
                        <option>Admission Inquiry</option>
                        <option>Course Details</option>
                        <option>Fee Structure</option>
                        <option>Scholarship Information</option>
                        <option>Exam & Results</option>
                        <option>Login Issue</option>
                        <option>Technical Support</option>
                        <option>Complaint / Feedback</option>
                        <option>Other</option>
                    </select>
                </div>

                <div class="field customSubject" id="customSubject">
                    <input type="text" name="custom_subject" placeholder="Custom Subject">
                </div>

                <div class="field">
                    <textarea name="message" placeholder="Enter Your Message"></textarea>
                </div>

                <button class="btn">Send Message</button>

            </form>
        </div>

    </div>

    <!-- FOOTER -->
     <?php
        include("../../components/footer/footer.php");
     ?>

    <script type="text/javascript" src="../../js/contactSubjectToggle.js"></script>

</body>

</html>