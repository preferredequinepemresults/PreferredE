<?php
// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Pages that don't require login (public pages)
$public_pages = ['registration.php', 'login.php', 'index.php'];

// Check if the user is logged in
if (!isset($_SESSION["UserName"])) {
    // Only redirect if NOT trying to access a public page
    if (!in_array($current_page, $public_pages)) {
        $current_url = urlencode($_SERVER['REQUEST_URI']); // Encode it for use in URL
        header("Location: login.php?redirect=$current_url");
        exit();
    }
}

// Set session timeout duration (in seconds)
$timeout_duration = 1800; // 30 minutes

// Session timeout logic (only for logged-in users)
if (isset($_SESSION["UserName"])) {
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();
        session_destroy();
        $current_url = urlencode($_SERVER['REQUEST_URI']); // Encode it for use in URL
        header("Location: login.php?timeout=true&redirect=$current_url");
        exit();
    }
    $_SESSION['LAST_ACTIVITY'] = time(); // Update last activity timestamp
}