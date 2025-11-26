<?php
session_start();

// Destroy all session data
$_SESSION = [];
session_unset();
session_destroy();

// Prevent caching (for extra security)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Redirect to login page
header("Location: index.php");
exit;
?>
