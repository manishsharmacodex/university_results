<?php

$isHttps =
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || ($_SERVER['SERVER_PORT'] == 443);

$protocol = $isHttps ? "https" : "http";

/*
|-----------------------------------------
| Load .env safely
|-----------------------------------------
*/
$envFile = __DIR__ . '/../.env';

$projectFolder = basename(dirname(__DIR__)); // stronger fallback

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {

        $line = trim($line);

        // ignore comments
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (strpos($line, '=') === false)
            continue;

        [$key, $value] = explode('=', $line, 2);

        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        if ($key === 'PROJECT_FOLDER' && $value !== '') {
            $projectFolder = trim($value, '/');
        }
    }
}

/*
|-----------------------------------------
| BASE URL (clean)
|-----------------------------------------
*/
define(
    'BASE_URL',
    $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . $projectFolder . "/"
    // $protocol . "://" . $_SERVER['HTTP_HOST'] . "/"
);