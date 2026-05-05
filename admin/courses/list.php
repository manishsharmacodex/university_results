<?php
include("../../config/auth.php");
include("../../server/connection.php");

/* ================= ADD COURSE ================= */
$message = ''; // initialize message

if (isset($_POST['add_course'])) {
    $course_name = strtoupper($conn->real_escape_string($_POST['course_name']));
    $department_id = $_POST['department_id'];

    // Check if course already exists for the same department
    $check = $conn->query("SELECT * FROM courses WHERE course_name='$course_name' AND department_id='$department_id'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO courses (course_name, department_id) VALUES ('$course_name', '$department_id')");
        $message = "Course added successfully!";
    } else {
        $message = "Course already exists in this department!";
    }
}

$result = $conn->query("
    SELECT courses.id, courses.course_name, departments.name AS department_name
    FROM courses
    JOIN departments ON courses.department_id = departments.id
");

$departments = $conn->query("SELECT * FROM departments");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Courses</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ====== Copy your department page CSS ====== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f4f6fb;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: #111827;
            color: white;
            padding: 20px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .sidebar a {
            display: flex;
            gap: 10px;
            align-items: center;
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px;
            margin: 6px 0;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #2563eb;
            color: white;
            transform: translateX(5px);
        }

        .main {
            flex: 1;
            padding: 30px;
        }

        .main h2 {
            margin-bottom: 10px;
            color: #111827;
        }

        .breadcrumb {
            margin-bottom: 20px;
            color: #6b7280;
        }

        .breadcrumb a {
            text-decoration: none;
            color: #2563eb;
        }

        .add-btn {
            display: inline-block;
            margin-bottom: 15px;
            padding: 10px 15px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border-radius: 8px;
            text-decoration: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        th {
            background: #111827;
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f3f4f6;
        }

        .action a {
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            margin-right: 5px;
        }

        .edit {
            background: #f59e0b;
            color: white;
        }

        .delete {
            background: #ef4444;
            color: white;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .modal-box {
            width: 360px;
            background: #ffffff;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
            transform: translateY(-20px) scale(0.95);
            animation: modalShow 0.25s ease forwards;
        }

        @keyframes modalShow {
            to {
                transform: translateY(0) scale(1);
            }
        }

        .modal-box h3 {
            margin-bottom: 15px;
            text-align: center;
            color: #111827;
        }

        .modal-box input,
        .modal-box select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        .save-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 8px;
            background: #ef4444;
            color: white;
            cursor: pointer;
        }


        .message {
            margin-bottom: 15px;
            color: green;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="sidebar">
            <h2><i class="fa-solid fa-user-shield"></i> Admin</h2>

            <a href="../dashboard/index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="../department/list.php"><i class="fa-solid fa-building"></i> Departments</a>
            <a href="../courses/list.php"><i class="fa-solid fa-book"></i> Courses</a>
            <a href="../semesters/list.php"><i class="fa-solid fa-calendar"></i> Semesters</a>
            <a href="../bank/list.php"><i class="fa-solid fa-bank"></i> Banks</a>
            <a href="../../student_details/add_students.php"><i class="fa-solid fa-user-plus"></i> Add Student</a>
            <a href="../../student_details/student_list.php"><i class="fa-solid fa-users"></i> Student List</a>
            <a href="../auth/logout.php" style="background:#ef4444; color:white;">Logout</a>
        </div>

        <div class="main">
            <h2>Courses</h2>

            <div class="breadcrumb">
                <a href="../dashboard/index.php">Home</a> / Courses
            </div>


            <?php if ($message != ''): ?>
                <div class="message"><?= $message ?></div>
            <?php endif; ?>

            <a class="add-btn" href="#" onclick="document.getElementById('addModal').style.display='flex'">
                <i class="fa fa-plus"></i> Add New Course
            </a>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['course_name'] ?></td>
                        <td><?= $row['department_name'] ?></td>
                        <td class="action">
                            <a href="#" class="edit" onclick="
    document.getElementById('course_id').value='<?= $row['id'] ?>';
    document.getElementById('course_name').value='<?= addslashes($row['course_name']) ?>';
    document.getElementById('department_select').value='<?= $row['department_id'] ?>';
    document.getElementById('editModal').style.display='flex';
">Edit</a>

                            <a class="delete" href="delete.php?id=<?= $row['id'] ?>"
                                onclick="return confirm('Delete this course?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>

        </div>
    </div>

    <!-- ADD COURSE MODAL -->
    <div class="modal" id="addModal">
        <div class="modal-box">
            <h3>Add Course</h3>
            <form method="POST">
                <input type="text" name="course_name" placeholder="Course Name" required>
                <select name="department_id" required>
                    <option value="">Select Department</option>
                    <?php while ($dept = $departments->fetch_assoc()) { ?>
                        <option value="<?= $dept['id'] ?>"><?= $dept['name'] ?></option>
                    <?php } ?>
                </select>

                <button type="submit" name="add_course" class="save-btn">Add Course</button>
                <button type="button" class="close-btn"
                    onclick="document.getElementById('addModal').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>

    <!-- EDIT COURSE MODAL -->
    <div class="modal" id="editModal">
        <div class="modal-box">
            <h3>Edit Course</h3>
            <form method="POST" action="edit.php">
                <input type="hidden" name="id" id="course_id">
                <input type="text" name="course_name" id="course_name" required>
                <select name="department_id" id="department_select" required>
                    <option value="">Select Department</option>
                    <?php
                    $departments->data_seek(0); // reset pointer
                    while ($dept = $departments->fetch_assoc()) { ?>
                        <option value="<?= $dept['id'] ?>"><?= $dept['name'] ?></option>
                    <?php } ?>
                </select>

                <button type="submit" class="save-btn">Update</button>
                <button type="button" class="close-btn"
                    onclick="document.getElementById('editModal').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // Uppercase input
        document.querySelectorAll("input[type='text'], textarea").forEach(field => {
            field.addEventListener("input", function () {
                this.value = this.value.toUpperCase();
            });
        });
    </script>

</body>

</html>