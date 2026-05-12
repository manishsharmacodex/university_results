<?php require_once __DIR__ . "/backend/config/config.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>404 - Page Not Found</title>
    <style>
        body{
            font-family: Arial;
            text-align:center;
            padding:100px;
        }
        h1{
            font-size:80px;
        }
        a{
            text-decoration:none;
            padding:12px 20px;
            background:#007bff;
            color:white;
            border-radius:6px;
        }
    </style>
</head>
<body>

<h1>404</h1>
<h2>Page Not Found</h2>
<p>The page you entered does not exist.</p>

<a href="<?= BASE_URL ?>">Go Home</a>

</body>
</html>