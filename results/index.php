<?php
include("../server/connection.php");

$student = null;
$results = [];
$message = "";

// SEARCH STUDENT + RESULTS
if (isset($_POST['search'])) {

    $roll = $_POST['roll_number'];

    // FETCH STUDENT
    $query = "SELECT * FROM student_details WHERE student_roll_number='$roll'";
    $data = mysqli_query($conn, $query);
    $student = mysqli_fetch_assoc($data);

    if ($student) {

        // FETCH SUBJECTS
        $res = mysqli_query($conn, "SELECT * FROM student_results WHERE roll_number='$roll'");
        while ($row = mysqli_fetch_assoc($res)) {
            $results[] = $row;
        }

    } else {
        $message = "❌ Student not found!";
    }
}

// SAVE OR UPDATE RESULT
if (isset($_POST['submit'])) {

    $roll = $_POST['roll_number'];
    $department = $_POST['department'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];

    $subjects = $_POST['subject'];
    $marks = $_POST['marks'];
    $ids = $_POST['result_id']; // hidden ids for update

    for ($i = 0; $i < count($subjects); $i++) {

        $sub = $subjects[$i];
        $mark = $marks[$i];

        if (!empty($sub) && !empty($mark)) {

            // UPDATE EXISTING
            if (!empty($ids[$i])) {

                $id = $ids[$i];

                $query = "UPDATE student_results 
                          SET subject_name='$sub', marks='$mark',
                              department='$department', year='$year', semester='$semester'
                          WHERE id='$id'";

                mysqli_query($conn, $query);

            } else {
                // INSERT NEW

                $query = "INSERT INTO student_results 
                (roll_number, department, year, semester, subject_name, marks)
                VALUES ('$roll', '$department', '$year', '$semester', '$sub', '$mark')";

                mysqli_query($conn, $query);
            }
        }
    }

    $message = "✅ Result Saved/Updated Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Result Management</title>

    <style>
        body { font-family: Arial; background: #f4f6f9; }

        .container {
            width: 650px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background: #004aad;
            color: white;
            border: none;
            cursor: pointer;
        }

        .add-btn { background: green; }

        .delete-btn {
            background: red;
            width: auto;
            padding: 5px 10px;
        }

        .row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .row input { width: 40%; }

        .msg { text-align: center; color: red; }
    </style>
</head>

<body>

<div class="container">

<h2>Result Management System</h2>

<p class="msg"><?php echo $message; ?></p>

<form method="POST">

    <!-- SEARCH -->
    <label>Roll Number</label>
    <input type="text" name="roll_number" value="<?php echo $student['student_roll_number'] ?? ''; ?>" required>

    <button name="search">Search</button>

    <?php if ($student) { ?>

    <!-- STUDENT INFO -->
    <label>Student Name</label>
    <input type="text" value="<?php echo $student['student_name']; ?>" readonly>

    <label>Department</label>
    <input type="text" name="department" value="<?php echo $student['department']; ?>" readonly>

    <!-- YEAR -->
    <label>Year</label>
    <select name="year">
        <option>1st Year</option>
        <option>2nd Year</option>
        <option>3rd Year</option>
    </select>

    <!-- SEM -->
    <label>Semester</label>
    <select name="semester">
        <option>Sem 1</option>
        <option>Sem 2</option>
        <option>Sem 3</option>
        <option>Sem 4</option>
        <option>Sem 5</option>
        <option>Sem 6</option>
    </select>

    <h3>Subjects</h3>

    <div id="subjects">

        <!-- EXISTING SUBJECTS -->
        <?php if (!empty($results)) {
            foreach ($results as $row) { ?>
                
                <div class="row">
                    <input type="hidden" name="result_id[]" value="<?php echo $row['id']; ?>">
                    <input type="text" name="subject[]" value="<?php echo $row['subject_name']; ?>">
                    <input type="number" name="marks[]" value="<?php echo $row['marks']; ?>">
                    <button type="button" class="delete-btn" onclick="removeRow(this)">X</button>
                </div>

        <?php } } else { ?>

            <!-- EMPTY FIRST ROW -->
            <div class="row">
                <input type="hidden" name="result_id[]">
                <input type="text" name="subject[]" placeholder="Subject">
                <input type="number" name="marks[]" placeholder="Marks">
                <button type="button" class="delete-btn" onclick="removeRow(this)">X</button>
            </div>

        <?php } ?>

    </div>

    <button type="button" class="add-btn" onclick="addRow()">+ Add Subject</button>

    <button name="submit">Save / Update Result</button>

    <?php } ?>

</form>

</div>

<script>
function addRow() {
    let div = document.createElement("div");
    div.classList.add("row");

    div.innerHTML = `
        <input type="hidden" name="result_id[]">
        <input type="text" name="subject[]" placeholder="Subject">
        <input type="number" name="marks[]" placeholder="Marks">
        <button type="button" class="delete-btn" onclick="removeRow(this)">X</button>
    `;

    document.getElementById("subjects").appendChild(div);
}

function removeRow(btn) {
    btn.parentElement.remove();
}
</script>

</body>
</html>