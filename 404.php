<?php require_once __DIR__ . "/backend/config/config.php"; ?>

<!DOCTYPE html>
<html>

<head>
    <title>404 - Page Not Found</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Manrope", sans-serif;
        }

        body {
            width: 100%;
            height: 100vh;
            background-color: #010101;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        h1 {
            font-size: 150px;
        }

        h2 {
            font-size: 50px;
        }

        a {
            text-decoration: none;
            padding: 12px 20px;
            background: #007bff;
            color: white;
            border-radius: 6px;
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <h1>404</h1>
    <h2>Page Not Found</h2>

    <a href="<?= BASE_URL ?>">Go Home</a>

</body>

</html>