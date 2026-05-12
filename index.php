<?php

require_once __DIR__ . "/backend/config/config.php";

/*
|-----------------------------------------
| Validate BASE_URL
|-----------------------------------------
*/
if (!defined('BASE_URL')) {
    http_response_code(500);
    die("Configuration error: BASE_URL not defined");
}

/*
|-----------------------------------------
| Prevent header issues
|-----------------------------------------
*/
if (headers_sent()) {
    die("Redirect failed: headers already sent");
}

/*
|-----------------------------------------
| Build Safe URL
|-----------------------------------------
*/
$redirectUrl = BASE_URL . 'frontend/main.php';

/*
|-----------------------------------------
| Redirect (SEO-safe temporary redirect)
|-----------------------------------------
*/
header("Location: $redirectUrl", true, 302);
exit;