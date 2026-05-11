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
    <link rel="stylesheet" type="text/css" href="../../../admin/css/sidebar.css">
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

            <a href="../add_student/add_students.php" class="<?= $activePage == 'add_students' ? 'active' : '' ?>"
                target="_BLANK">
                <i class="fa-solid fa-user-plus"></i>Add Student
            </a>

            <a href="./student_list.php" class="<?= $activePage == 'student_list' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i>Student List
            </a>

            <!-- SHOP MENU -->
            <div class="dropdown">

                <a href="javascript:void(0);" class="dropdown-btn">
                    <i class="fa-solid fa-shop"></i>
                    University Manage
                    <i class="fa-solid fa-caret-down dropdown-icon"></i>
                </a>

                <div class="dropdown-container">

                    <a href="../../../admin/banner/list.php" class="<?= $activePage == 'banner' ? 'active' : '' ?>">
                        <i class="fa-solid fa-image"></i>Banner Update
                    </a>

                    <a href="../../../admin/admission_form/list.php" class="<?= $activePage == 'admission_form' ? 'active' : '' ?>">
                        <i class="fa-solid fa-file-pen"></i>Admission Form Update
                    </a>

                </div>
            </div>

            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>

        <!-- MAIN -->
        <div class="main">
            <h2 class="header-title breadcrum-header">Student Lists</h2>
            <div class="breadcrumb">
                <a href="../../../admin/dashboard/index.php">Dashboard</a> / Student Lists
            </div>

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
                                <img src="../../../admin/uploads/student_profile/<?= $row['photo'] ?>" class="avatar">
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

            <!-- Pagination -->
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

                    document.getElementById("mPhoto").src = data.photo_url
                        ? data.photo_url
                        : "https://via.placeholder.com/80";
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
                    alert("Student Details Updated Successfully");
                    closeModal();
                    location.reload();
                });

        }

        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }

    </script>

    <script type="text/javascript" src="../../../admin/js/dropdownToggle.js"></script>

</body>

</html>