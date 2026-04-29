<?php
include("../server/connection.php");

$student = null;
$results = [];
$message = "";

if (isset($_POST['submit'])) {

    $roll = $_POST['roll_number'];

    // FETCH STUDENT
    $stmt = $conn->prepare("SELECT * FROM student_details WHERE student_roll_number = ?");
    $stmt->bind_param("s", $roll);
    $stmt->execute();
    $studentData = $stmt->get_result();

    if ($studentData->num_rows > 0) {

        $student = $studentData->fetch_assoc();

        // FETCH RESULTS (DYNAMIC SUBJECTS)
        $stmt2 = $conn->prepare("SELECT * FROM student_results WHERE roll_number = ?");
        $stmt2->bind_param("s", $roll);
        $stmt2->execute();
        $resultData = $stmt2->get_result();

        while ($row = $resultData->fetch_assoc()) {
            $results[] = $row;
        }

    } else {
        $message = "❌ No student found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>University Marksheet</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .search-box {
            text-align: center;
            margin: 30px 0;
        }

        input {
            padding: 10px;
            width: 60%;
        }

        button {
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }

        .marksheet {
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #3498db;
            color: white;
        }

        .total {
            font-weight: bold;
        }

        .status {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }

        .pass { color: green; }
        .fail { color: red; }
    </style>
</head>

<body>

<div class="container">

    <div class="search-box">
        <form method="POST">
            <input type="text" name="roll_number" placeholder="Enter Roll Number" required>
            <button name="submit">Search</button>
        </form>
        <p style="color:red;"><?php echo $message; ?></p>
    </div>

<?php if ($student && count($results) > 0) {

    $total = 0;
    $max = 0;

    foreach ($results as $row) {
        $total += $row['marks'];
        $max += 100;
    }

    $percentage = ($total / $max) * 100;
    $status = ($percentage >= 40) ? "PASS" : "FAIL";
?>

    <div class="marksheet" id="resultArea">

        <h2 style="text-align:center;">University Marksheet</h2>

        <p><strong>Name:</strong> <?php echo $student['student_name']; ?></p>
        <p><strong>Roll No:</strong> <?php echo $student['student_roll_number']; ?></p>
        <p><strong>Department:</strong> <?php echo $student['department']; ?></p>

        <table>
            <tr>
                <th>Subject</th>
                <th>Marks</th>
            </tr>

            <?php foreach ($results as $row) { ?>
                <tr>
                    <td><?php echo $row['subject_name']; ?></td>
                    <td><?php echo $row['marks']; ?></td>
                </tr>
            <?php } ?>

            <tr class="total">
                <td>Total</td>
                <td><?php echo $total; ?></td>
            </tr>

            <tr class="total">
                <td>Percentage</td>
                <td><?php echo number_format($percentage,2); ?>%</td>
            </tr>
        </table>

        <div class="status <?php echo strtolower($status); ?>">
            Result: <?php echo $status; ?>
        </div>

        <button onclick="downloadPDF()">Download PDF</button>

    </div>

<?php } ?>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<script>
function downloadPDF() {
    const element = document.getElementById("resultArea");
    html2pdf().from(element).save("Marksheet.pdf");
}
</script>

</body>
</html>