<?php
include("../server/connection.php");

/* =========================
   DELETE STUDENT
========================= */
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM student_details WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: student_list.php");
    exit;
}

/* =========================
   UPDATE STUDENT (WITH PHOTO)
========================= */
if (isset($_POST['update'])) {

    $id = $_POST['id'];

    $old = $conn->prepare("SELECT photo FROM student_details WHERE id=?");
    $old->bind_param("i", $id);
    $old->execute();
    $oldPhoto = $old->get_result()->fetch_assoc()['photo'];

    $photoName = $oldPhoto;

    if (!empty($_FILES['photo']['name'])) {

        $targetDir = "uploads/";
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $targetDir . $fileName);

        $photoName = $fileName;
    }

    $stmt = $conn->prepare("UPDATE student_details SET 
        full_name=?,
        father_name=?,
        email=?,
        phone=?,
        course=?,
        semester=?,
        photo=?
        WHERE id=?");

    $stmt->bind_param(
        "sssssssi",
        $_POST['full_name'],
        $_POST['father_name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['course'],
        $_POST['semester'],
        $photoName,
        $id
    );

    $stmt->execute();

    header("Location: student_list.php");
    exit;
}

/* =========================
   SEARCH
========================= */
$search = $_GET['search'] ?? '';

if (!empty($search)) {

    $stmt = $conn->prepare("SELECT * FROM student_details WHERE student_id LIKE ? ORDER BY id DESC");
    $like = "%$search%";
    $stmt->bind_param("s", $like);

} else {

    $stmt = $conn->prepare("SELECT * FROM student_details ORDER BY id DESC");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Student List</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: Poppins;
            margin: 0;
            background: #f1f5f9;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .header {
            background: #2563eb;
            color: #fff;
            padding: 18px;
            border-radius: 12px;
            text-align: center;
        }

        .card {
            background: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .search-box input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
        }

        .search-box button {
            padding: 12px 18px;
            border: none;
            background: #2563eb;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8fafc;
            padding: 12px;
            font-size: 14px;
            text-align: center;
        }

        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        tr:hover {
            background: #f9fafb;
        }

        .btn {
            padding: 6px 10px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .edit {
            background: #3b82f6;
            color: #fff;
        }

        .delete {
            background: #ef4444;
            color: #fff;
        }

        .photo {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            width: 600px;
            padding: 20px;
            border-radius: 12px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 22px;
            cursor: pointer;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .field {
            display: flex;
            flex-direction: column;
            font-size: 13px;
        }

        input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .update-btn {
            width: 100%;
            margin-top: 15px;
            padding: 12px;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .photo-box {
            text-align: center;
            margin-top: 15px;
        }

        .photo-box img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #2563eb;
        }
    </style>

</head>

<body>

<div class="container">

    <div class="header">
        <h2>🎓 Student ERP Dashboard</h2>
    </div>

    <div class="card">

        <form method="GET" class="search-box">
            <input type="text" name="search" placeholder="Search Student ID" value="<?= $search ?>">
            <button>Search</button>
        </form>

        <table>

            <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Roll No</th>
                <th>Name</th>
                <th>Dept</th>
                <th>Course</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { ?>

                <tr>

                    <td><?= $row['id'] ?></td>

                    <td>
                        <?php if ($row['photo']) { ?>
                            <img class="photo" src="uploads/<?= $row['photo'] ?>">
                        <?php } ?>
                    </td>

                    <td><?= $row['student_id'] ?></td>
                    <td><?= $row['full_name'] ?></td>
                    <td><?= $row['department'] ?></td>
                    <td><?= $row['course'] ?></td>
                    <td><?= $row['phone'] ?></td>

                    <td>

                        <button class="btn edit" onclick='openModal(
"<?= $row['id'] ?>",
"<?= htmlspecialchars($row['full_name']) ?>",
"<?= htmlspecialchars($row['father_name']) ?>",
"<?= htmlspecialchars($row['email']) ?>",
"<?= htmlspecialchars($row['phone']) ?>",
"<?= htmlspecialchars($row['course']) ?>",
"<?= htmlspecialchars($row['semester']) ?>",
"<?= $row['photo'] ?>"
)'>Edit</button>

                        <a class="btn delete" href="?delete=<?= $row['id'] ?>"
                           onclick="return confirm('Delete this student?')">Delete</a>

                    </td>

                </tr>

            <?php } ?>

        </table>

    </div>
</div>

<div class="modal" id="editModal">

    <div class="modal-content">

        <span class="close" onclick="closeModal()">&times;</span>

        <h3>Edit Student</h3>

        <form method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id" id="id">

            <div class="grid">

                <div class="field">
                    <label>Name</label>
                    <input type="text" name="full_name" id="full_name">
                </div>

                <div class="field">
                    <label>Father</label>
                    <input type="text" name="father_name" id="father_name">
                </div>

                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" id="email">
                </div>

                <div class="field">
                    <label>Phone</label>
                    <input type="text" name="phone" id="phone">
                </div>

                <div class="field">
                    <label>Course</label>
                    <input type="text" name="course" id="course">
                </div>

                <div class="field">
                    <label>Semester</label>
                    <input type="text" name="semester" id="semester">
                </div>

            </div>

            <div class="photo-box">
                <img id="photoPreview">
                <input type="file" name="photo">
            </div>

            <button class="update-btn" type="submit" name="update">Update</button>

        </form>

    </div>
</div>

<script>

function openModal(id, name, father, email, phone, course, semester, photo) {

    document.getElementById('editModal').style.display = 'flex';

    document.getElementById('id').value = id;
    document.getElementById('full_name').value = name;
    document.getElementById('father_name').value = father;
    document.getElementById('email').value = email;
    document.getElementById('phone').value = phone;
    document.getElementById('course').value = course;
    document.getElementById('semester').value = semester;

    document.getElementById('photoPreview').src = 'uploads/' + photo;
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

/* ✅ ADDED ONLY THIS (AUTO SEARCH ON TYPE + CLEAR INPUT) */
document.querySelector('input[name="search"]').addEventListener('input', function () {
    this.form.submit();
});

</script>

</body>
</html>