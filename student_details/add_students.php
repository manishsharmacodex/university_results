<?php
include("../server/connection.php");

$message = "";

// FUNCTION TO CONVERT dd/mm/yyyy → yyyy-mm-dd
function convertDate($date) {
    $parts = explode('/', $date);
    if (count($parts) == 3) {
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    return null;
}

// HANDLE FORM SUBMISSION
if (isset($_POST['submit'])) {

    $student_id = $_POST['student_id'];
    $full_name = $_POST['full_name'];
    $father_name = $_POST['father_name'];

    $dob_input = $_POST['dob'];
    $admission_input = $_POST['admission_date'];

    $dob = convertDate($dob_input);
    $admission_date = convertDate($admission_input);

    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $course = $_POST['course'];
    $department = $_POST['department'];
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
        (student_id, full_name, father_name, dob, gender, email, phone, address, course, department, semester, admission_date, photo)
        VALUES 
        ('$student_id', '$full_name', '$father_name', '$dob', '$gender', '$email', '$phone', '$address', '$course', '$department', '$semester', '$admission_date', '$photo_name')";

        if ($conn->query($sql) === TRUE) {
            $message = "Student added successfully!";
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
body{
    margin:0;
    font-family:Arial, sans-serif;
    background:#f1f5f9;
}

.container{
    max-width:1000px;
    margin:60px auto;
    padding:0 20px;
}

.card{
    background:#fff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,0.06);
}

.msg{
    padding:12px;
    margin-bottom:15px;
    background:#dcfce7;
    border:1px solid #86efac;
    border-radius:8px;
    font-size:14px;
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(2, 1fr);
    gap:15px;
}

label{
    font-size:13px;
    font-weight:600;
    color:#334155;
    display:block;
    margin-bottom:5px;
}

input, select, textarea{
    width:100%;
    padding:11px;
    border:1px solid #e2e8f0;
    border-radius:8px;
    outline:none;
    font-size:14px;
}

input:focus, select:focus, textarea:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 3px rgba(37,99,235,0.15);
}

textarea{
    height:90px;
    resize:none;
}

.full{
    grid-column:span 2;
}

button{
    background:#2563eb;
    color:#fff;
    border:none;
    padding:12px 18px;
    width:220px;
    border-radius:8px;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
}

button:hover{
    background:#1d4ed8;
}

@media(max-width:768px){
    .form-grid{
        grid-template-columns:1fr;
    }
    .full{
        grid-column:span 1;
    }
    button{
        width:100%;
    }
}
</style>

</head>

<body>

<div class="container">

<div class="card">

<?php if($message != "") { ?>
    <div class="msg"><?php echo $message; ?></div>
<?php } ?>

<form method="POST" enctype="multipart/form-data">

<div class="form-grid">

    <div>
        <label>Student ID</label>
        <input type="text" name="student_id" placeholder="Enter Student ID" required>
    </div>

    <div>
        <label>Full Name</label>
        <input type="text" name="full_name" placeholder="Enter Full Name" required>
    </div>

    <div>
        <label>Father Name</label>
        <input type="text" name="father_name" placeholder="Enter Father Name">
    </div>

    <div>
        <label>Date of Birth</label>
        <input type="text" id="dob" name="dob" placeholder="DDMMYYYY (Auto format)">
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
        <input type="email" name="email" placeholder="Enter Email Address">
    </div>

    <div>
        <label>Phone</label>
        <input type="text" name="phone" placeholder="Enter Phone Number">
    </div>

    <div>
        <label>Course</label>
        <input type="text" name="course" placeholder="Enter Course">
    </div>

    <div>
        <label>Department</label>
        <input type="text" name="department" placeholder="Enter Department">
    </div>

    <div>
        <label>Semester</label>
        <input type="text" name="semester" placeholder="Enter Semester">
    </div>

    <div>
        <label>Admission Date</label>
        <input type="text" id="admission_date" name="admission_date" placeholder="DDMMYYYY (Auto format)">
    </div>

    <div>
        <label>Photo</label>
        <input type="file" name="photo">
    </div>

    <div class="full">
        <label>Address</label>
        <textarea name="address" placeholder="Enter Full Address"></textarea>
    </div>

    <div class="full">
        <button type="submit" name="submit">SAVE STUDENT</button>
    </div>

</div>

</form>

</div>

</div>

<script>
// AUTO FORMAT FUNCTION (DD/MM/YYYY)
function formatDate(input) {
    let value = input.value.replace(/\D/g, ''); // remove non-digits

    if (value.length > 2) {
        value = value.slice(0,2) + '/' + value.slice(2);
    }
    if (value.length > 5) {
        value = value.slice(0,5) + '/' + value.slice(5,9);
    }

    input.value = value;
}

// APPLY TO BOTH FIELDS
document.getElementById("dob").addEventListener("input", function(){
    formatDate(this);
});

document.getElementById("admission_date").addEventListener("input", function(){
    formatDate(this);
});
</script>

</body>
</html>