<?php

/*
|--------------------------------------------------------------------------
| Project Root Folder Name
|--------------------------------------------------------------------------
| Change this only if you rename your project folder
*/
define('PROJECT_FOLDER', 'university_results');

/*
|--------------------------------------------------------------------------
| Detect HTTPS safely (proxy + server compatible)
|--------------------------------------------------------------------------
*/
$isHttps =
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || ($_SERVER['SERVER_PORT'] == 443);

$protocol = $isHttps ? "https" : "http";

/*
|--------------------------------------------------------------------------
| BASE URL (Production Safe)
|--------------------------------------------------------------------------
| Example:
| https://localhost/university_results/
| https://yourdomain.com/university_results/
*/
define(
    'BASE_URL',
    $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . PROJECT_FOLDER . "/"
);

?>