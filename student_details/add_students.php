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


// functions for auto generate sections with check department and course and semester check
function generateSection($conn, $department, $course, $semester)
{
    $sections = range('A', 'Z');

    $sql = "SELECT section, COUNT(*) as total 
            FROM student_details 
            WHERE department='$department' 
            AND course='$course' 
            AND semester='$semester'
            GROUP BY section
            ORDER BY section ASC";

    $result = $conn->query($sql);

    $sectionCounts = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $sectionCounts[$row['section']] = $row['total'];
        }
    }

    // find available section with < 5 students
    foreach ($sections as $sec) {
        if (!isset($sectionCounts[$sec]) || $sectionCounts[$sec] < 50) {
            return $sec;
        }
    }

    return "A"; // fallback (should rarely happen)
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

    $university = $_POST['university'];

    // $section = $_POST['section'];
    // $section = generateSection($conn, $department, $course, $semester);

    $semester = $_POST['semester'];

    if ($semester == "" || $semester == "Select Semester") {
        $message = "Please select semester before submitting!";
    } else {
        $section = generateSection($conn, $department, $course, $semester);
    }

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
        (student_id, full_name, father_name, mother_name, dob, gender, email, phone, address, course, department, semester, admission_date, photo, university, section)
        VALUES 
        ('$student_id', '$full_name', '$father_name', '$mother_name', '$dob', '$gender', '$email', '$phone', '$address', '$course', '$department', '$semester', '$admission_date', '$photo_name', '$university', '$section')";

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
    <link rel="stylesheet" type="text/css" href="../css/font.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        /* success icon */
        .success_icon {
            width: 100px;
            height: 60px;
        }

        /* photoPreview */
        .preview-box {
            width: 120px;
            height: 120px;
            position: relative;
            margin-top: 10px;
        }

        #photoPreview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            display: none;
            position: absolute;
            top: 0;
            left: 0;
        }

        /* ✅ TOP RIGHT CROSS ICON */
        .remove-photo {
            display: none;
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 24px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .remove-photo:hover {
            background: #dc2626;
        }

        .form-title {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1e293b;
            border-bottom: 1px solid #6b7280;
            padding-bottom: 20px;
        }


        /* ===== GLASS PREVIEW POPUP ===== */
        #previewPopup {
            backdrop-filter: blur(8px);
        }

        #previewPopup .popup-box {
            width: 90%;
            max-width: 600px;
            padding: 18px 20px;
            border-radius: 14px;

            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);

            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);

            text-align: left;
        }

        /* TITLE */
        #previewPopup h2 {
            font-size: 16px;
            margin-bottom: 8px;
            color: #0f172a;
            font-weight: 600;
        }

        /* ===== COMPACT GRID ===== */
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px 14px;
            margin-top: 10px;
        }

        /* FIELD CARD */
        .preview-item {
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(226, 232, 240, 0.6);
            padding: 8px 10px;
            border-radius: 8px;

            display: flex;
            flex-direction: column;
        }

        /* LABEL */
        .preview-item span {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 2px;
        }

        /* VALUE */
        .preview-item b {
            font-size: 13px;
            color: #0f172a;
            font-weight: 600;
            word-break: break-word;
        }

        /* FULL WIDTH */
        .preview-item.full {
            grid-column: span 2;
        }

        /* BUTTONS */
        .preview-actions {
            margin-top: 12px;
            display: flex;
            gap: 10px;
        }

        .preview-actions button {
            flex: 1;
            padding: 8px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }

        /* CONFIRM */
        .btn-confirm {
            background: rgba(34, 197, 94, 0.9);
            color: #fff;
        }

        .btn-confirm:hover {
            background: rgba(22, 163, 74, 1);
        }

        /* EDIT */
        .btn-edit {
            background: rgba(226, 232, 240, 0.8);
            color: #0f172a;
        }

        .btn-edit:hover {
            background: rgba(203, 213, 225, 1);
        }

        /* SCROLL CONTROL */
        #previewContent {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 4px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h2 class="form-title">Register New Student</h2>
            <?php if ($message != "") { ?>
                <div class="msg"><?php echo $message; ?></div>
            <?php } ?>

            <form id="studentForm" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div>
                        <label>Student Name</label>
                        <input type="text" name="full_name" placeholder="Enter Student Name" autocomplete="off">
                    </div>

                    <div>
                        <label>Father Name</label>
                        <input type="text" name="father_name" placeholder="Enter Father Name" autocomplete="off">
                    </div>

                    <div>
                        <label>Mother Name</label>
                        <input type="text" name="mother_name" placeholder="Enter Mother Name" autocomplete="off">
                    </div>

                    <div>
                        <label>Date of Birth</label>
                        <input type="text" id="dob" name="dob" placeholder="DD/MM/YYYY" autocomplete="off">
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
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="Enter Email Address" autocomplete="off">
                    </div>

                    <div>
                        <label>Phone Number</label>
                        <input type="text" name="phone" placeholder="Enter Phone Number" autocomplete="off"
                            maxlength="10">
                    </div>

                    <div>
                        <label>Department</label>
                        <select name="department" id="department">
                            <option value="">Select Department</option>
                            <option value="SET">SET</option>
                            <option value="SOB">SOB</option>
                            <option value="LLM">LLM</option>
                            <option value="OTHER">OTHER</option>
                        </select>
                    </div>

                    <div>
                        <label>School</label>
                        <select name="course" id="course">
                            <option value="">Select Course</option>
                        </select>
                    </div>

                    <div>
                        <label>Semester Allotment</label>
                        <select name="semester">
                            <option value="" selected>Select Semester</option>
                            <option value="Semester 1">Semester 1</option>
                        </select>
                    </div>


                    <!-- in future this option will work for live section value -->
                    <!-- <div>
                        <label>Section</label>
                        <input type="text" name="section" readonly placeholder="Auto Generated Section"
                            >
                    </div> -->

                    <div>
                        <label>Admission Date</label>
                        <input type="text" id="admission_date" name="admission_date" placeholder="DD/MM/YYYY"
                            autocomplete="off">
                    </div>

                    <div>
                        <label>University</label>
                        <select name="university">
                            <option value="">Select University</option>
                            <option value="Sushant University">Sushant University</option>
                        </select>
                    </div>

                    <div class="photo-wrapper">
                        <label>Photo</label>
                        <input type="file" name="photo" id="photoInput">

                        <div class="preview-box">
                            <img id="photoPreview">
                            <span id="removePhoto" class="remove-photo">✕</span>
                        </div>
                    </div>

                    <div class="full">
                        <label>Permanent Address</label>
                        <textarea name="address" placeholder="Enter Permanent Full Address"
                            autocomplete="off"></textarea>
                    </div>

                    <div class="full">
                        <button type="button" onclick="openPreview()">ADD STUDENT</button>
                    </div>

                </div>
            </form>
        </div>
    </div>



    <!-- PREVIEW POPUP -->
    <div class="popup-overlay" id="previewPopup">
        <div class="popup-box" style="text-align:left; max-width:500px;">
            <h2>Confirm Student Details</h2>

            <div id="previewContent" style="font-size:14px; margin-top:15px;"></div>

            <div class="preview-actions">
                <button type="button" class="btn-confirm" onclick="submitFinal()">Confirm & Add</button>
                <button type="button" class="btn-edit" onclick="closePreview()">Edit</button>
            </div>
        </div>
    </div>


    <!-- SUCCESS POPUP -->
    <div class="popup-overlay" id="popup">
        <div class="popup-box">

            <div class="success-icon">
                <img src="../src/images/success_icon.png" alt="Success OK" class="success_icon">
            </div>

            <h2>Student Added Successfully</h2>

            <div class="popup-id">
                <?php
                if (isset($student_id)) {
                    echo $student_id;
                }
                ?>
            </div>

            <button class="popup-close" onclick="closePopup()">Done</button>
        </div>
    </div>

    <script>
        // funtion for work filter data like department and course
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


        // close popup script code
        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        <?php if ($message != "" && strpos($message, "Student added successfully") !== false) { ?>
            document.getElementById("popup").style.display = "flex";
            document.getElementById("popup").classList.add("show");
        <?php } ?>





        // script code for image photoPreview
        let photoInput = document.getElementById("photoInput");
        let preview = document.getElementById("photoPreview");
        let removeBtn = document.getElementById("removePhoto");

        photoInput.addEventListener("change", function (event) {
            let file = event.target.files[0];

            if (file) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                    removeBtn.style.display = "inline-block";
                };
                reader.readAsDataURL(file);
            }
        });

        removeBtn.addEventListener("click", function () {
            photoInput.value = "";
            preview.src = "";
            preview.style.display = "none";
            removeBtn.style.display = "none";
        });





        // script code for auto generate section if not condition check from php just random section generate
        // function generateSection() {
        //     const sections = ["A", "B", "C", "D", "E"];

        //     // shuffle array
        //     for (let i = sections.length - 1; i > 0; i--) {
        //         const j = Math.floor(Math.random() * (i + 1));
        //         [sections[i], sections[j]] = [sections[j], sections[i]];
        //     }

        //     // pick first section after shuffle
        //     return sections[0];
        // }

        // // trigger on department OR course change
        // function updateSection() {
        //     let dept = document.getElementById("department").value;
        //     let course = document.getElementById("course").value;

        //     if (dept !== "" && course !== "") {
        //         document.getElementById("section").value = generateSection();
        //     } else {
        //         document.getElementById("section").value = "";
        //     }
        // }

        // // attach events
        // document.getElementById("department").addEventListener("change", updateSection);
        // document.getElementById("course").addEventListener("change", updateSection);




        function openPreview() {

            let form = document.getElementById("studentForm");

            if (!form) {
                alert("Form not found");
                return;
            }

            let html = `
    <div class="preview-grid">

        <div class="preview-item"><span>Name</span><b>${form.full_name.value}</b></div>
        <div class="preview-item"><span>Father</span><b>${form.father_name.value}</b></div>

        <div class="preview-item"><span>Mother</span><b>${form.mother_name.value}</b></div>
        <div class="preview-item"><span>DOB</span><b>${form.dob.value}</b></div>

        <div class="preview-item"><span>Gender</span><b>${form.gender.value}</b></div>
        <div class="preview-item"><span>Email</span><b>${form.email.value}</b></div>

        <div class="preview-item"><span>Phone</span><b>${form.phone.value}</b></div>
        <div class="preview-item"><span>Department</span><b>${form.department.value}</b></div>

        <div class="preview-item"><span>Course</span><b>${form.course.value}</b></div>
        <div class="preview-item"><span>Semester</span><b>${form.semester.value}</b></div>

        <div class="preview-item"><span>University</span><b>${form.university.value}</b></div>

        <div class="preview-item full"><span>Address</span><b>${form.address.value}</b></div>

    </div>
`;

            document.getElementById("previewContent").innerHTML = html;
            document.getElementById("previewPopup").style.display = "flex";
        }

        function closePreview() {
            document.getElementById("previewPopup").style.display = "none";
        }

        function submitFinal() {

            let form = document.getElementById("studentForm");

            if (!form) {
                alert("Form not found");
                return;
            }

            // create REAL submit button
            let btn = document.createElement("button");
            btn.type = "submit";
            btn.name = "submit";
            btn.style.display = "none";

            form.appendChild(btn);

            btn.click(); // trigger submit
        }
    </script>
</body>

</html>