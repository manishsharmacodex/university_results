<?php
include("../../server/connection.php");
include("../../config/auth.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: list.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$page = (int)($_POST['page'] ?? 1);

/* VALIDATION */
if ($id <= 0 || $title === '' || $description === '') {
    $_SESSION['message'] = "Invalid data!";
    header("Location: list.php?page=" . $page);
    exit;
}

$newImage = null;

/* HANDLE IMAGE UPLOAD */
if (!empty($_FILES['banner_image']['name'])) {

    $file = $_FILES['banner_image'];
    $newImage = time() . "_" . $file['name'];
    $tmp_name = $file['tmp_name'];

    $uploadPath = "../uploads/banners/" . $newImage;

    if (!move_uploaded_file($tmp_name, $uploadPath)) {
        $_SESSION['message'] = "Image upload failed!";
        header("Location: list.php?page=" . $page);
        exit;
    }

    /* GET OLD IMAGE */
    $stmt = $conn->prepare("SELECT image FROM banners WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $old = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!empty($old['image'])) {
            $oldPath = "../uploads/banners/" . $old['image'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
    }
}

/* UPDATE QUERY */
if ($newImage) {
    $stmt = $conn->prepare("UPDATE banners SET title=?, description=?, image=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $description, $newImage, $id);
} else {
    $stmt = $conn->prepare("UPDATE banners SET title=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $description, $id);
}

/* EXECUTE */
if ($stmt && $stmt->execute()) {

    $_SESSION['message'] = ($stmt->affected_rows > 0)
        ? "Banner updated successfully!"
        : "No changes made!";

} else {
    $_SESSION['message'] = "Update failed!";
}

$stmt->close();

header("Location: list.php?page=" . $page);
exit;
?>