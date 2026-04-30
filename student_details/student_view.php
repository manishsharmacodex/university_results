<?php
include("../server/connection.php");

if (!isset($_GET['id'])) {
    die("Student ID missing");
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM student_details WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Student not found");
}

/* SAFE OUTPUT FUNCTION */
function e($str) {
    return htmlspecialchars($str ?? '');
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Student Profile</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: Poppins;
            background: #f5f7fb;
            color: #111827;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .card {
            width: 900px;
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .header {
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            padding: 20px;
            text-align: center;
            color: #fff;
        }

        .header h2 {
            margin: 0;
        }

        .content {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 20px;
            padding: 20px;
        }

        .photo {
            text-align: center;
        }

        .photo img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            border: 3px solid #2563eb;
        }

        .info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .box {
            background: #f1f5f9;
            padding: 10px;
            border-radius: 8px;
        }

        .label {
            font-size: 12px;
            color: #2563eb;
        }

        .value {
            font-size: 14px;
            margin-top: 4px;
            font-weight: 500;
        }

        .full {
            grid-column: span 2;
        }

        .btns {
            text-align: center;
            padding: 15px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin: 5px;
            text-decoration: none;
            color: #fff;
            display: inline-block;
        }

        .back {
            background: #6b7280;
        }

        .print {
            background: #16a34a;
        }

        /* PRINT STYLE */
        @media print {
            body {
                background: #fff;
            }
            .btns {
                display: none;
            }
            .card {
                box-shadow: none;
            }
        }

        /* MOBILE */
        @media (max-width: 768px) {
            .content {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="card">

    <div class="header">
        <h2>🎓 Student Profile</h2>
    </div>

    <div class="content">

        <!-- PHOTO -->
        <div class="photo">

            <?php
            $img = (!empty($data['photo'])) ? "uploads/" . $data['photo'] : "https://via.placeholder.com/200";
            ?>

            <img src="<?= $img ?>" alt="Student Photo">

            <h3><?= e($data['full_name']) ?></h3>
            <p><?= e($data['student_id']) ?></p>
        </div>

        <!-- INFO -->
        <div class="info">

            <div class="box">
                <div class="label">Father Name</div>
                <div class="value"><?= e($data['father_name']) ?></div>
            </div>

            <div class="box">
                <div class="label">Gender</div>
                <div class="value"><?= e($data['gender']) ?></div>
            </div>

            <div class="box">
                <div class="label">Email</div>
                <div class="value"><?= e($data['email']) ?></div>
            </div>

            <div class="box">
                <div class="label">Phone</div>
                <div class="value"><?= e($data['phone']) ?></div>
            </div>

            <div class="box">
                <div class="label">Department</div>
                <div class="value"><?= e($data['department']) ?></div>
            </div>

            <div class="box">
                <div class="label">Course</div>
                <div class="value"><?= e($data['course']) ?></div>
            </div>

            <div class="box">
                <div class="label">Semester</div>
                <div class="value"><?= e($data['semester']) ?></div>
            </div>

            <div class="box">
                <div class="label">Admission Date</div>
                <div class="value"><?= e($data['admission_date']) ?></div>
            </div>

            <div class="box full">
                <div class="label">Address</div>
                <div class="value"><?= e($data['address']) ?></div>
            </div>

        </div>
    </div>

    <div class="btns">
        <a class="btn back" href="student_list.php">⬅ Back</a>
        <button class="btn print" onclick="window.print()">🖨 Print</button>
    </div>

</div>

</body>
</html>