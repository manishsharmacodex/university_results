<?php
include("../../../server/connection.php");

$activePage = "student_list"; // change per page

/* ================= PAGINATION ================= */
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_result = $conn->query("SELECT COUNT(*) as total FROM student_details");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

/* MAIN QUERY */
$sql = "
SELECT s.*,
       c.course_name,
       d.name AS department_name,
       bm.bank_name
FROM student_details s
LEFT JOIN courses c ON s.course = c.id
LEFT JOIN departments d ON s.department = d.id
LEFT JOIN banks b ON s.bank_name = b.id
LEFT JOIN bank_master bm ON b.bank_master_id = bm.id
ORDER BY s.id DESC
LIMIT $limit OFFSET $offset
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student List</title>
    <link rel="stylesheet" type="text/css" href="../../../css/font.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(120deg, #eef2ff, #f8fafc);
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: #111827;
            color: white;
            padding: 20px;

            height: 100vh;
            /* full screen height */
            position: sticky;
            /* stays fixed while page scrolls */
            top: 0;

            overflow-y: auto;
            /* enables vertical scroll */
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

        .sidebar a.logout-btn {
            background: #ef4444;
            color: white;
        }

        .main {
            flex: 1;
            padding: 25px;
        }

        .breadcrumb {
            margin-bottom: 20px;
            color: #6b7280;
        }

        .breadcrumb a {
            text-decoration: none;
            color: #2563eb;
        }

        .breadcrum-header {
            width: 100%;
            display: block;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #ffffff !important;
            padding: 18px 25px;
            border-radius: 10px;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            margin-bottom: 15px;
        }

        .card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #111827;
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 14px;
            border-top: 1px solid #eee;
        }

        tr:hover {
            background: #f3f6ff;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-edit {
            background: #f59e0b;
        }

        .btn-save {
            background: #22c55e;
        }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .panel {
            width: 95%;
            max-width: 1050px;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            max-height: 92vh;
            display: flex;
            flex-direction: column;
        }

        .hero {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .hero img {
            width: 75px;
            height: 75px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid white;
        }

        .content {
            padding: 20px;
            overflow-y: auto;
        }

        .section {
            background: #f8fafc;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 14px;
            border: 1px solid #e5e7eb;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .item {
            background: white;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #eee;
        }

        .input {
            width: 100%;
            border: 1px solid #ddd;
            padding: 6px;
            border-radius: 6px;
        }

        .footer {
            padding: 12px;
            text-align: right;
            border-top: 1px solid #eee;
        }

        .close-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 9px 14px;
            border-radius: 8px;
            cursor: pointer;
        }

        .pagination {
            margin-top: 15px;
            padding: 10px;
        }

        .pagination a {
            padding: 6px 10px;
            background: #f3f4f6;
            margin-right: 5px;
            border-radius: 6px;
            text-decoration: none;
            color: #111827;
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
            <a href="../../../admin/dashboard/index.php" class="<?= $activePage == 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i>Dashboard
            </a>

            <a href="../../../admin/department/list.php" class="<?= $activePage == 'department' ? 'active' : '' ?>">
                <i class="fa-solid fa-building"></i>Department
            </a>

            <a href="../../../admin/courses/list.php" class="<?= $activePage == 'courses' ? 'active' : '' ?>">
                <i class="fa-solid fa-book"></i>Courses
            </a>

            <a href="../../../admin/semesters/list.php" class="<?= $activePage == 'semester' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar"></i>Semester
            </a>

            <a href="../../../admin/bank/list.php" class="<?= $activePage == 'bank' ? 'active' : '' ?>">
                <i class="fa-solid fa-bank"></i>Bank
            </a>

            <a href="./add_students.php" class="<?= $activePage == 'add_students' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-plus"></i>Add Student
            </a>

            <a href="./student_list.php" class="<?= $activePage == 'student_list' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i>Student List
            </a>

            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>

        <!-- MAIN -->
        <div class="main">
            <h2 class="header-title breadcrum-header">Student Lists</h2>
            <div class="breadcrumb">
                <a href="../../../admin/dashboard/index.php">Home</a> / Student Lists
            </div>

            <div class="card">

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Course</th>
                        <th>Action</th>
                    </tr>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['student_id'] ?></td>

                            <td>
                                <?php if ($row['photo']) { ?>
                                    <img src="uploads/<?= $row['photo'] ?>" class="avatar">
                                <?php } ?>
                            </td>

                            <td><?= $row['full_name'] ?></td>
                            <td><?= $row['phone'] ?></td>
                            <td><?= $row['course_name'] ?></td>

                            <td>
                                <button class="btn" onclick="openModal(<?= $row['id'] ?>)">View</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                </table>

            </div>

        </div>
    </div>

    <!-- MODAL -->
    <div class="modal" id="modal">
        <div class="panel">

            <div class="hero">
                <img id="mPhoto">
                <div>
                    <h3 id="mName"></h3>
                    <small id="mId"></small>
                </div>
            </div>

            <div class="content" id="modalContent"></div>

            <div class="footer">
                <button class="btn btn-edit" onclick="enableEdit()">Edit</button>
                <button class="btn btn-save" onclick="saveData()">Save</button>
                <button class="close-btn" onclick="closeModal()">Close</button>
            </div>

        </div>
    </div>

    <script>

        let currentId = 0;

        function openModal(id) {

            currentId = id;

            fetch("get_student.php?id=" + id)
                .then(res => res.json())
                .then(data => {

                    document.getElementById("mPhoto").src = data.photo ? `uploads/${data.photo}` : `https://via.placeholder.com/80`;
                    document.getElementById("mName").innerText = data.full_name;
                    document.getElementById("mId").innerText = data.student_id;

                    document.getElementById("modalContent").innerHTML = `

<div class="section">
<div class="section-title">Academic</div>
<div class="row">
<div class="item">Department<br><input class="input" name="department_name" value="${data.department_name}" disabled></div>
<div class="item">Course<br><input class="input" name="course_name" value="${data.course_name}" disabled></div>
<div class="item">Semester<br><input class="input" name="semester" value="${data.semester}" disabled></div>
<div class="item">Section<br><input class="input" name="section" value="${data.section}" disabled></div>
</div>
</div>

<div class="section">
<div class="section-title">Personal</div>
<div class="row">
<div class="item">Father<br><input class="input" value="${data.father_name}" disabled></div>
<div class="item">Mother<br><input class="input" value="${data.mother_name}" disabled></div>
<div class="item">DOB<br><input class="input" value="${data.dob}" disabled></div>
<div class="item">Gender<br><input class="input" value="${data.gender}" disabled></div>
</div>
</div>

<div class="section">
<div class="section-title">Contact</div>
<div class="row">
<div class="item">Email<br><input class="input" name="email" value="${data.email}" disabled></div>
<div class="item">Phone<br><input class="input" name="phone" value="${data.phone}" disabled></div>
<div class="item">Address<br><input class="input" value="${data.address}" disabled></div>
</div>
</div>

<div class="section">
<div class="section-title">Other</div>
<div class="row">
<div class="item">Bank<br><input class="input" value="${data.bank_name}" disabled></div>
<div class="item">Aadhaar<br><input class="input" value="${data.aadhaar_number}" disabled></div>
<div class="item">University<br><input class="input" value="${data.university}" disabled></div>
<div class="item">Admission<br><input class="input" value="${data.admission_date}" disabled></div>
</div>
</div>

`;

                    document.getElementById("modal").style.display = "flex";

                });
        }

        function enableEdit() {
            document.querySelectorAll(".input").forEach(el => el.disabled = false);
        }

        function saveData() {

            let data = {
                id: currentId,
                email: document.querySelector('[name="email"]').value,
                phone: document.querySelector('[name="phone"]').value
            };

            fetch("update_student.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            })
                .then(res => res.text())
                .then(res => {
                    alert("Updated");
                    closeModal();
                    location.reload();
                });

        }

        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }

    </script>

</body>

</html>