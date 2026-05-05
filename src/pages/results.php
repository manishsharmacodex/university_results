<?php
include("../../server/connection.php");

$student = null;
$results = [];
$message = "";

$roll = $_POST['roll_number'] ?? '';
$year = $_POST['year'] ?? '';
$semester = $_POST['semester'] ?? '';

if (isset($_POST['submit'])) {

    $stmt = $conn->prepare("SELECT * FROM student_details WHERE student_roll_number = ?");
    $stmt->bind_param("s", $roll);
    $stmt->execute();
    $studentData = $stmt->get_result();

    if ($studentData->num_rows > 0) {

        $student = $studentData->fetch_assoc();

        $stmt2 = $conn->prepare("
            SELECT * FROM student_results 
            WHERE roll_number = ? 
            AND year = ? 
            AND semester = ?
        ");

        $stmt2->bind_param("sss", $roll, $year, $semester);
        $stmt2->execute();
        $resultData = $stmt2->get_result();

        while ($row = $resultData->fetch_assoc()) {
            $results[] = $row;
        }

    } else {
        $message = "❌ No student found!";
    }
}

function gradePoint($marks)
{
    if ($marks >= 90)
        return 10;
    if ($marks >= 80)
        return 9;
    if ($marks >= 70)
        return 8;
    if ($marks >= 60)
        return 7;
    if ($marks >= 50)
        return 6;
    if ($marks >= 40)
        return 5;
    return 0;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>University Result 2026</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">

    <style>
        body {
            background: linear-gradient(120deg, #eef2f3, #d9e4f5);
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            max-width: 950px;
            margin: 40px auto;
        }

        .card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #2980b9;
        }

        .table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .table th {
            background: #3498db;
            color: white;
        }

        .result-box {
            margin-top: 20px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .pass {
            color: green;
        }

        .fail {
            color: red;
        }

        .debarred {
            color: orange;
        }

        .hide {
            display: none;
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="card">

            <h2>University Result 2026</h2>

            <form method="POST">

                <input type="text" name="roll_number" placeholder="Enter Roll Number" required>

                <!-- YEAR -->
                <select name="year" id="year" onchange="updateSemester()" required>
                    <option value="">Select Year</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                </select>

                <!-- SEMESTER (HIDDEN INITIALLY) -->
                <select name="semester" id="semester" class="hide" required>
                    <option value="">Select Semester</option>
                </select>

                <button name="submit">Search Result</button>

            </form>

            <p style="color:red;text-align:center;"><?php echo $message; ?></p>

        </div>

        <?php if ($student && count($results) > 0) { ?>

            <div class="card" style="margin-top:20px;" id="resultArea">

                <h2>Marksheet</h2>

                <p><b>Name:</b> <?php echo $student['student_name']; ?></p>
                <p><b>Roll:</b> <?php echo $student['student_roll_number']; ?></p>
                <p><b>Department:</b> <?php echo $student['department']; ?></p>
                <p><b>Year:</b> <?php echo $year; ?></p>
                <p><b>Semester:</b> <?php echo $semester; ?></p>

                <table class="table">

                    <tr>
                        <th>Subject</th>
                        <th>Marks</th>
                        <th>Status</th>
                        <th>Grade</th>
                    </tr>

                    <?php
                    $total = 0;
                    $gradeTotal = 0;
                    $count = 0;

                    $hasFail = false;
                    $hasDebar = false;

                    foreach ($results as $row) {

                        $marks = $row['marks'];
                        $status = $row['exam_status'] ?? 'Present';

                        if ($status == "Debarred") {
                            $st = "DEBARRED";
                            $gp = 0;
                            $hasDebar = true;
                        } else if ($marks < 40) {
                            $st = "FAIL";
                            $gp = 0;
                            $hasFail = true;
                        } else {
                            $st = "PASS";
                            $gp = gradePoint($marks);
                        }

                        $total += $marks;
                        $gradeTotal += $gp;
                        $count++;
                        ?>

                        <tr>
                            <td><?php echo $row['subject_name']; ?></td>
                            <td><?php echo $marks; ?></td>
                            <td><?php echo $st; ?></td>
                            <td><?php echo $gp; ?></td>
                        </tr>

                    <?php } ?>

                </table>

                <?php
                $percentage = ($count > 0) ? ($total / ($count * 100)) * 100 : 0;
                $cgpa = ($count > 0) ? $gradeTotal / $count : 0;

                if ($hasDebar) {
                    $final = "DEBARRED";
                    $class = "debarred";
                } else if ($hasFail) {
                    $final = "FAIL";
                    $class = "fail";
                } else {
                    $final = "PASS";
                    $class = "pass";
                }
                ?>

                <div class="result-box <?php echo $class; ?>">
                    Result: <?php echo $final; ?><br>
                    Percentage: <?php echo number_format($percentage, 2); ?>%<br>
                    CGPA: <?php echo number_format($cgpa, 2); ?>
                </div>

            </div>

        <?php } ?>

    </div>

    <script>

        // SEMESTER CONTROL (HIDE UNTIL YEAR SELECTED)
        function updateSemester() {

            let year = document.getElementById("year").value;
            let sem = document.getElementById("semester");

            sem.innerHTML = '<option value="">Select Semester</option>';

            if (year === "") {
                sem.classList.add("hide");
                return;
            }

            sem.classList.remove("hide");

            let options = [];

            if (year === "1st Year") options = ["Sem 1", "Sem 2"];
            else if (year === "2nd Year") options = ["Sem 3", "Sem 4"];
            else if (year === "3rd Year") options = ["Sem 5", "Sem 6"];

            options.forEach(s => {
                let opt = document.createElement("option");
                opt.value = s;
                opt.text = s;
                sem.appendChild(opt);
            });

        }

    </script>

</body>

</html>