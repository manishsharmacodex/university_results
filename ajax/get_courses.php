<?php
include("../server/connection.php");

if(isset($_POST['department_id'])){

    $department_id = $_POST['department_id'];

    $query = mysqli_query($conn,
        "SELECT * FROM courses
        WHERE department_id='$department_id'
        ORDER BY course_name ASC"
    );

    echo '<option value="">Select Course</option>';

    while($row = mysqli_fetch_assoc($query)){

        echo '<option value="'.$row['course_name'].'">
                '.$row['course_name'].'
            </option>';
    }
}
?>