<?php
include("../../config/auth.php");
include("../../server/connection.php");

$result = $conn->query("SELECT * FROM departments");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Departments</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", sans-serif;
        }

        body {
            background: #f4f6fb;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* ================= SIDEBAR (UNCHANGED) ================= */
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

        /* ================= MAIN (UNCHANGED) ================= */
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

        /* ================= MODAL (NEW ONLY) ================= */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .modal-box {
            background: white;
            width: 380px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .modal-box h3 {
            margin-bottom: 15px;
        }

        .modal-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .modal-box button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 5px;
        }

        .save-btn {
            background: #2563eb;
            color: white;
        }

        .close-btn {
            background: #ef4444;
            color: white;
        }

    </style>
</head>

<body>

<div class="container">

    <!-- SIDEBAR (UNCHANGED) -->
    <div class="sidebar">
        <h2><i class="fa-solid fa-user-shield"></i> Admin</h2>

        <a href="../dashboard/index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="../department/list.php"><i class="fa-solid fa-building"></i> Departments</a>
        <a href="../courses/list.php"><i class="fa-solid fa-book"></i> Courses</a>
        <a href="../semesters/list.php"><i class="fa-solid fa-calendar"></i> Semesters</a>
        <a href="../bank/list.php"><i class="fa-solid fa-bank"></i> Banks</a>
        <a href="../../student_details/add_students.php"><i class="fa-solid fa-user-plus"></i> Add Student</a>
        <a href="../../student_details/student_list.php"><i class="fa-solid fa-users"></i> Student List</a>

        <a href="../auth/logout.php" style="background:#ef4444; color:white;">
            Logout
        </a>
    </div>

    <!-- MAIN -->
    <div class="main">

        <h2>Departments</h2>

        <div class="breadcrumb">
            <a href="../dashboard/index.php">Home</a> / Departments
        </div>

        <a class="add-btn" href="add.php">
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

                        <!-- EDIT BUTTON OPENS MODAL -->
                        <a href="#"
                           class="edit"
                           onclick="openModal(<?= $row['id'] ?>, '<?= addslashes($row['name']) ?>')">
                            Edit
                        </a>

                        <a class="delete"
                           href="delete.php?id=<?= $row['id'] ?>"
                           onclick="return confirm('Delete this department?')">
                            Delete
                        </a>

                    </td>
                </tr>
            <?php } ?>

        </table>

    </div>
</div>

<!-- ================= POPUP MODAL ================= -->
<div class="modal" id="editModal">
    <div class="modal-box">

        <h3>Edit Department</h3>

        <form method="POST" action="edit.php">

            <input type="hidden" name="id" id="dept_id">

            <input type="text" name="name" id="dept_name" required>

            <button type="submit" class="save-btn">Save Changes</button>
            <button type="button" class="close-btn" onclick="closeModal()">Cancel</button>

        </form>

    </div>
</div>

<!-- ================= JS ================= -->
<script>
function openModal(id, name) {
    document.getElementById('dept_id').value = id;
    document.getElementById('dept_name').value = name;
    document.getElementById('editModal').style.display = "flex";
}

function closeModal() {
    document.getElementById('editModal').style.display = "none";
}
</script>

</body>
</html>