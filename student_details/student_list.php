<?php
include("../server/connection.php");

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
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Student ERP</title>
<link rel="stylesheet" type="text/css" href="../css/font.css">

<style>
body{
    margin:0;
    padding: 0;
    box-sizing: border-box;
    background:#f4f6fb;
}

/* HEADER */
.header{
    background:#111827;
    color:#fff;
    padding:16px 25px;
    font-size:18px;
    font-weight:600;
}

/* TABLE WRAPPER */
.wrapper{
    padding:20px;
}

.card{
    background:#fff;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    overflow:hidden;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}

th{
    text-align:left;
    padding:14px;
    font-size:12px;
    color:#6b7280;
    background:#f9fafb;
    text-transform:uppercase;
}

td{
    padding:14px;
    border-top:1px solid #eee;
    font-size:14px;
}

tr:hover{
    background:#f3f6ff;
    cursor:pointer;
}

/* AVATAR */
.avatar{
    width:42px;
    height:42px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #e5e7eb;
}

/* BUTTON */
.btn{
    background:#2563eb;
    color:#fff;
    border:none;
    padding:6px 12px;
    border-radius:6px;
    font-size:12px;
    cursor:pointer;
}

/* ================= MODAL ================= */
.modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.55);
    display:none;
    justify-content:center;
    align-items:center;
    padding:20px;
}

/* MAIN PANEL */
.panel{
    width:100%;
    max-width:950px;
    background:#fff;
    border-radius:14px;
    overflow:hidden;
    display:flex;
    flex-direction:column;
    max-height:90vh;
    overflow-y: scroll;
}

/* HERO HEADER */
.hero{
    background:linear-gradient(135deg,#1e3a8a,#2563eb);
    color:#fff;
    padding:22px;
    display:flex;
    align-items:center;
    gap:15px;
}

.hero img{
    width:75px;
    height:75px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #fff;
}

.hero h2{
    margin:0;
    font-size:18px;
}

.hero small{
    opacity:0.8;
}

/* CONTENT */
.content{
    padding:18px;
    overflow-y:auto;
}

/* SECTION CARD */
.section{
    background:#f9fafb;
    border:1px solid #e5e7eb;
    border-radius:10px;
    padding:12px 14px;
    margin-bottom:12px;
}

.section-title{
    font-size:12px;
    font-weight:600;
    color:#2563eb;
    margin-bottom:10px;
    text-transform:uppercase;
}

/* ROW */
.row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:8px 20px;
}

.item{
    font-size:13px;
}

.label{
    font-size:11px;
    color:#6b7280;
}

.value{
    font-weight:600;
    color:#111827;
}

/* FOOTER */
.footer{
    padding:12px;
    border-top:1px solid #eee;
    text-align:right;
}

.close-btn{
    background:#ef4444;
    color:#fff;
    border:none;
    padding:7px 14px;
    border-radius:6px;
    cursor:pointer;
}

/* MOBILE */
@media(max-width:768px){
    .row{
        grid-template-columns:1fr;
    }
}
</style>
</head>

<body>

<div class="header">🎓 Student Management ERP</div>

<div class="wrapper">
<div class="card">

<table>
<tr>
    <th>ID</th>
    <th>Photo</th>
    <th>Name</th>
    <th>Phone</th>
    <th>Course</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr onclick="openModal(<?= $row['id'] ?>)">

    <td><?= $row['student_id'] ?></td>

    <td>
        <?php if($row['photo']) { ?>
            <img src="uploads/<?= $row['photo'] ?>" class="avatar">
        <?php } ?>
    </td>

    <td><?= $row['full_name'] ?></td>
    <td><?= $row['phone'] ?></td>
    <td><?= $row['course_name'] ?></td>

    <td>
        <button class="btn" onclick="event.stopPropagation(); openModal(<?= $row['id'] ?>)">
            View
        </button>
    </td>

</tr>
<?php endwhile; ?>

</table>

</div>
</div>

<!-- MODAL -->
<div class="modal" id="modal">
    <div class="panel">

        <div id="modalContent"></div>

        <div class="footer">
            <button class="close-btn" onclick="closeModal()">Close</button>
        </div>

    </div>
</div>

<script>
function openModal(id){

fetch("get_student.php?id=" + id)
.then(res => res.json())
.then(data => {

let photo = data.photo
? `uploads/${data.photo}`
: `https://via.placeholder.com/80`;

let html = `

<div class="hero">
    <img src="${photo}">
    <div>
        <h2>${data.full_name}</h2>
        <small>${data.student_id}</small>
    </div>
</div>

<div class="content">

    <div class="section">
        <div class="section-title">Academic Information</div>
        <div class="row">
            <div class="item"><div class="label">Department</div><div class="value">${data.department_name}</div></div>
            <div class="item"><div class="label">Course</div><div class="value">${data.course_name}</div></div>
            <div class="item"><div class="label">Semester</div><div class="value">${data.semester}</div></div>
            <div class="item"><div class="label">Section</div><div class="value">${data.section}</div></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Personal Information</div>
        <div class="row">
            <div class="item"><div class="label">Father</div><div class="value">${data.father_name}</div></div>
            <div class="item"><div class="label">Mother</div><div class="value">${data.mother_name}</div></div>
            <div class="item"><div class="label">DOB</div><div class="value">${data.dob}</div></div>
            <div class="item"><div class="label">Gender</div><div class="value">${data.gender}</div></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Contact Information</div>
        <div class="row">
            <div class="item"><div class="label">Email</div><div class="value">${data.email}</div></div>
            <div class="item"><div class="label">Phone</div><div class="value">${data.phone}</div></div>
            <div class="item"><div class="label">Address</div><div class="value">${data.address}</div></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Other Details</div>
        <div class="row">
            <div class="item"><div class="label">Bank</div><div class="value">${data.bank_name}</div></div>
            <div class="item"><div class="label">Aadhaar</div><div class="value">${data.aadhaar_number}</div></div>
            <div class="item"><div class="label">University</div><div class="value">${data.university}</div></div>
            <div class="item"><div class="label">Admission</div><div class="value">${data.admission_date}</div></div>
        </div>
    </div>

</div>

`;

document.getElementById("modalContent").innerHTML = html;
document.getElementById("modal").style.display = "flex";

});
}

function closeModal(){
document.getElementById("modal").style.display = "none";
}

/* outside click close */
document.getElementById("modal").addEventListener("click", function(e){
    if(e.target === this){
        closeModal();
    }
});

/* ESC close */
document.addEventListener("keydown", function(e){
    if(e.key === "Escape"){
        closeModal();
    }
});
</script>

</body>
</html>