<?php
session_start();

// Clear all session data
$_SESSION = [];

// Destroy session on server
session_destroy();

// Delete session cookie securely
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 86400, // expired in the past (1 day ago)
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => 'Lax' // improves security against CSRF
        ]
    );
}

// Redirect safely to login page
header('Location: login.php');
exit;