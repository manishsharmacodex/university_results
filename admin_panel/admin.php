<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sushant University</title>
    <link rel="stylesheet" type="text/css" href="../css/font.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            background: #0b1220;
            color: #fff;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 260px;
            background: #0f172a;
            padding: 15px;
            height: 100vh;
        }

        .sidebar h2 {
            color: #00d9ff;
            margin-bottom: 20px;
        }

        .menu-item {
            padding: 12px;
            margin-bottom: 8px;
            border-radius: 8px;
            cursor: pointer;
            color: #b8c1d1;
            transition: 0.3s;
        }

        .menu-item:hover {
            background: rgba(0, 217, 255, 0.1);
            color: #00d9ff;
        }

        .submenu {
            margin-left: 15px;
            display: none;
        }

        .submenu .menu-item {
            font-size: 13px;
        }

        .content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .card {
            background: rgba(255, 255, 255, 0.06);
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
        }

        h1 {
            color: #00d9ff;
            margin-bottom: 10px;
        }

        p {
            color: #b8c1d1;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">

        <h2>Admin Panel</h2>

        <div class="menu-item" onclick="showContent('dashboard')">Dashboard</div>
        <div class="menu-item" onclick="showContent('addStudent')">Add Student</div>
        <div class="menu-item" onclick="showContent('studentList')">Student List</div>
        <div class="menu-item" onclick="showContent('marks')">Marks / Results</div>
        <div class="menu-item" onclick="showContent('fees')">Fees</div>
        <div class="menu-item" onclick="showContent('teacher')">Teacher / Faculty List</div>
        <div class="menu-item" onclick="showContent('staff')">Staff / Employee List</div>
        <div class="menu-item" onclick="showContent('subject')">Subject Add</div>

        <div class="menu-item" onclick="toggleMenu('attendanceMenu')">Attendance ▼</div>
        <div class="submenu" id="attendanceMenu">
            <div class="menu-item" onclick="showContent('facultyAttendance')">Faculty Attendance</div>
            <div class="menu-item" onclick="showContent('studentAttendance')">Student Attendance</div>
        </div>

        <div class="menu-item" onclick="toggleMenu('settingsMenu')">Settings ⚙</div>
        <div class="submenu" id="settingsMenu">
            <div class="menu-item" onclick="showContent('changePassword')">Change Password</div>
            <div class="menu-item" onclick="showContent('changeUsername')">Change Username</div>
        </div>

    </div>

    <!-- CONTENT -->
    <div class="content" id="contentArea">

        <h1>Dashboard</h1>
        <div class="card">
            <p>Welcome to Admin Dashboard</p>
            <p>Manage Students, Faculty, Attendance, Results and Fees from here.</p>
        </div>

    </div>

    <script>
        function toggleMenu(id) {
            let menu = document.getElementById(id);
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }

        async function showContent(page) {

            let content = document.getElementById("contentArea");

            const pages = {
                dashboard: `
                    <h1>Dashboard</h1>
                    <div class="card">
                        <p>Welcome Admin! Manage everything from here.</p>
                    </div>
                `,

                marks: `
                    <h1>Marks / Results</h1>
                    <div class="card">
                        <p>Enter and manage student marks.</p>
                    </div>
                `,

                fees: `
                    <h1>Fees Management</h1>
                    <div class="card">
                        <p>Track student fees payments.</p>
                    </div>
                `,

                teacher: `
                    <h1>Faculty List</h1>
                    <div class="card">
                        <p>Teacher details will be listed here.</p>
                    </div>
                `,

                staff: `
                    <h1>Staff List</h1>
                    <div class="card">
                        <p>Employee records management.</p>
                    </div>
                `,

                subject: `
                    <h1>Subject Add</h1>
                    <div class="card">
                        <p>Add and manage subjects.</p>
                    </div>
                `,

                facultyAttendance: `
                    <h1>Faculty Attendance</h1>
                    <div class="card">
                        <p>Mark faculty attendance here.</p>
                    </div>
                `,

                studentAttendance: `
                    <h1>Student Attendance</h1>
                    <div class="card">
                        <p>Mark student attendance here.</p>
                    </div>
                `,

                changePassword: `
                    <h1>Change Password</h1>
                    <div class="card">
                        <p>Update admin password securely.</p>
                    </div>
                `,

                changeUsername: `
                    <h1>Change Username</h1>
                    <div class="card">
                        <p>Update admin username here.</p>
                    </div>
                `
            };

            /* =========================
               ADD STUDENT (PHP LOAD)
            ========================= */
            if (page === "addStudent") {
                content.innerHTML = `
                    <h1>Add Student</h1>
                    <div class="card" id="studentBox">
                        <p>Loading form...</p>
                    </div>
                `;

                try {
                    const res = await fetch("../student_details/add_students.php");
                    const html = await res.text();
                    document.getElementById("studentBox").innerHTML = html;
                } catch (error) {
                    document.getElementById("studentBox").innerHTML =
                        "<p style='color:red;'>Failed to load Add Student page</p>";
                }

                return;
            }

            /* =========================
               STUDENT LIST (NEW PHP LOAD)
            ========================= */
            if (page === "studentList") {
                content.innerHTML = `
                    <h1>Student List</h1>
                    <div class="card" id="studentListBox">
                        <p>Loading student list...</p>
                    </div>
                `;

                try {
                    const res = await fetch("../student_details/student_list.php");
                    const html = await res.text();
                    document.getElementById("studentListBox").innerHTML = html;
                } catch (error) {
                    document.getElementById("studentListBox").innerHTML =
                        "<p style='color:red;'>Failed to load Student List page</p>";
                }

                return;
            }

            content.innerHTML = pages[page] || "<h1>Page Not Found</h1>";
        }
    </script>

</body>

</html>