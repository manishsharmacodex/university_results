<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sushant University - Home Page</title>
    <link rel="stylesheet" type="text/css" href="./index.css">
    <!-- css link for google fonts -->
     <link rel="stylesheet" href="./font.css">
</head>
<body>
    <div class="navigation-bar">
        <img src="./src/logo.png" alt="logo" class="logo">
        <ul>
            <a href="./index.php"><li>Home</li></a>
            <a href="./school/school.php"><li>School</li></a>
            <a href="./results_check/index.php"><li>Exam & Result</li></a>
            <a href="#"><li>Contact Us</li></a>
            <a href="#"><li>Our Blog</li></a>
        </ul>
        <div class="nav-btn">
            <button class="login-btn"><a href="">Student Login</a></button>
            <button class="emp-login-btn"><a href="./admin/admin_login.php" target="next_page">Employee Login</a></button>
        </div>
    </div>

    <!-- <marquee><h1 class="highlights">
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quod asperiores minima quasi labore quisquam. Sint amet eaque veritatis mollitia unde eos vel eligendi impedit expedita ipsum,
    </h1></marquee> -->

    <div class="registeration-form-box">
        <div class="left-col">
            <h1 class="text">Sushant University
                <br><span class="second-text">Gurugram, Haryana</span>
            </h1>
            <p>
                Lorem ipsum dolor sit amet consectetur, adipisicing elit. Tenetur voluptatibus alias in eos totam cupiditate, minima soluta dolore quas illum nostrum, doloremque beatae consequuntur blanditiis aliquam id impedit reiciendis reprehenderit.
            </p>

            <div class="left-button">
                <button>Fee Payment</button>
                <button>TCS Login</button>
            </div>
        </div>

        <div class="right-col">
            <form action="" class="form">
                <h1 class="form-title">Student Register Form - 2025</h1>
                <div class="user-feild">
                    <label for="">First Name</label>
                    <input type="text" placeholder="enter your first Name : ">
                </div>

                <div class="user-feild">
                    <label for="">Last Name</label>
                    <input type="text" placeholder="enter your last Name : ">
                </div>

                <div class="user-feild">
                    <label for="">Email</label>
                    <input type="text" placeholder="enter your email : ">
                </div>

                <div class="user-feild">
                    <label for="">Phone Number</label>
                    <input type="text" placeholder="enter your contact number : ">
                </div>
                <button class="form-btn">Submit</button>

                <p class="desc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Cupiditate in, aliquid illo,  <a href="#" class="dec-links">click here</a></p>
            </form>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 Sushant University All Rights Reserved</p>
    </div>


    <script type="text/JavaScript" src="./js/script.js"></script>
</body>
</html>