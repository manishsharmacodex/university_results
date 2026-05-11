<?php

require_once __DIR__ . "/backend/config/config.php";

// Safe redirect using global BASE_URL
header("Location: " . BASE_URL . "frontend/main.php");
exit;

?>