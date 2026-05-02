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
        /* =========================
   PREMIUM UI SYSTEM (FINAL CLEAN)
========================= */

        body {
            background: linear-gradient(135deg, #eef2ff, #f8fafc);
            color: #0f172a;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            text-transform: capitalize;
        }

        /* =========================
   CARD (GLASS PREMIUM)
========================= */
        .card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
            padding: 40px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 70px rgba(15, 23, 42, 0.12);
        }

        /* =========================
   TITLE
========================= */
        .form-title {
            text-align: center;
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* =========================
   FORM GRID
========================= */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 22px 28px;
        }

        /* =========================
   LABELS
========================= */
        label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 6px;
            display: block;
        }

        /* =========================
   INPUTS / SELECT / TEXTAREA
========================= */
        input,
        select {
            width: 100%;
            height: 45px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            font-size: 14px;
            transition: all 0.25s ease;
            box-sizing: border-box;
        }

        input:hover,
        select:hover,
        textarea:hover {
            background: #fff;
            border-color: #c7d2fe;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            background: #fff;
        }

        input::placeholder {
            color: #94a3b8;
        }



        /* TEXTAREA SAME HEIGHT AS INPUT */
        textarea {
            width: 49%;
            height: 100px;
            /* match input height */
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            font-size: 14px;
            resize: none;
            /* optional: disable resizing */
            transition: all 0.25s ease;
            box-sizing: border-box;
        }

        /* Hover & focus (keep consistent) */
        textarea:hover {
            background: #fff;
            border-color: #c7d2fe;
        }

        textarea:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            background: #fff;
        }

        /* =========================
   BUTTON
========================= */
        button {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: white;
            border: none;
            padding: 12px 18px;
            border-radius: 12px;
            width: 220px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(37, 99, 235, 0.35);
        }

        /* =========================
   FULL WIDTH FIELD
========================= */
        .full {
            grid-column: span 2;
        }


        /* =========================
   SUCCESS MESSAGE
========================= */
        .msg {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 10px;
            font-size: 14px;
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            border: 1px solid #86efac;
            color: #166534;
            font-weight: 600;
        }

        /* =========================
   PHOTO UPLOAD
========================= */
        .preview-box {
            width: 120px;
            height: 120px;
            margin-top: 10px;
            position: relative;
            border-radius: 14px;
            border: 2px dashed #cbd5e1;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #photoPreview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            border: none;
        }

        /* REMOVE PHOTO BUTTON */
        .remove-photo {
            display: none;
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            background: #ef4444;
            color: #fff;
            border-radius: 50%;
            text-align: center;
            line-height: 24px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }

        /* =========================
   POPUP BASE success model
========================= */
        .popup-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .popup-box {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            text-align: center;
            min-width: 320px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.25);
        }

        /* SUCCESS ID */
        .popup-id {
            margin-top: 15px;
            font-size: 20px;
            font-weight: 700;
            color: #16a34a;
        }

        /* SUCCESS ICON */
        .success_icon {
            width: 100px;
            height: 60px;
        }

        /* =========================
   PREVIEW MODAL (FULL SCREEN)
========================= */
        #previewPopup {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(6px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        #previewPopup .popup-box {
            width: 100%;
            max-width: 1080px;
            height: 80vh;
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
        }

        /* HEADER */
        #previewPopup h2 {
            margin: 0;
            padding: 16px 20px;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(90deg, #1e3a8a, #2563eb, #1d4ed8);
        }

        /* CONTENT */
        #previewContent {
            flex: 1;
            overflow-y: auto;
            padding: 22px;
            background: #f1f5f9;
        }

        /* GRID */
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(3, 2fr);
            gap: 12px;
        }

        /* ITEM */
        .preview-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px;
            display: flex;
            flex-direction: column;
        }


        .preview-item span {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
        }

        .preview-item b {
            font-size: 14px;
            color: #0f172a;
        }

        /* FULL */
        .preview-item.full {
            grid-column: span 3;
        }

        /* ACTIONS */
        .preview-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 14px 20px;
            border-top: 1px solid #e5e7eb;
        }

        .photo-container {
            width: 150px;
            display: flex;
            align-items: center;
            justify-content: center;

        }

        .preview-img {
            width: 120px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-top: 8px;
        }

        .btn-confirm {
            background: #2563eb;
        }

        .btn-edit {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #e2e8f0;
        }

        /* =========================
   RESPONSIVE
========================= */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            button {
                width: 100%;
            }

            .preview-grid {
                grid-template-columns: 1fr;
            }

            .preview-item.full {
                grid-column: span 1;
            }
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
                            <option value="SELECT GENDER" selected>SELECT GENDER</option>
                            <option value="MALE">MALE</option>
                            <option value="FEMALE">FEMALE</option>
                            <option value="OTHER">OTHER</option>
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
                        <?php
                        $deptResult = $conn->query("SELECT * FROM departments");
                        ?>
                        <label>School</label>
                        <select name="department" id="department">
                            <!-- <option value="" selected>Select Department</option>
                            <option value="SET">SET</option>
                            <option value="SOB">SOB</option>
                            <option value="LLM">LLM</option>
                            <option value="OTHER">OTHER</option> -->
                            <option value="">SELECT SCHOOL</option>
                            <?php while ($row = $deptResult->fetch_assoc()) { ?>
                                <option value="<?= $row['id'] ?>">
                                    <?= $row['name'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div>
                        <label>Course</label>
                        <select name="course" id="course">
                            <option value="">SELECT COURSE</option>
                        </select>
                    </div>

                    <div>
                        <?php
                        $semResult = $conn->query("SELECT * FROM semesters");
                        ?>
                        <label>Semester Allotment</label>
                        <select name="semester">
                            <option value="">SELECT SEMESTER</option>

                            <?php while ($row = $semResult->fetch_assoc()) { ?>
                                <option value="<?= $row['semester_name'] ?>">
                                    <?= $row['semester_name'] ?>
                                </option>
                            <?php } ?>

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
                            <option value="">SELECT UNIVERSITY</option>
                            <option value="SUSHANT UNIVERSITY">SUSHANT UNIVERSITY</option>
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
        <div class="popup-box">
            <h2>Confirm Student Details</h2>

            <div id="previewContent"></div>

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
        // funtion for date of birth /
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



        document.querySelectorAll("input[type='text'], textarea").forEach(field => {
            field.addEventListener("input", function () {
                this.value = this.value.toUpperCase();
            });
        });

        // this is filter data from department to courses
        // document.getElementById("department").addEventListener("change", function () {
        //     let dept = this.value;
        //     let courseSelect = document.getElementById("course");

        //     courseSelect.innerHTML = '<option value="">Select Course</option>';

        //     if (coursesByDept[dept]) {
        //         coursesByDept[dept].forEach(course => {
        //             let option = document.createElement("option");
        //             option.value = course;
        //             option.textContent = course;
        //             courseSelect.appendChild(option);
        //         });
        //     }
        // });


        // ajax with backend logic filters
        document.getElementById("department").addEventListener("change", function () {

            let dept_id = this.value;
            let courseSelect = document.getElementById("course");

            courseSelect.innerHTML = '<option value="">Loading...</option>';

            fetch("get_courses.php?dept_id=" + dept_id)
                .then(res => res.json())
                .then(data => {

                    courseSelect.innerHTML = '<option value="">Select Course</option>';

                    data.forEach(course => {
                        let opt = document.createElement("option");
                        opt.value = course.id;
                        opt.textContent = course.course_name;
                        courseSelect.appendChild(opt);
                    });
                });
        });




        // close popup script code
        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        <?php if ($message != "" && strpos($message, "Student added successfully") !== false) { ?>
            document.getElementById("popup").style.display = "flex";
            document.getElementById("popup").classList.add("show");
        <?php } ?>




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



        // script code for image photoPreview
        let photoInput = document.getElementById("photoInput");
        let preview = document.getElementById("photoPreview");
        let removeBtn = document.getElementById("removePhoto");

        photoInput.addEventListener("change", function (event) {
            let file = event.target.files[0];

            if (file) {
                let reader = new FileReader();

                reader.onload = function (e) {
                    selectedPhotoDataURL = e.target.result; // ✅ store for modal

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

        let selectedPhotoDataURL = "";

        function openPreview() {

            let form = document.getElementById("studentForm");

            if (!form) {
                alert("Form not found");
                return;
            }

            let html = `
    <div class="preview-grid">

        <div class="preview-item"><span>STUDENT NAME</span><b>${form.full_name.value}</b></div>

        <div class="preview-item"><span>FATHER NAME</span><b>${form.father_name.value}</b></div>

        <div class="preview-item"><span>MOTHER NAME</span><b>${form.mother_name.value}</b></div>

        <div class="preview-item"><span>DATE OF BIRTH</span><b>${form.dob.value}</b></div>

        <div class="preview-item"><span>GENDER</span><b>${form.gender.value}</b></div>

        <div class="preview-item"><span>EMAIL ADDRESS</span><b>${form.email.value}</b></div>

        <div class="preview-item"><span>PHONE NUMBER</span><b>${form.phone.value}</b></div>

        <div class="preview-item"><span>SCHOOL</span><b>${form.department.value}</b></div>

        <div class="preview-item"><span>COURSE</span><b>${form.course.value}</b></div>

        <div class="preview-item"><span>SEMESTER</span><b>${form.semester.value}</b></div>

        <div class="preview-item"><span>ADMISSION DATE</span><b>${form.admission_date.value}</b></div>

        <div class="preview-item"><span>UNIVERSITY</span><b>${form.university.value}</b></div>

        <div class="preview-item full"><span>PERMANENT ADDRESS</span><b>${form.address.value}</b></div>

        <div class="preview-item photo-container">
            <span>Photo</span>
                <img class="preview-img" src="${selectedPhotoDataURL}">
        </div>
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