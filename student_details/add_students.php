<?php
include("../server/connection.php");

/* =========================
   AJAX: LIVE ROLL NUMBER
========================= */
if (isset($_GET['get_roll']) && isset($_GET['department'])) {

    $department = $_GET['department'];
    $year = date("Y");

    $query = $conn->prepare("SELECT COUNT(*) as total FROM student_details WHERE department=?");
    $query->bind_param("s", $department);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();

    $count = $result['total'] + 1;
    $roll = $department . $year . str_pad($count, 4, "0", STR_PAD_LEFT);

    echo $roll;
    exit;
}

/* =========================
   FORM SUBMIT
========================= */
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $department = $_POST['department'];

    $year = date("Y");
    $query = $conn->prepare("SELECT COUNT(*) as total FROM student_details WHERE department=?");
    $query->bind_param("s", $department);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();

    $count = $result['total'] + 1;
    $student_id = $department . $year . str_pad($count, 4, "0", STR_PAD_LEFT);

    // upload
    $photoName = "";
    if (!empty($_FILES['photo']['name'])) {

        $targetDir = "uploads/";
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $targetDir . $fileName);

        $photoName = $fileName;
    }

    $stmt = $conn->prepare("INSERT INTO student_details 
    (student_id, full_name, father_name, dob, gender, email, phone, address, course, department, semester, admission_date, photo)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssssssssssss",
        $student_id,
        $_POST['full_name'],
        $_POST['father_name'],
        $_POST['dob'],
        $_POST['gender'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['course'],
        $department,
        $_POST['semester'],
        $_POST['admission_date'],
        $photoName
    );

    if ($stmt->execute()) {
        $message = "Student Added Successfully! Roll No: " . $student_id;
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>University ERP - Add Student</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Poppins;
        }

        body {
            background: #f4f6fb;
            display: flex;
            justify-content: center;
        }

        /* CARD */
        .erp-card {
            width: 950px;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        /* HEADER */
        .header {
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            padding: 20px;
            text-align: center;
            color: #fff;
        }

        .header h2 {
            font-size: 22px;
        }

        /* ROLL */
        .roll {
            background: #f1f5f9;
            text-align: center;
            padding: 12px;
            font-weight: 600;
            color: #111827;
        }

        /* FORM */
        .form {
            padding: 25px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* SECTION TITLE */
        .section-title {
            grid-column: span 2;
            font-size: 13px;
            font-weight: 600;
            color: #4f46e5;
            margin-top: 10px;
            border-left: 4px solid #4f46e5;
            padding-left: 10px;
        }

        /* LABEL */
        label {
            font-size: 12px;
            color: #374151;
            margin-bottom: 4px;
        }

        /* INPUT BOX */
        .input-box {
            display: flex;
            flex-direction: column;
        }

        /* INPUT */
        input,
        select,
        textarea {
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            outline: none;
            transition: 0.2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        textarea {
            resize: none;
            height: 80px;
        }

        .full {
            grid-column: span 2;
        }

        /* BUTTON */
        button {
            grid-column: span 2;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }

        /* MESSAGE */
        .msg {
            text-align: center;
            margin: 10px;
            padding: 10px;
            background: #ecfdf5;
            color: #065f46;
            border-radius: 8px;
            border: 1px solid #a7f3d0;
        }

        /* IMAGE */
        img {
            width: 100px;
            margin-top: 8px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>

    <div class="erp-card">

        <div class="header">
            <h2>🎓 University ERP - Add Student</h2>
        </div>

        <div class="roll" id="rollPreview">
            Roll No: Auto Generate
        </div>

        <?php if ($message != "") { ?>
            <div class="msg"><?= $message ?></div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="form">

                <div class="section-title">PERSONAL INFORMATION</div>

                <div class="input-box">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required>
                </div>

                <div class="input-box">
                    <label>Father Name</label>
                    <input type="text" name="father_name">
                </div>

                <div class="input-box">
                    <label>Date of Birth</label>
                    <input type="date" name="dob">
                </div>

                <div class="input-box">
                    <label>Gender</label>
                    <select name="gender">
                        <option value="">Select</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>

                <div class="section-title">CONTACT DETAILS</div>

                <div class="input-box">
                    <label>Email</label>
                    <input type="email" name="email">
                </div>

                <div class="input-box">
                    <label>Phone</label>
                    <input type="text" name="phone">
                </div>

                <div class="full input-box">
                    <label>Address</label>
                    <textarea name="address"></textarea>
                </div>

                <div class="section-title">ACADEMIC DETAILS</div>

                <div class="input-box">
                    <label>Department</label>
                    <select name="department" id="department" onchange="getRoll()" required>
                        <option value="">Select</option>
                        <option>SET</option>
                        <option>SOB</option>
                        <option>PHARM</option>
                        <option>LAW</option>
                    </select>
                </div>

                <div class="input-box">
                    <label>Course (Manual Select)</label>
                    <select name="course" id="course">
                        <option value="">Select Course</option>
                        <option>B.Tech</option>
                        <option>M.Tech</option>
                        <option>BCA</option>
                        <option>BBA</option>
                        <option>MBA</option>
                        <option>B.Pharm</option>
                        <option>M.Pharm</option>
                        <option>LLB</option>
                        <option>LLM</option>
                    </select>
                </div>

                <div class="input-box">
                    <label>Semester</label>
                    <select name="semester">
                        <option value="">Select Semester</option>
                        <option>Semester 1</option>
                        <option>Semester 2</option>
                        <option>Semester 3</option>
                        <option>Semester 4</option>
                        <option>Semester 5</option>
                        <option>Semester 6</option>
                        <option>Semester 7</option>
                        <option>Semester 8</option>
                    </select>
                </div>

                <div class="input-box">
                    <label>Admission Date</label>
                    <input type="date" name="admission_date">
                </div>

                <div class="full input-box">
                    <label>Passport Photo</label>
                    <input type="file" name="photo" onchange="previewImage(event)">
                    <img id="preview">
                </div>

                <button type="submit">Save Student</button>

            </div>
        </form>

    </div>

    <script>
        function getRoll() {
            let d = document.getElementById("department").value;
            if (!d) return;

            fetch("?get_roll=1&department=" + d)
                .then(r => r.text())
                .then(t => {
                    document.getElementById("rollPreview").innerHTML = "Roll No: " + t;
                });
        }

        function previewImage(e) {
            let r = new FileReader();
            r.onload = () => document.getElementById("preview").src = r.result;
            r.readAsDataURL(e.target.files[0]);
        }
    </script>

</body>

</html>