<?php
include("../server/connection.php");

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
    <title>Student List</title>
    <link rel="stylesheet" type="text/css" href="../css/font.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f4f6fb;
            font-family: Arial;
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
        }

        .sidebar a:hover {
            background: #2563eb;
            color: white;
        }

        .main {
            flex: 1;
            padding: 25px;
        }

        .header-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .breadcrumb {
            margin-bottom: 15px;
            color: #6b7280;
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

        /* ================= MODAL UPGRADED (ERP STYLE) ================= */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            display: none;
            justify-content: center;
            align-items: center;
            padding: 20px;
            backdrop-filter: blur(6px);
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
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
        }

        /* sticky header */
        .hero {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 15px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .hero img {
            width: 75px;
            height: 75px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid white;
        }

        .hero h3 {
            font-size: 18px;
        }

        .hero small {
            opacity: 0.85;
        }

        /* scroll body */
        .content {
            padding: 20px;
            overflow-y: auto;
        }

        /* GRID CARD STYLE */
        .section {
            background: linear-gradient(180deg, #ffffff, #f8fafc);
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 14px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.04);
        }

        .section-title {
            font-size: 11px;
            letter-spacing: 1px;
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
            background: #f9fafb;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #eee;
        }

        .label {
            font-size: 11px;
            color: #6b7280;
        }

        .value {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-top: 3px;
        }

        .footer {
            padding: 12px;
            text-align: right;
            border-top: 1px solid #eee;
            background: #fff;
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

        @media(max-width:768px) {
            .row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- SIDEBAR (UNCHANGED) -->
        <div class="sidebar">
            <h2>Admin</h2>

            <a href="../admin/dashboard/index.php"><i class="fa fa-gauge"></i> Dashboard</a>
            <a href="../admin/department/list.php"><i class="fa fa-building"></i> Departments</a>
            <a href="../admin/courses/list.php"><i class="fa fa-book"></i> Courses</a>
            <a href="../admin/semesters/list.php"><i class="fa fa-calendar"></i> Semesters</a>
            <a href="../admin/bank/list.php"><i class="fa fa-bank"></i> Banks</a>

            <a href="add_students.php"><i class="fa fa-user-plus"></i> Add Student</a>
            <a href="student_list.php"><i class="fa fa-users"></i> Student List</a>

            <a href="../admin/auth/logout.php" style="background:#ef4444;">Logout</a>
        </div>

        <!-- MAIN -->
        <div class="main">

            <div class="header-title">Student List</div>
            <div class="breadcrumb">Home / Students</div>

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

                <!-- PAGINATION -->
                <div class="pagination">
                    <?php if ($page > 1) { ?>
                        <a href="?page=<?= $page - 1 ?>">Prev</a>
                    <?php } ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <a class="<?= $i == $page ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                    <?php } ?>

                    <?php if ($page < $total_pages) { ?>
                        <a href="?page=<?= $page + 1 ?>">Next</a>
                    <?php } ?>
                </div>

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
                <button class="close-btn" onclick="closeModal()">Close</button>
            </div>

        </div>
    </div>

    <script>

        function openModal(id) {

            fetch("get_student.php?id=" + id)
                .then(res => res.json())
                .then(data => {

                    document.getElementById("mPhoto").src = data.photo ? `uploads/${data.photo}` : `https://via.placeholder.com/80`;
                    document.getElementById("mName").innerText = data.full_name;
                    document.getElementById("mId").innerText = data.student_id;

                    document.getElementById("modalContent").innerHTML = `

<div class="section">
<div class="section-title">Academic Information</div>
<div class="row">
<div class="item"><div class="label">Department</div><div class="value">${data.department_name}</div></div>
<div class="item"><div class="label">Course</div><div class="value">${data.course_name}</div></div>
<div class="item"><div class="label">Semester</div><div class="value">${data.semester}</div></div>
<div class="item"><div class="label">Section</div><div class="value">${data.section}</div></div>
</div>
</div>

<div class="section">
<div class="section-title">Personal Details</div>
<div class="row">
<div class="item"><div class="label">Father</div><div class="value">${data.father_name}</div></div>
<div class="item"><div class="label">Mother</div><div class="value">${data.mother_name}</div></div>
<div class="item"><div class="label">DOB</div><div class="value">${data.dob}</div></div>
<div class="item"><div class="label">Gender</div><div class="value">${data.gender}</div></div>
</div>
</div>

<div class="section">
<div class="section-title">Contact</div>
<div class="row">
<div class="item"><div class="label">Email</div><div class="value">${data.email}</div></div>
<div class="item"><div class="label">Phone</div><div class="value">${data.phone}</div></div>
<div class="item"><div class="label">Address</div><div class="value">${data.address}</div></div>
</div>
</div>

<div class="section">
<div class="section-title">Other</div>
<div class="row">
<div class="item"><div class="label">Bank</div><div class="value">${data.bank_name}</div></div>
<div class="item"><div class="label">Aadhaar</div><div class="value">${data.aadhaar_number}</div></div>
<div class="item"><div class="label">University</div><div class="value">${data.university}</div></div>
<div class="item"><div class="label">Admission Date</div><div class="value">${data.admission_date}</div></div>
</div>
</div>

`;

                    document.getElementById("modal").style.display = "flex";

                });
        }

        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }

    </script>

</body>

</html>