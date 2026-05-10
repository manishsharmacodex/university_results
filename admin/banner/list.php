<?php
include("../../server/connection.php");
include("../../config/auth.php");

// session history check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
// session history check


/* ================= ADD BANNER ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    /* VALIDATION */
    if ($title === '' || $description === '') {
        $_SESSION['message'] = "Invalid data!";
        header("Location: list.php");
        exit;
    }

    if (empty($_FILES['banner_image']['name'])) {
        $_SESSION['message'] = "Image is required!";
        header("Location: list.php");
        exit;
    }

    $file = $_FILES['banner_image'];
    $file_name = time() . "_" . basename($file['name']);
    $tmp_name = $file['tmp_name'];

    $upload_path = "../uploads/banners/" . $file_name;

    /* UPLOAD CHECK */
    if (!move_uploaded_file($tmp_name, $upload_path)) {
        $_SESSION['message'] = "Upload failed!";
        header("Location: list.php");
        exit;
    }

    /* INSERT */
    $stmt = $conn->prepare("
        INSERT INTO banners (image, title, description)
        VALUES (?, ?, ?)
    ");

    if (!$stmt) {
        $_SESSION['message'] = "Database error!";
        header("Location: list.php");
        exit;
    }

    $stmt->bind_param("sss", $file_name, $title, $description);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Banner added successfully!";
    } else {
        $_SESSION['message'] = "Insert failed!";
    }

    $stmt->close();

    header("Location: list.php");
    exit;
}

$activePage = "banner";

/* ================= PAGINATION ================= */
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page'])
    ? (int) $_GET['page']
    : 1;

$offset = ($page - 1) * $limit;

/* TOTAL RECORDS */
$total_result = $conn->query("SELECT COUNT(*) AS total FROM banners");
$total_row = $total_result->fetch_assoc();
$total_records = (int) $total_row['total'];

$total_pages = ceil($total_records / $limit);

/* FETCH BANNERS */
$result = $conn->query("
    SELECT *
    FROM banners
    ORDER BY id DESC
    LIMIT $limit OFFSET $offset
");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banners</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
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
            width: 260px;
            background: linear-gradient(180deg, #111827, #0f172a);
            color: white;
            padding: 20px;

            height: 100vh;
            position: sticky;
            top: 0;

            overflow-y: auto;
            overflow-x: hidden;

            border-right: 1px solid rgba(255, 255, 255, 0.05);

            scrollbar-width: thin;
            scrollbar-color: #2563eb #111827;
            /* enables vertical scroll */
        }

        /* Custom Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #111827;
            border-radius: 20px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #3b82f6, #2563eb);
            border-radius: 20px;
            transition: 0.3s;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #60a5fa, #2563eb);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .sidebar a {
            display: flex;
            gap: 12px;
            align-items: center;
            color: #cbd5e1;
            text-decoration: none;
            padding: 13px 14px;
            margin: 7px 0;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 15px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sidebar a::before {
            content: "";
            position: absolute;
            left: -100%;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.08);
            transition: 0.4s;
        }

        .sidebar a:hover::before {
            left: 0;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            transform: translateX(6px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        }

        .sidebar a.logout-btn {
            background: #ef4444;
            color: white;
        }

        .dropdown-container {
            display: none;
            padding-left: 14px;
            margin-top: 4px;
            border-left: 2px solid rgba(255, 255, 255, 0.08);
        }

        .dropdown-container a {
            font-size: 14px;
            margin: 5px 0;
            background: rgba(255, 255, 255, 0.04);
        }

        .breadcrum-header {
            width: 100%;
            display: block;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #ffffff !important;
            padding: 18px 25px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            margin-bottom: 15px;
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

        .save-btn.delete-btn {
            background: #ef4444;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px); */
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
            text-align: center;
        }

        @keyframes modalShow {
            to {
                transform: translateY(0) scale(1);
            }
        }

        .modal-box h3 {
            text-align: center;
            color: #111827;
            margin-bottom: 15px;
        }

        .modal-box select,
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

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #111827;
            color: white;
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 14px;
            opacity: 0;
            transform: translateY(20px);
            transition: 0.4s ease;
            z-index: 9999;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast.success {
            background: #16a34a;
        }

        .toast.error {
            background: #ef4444;
        }

        /* DROPDOWN MENU */
        .dropdown-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dropdown-container {
            display: none;
            padding-left: 12px;
        }

        .dropdown-container a {
            font-size: 14px;
            margin: 4px 0;
            background: rgba(255, 255, 255, 0.05);
        }

        .dropdown.active .dropdown-container {
            display: block;
        }

        .dropdown-icon {
            margin-left: auto;
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

            <a href="../department/list.php" class="<?= $activePage == 'department' ? 'active' : '' ?>">
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

            <a href="../../src/pages/add_student/add_students.php"
                class="<?= $activePage == 'add_students' ? 'active' : '' ?>" target="_BLANK">
                <i class="fa-solid fa-user-plus"></i>Add Student
            </a>

            <a href="../../src/pages/student_list/student_list.php"
                class="<?= $activePage == 'student_list' ? 'active' : '' ?>">
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

                    <a href="../banner/list.php" class="<?= $activePage == 'banner' ? 'active' : '' ?>">
                        <i class="fa-solid fa-image"></i>Banner Update
                    </a>

                    <a href="../admission_form/list.php" class="<?= $activePage == 'admission_form' ? 'active' : '' ?>">
                        <i class="fa-solid fa-file-pen"></i>Admission Form Update
                    </a>

                </div>

            </div>

            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>

        <!-- Main Dashboard -->
        <div class="main">
            <h2 class="breadcrum-header">Banner</h2>
            <div class="breadcrumb"><a href="../dashboard/index.php">Dashboard</a> / Banner</div>

            <?php if ($message != ''): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        showToast("<?= $message ?>", "success");
                    });
                </script>
            <?php endif; ?>

            <a class="add-btn" href="#" onclick="document.getElementById('addModal').style.display='flex'">
                <i class="fa fa-plus"></i> Add New Banner
            </a>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>

                        <td>
                            <img src="../uploads/banners/<?= $row['image'] ?>" width="80">
                        </td>

                        <td><?= htmlspecialchars($row['title']) ?></td>

                        <td><?= htmlspecialchars($row['description']) ?></td>

                        <td class="action">

                            <a href="#" class="edit" onclick="
                document.getElementById('edit_id').value='<?= $row['id'] ?>';
                document.getElementById('edit_title').value='<?= htmlspecialchars($row['title']) ?>';
                document.getElementById('edit_description').value='<?= htmlspecialchars($row['description']) ?>';
                document.getElementById('editModal').style.display='flex';
           ">
                                Edit
                            </a>

                            <a href="#" class="delete" onclick="openDeleteModal(<?= $row['id'] ?>)">
                                Delete
                            </a>

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

    <!-- ADD MODAL -->
    <div class="modal" id="addModal">
        <div class="modal-box">
            <h3>Add Banner</h3>

            <form method="POST" enctype="multipart/form-data">

                <input type="text" name="title" id="edit_title" placeholder="Enter Title" required>

                <input type="text" name="description" id="edit_description" placeholder="Enter Description" required>

                <input type="file" name="banner_image" id="edit_image" accept="image/*" required>

                <button type="submit" class="save-btn">Add Banner</button>

                <button type="button" class="close-btn"
                    onclick="document.getElementById('addModal').style.display='none'">
                    Cancel
                </button>

            </form>
        </div>
    </div>

    <!-- EDIT BANNER MODAL -->
    <div class="modal" id="editModal">
        <div class="modal-box">
            <h3>Edit Banner</h3>

            <form method="POST" action="edit.php" enctype="multipart/form-data">

                <!-- hidden ID -->
                <input type="hidden" name="id" id="edit_id">

                <!-- title -->
                <input type="text" name="title" id="edit_title" placeholder="Enter Title">

                <!-- description -->
                <input type="text" name="description" id="edit_description" placeholder="Enter Description">

                <!-- image (optional update) -->
                <input type="file" name="banner_image" id="edit_image" accept="image/*">

                <button type="submit" class="save-btn">Update</button>

                <button type="button" class="close-btn"
                    onclick="document.getElementById('editModal').style.display='none'">
                    Cancel
                </button>

            </form>
        </div>
    </div>

    <!-- DELETE BANK MODAL -->
    <div id="deleteModal" class="modal">
        <div class="modal-box">
            <h3>Delete Banner ?</h3>

            <form method="POST" action="delete.php">
                <input type="hidden" name="id" id="delete_id">

                <button type="submit" class="save-btn delete-btn">
                    Yes, Delete
                </button>

                <button type="button" class="close-btn"
                    onclick="document.getElementById('deleteModal').style.display='none'">
                    Cancel
                </button>
            </form>
        </div>
    </div>


    <!-- Toast Notification Message -->
    <div id="toast" class="toast"></div>


    <script>
        function openDeleteModal(id) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById("deleteModal").style.display = "none";
        }

        function showToast(message, type = "success") {
            const toast = document.getElementById("toast");

            toast.className = "toast show " + type;
            toast.innerText = message;

            setTimeout(() => {
                toast.classList.remove("show");
            }, 3000);
        }


        // Dropdown Toggle
        const dropdownBtn = document.querySelector(".dropdown-btn");

        dropdownBtn.addEventListener("click", function () {
            this.parentElement.classList.toggle("active");
        });
    </script>

</body>

</html>