<?php
include("../server/connection.php");

if (isset($_POST['submit'])) {
    $student_roll_number = $_POST['roll_number'];
    $student_name        = $_POST['student_name'];
    $student_department  = $_POST['student_department'];

    $query = "INSERT INTO university_results.student_details 
              (student_roll_number, student_name, department) 
              VALUES('$student_roll_number','$student_name','$student_department')";

    $data = mysqli_query($conn, $query);
    if ($data) {
        $success_message = "✅ New student has been added successfully!";
    } else {
        $error_message = "❌ Failed to add new student. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student - University Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Roboto', sans-serif; }

        body {
            background-color: #f5f7fa;
            padding: 40px 20px;
            color: #333;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 40px 50px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #004aad;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #555;
        }

        input[type="text"], select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }

        input[type="text"]:focus, select:focus {
            border-color: #004aad;
            outline: none;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background-color: #004aad;
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background-color: #003080;
        }

        .message {
            text-align: center;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 500;
        }

        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }

        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }

        @media (max-width: 600px) {
            .container { padding: 30px 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Student</h1>

        <?php if(isset($success_message)) { ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php } elseif(isset($error_message)) { ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php } ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="roll_number">Roll Number</label>
                <input type="text" id="roll_number" name="roll_number" placeholder="Enter Roll Number" required>
            </div>

            <div class="form-group">
                <label for="student_name">Student Name</label>
                <input type="text" id="student_name" name="student_name" placeholder="Enter Full Name" required>
            </div>

            <div class="form-group">
                <label for="student_department">School Department</label>
                <select id="student_department" name="student_department" required>
                    <option value="">Select Department</option>
                    <option value="BCA">BCA</option>
                    <option value="MCA">MCA</option>
                    <option value="B.TECH">B.TECH</option>
                    <option value="M.TECH">M.TECH</option>
                </select>
            </div>

            <button type="submit" name="submit" class="submit-btn">Add Student</button>
        </form>

        <footer>
            2025 &copy; Sushant University. All Rights Reserved.
        </footer>
    </div>
</body>
</html>