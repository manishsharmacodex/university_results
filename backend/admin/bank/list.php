<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ================= DB CONNECTION ================= */
include(__DIR__ . "/../../server/connection.php");

include("../../config/auth.php");

/* ================= FLASH MESSAGE ================= */
$message = "";
$messageType = "success";

if (isset($_SESSION['message'])) {

    // If old message format is string
    if (is_string($_SESSION['message'])) {

        $message = $_SESSION['message'];
        $messageType = "success";

    }

    // If new message format is array
    elseif (is_array($_SESSION['message'])) {

        $message = $_SESSION['message']['text'] ?? "";
        $messageType = $_SESSION['message']['type'] ?? "success";

    }

    unset($_SESSION['message']);
}

/* ================= ADD BANK ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bank_master_id'])) {

    $bank_master_id = (int) $_POST['bank_master_id'];

    if ($bank_master_id <= 0) {

        $_SESSION['message'] = [
            'text' => 'Invalid Bank ID!',
            'type' => 'error'
        ];

        header("Location: list.php");
        exit;
    }

    /* CHECK EXISTING */
    $checkStmt = $conn->prepare("
        SELECT id 
        FROM banks 
        WHERE bank_master_id = ?
    ");

    $checkStmt->bind_param("i", $bank_master_id);
    $checkStmt->execute();

    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {

        $_SESSION['message'] = [
            'text' => 'Bank already exists!',
            'type' => 'error'
        ];

        $checkStmt->close();

        header("Location: list.php");
        exit;
    }

    $checkStmt->close();

    /* INSERT BANK */
    $insertStmt = $conn->prepare("
        INSERT INTO banks (bank_master_id)
        VALUES (?)
    ");

    if (!$insertStmt) {

        $_SESSION['message'] = [
            'text' => 'Database error!',
            'type' => 'error'
        ];

        header("Location: list.php");
        exit;
    }

    $insertStmt->bind_param("i", $bank_master_id);

    if ($insertStmt->execute()) {

        $_SESSION['message'] = [
            'text' => 'Bank added successfully!',
            'type' => 'success'
        ];

    } else {

        $_SESSION['message'] = [
            'text' => 'Insert failed!',
            'type' => 'error'
        ];
    }

    $insertStmt->close();

    header("Location: list.php");
    exit;
}

$activePage = "bank";

/* ================= PAGINATION ================= */
$limit = 6;

$page = isset($_GET['page']) && is_numeric($_GET['page'])
    ? (int) $_GET['page']
    : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

$limit = (int) $limit;
$offset = (int) $offset;

/* ================= TOTAL RECORDS ================= */
$totalResult = $conn->query("
    SELECT COUNT(*) AS total 
    FROM banks
");

$totalRow = $totalResult->fetch_assoc();

$totalRecords = (int) $totalRow['total'];

$totalPages = ceil($totalRecords / $limit);

/* ================= FETCH BANKS ================= */
$result = $conn->query("
    SELECT 
        banks.id,
        banks.bank_master_id,
        bank_master.bank_name
    FROM banks
    INNER JOIN bank_master
        ON banks.bank_master_id = bank_master.id
    ORDER BY banks.id ASC
    LIMIT $limit OFFSET $offset
");

/* ================= DROPDOWN BANKS ================= */
$bankMasterResult = $conn->query("
    SELECT *
    FROM bank_master
    ORDER BY bank_name ASC
");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banks</title>
    <link rel="stylesheet" type="text/css" href="../../css/font.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../css/sidebar.css">
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
            <h2 class="breadcrum-header">Banks</h2>
            <div class="breadcrumb"><a href="../dashboard/index.php">Dashboard</a> / Banks</div>

            <?php if ($message != ""): ?>

                <script>
                    document.addEventListener("DOMContentLoaded", function () {

                        showToast(
                            "<?= htmlspecialchars($message, ENT_QUOTES) ?>",
                            "<?= htmlspecialchars($messageType) ?>"
                        );

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
                <?php if ($result->num_rows > 0): ?>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td><?= (int) $row['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($row['bank_name']) ?>
                            </td>

                            <td class="action">

                                <a href="#" class="edit" onclick="
                                    document.getElementById('edit_bank_id').value='<?= (int) $row['id'] ?>';
                                    document.getElementById('edit_bank_master_id').value='<?= (int) $row['bank_master_id'] ?>';
                                    document.getElementById('editModal').style.display='flex';
                                ">
                                    Edit
                                </a>

                                <a href="#" class="delete" onclick="openDeleteModal(<?= (int) $row['id'] ?>)">
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="3" style="text-align:center;">
                            No banks found
                        </td>
                    </tr>

                <?php endif; ?>
            </table>

            <!-- PAGINATION -->
            <div class="pagination">

                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>">Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                    <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">

                        <?= $i ?>

                    </a>

                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>">Next</a>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- ADD MODAL -->
    <div class="modal" id="addModal">

        <div class="modal-box">

            <h3>Add Bank</h3>

            <form method="POST">

                <select name="bank_master_id" required>

                    <option value="">Select Bank</option>

                    <?php while ($bank = $bankMasterResult->fetch_assoc()): ?>

                        <option value="<?= (int) $bank['id'] ?>">

                            <?= htmlspecialchars($bank['bank_name']) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

                <button type="submit" class="save-btn">
                    Add Bank
                </button>

                <button type="button" class="close-btn" onclick="closeModal('addModal')">

                    Cancel

                </button>

            </form>

        </div>

    </div>

    <!-- EDIT MODAL -->
    <div class="modal" id="editModal">

        <div class="modal-box">

            <h3>Edit Bank</h3>

            <form method="POST" action="edit.php">

                <input type="hidden" name="id" id="edit_bank_id">

                <select name="bank_master_id" id="edit_bank_master_id" required>

                    <option value="">Select Bank</option>

                    <?php
                    $bankMasterEdit = $conn->query("
                    SELECT *
                    FROM bank_master
                    ORDER BY bank_name ASC
                ");

                    while ($bank = $bankMasterEdit->fetch_assoc()):
                        ?>

                        <option value="<?= (int) $bank['id'] ?>">

                            <?= htmlspecialchars($bank['bank_name']) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

                <button type="submit" class="save-btn">
                    Update
                </button>

                <button type="button" class="close-btn" onclick="closeModal('editModal')">

                    Cancel

                </button>

            </form>

        </div>

    </div>

    <!-- DELETE MODAL -->
    <div class="modal" id="deleteModal">

        <div class="modal-box">

            <h3>Delete Bank?</h3>

            <form method="POST" action="delete.php">

                <input type="hidden" name="id" id="delete_id" required>

                <button type="submit" class="save-btn delete-btn">

                    Yes, Delete

                </button>

                <button type="button" class="close-btn" onclick="closeModal('deleteModal')">

                    Cancel

                </button>

            </form>

        </div>

    </div>

    <!-- TOAST -->
    <div id="toast" class="toast"></div>

    <script>
        function openDeleteModal(id) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        window.onclick = function (e) {
            const modals = document.querySelectorAll(".modal");
            modals.forEach(modal => {

                if (e.target === modal) {
                    modal.style.display = "none";
                }
            });
        }
    </script>

    <script type="text/javascript" src="../js/dropdownToggle.js"></script>
    <script type="text/javascript" src="../js/toastNotification.js"></script>

</body>

</html>