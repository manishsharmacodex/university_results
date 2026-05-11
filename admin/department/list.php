<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ================= DB CONNECTION ================= */
include("../../server/connection.php");
include("../../config/auth.php");

/* ================= FLASH MESSAGE ================= */
$message = "";
$messageType = "success";

if (isset($_SESSION['message'])) {

    // Old message format
    if (is_string($_SESSION['message'])) {

        $message = $_SESSION['message'];
        $messageType = "success";

    }

    // New message format
    elseif (is_array($_SESSION['message'])) {

        $message = $_SESSION['message']['text'] ?? "";
        $messageType = $_SESSION['message']['type'] ?? "success";

    }

    unset($_SESSION['message']);
}

/* ================= ADD DEPARTMENT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_department'])) {

    $name = strtoupper(trim($_POST['name']));

    /* VALIDATION */
    if ($name === '') {

        $_SESSION['message'] = [
            'text' => 'Invalid department name!',
            'type' => 'error'
        ];

        header("Location: list.php");
        exit;
    }

    /* CHECK EXISTING */
    $checkStmt = $conn->prepare("
        SELECT id
        FROM departments
        WHERE name = ?
    ");

    $checkStmt->bind_param("s", $name);

    $checkStmt->execute();

    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {

        $_SESSION['message'] = [
            'text' => 'Department already exists!',
            'type' => 'error'
        ];

        $checkStmt->close();

        header("Location: list.php");
        exit;
    }

    $checkStmt->close();

    /* INSERT DEPARTMENT */
    $insertStmt = $conn->prepare("
        INSERT INTO departments (name)
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

    $insertStmt->bind_param("s", $name);

    if ($insertStmt->execute()) {

        $_SESSION['message'] = [
            'text' => 'Department added successfully!',
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

$activePage = "department";

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
$total_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM departments
");

$total_row = $total_result->fetch_assoc();

$total_records = (int) $total_row['total'];

$total_pages = ceil($total_records / $limit);

/* ================= FETCH DEPARTMENTS ================= */
$result = $conn->query("
    SELECT *
    FROM departments
    ORDER BY id ASC
    LIMIT $limit OFFSET $offset
");
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Departments</title>

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

            <a href="../../src/pages/add_student/add_students.php"
                class="<?= $activePage == 'add_students' ? 'active' : '' ?>" target="_BLANK">

                <i class="fa-solid fa-user-plus"></i>Add Student

            </a>

            <a href="../../src/pages/student_list/student_list.php"
                class="<?= $activePage == 'student_list' ? 'active' : '' ?>">

                <i class="fa-solid fa-users"></i>Student List

            </a>

            <!-- DROPDOWN -->
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

            <a href="../auth/logout.php" class="logout-btn">

                Logout

            </a>

        </div>

        <!-- MAIN -->
        <div class="main">

            <h2 class="breadcrum-header">Departments</h2>

            <div class="breadcrumb">

                <a href="../dashboard/index.php">Dashboard</a> / Departments

            </div>

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

            <!-- ADD BUTTON -->
            <a class="add-btn" href="#" onclick="document.getElementById('addModal').style.display='flex'">

                <i class="fa fa-plus"></i> Add New Department

            </a>

            <table>

                <tr>

                    <th>ID</th>

                    <th>Department Name</th>

                    <th>Action</th>

                </tr>

                <?php if ($result->num_rows > 0): ?>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td><?= (int) $row['id'] ?></td>

                            <td>

                                <?= htmlspecialchars($row['name']) ?>

                            </td>

                            <td class="action">

                                <a href="#" class="edit" onclick="
                                        document.getElementById('dept_id').value='<?= (int) $row['id'] ?>';
                                        document.getElementById('dept_name').value='<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>';
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

                            No departments found

                        </td>

                    </tr>

                <?php endif; ?>

            </table>

            <!-- PAGINATION -->
            <div class="pagination">

                <?php if ($page > 1): ?>

                    <a href="?page=<?= $page - 1 ?>">

                        Prev

                    </a>

                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>

                    <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">

                        <?= $i ?>

                    </a>

                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>

                    <a href="?page=<?= $page + 1 ?>">

                        Next

                    </a>

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

                <button type="submit" name="add_department" class="save-btn">

                    Add Department

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

            <h3>Edit Department</h3>

            <form method="POST" action="edit.php">

                <input type="hidden" name="id" id="dept_id">

                <input type="text" name="name" id="dept_name" required>

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

            <h3>Delete Department?</h3>

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

    <script type="text/javascript" src="../js/upperCase.js"></script>

</body>

</html>