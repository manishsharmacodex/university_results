<?php
include("../../config/auth.php");
include("../../server/connection.php");

/* ================= ADD DEPARTMENT ================= */
$message = ''; // Initialize message

if (isset($_POST['add_department'])) {
    $name = strtoupper($conn->real_escape_string($_POST['name']));

    // Check if department already exists
    $check = $conn->query("SELECT * FROM departments WHERE name='$name'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO departments (name) VALUES ('$name')");
        $message = "Department added successfully!";
    } else {
        $message = "Department already exists!";
    }
}

$activePage = "department"; // change per page

/* ================= PAGINATION ================= */
$limit = 6; // number of records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total departments
$total_result = $conn->query("SELECT COUNT(*) as total FROM departments");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Fetch departments for current page
$result = $conn->query("SELECT * FROM departments ORDER BY id ASC LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Departments</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
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

        .sidebar a:hover,
        .sidebar a.active {
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
            background: #fff;
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

        .modal-box input {
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

        /* Pagination */
        .pagination {
            margin-top: 20px;
        }

        .pagination a {
            padding: 6px 12px;
            margin-right: 4px;
            border-radius: 4px;
            text-decoration: none;
            color: #111827;
            background: #f3f4f6;
        }

        .pagination a.active {
            background: #2563eb;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2><i class="fa-solid fa-user-shield"></i> Admin</h2>
            <a href="../dashboard/index.php" class="<?= $activePage == 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i>Dashboard
            </a>

            <a href="./list.php" class="<?= $activePage == 'department' ? 'active' : '' ?>">
                <i class="fa-solid fa-building"></i>Department
            </a>

            <a href="../courses/list.php" class="<?= $activePage == 'courses' ? 'active' : '' ?>">
                <i class="fa-solid fa-book"></i>Courses
            </a>

            <a href="../semesters/list.php" class="<?= $activePage == 'semester' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar"></i>Semester
            </a>

            <a href="../bank/list.php" class="<?= $activePage == 'bank' ? 'active' : '' ?>">
                <i class="fa-solid fa-bank"></i>Bank
            </a>

            <a href="../../student_details/add_students.php"
                class="<?= $activePage == 'add_students' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-plus"></i>AddStudent
            </a>

            <a href="../../student_details/student_list.php"
                class="<?= $activePage == 'student_list' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i>Student List
            </a>

            <a href="../auth/logout.php" style="background:#ef4444; color:white;">Logout</a>
        </div>

        <div class="main">

            <h2>Departments</h2>

            <div class="breadcrumb">
                <a href="../dashboard/index.php">Home</a> / Departments
            </div>

            <?php if ($message != ''): ?>
                <div class="message"><?= $message ?></div>
            <?php endif; ?>

            <!-- Add Department Button -->
            <a class="add-btn" href="#" onclick="document.getElementById('addModal').style.display='flex'">
                <i class="fa fa-plus"></i> Add New Department
            </a>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Department Name</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['name'] ?></td>
                        <td class="action">
                            <a href="#" class="edit" onclick="
                                document.getElementById('dept_id').value='<?= $row['id'] ?>';
                                document.getElementById('dept_name').value='<?= addslashes($row['name']) ?>';
                                document.getElementById('editModal').style.display='flex';
                            ">Edit</a>

                            <a class="delete" href="delete.php?id=<?= $row['id'] ?>"
                                onclick="return confirm('Delete this department?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>

            <!-- Pagination Links -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>">Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>">Next</a>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- ADD MODAL -->
    <div class="modal" id="addModal">
        <div class="modal-box">
            <h3>Add Department</h3>
            <form method="POST">
                <input type="text" name="name" placeholder="Department Name" required>
                <button type="submit" name="add_department" class="save-btn">Add Department</button>
                <button type="button" class="close-btn"
                    onclick="document.getElementById('addModal').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal" id="editModal">
        <div class="modal-box">
            <h3>Edit Department</h3>
            <form method="POST" action="edit.php">
                <input type="hidden" name="id" id="dept_id">
                <input type="text" name="name" id="dept_name" required>
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