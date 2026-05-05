<?php
include("../server/connection.php");

/* ================= PAGINATION ================= */
$limit = 6; // students per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

/* COUNT TOTAL RECORDS */
$total_result = $conn->query("SELECT COUNT(*) as total FROM student_details");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

/* MAIN QUERY WITH LIMIT */
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

        /* ================= LAYOUT ================= */
        .container {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
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

        /* MAIN */
        .main {
            flex: 1;
            padding: 25px;
        }

        .header-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #111827;
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

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #111827;
            color: white;
            text-align: left;
            padding: 14px;
            font-size: 13px;
        }

        td {
            padding: 14px;
            border-top: 1px solid #eee;
            font-size: 14px;
        }

        tr:hover {
            background: #f3f6ff;
            cursor: pointer;
        }

        /* AVATAR */
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* BUTTON */
        .btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }

        /* MODAL */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
        }

        .panel {
            width: 90%;
            max-width: 900px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            max-height: 90vh;
            overflow-y: auto;
        }

        .hero {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white;
            padding: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .hero img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .content {
            padding: 15px;
        }

        .section {
            background: #f9fafb;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 10px;
            border: 1px solid #eee;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .item {
            font-size: 13px;
        }

        .label {
            font-size: 11px;
            color: #6b7280;
        }

        .value {
            font-weight: bold;
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
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
        }

        .breadcrumb {
            margin-bottom: 20px;
            color: #6b7280;
        }

        .breadcrumb a {
            text-decoration: none;
            color: #2563eb;
        }

        .pagination {
            margin-top: 15px;
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pagination a {
            padding: 6px 10px;
            background: #f3f4f6;
            border-radius: 6px;
            text-decoration: none;
            color: #111827;
            font-size: 13px;
        }

        .pagination a.active {
            background: #2563eb;
            color: white;
        }

        /* RESPONSIVE */
        @media(max-width:768px) {
            .row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- ================= SIDEBAR ================= -->
        <div class="sidebar">
            <h2><i class="fa-solid fa-user-shield"></i> Admin</h2>

            <a href="../admin/dashboard/index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="../admin/department/list.php"><i class="fa-solid fa-building"></i> Departments</a>
            <a href="../admin/courses/list.php"><i class="fa-solid fa-book"></i> Courses</a>
            <a href="../admin/semesters/list.php"><i class="fa-solid fa-calendar"></i> Semesters</a>
            <a href="../admin/bank/list.php"><i class="fa-solid fa-bank"></i> Banks</a>

            <a href="add_students.php"><i class="fa-solid fa-user-plus"></i> Add Student</a>
            <a href="student_list.php"><i class="fa-solid fa-users"></i> Student List</a>

            <a href="../admin/auth/logout.php" style="background:#ef4444;color:white;">Logout</a>
        </div>  

        <!-- ================= MAIN ================= -->
        <div class="main">

            <div class="header-title">Student List</div>

            <div class="breadcrumb"><a href="../admin/dashboard/index.php">Home</a> / Student List</div>

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
                        <tr onclick="openModal(<?= $row['id'] ?>)">

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
                                <button class="btn" onclick="event.stopPropagation(); openModal(<?= $row['id'] ?>)">
                                    View
                                </button>
                            </td>

                        </tr>
                    <?php endwhile; ?>

                </table>

                <!-- ================= PAGINATION ================= -->
                <div class="pagination">

                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>">Prev</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>">Next</a>
                    <?php endif; ?>

                </div>

            </div>
        </div>
    </div>

    <!-- ================= MODAL ================= -->
    <div class="modal" id="modal">
        <div class="panel">

            <div id="modalContent"></div>

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

                    let photo = data.photo
                        ? `uploads/${data.photo}`
                        : `https://via.placeholder.com/80`;

                    document.getElementById("modalContent").innerHTML = `
<div class="hero">
    <img src="${photo}">
    <div>
        <h3>${data.full_name}</h3>
        <small>${data.student_id}</small>
    </div>
</div>

<div class="content">

    <div class="section">
        <div class="section-title">Academic</div>
        <div class="row">
            <div class="item"><div class="label">Department</div><div class="value">${data.department_name}</div></div>
            <div class="item"><div class="label">Course</div><div class="value">${data.course_name}</div></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Contact</div>
        <div class="row">
            <div class="item"><div class="label">Email</div><div class="value">${data.email}</div></div>
            <div class="item"><div class="label">Phone</div><div class="value">${data.phone}</div></div>
        </div>
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