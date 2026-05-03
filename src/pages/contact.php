<?php
include("../../server/connection.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);

    $subject = ($_POST['subject'] == "Other")
        ? htmlspecialchars($_POST['custom_subject'])
        : htmlspecialchars($_POST['subject']);

    $msg = htmlspecialchars($_POST['message']);

    $stmt = $conn->prepare("INSERT INTO university_results.contact_us (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $msg);

    if ($stmt->execute()) {
        $message = "✅ Thank you, $name! Your message has been sent successfully.";
    } else {
        $message = "❌ Something went wrong. Please try again.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact Us - Sushant University</title>

<style>
:root {
    --bg: #0b1220;
    --primary: #00d9ff;
    --muted: #b8c1d1;
    --radius: 14px;
}

body {
    margin: 0;
    font-family: sans-serif;
    background: radial-gradient(circle at top, #14213d, var(--bg));
    color: #fff;
}

a {
    text-decoration: none;
    color: inherit;
}

/* NAVBAR */
.navbar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 60px;
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(14px);
}

.logo { color: var(--primary); font-weight: bold; }

.navbar ul {
    display:flex;
    gap:20px;
    list-style:none;
}

.navbar li { color:var(--muted); cursor:pointer; }

.nav-buttons button {
    padding:8px 14px;
    border-radius:20px;
    cursor:pointer;
}

.student-btn { background:var(--primary); border:none; }
.admin-btn {
    border:1px solid var(--primary);
    background:transparent;
    color:var(--primary);
}

/* HERO */
.hero {
    text-align:center;
    padding:50px 20px;
}

.hero h1 span { color:var(--primary); }

/* MAIN */
.container {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:30px;
    padding:50px;
}

/* LEFT INFO */
.info-box {
    background:rgba(255,255,255,0.05);
    padding:30px;
    border-radius:var(--radius);
}

.info-item {
    margin:15px 0;
    color:var(--muted);
}

/* FORM */
.form-box {
    background:#fff;
    color:#000;
    padding:30px;
    border-radius:var(--radius);
    box-shadow:0 15px 40px rgba(0,0,0,0.3);
}

/* FIELD SYSTEM (IMPORTANT FIX) */
.field {
    margin-bottom:18px;
}

/* ALL INPUTS SAME HEIGHT */
.field input,
.field select,
.field textarea {
    width:100%;
    height:50px;
    padding:0 14px;
    border:1px solid #ccc;
    border-radius:10px;
    font-size:14px;
    transition:0.2s;
}

/* TEXTAREA CUSTOM HEIGHT */
.field textarea {
    height:120px;
    padding:12px;
    resize: none; /* ❌ REMOVE DRAG */
}

/* FOCUS EFFECT */
.field input:focus,
.field select:focus,
.field textarea:focus {
    border-color:var(--primary);
    outline:none;
    box-shadow:0 0 0 2px rgba(0,217,255,0.2);
}

/* BUTTON */
.btn {
    width:100%;
    height:50px;
    background:var(--primary);
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
    transition:0.3s;
}

.btn:hover {
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(0,217,255,0.3);
}

/* MESSAGE */
.success {
    background:#16a34a;
    color:#fff;
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
}

.error {
    background:#dc2626;
    color:#fff;
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
}

/* FOOTER */
.footer {
    padding:50px;
    background:#050a14;
    margin-top:40px;
}

.footer-grid {
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
}

.footer h3 { color:var(--primary); }

.footer p, .footer a {
    color:var(--muted);
    font-size:13px;
}

/* RESPONSIVE */
@media(max-width:900px){
    .container { grid-template-columns:1fr; }
}
</style>

<script>
function toggleCustomSubject(val){
    document.getElementById("customSubject").style.display =
        val === "Other" ? "block" : "none";
}
</script>

</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">🎓 Sushant University</div>

    <ul>
        <a href="../../index.php"><li>Home</li></a>
        <li>Programs</li>
        <li>Admissions</li>
        <a href="../../results_check/results.php"><li>Exam & Results</li></a>
        <a href="./contact.php"><li>Contact</li></a>
    </ul>

    <div class="nav-buttons">
        <button class="student-btn">Student Login</button>
        <a href="../../admin/auth/login.php"><button class="admin-btn">Admin Login</button></a>
    </div>
</div>

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
        <p class="info-item">📍 Gurugram, Haryana, India</p>
        <p class="info-item">📞 +91 9876543210</p>
        <p class="info-item">✉️ support@sushantuniversity.com</p>
        <p class="info-item">🕒 Mon - Sat (9AM - 5PM)</p>
    </div>

    <!-- FORM -->
    <div class="form-box">

        <h2>Send Message</h2>

        <?php if ($message) { ?>
            <div class="<?= strpos($message,'✅')!==false ? 'success':'error' ?>">
                <?= $message ?>
            </div>
        <?php } ?>

        <form method="POST">

            <div class="field">
                <input type="text" name="name" placeholder="Full Name" required>
            </div>

            <div class="field">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="field">
                <select name="subject" onchange="toggleCustomSubject(this.value)" required>
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

            <div class="field" id="customSubject" style="display:none;">
                <input type="text" name="custom_subject" placeholder="Custom Subject">
            </div>

            <div class="field">
                <textarea name="message" placeholder="Your Message" required></textarea>
            </div>

            <button class="btn">Send Message</button>

        </form>
    </div>

</div>

<!-- FOOTER -->
<div class="footer">
    <div class="footer-grid">

        <div>
            <h3>About</h3>
            <p>Top private university focused on innovation.</p>
        </div>

        <div>
            <h3>Links</h3>
            <a href="#">Admissions</a>
            <a href="#">Programs</a>
        </div>

        <div>
            <h3>Support</h3>
            <a href="#">Help Center</a>
        </div>

        <div>
            <h3>Contact</h3>
            <p>Email: info@university.com</p>
            <p>Phone: +91 99999 99999</p>
        </div>

    </div>
</div>

</body>
</html>