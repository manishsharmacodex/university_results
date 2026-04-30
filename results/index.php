<?php
include("../server/connection.php");

$student = null;
$subjects = [];

/* ================= SEARCH STUDENT ================= */
if (isset($_POST['search'])) {

    $roll_number = $_POST['roll_number'];

    // student info
    $q1 = "SELECT * FROM university_results.student_details 
           WHERE student_roll_number = '$roll_number'";
    $res1 = mysqli_query($conn, $q1);
    $student = mysqli_fetch_assoc($res1);

    if (!$student) {
        $error_message = "No student found";
    }
}

/* ================= LOAD SUBJECTS (AJAX LIKE PHP) ================= */
if (isset($_POST['load_subjects'])) {

    $year = $_POST['year'];
    $semester = $_POST['semester'];

    $q = "SELECT subject_name FROM university_results.subjects 
          WHERE year='$year' AND semester='$semester'";

    $res = mysqli_query($conn, $q);

    while($row = mysqli_fetch_assoc($res)){
        $subjects[] = $row['subject_name'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Dynamic ERP System</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Poppins;}

body{background:#eef2f7;}

.header{
background:linear-gradient(135deg,#1e3c72,#2a5298);
color:white;
padding:15px;
text-align:center;
font-weight:600;
}

.wrapper{display:flex;gap:20px;padding:20px;}

.main{
flex:2;
background:white;
padding:25px;
border-radius:12px;
box-shadow:0 10px 20px rgba(0,0,0,0.08);
}

.profile{
flex:1;
background:white;
padding:20px;
border-radius:12px;
text-align:center;
box-shadow:0 10px 20px rgba(0,0,0,0.08);
}

.profile img{
width:120px;height:140px;border-radius:10px;
object-fit:cover;border:3px solid #2a5298;
}

input,select{
width:100%;
padding:10px;
margin:6px 0;
border-radius:6px;
border:1px solid #ddd;
}

button{
width:100%;
padding:12px;
border:none;
border-radius:6px;
color:white;
cursor:pointer;
}

.search{background:#6c757d;}
.load{background:#2a5298;}

.subject-box{
margin-top:15px;
padding:15px;
background:#f5f7fb;
border-radius:8px;
}

.subject-box ul{
padding-left:20px;
}

@media(max-width:900px){
.wrapper{flex-direction:column;}
}
</style>
</head>

<body>

<div class="header">
🎓 Dynamic University ERP - Subject Management System
</div>

<div class="wrapper">

<!-- MAIN -->
<div class="main">

<form method="POST">

<input type="text" name="roll_number" placeholder="Enter Roll Number" required>
<button class="search" name="search">Search Student</button>

</form>

<hr>

<form method="POST">

<label>Year</label>
<select name="year" required>
<option value="">Select Year</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
</select>

<label>Semester</label>
<select name="semester" required>
<option value="">Select Semester</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
</select>

<button class="load" name="load_subjects">Load Subjects</button>

</form>

<!-- SUBJECT DISPLAY -->
<div class="subject-box">

<?php if(!empty($subjects)) { ?>
<h3>Subjects:</h3>
<ul>
<?php foreach($subjects as $sub){ ?>
<li><?php echo $sub; ?></li>
<?php } ?>
</ul>
<?php } else { ?>
<p>Select year and semester to load subjects</p>
<?php } ?>

</div>

</div>

<!-- PROFILE -->
<div class="profile">

<?php if($student){ ?>

<img src="../student_details/<?php echo $student['photo']; ?>">

<h3><?php echo $student['student_name']; ?></h3>
<p><?php echo $student['department']; ?></p>
<p>Sem: <?php echo $student['semester']; ?></p>
<p>Year: <?php echo $student['admission_year']; ?></p>

<?php } else { ?>
<p>No student selected</p>
<?php } ?>

</div>

</div>

</body>
</html>