<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ---------------- CACHE PREVENTION ---------------- */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* ---------------- SECURITY HEADERS ---------------- */
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

/* Optional (enable only if using HTTPS)
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
*/

/* ---------------- LOGIN CHECK ---------------- */
if (empty($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>