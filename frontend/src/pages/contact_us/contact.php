<?php
require_once(__DIR__ . "/../../../../backend/config/config.php");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - Alpha University</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- <link rel="stylesheet" type="text/css" href="../../css/font.css"> -->
    <link rel="stylesheet" href="<?= BASE_URL?>frontend/src/css/font.css">
    <!-- <link rel="stylesheet" type="text/css" href="../../css/global.css"> -->
    <link rel="stylesheet" href="<?= BASE_URL?>frontend/src/css/global.css">
    <!-- <link rel="stylesheet" type="text/css" href="../../css/contact.css"> -->
    <link rel="stylesheet" href="<?= BASE_URL?>frontend/src/css/contact.css">
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

            <div id="responseMsg"></div>

            <form id="contactForm">

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


<script>
    document.getElementById("contactForm").addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = {
            name: document.querySelector("[name='name']").value,
            email: document.querySelector("[name='email']").value,
            subject: document.querySelector("[name='subject']").value,
            custom_subject: document.querySelector("[name='custom_subject']").value,
            message: document.querySelector("[name='message']").value
        };

        try {
            const res = await fetch("/backend/api/contact.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(formData)
            });

            const data = await res.json();

            if (data.success) {
                alert("✅ " + data.message);
                document.getElementById("contactForm").reset();
            } else {
                alert("❌ " + data.message);
            }

        } catch (err) {
            alert("❌ Server error");
            console.error(err);
        }
    });
</script>