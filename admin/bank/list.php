<?php
include("../../config/auth.php");
include("../../server/connection.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

/* ================= ADD BANK ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bank_master_id'])) {
    $bank_master_id = intval($_POST['bank_master_id']);

    // Check if bank already exists
    $check = $conn->query("SELECT * FROM banks WHERE bank_master_id='$bank_master_id'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO banks (bank_master_id) VALUES ('$bank_master_id')");
        $message = "Bank added successfully!";
    } else {
        $message = "Bank already exists!";
    }
}

$activePage = "bank"; // change per page

/* ================= PAGINATION ================= */
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total banks
$total_result = $conn->query("SELECT COUNT(*) AS total FROM banks");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Fetch banks for current page
$result = $conn->query("
    SELECT banks.id, bank_master.bank_name, banks.bank_master_id
    FROM banks
    JOIN bank_master ON banks.bank_master_id = bank_master.id
    ORDER BY banks.id ASC
    LIMIT $limit OFFSET $offset
");

// Fetch all bank_master for dropdown
$bank_master_result = $conn->query("SELECT * FROM bank_master ORDER BY bank_name ASC");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banks</title>
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

        .message {
            margin-bottom: 15px;
            color: green;
            font-weight: bold;
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

            <a href="./list.php" class="<?= $activePage == 'bank' ? 'active' : '' ?>">
                <i class="fa-solid fa-bank"></i>Bank
            </a>

            <a href="../../src/pages/student_details/add_students.php"
                class="<?= $activePage == 'add_students' ? 'active' : '' ?>" target="_BLANK">
                <i class="fa-solid fa-user-plus"></i>Add Student
            </a>

            <a href="../../src/pages/student_details/student_list.php"
                class="<?= $activePage == 'student_list' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i>Student List
            </a>

            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="main">
            <h2 class="breadcrum-header">Banks</h2>
            <div class="breadcrumb"><a href="../dashboard/index.php">Dashboard</a> / Banks</div>

            <?php if ($message != ''): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        showToast("<?= $message ?>", "success");
                    });
                </script>
            <?php endif; ?>

            <a class="add-btn" href="#" onclick="document.getElementById('addModal').style.display='flex'">
                <i class="fa fa-plus"></i> Add New Bank
            </a>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Bank Name</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['bank_name'] ?></td>
                        <td class="action">
                            <a href="#" class="edit" onclick="
                        document.getElementById('edit_bank_id').value='<?= $row['id'] ?>';
                        document.getElementById('edit_bank_master_id').value='<?= $row['bank_master_id'] ?>';
                        document.getElementById('editModal').style.display='flex';
                    ">Edit</a>
                            <a href="#" class="delete" onclick="openDeleteModal(<?= $row['id'] ?>)">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?><a href="?page=<?= $page - 1 ?>">Prev</a><?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?><a href="?page=<?= $page + 1 ?>">Next</a><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ADD BANK MODAL -->
    <div class="modal" id="addModal">
        <div class="modal-box">
            <h3>Add Bank</h3>
            <form method="POST">
                <select name="bank_master_id" required>
                    <option value="">Select Bank</option>
                    <?php while ($bank = $bank_master_result->fetch_assoc()): ?>
                        <option value="<?= $bank['id'] ?>"><?= $bank['bank_name'] ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="save-btn">Add Bank</button>
                <button type="button" class="close-btn"
                    onclick="document.getElementById('addModal').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>

    <!-- EDIT BANK MODAL -->
    <div class="modal" id="editModal">
        <div class="modal-box">
            <h3>Edit Bank</h3>
            <form method="POST" action="edit.php">
                <input type="hidden" name="id" id="edit_bank_id">
                <select name="bank_master_id" id="edit_bank_master_id" required>
                    <option value="">Select Bank</option>
                    <?php
                    $bank_master_edit = $conn->query("SELECT * FROM bank_master ORDER BY bank_name ASC");
                    while ($bank = $bank_master_edit->fetch_assoc()): ?>
                        <option value="<?= $bank['id'] ?>"><?= $bank['bank_name'] ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="save-btn">Update</button>
                <button type="button" class="close-btn"
                    onclick="document.getElementById('editModal').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>


    <div id="deleteModal" class="modal">
        <div class="modal-box" style="text-align:center;">
            <h3>Delete Department?</h3>

            <form method="POST" action="delete.php">
                <input type="hidden" name="id" id="delete_id">

                <button type="submit" class="save-btn" style="background:#ef4444;">
                    Yes, Delete
                </button>

                <button type="button" class="close-btn"
                    onclick="document.getElementById('deleteModal').style.display='none'">
                    Cancel
                </button>
            </form>
        </div>
    </div>



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
    </script>

</body>

</html>