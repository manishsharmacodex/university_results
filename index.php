<?php

require_once __DIR__ . "/backend/config/config.php";

/*
|--------------------------------------
| Safe redirect (production safe)
|--------------------------------------
*/

// ensure BASE_URL exists
if (!defined('BASE_URL')) {
    die("BASE_URL is not defined in config");
}

// build safe URL
$redirectUrl = rtrim(BASE_URL, '/') . '/frontend/main.php';

// redirect
header("Location: $redirectUrl", true, 302);
exit;

?>