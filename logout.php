<?php
session_start();

// Clear all session data
$_SESSION = array();

// Clear the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Destroy the session
session_destroy();

// Clear your custom cookie
setcookie("LoggedInUser", "", time() - 3600, "/");

// Redirect to login page
header("Location: login.php");
exit();