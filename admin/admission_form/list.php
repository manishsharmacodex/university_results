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

    // Old string format
    if (is_string($_SESSION['message'])) {

        $message = $_SESSION['message'];
        $messageType = "success";

    }

    // Array format
    elseif (is_array($_SESSION['message'])) {

        $message = $_SESSION['message']['text'] ?? "";
        $messageType = $_SESSION['message']['type'] ?? "success";

    }

    unset($_SESSION['message']);
}

/* ================= FETCH DATA ================= */
$id = 1;

$stmt = $conn->prepare("
    SELECT *
    FROM admission_form_settings
    WHERE id = ?
");

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$data = $result->fetch_assoc();

if (!$data) {
    die("No admission form settings found.");
}

$activePage = "admission_form";
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admission Form Settings</title>

    <link rel="stylesheet" type="text/css" href="../../css/font.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" type="text/css" href="../css/sidebar.css">

</head>

<body>

    <div class="container">

        <!-- SIDEBAR -->
        <div class="sidebar">

            <h2>

                <i class="fa-solid fa-user-shield"></i>
                Admin

            </h2>

            <a href="../dashboard/index.php" class="<?= $activePage == 'dashboard' ? 'active' : '' ?>">

                <i class="fa-solid fa-gauge"></i>
                Dashboard

            </a>

            <a href="../department/list.php" class="<?= $activePage == 'department' ? 'active' : '' ?>">

                <i class="fa-solid fa-building"></i>
                Department

            </a>

            <a href="../courses/list.php" class="<?= $activePage == 'courses' ? 'active' : '' ?>">

                <i class="fa-solid fa-book"></i>
                Courses

            </a>

            <a href="../semesters/list.php" class="<?= $activePage == 'semester' ? 'active' : '' ?>">

                <i class="fa-solid fa-calendar"></i>
                Semester

            </a>

            <a href="../bank/list.php" class="<?= $activePage == 'bank' ? 'active' : '' ?>">

                <i class="fa-solid fa-bank"></i>
                Bank

            </a>

            <a href="../../src/pages/add_student/add_students.php"
                class="<?= $activePage == 'add_students' ? 'active' : '' ?>" target="_BLANK">

                <i class="fa-solid fa-user-plus"></i>
                Add Student

            </a>

            <a href="../../src/pages/student_list/student_list.php"
                class="<?= $activePage == 'student_list' ? 'active' : '' ?>">

                <i class="fa-solid fa-users"></i>
                Student List

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

                        <i class="fa-solid fa-image"></i>
                        Banner Update

                    </a>

                    <a href="./list.php" class="<?= $activePage == 'admission_form' ? 'active' : '' ?>">

                        <i class="fa-solid fa-file-pen"></i>
                        Admission Form Update

                    </a>

                </div>

            </div>

            <a href="../auth/logout.php" class="logout-btn">

                Logout

            </a>

        </div>

        <!-- MAIN -->
        <div class="main">

            <h2 class="breadcrum-header">

                Admission Form Settings

            </h2>

            <div class="breadcrumb">

                <a href="../dashboard/index.php">Dashboard</a>
                /
                Admission Form Settings

            </div>

            <!-- TOAST MESSAGE -->
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

            <!-- TABLE -->
            <table>

                <tr>

                    <th>ID</th>

                    <th>Form Title</th>

                    <th>Description</th>

                    <th>Button Text</th>

                    <th>Background Color</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

                <tr>

                    <td>

                        <?= (int) $data['id'] ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($data['form_title']) ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($data['form_description']) ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($data['button_text']) ?>

                    </td>

                    <td>

                        <div style="
                            width:40px;
                            height:40px;
                            border-radius:8px;
                            border:1px solid #ccc;
                            background: <?= htmlspecialchars($data['background_color']) ?>;
                            margin:auto;
                        "></div>

                        <div style="
                            margin-top:5px;
                            font-size:12px;
                            text-align:center;
                        ">
                            <?= htmlspecialchars($data['background_color']) ?>
                        </div>

                    </td>

                    <td>

                        <?= htmlspecialchars($data['form_status']) ?>

                    </td>

                    <td class="action">

                        <a href="#" class="edit" onclick="document.getElementById('viewModal').style.display='flex'">

                            View

                        </a>

                    </td>

                </tr>

            </table>

        </div>

    </div>

    <!-- VIEW / UPDATE MODAL -->
    <div class="modal" id="viewModal">

        <div class="modal-box" style="max-width:700px;">

            <h3>

                Update Admission Form

            </h3>

            <form method="POST" action="./update_form.php">

                <label>

                    Form Title

                </label>

                <input type="text" name="form_title" value="<?= htmlspecialchars($data['form_title'] ?? '') ?>"
                    required>

                <label>

                    Form Description

                </label>

                <textarea name="form_description" required
                    style="height:120px; resize:none;"><?= htmlspecialchars($data['form_description'] ?? '') ?></textarea>

                <label>

                    Button Text

                </label>

                <input type="text" name="button_text" value="<?= htmlspecialchars($data['button_text'] ?? '') ?>"
                    required>

                <label>

                    Background Color

                </label>

                <input type="color" name="background_color"
                    value="<?= htmlspecialchars($data['background_color'] ?? '#ffffff') ?>">

                <label>

                    Admission Status

                </label>

                <select name="form_status">

                    <option value="Open" <?= (($data['form_status'] ?? '') === 'Open') ? 'selected' : '' ?>>

                        Open

                    </option>

                    <option value="Closed" <?= (($data['form_status'] ?? '') === 'Closed') ? 'selected' : '' ?>>

                        Closed

                    </option>

                </select>

                <button type="submit" class="save-btn">

                    Update

                </button>

                <button type="button" class="close-btn" onclick="closeModal('viewModal')">

                    Cancel

                </button>

            </form>

        </div>

    </div>

    <!-- TOAST -->
    <div id="toast" class="toast"></div>

    <script>

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

    <!-- JS -->
    <script type="text/javascript" src="../js/dropdownToggle.js"></script>

    <script type="text/javascript" src="../js/toastNotification.js"></script>

</body>

</html>