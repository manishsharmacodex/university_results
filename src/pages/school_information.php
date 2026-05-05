<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Information - Alpha University</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">

    <style>
        :root {
            --bg: #0b1220;
            --primary: #00d9ff;
            --muted: #b8c1d1;
            --radius: 14px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        body {
            background: radial-gradient(circle at top, #14213d, var(--bg));
            color: #fff;
            overflow-x: hidden;
        }

        /* ================= NAVBAR ================= */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 60px;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(14px);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-weight: 700;
            font-size: 20px;
            color: var(--primary);
        }

        .navbar ul {
            display: flex;
            gap: 22px;
            list-style: none;
        }

        .navbar ul li {
            color: var(--muted);
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }

        .navbar ul li:hover {
            color: var(--primary);
        }

        /* ================= NAV BUTTONS (NEW) ================= */
        .nav-buttons {
            display: flex;
            gap: 10px;
        }

        .nav-btn {
            padding: 8px 14px;
            border-radius: 25px;
            border: 1px solid transparent;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: 0.3s;
        }

        .student-btn {
            background: var(--primary);
            color: #000;
        }

        .admin-btn {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 217, 255, 0.2);
        }

        /* ================= PAGE HEADER ================= */
        .container {
            padding: 60px;
        }

        .title {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .subtitle {
            color: var(--muted);
            margin-bottom: 40px;
            font-size: 14px;
        }

        /* ================= GRID ================= */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
        }

        /* ================= CARD ================= */
        .card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius);
            padding: 25px;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: 0.4s;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 217, 255, 0.15);
        }

        .card h2 {
            font-size: 38px;
            color: var(--primary);
        }

        .card span {
            display: block;
            margin: 8px 0;
            font-weight: 500;
            color: #fff;
        }

        .card p {
            font-size: 13px;
            color: var(--muted);
        }

        /* ================= OVERLAY ================= */
        .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #1a237e, #00d9ff);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            transform: translateY(100%);
            transition: 0.4s ease;
        }

        .card:hover .overlay {
            transform: translateY(0);
        }

        .overlay h2 {
            font-size: 30px;
        }

        .overlay p {
            font-size: 13px;
            margin: 15px 0;
            opacity: 0.9;
        }

        .overlay button {
            padding: 10px 18px;
            border: none;
            background: #fff;
            color: #000;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
        }

        /* ================= FOOTER ================= */
        .footer {
            padding: 50px 60px;
            background: #050a14;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .footer h3 {
            color: var(--primary);
        }

        .footer p,
        .footer a {
            color: var(--muted);
            font-size: 13px;
            display: block;
            margin-bottom: 5px;
            text-decoration: none;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
            font-size: 12px;
            color: #777;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 900px) {
            .navbar {
                flex-direction: column;
                gap: 10px;
            }

            .container {
                padding: 30px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <div class="navbar">
        <div class="logo">Alpha University</div>
        <ul>
            <a href="../../index.php">
                <li>Home</li>
            </a>
            <li>Programs</li>
            <li>Admissions</li>
            <a href="./results.php" target="_BLANK">
                <li>Exam & Results</li>
            </a>
            <a href="./school_information.php">
                <li>School Informations</li>
            </a>
            <a href="./contact.php">
                <li>Contact</li>
            </a>
        </ul>
        <!-- ✅ NEW BUTTONS -->
        <div class="nav-buttons">
            <button class="nav-btn student-btn">Student Login</button>
            <a href="../../admin/auth/login.php" target="_BLANK"><button class="nav-btn admin-btn">Admin Login</button></a>
        </div>
    </div>

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
    <div class="footer">
        <div class="footer-grid">

            <div>
                <h3>About</h3>
                <p>Top private university in India focused on innovation.</p>
            </div>

            <div>
                <h3>Links</h3>
                <a href="#">Admissions</a>
                <a href="#">Programs</a>
                <a href="#">Results</a>
            </div>

            <div>
                <h3>Support</h3>
                <a href="#">Help Center</a>
                <a href="#">Privacy Policy</a>
            </div>

            <div>
                <h3>Contact</h3>
                <p>Email: contact@alphauniversity.com</p>
                <p>Phone: +91 99999 99999</p>
            </div>

        </div>

        <div class="footer-bottom">
            © 2026 Alpha University | All Rights Reserved
        </div>
</div>

</body>
</html>