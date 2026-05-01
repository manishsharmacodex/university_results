<?php
include("../server/connection.php");

$message = "";

// FUNCTION TO CONVERT dd/mm/yyyy → yyyy-mm-dd
function convertDate($date)
{
    $parts = explode('/', $date);
    if (count($parts) == 3) {
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    return null;
}

/* =========================
   AUTO STUDENT ID FUNCTION
========================= */
function generateStudentId($conn, $department, $course)
{
    $deptCode = strtoupper($department);
    $courseCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $course));

    $prefix = $deptCode . "-" . $courseCode;

    $sql = "SELECT student_id FROM student_details 
            WHERE student_id LIKE '$prefix%' 
            ORDER BY id DESC LIMIT 1";

    $result = $conn->query($sql);

    $number = 1;

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['student_id'];

        $parts = explode('-', $lastId);
        $lastNumber = end($parts);

        $number = (int) $lastNumber + 1;
    }

    return $prefix . "-" . str_pad($number, 4, "0", STR_PAD_LEFT);
}

// HANDLE FORM SUBMISSION
if (isset($_POST['submit'])) {

    $department = $_POST['department'];
    $course = $_POST['course'];

    // AUTO GENERATED STUDENT ID
    $student_id = generateStudentId($conn, $department, $course);

    $full_name = $_POST['full_name'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name']; // ✅ ADDED

    $dob_input = $_POST['dob'];
    $admission_input = $_POST['admission_date'];

    $dob = convertDate($dob_input);
    $admission_date = convertDate($admission_input);

    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $semester = $_POST['semester'];

    $photo_name = "";

    if (!empty($_FILES["photo"]["name"])) {

        $folder = "uploads/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $folder . $photo_name);
    }

    if ($dob && $admission_date) {

        $sql = "INSERT INTO student_details 
        (student_id, full_name, father_name, mother_name, dob, gender, email, phone, address, course, department, semester, admission_date, photo)
        VALUES 
        ('$student_id', '$full_name', '$father_name', '$mother_name', '$dob', '$gender', '$email', '$phone', '$address', '$course', '$department', '$semester', '$admission_date', '$photo_name')";

        if ($conn->query($sql) === TRUE) {
            $message = "Student added successfully! Student ID: " . $student_id;
        } else {
            $message = "Error: " . $conn->error;
        }

    } else {
        $message = "Invalid date format. Use dd/mm/yyyy";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Student</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
        }

        .container {
            max-width: 1000px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .card {
            background: #fff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        }

        .msg {
            padding: 12px;
            margin-bottom: 15px;
            background: #dcfce7;
            border: 1px solid #86efac;
            border-radius: 8px;
            font-size: 14px;
        }

        /* GRID */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            column-gap: 40px;
            row-gap: 18px;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            display: block;
            margin-bottom: 6px;
        }

        /* ✅ UNIFIED INPUT / SELECT / TEXTAREA SIZE */
        input,
        select,
        textarea {
            width: 100%;
            height: 45px;
            /* equal height */
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            outline: none;
            font-size: 14px;
            box-sizing: border-box;
            transition: 0.2s;
        }

        /* TEXTAREA FIX (keep bigger but aligned width) */
        textarea {
            height: 95px;
            resize: none;
        }

        /* FOCUS */
        input:focus,
        select:focus,
        textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* FULL WIDTH */
        .full {
            grid-column: span 2;
        }

        /* BUTTON */
        button {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 12px 18px;
            width: 220px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        button:hover {
            background: #1d4ed8;
        }

        /* MOBILE */
        @media(max-width:768px) {
            .form-grid {
                grid-template-columns: 1fr;
                column-gap: 0;
            }

            .full {
                grid-column: span 1;
            }

            button {
                width: 100%;
            }
        }

        /* DISABLED */
        input:disabled {
            background: #e5e7eb;
            color: #6b7280;
        }

        /* POPUP (UNCHANGED) */
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .popup-box {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            min-width: 300px;
        }

        .popup-id {
            margin-top: 15px;
            font-size: 20px;
            font-weight: bold;
            color: #16a34a;
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="card">

            <?php if ($message != "") { ?>
                <div class="msg"><?php echo $message; ?></div>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-grid">

                    <div>
                        <label>Student Name</label>
                        <input type="text" name="full_name" placeholder="Enter Full Name" autocomplete="off" required>
                    </div>

                    <div>
                        <label>Father Name</label>
                        <input type="text" name="father_name" placeholder="Enter Father Name" autocomplete="off">
                    </div>

                    <!-- ✅ ONLY NEW FIELD -->
                    <div>
                        <label>Mother Name</label>
                        <input type="text" name="mother_name" placeholder="Enter Mother Name" autocomplete="off">
                    </div>

                    <div>
                        <label>Date of Birth</label>
                        <input type="text" id="dob" name="dob" placeholder="DD/MM/YYYY" autocomplete="off">
                    </div>

                    <div>
                        <label>Department</label>
                        <select name="department" id="department" required>
                            <option value="">Select Department</option>
                            <option value="SET">SET</option>
                            <option value="SOB">SOB</option>
                            <option value="LLM">LLM</option>
                            <option value="OTHER">OTHER</option>
                        </select>
                    </div>

                    <div>
                        <label>School</label>
                        <select name="course" id="course" required>
                            <option value="">Select Course</option>
                        </select>
                    </div>

                    <div>
                        <label>Gender</label>
                        <select name="gender">
                            <option value="">Select Gender</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div>
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Enter Email Address" autocomplete="off">
                    </div>

                    <div>
                        <label>Phone</label>
                        <input type="text" name="phone" placeholder="Enter Phone Number" autocomplete="off">
                    </div>

                    <!-- <div>
                        <label>Semester</label>
                        <input type="text" value="1" disabled>
                        <input type="hidden" name="semester" value="1">
                    </div> -->

                    <!-- <div>
                        <label>Semester</label>
                        <select name="semester" required>
                            <option value="Select Semester" selected>Select Semester</option>
                            <option value="Semester 1" selected>Semester 1</option>
                        </select>
                    </div> -->

                    <div>
                        <label>Semester</label>
                        <select name="semester" required>
                            <option value="Select Semester" selected>Select Semester</option>
                            <option value="Semester 1">Semester 1</option>
                        </select>
                    </div>

                    <div>
                        <label>Admission Date</label>
                        <input type="text" id="admission_date" name="admission_date" placeholder="DD/MM/YYYY"
                            autocomplete="off">
                    </div>

                    <div>
                        <label>Photo</label>
                        <input type="file" name="photo">
                    </div>

                    <div class="full">
                        <label>Address</label>
                        <textarea name="address" placeholder="Enter Full Address" autocomplete="off"></textarea>
                    </div>

                    <div class="full">
                        <button type="submit" name="submit">ADD STUDENT</button>
                    </div>

                </div>

            </form>

        </div>

    </div>

    <!-- POPUP -->
    <div class="popup-overlay" id="popup">
        <div class="popup-box">
            <h2>Student Created Successfully</h2>
            <div class="popup-id">
                <?php
                if (isset($student_id)) {
                    echo $student_id;
                }
                ?>
            </div>
            <button class="popup-close" onclick="closePopup()">Close</button>
        </div>
    </div>

    <script>
        function formatDate(input) {
            let value = input.value.replace(/\D/g, '');

            if (value.length > 2) value = value.slice(0, 2) + '/' + value.slice(2);
            if (value.length > 5) value = value.slice(0, 5) + '/' + value.slice(5, 9);

            input.value = value;
        }

        document.getElementById("dob").addEventListener("input", function () {
            formatDate(this);
        });

        document.getElementById("admission_date").addEventListener("input", function () {
            formatDate(this);
        });

        const coursesByDept = {
            "SET": ["B.Tech CSE", "B.Tech IT", "MCA", "BCA"],
            "SOB": ["BBA", "MBA", "B.Com"],
            "LLM": ["LLB", "LLM"],
            "OTHER": ["Diploma", "Certificate Course"]
        };

        document.getElementById("department").addEventListener("change", function () {
            let dept = this.value;
            let courseSelect = document.getElementById("course");

            courseSelect.innerHTML = '<option value="">Select Course</option>';

            if (coursesByDept[dept]) {
                coursesByDept[dept].forEach(course => {
                    let option = document.createElement("option");
                    option.value = course;
                    option.textContent = course;
                    courseSelect.appendChild(option);
                });
            }
        });

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        <?php if ($message != "" && strpos($message, "Student added successfully") !== false) { ?>
            document.getElementById("popup").style.display = "flex";
            document.getElementById("popup").classList.add("show");
        <?php } ?>
    </script>

</body>

</html>