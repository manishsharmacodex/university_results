<?php

include("../../server/connection.php");

if (isset($_POST['upload_banner'])) {

    $file = $_FILES['banner_image'];

    $file_name = time() . "_" . $file['name'];
    $tmp_name = $file['tmp_name'];

    $upload_path = "../uploads/banners/" . $file_name;

    if (move_uploaded_file($tmp_name, $upload_path)) {

        $query = "INSERT INTO banners (image) VALUES ('$file_name')";
        mysqli_query($conn, $query);

        echo "<script>alert('Banner Uploaded Successfully');</script>";
    } else {
        echo "<script>alert('Upload Failed');</script>";
    }
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    mysqli_query($conn, "DELETE FROM banners WHERE id=$id");

    echo "<script>location.href='';</script>";
}

?>

<html>

<head>
    <title>Banner Add</title>
</head>

<body>
    <form method="POST" enctype="multipart/form-data">
        <h3>Upload Banner</h3>
        <input type="file" name="banner_image" required>
        <button type="submit" name="upload_banner">Upload</button>
    </form>


    <a href="?delete=<?= $row['id'] ?>">Delete</a>


    <?php
    $banners = mysqli_query($conn, "SELECT * FROM banners ORDER BY id DESC");
    ?>

    <h3>All Banners</h3>

    <?php while ($row = mysqli_fetch_assoc($banners)) { ?>
        <img src="../uploads/banners/<?= $row['image'] ?>" width="200">
    <?php } ?>
</body>

</html>