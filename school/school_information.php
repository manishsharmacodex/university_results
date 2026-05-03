<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Information - Sushant University</title>
    <link rel="stylesheet" type="text/css" href="../css/font.css">

    <style>
        :root {
            --bg: #0b1220;
            --primary: #00d9ff;
            --muted: #b8c1d1;
            --radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 25px;
            cursor: pointer;
        }

        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .btn-primary {
            padding: 9px 18px;
            border: none;
            background: linear-gradient(135deg, var(--primary), #00ffa3);
            color: #000;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
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
            background: #050a14;
            padding: 30px;
            text-align: center;
            margin-top: 50px;
        }

        .footer-links {
            margin-top: 10px;
        }

        .footer-links a {
            margin: 0 10px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13px;
        }

        .footer-links a:hover {
            color: var(--primary);
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
    <nav class="navbar">
        <div class="logo">🎓 SU</div>

        <ul class="nav-links">
            <a href="../index.php"><li>Home</li></a>
            <li>Programs</li>
            <li>Admissions</li>
            <a href="../results_check/results.php"><li>Exam & Results</li></a>
            <a href="./school_information.php">
                <li>Campus</li>
            </a>
            <a href="../src/pages/contact.php"><li>Contact</li></a>
        </ul>

        <a href="../admin/auth/login.php"><button class="btn-primary">Admin Login</button></a>
    </nav>

    <!-- PAGE -->
    <section class="container">

        <h1 class="title">Schools at Sushant University</h1>
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
    <footer class="footer">
        <p>© 2026 Sushant University</p>
        <div class="footer-links">
            <a href="#">Home</a>
            <a href="#">About</a>
            <a href="#">Contact</a>
        </div>
    </footer>

</body>
</html>