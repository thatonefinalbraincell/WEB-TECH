<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy session cookie in browser if present
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session on server
session_destroy();

// Redirect back to home search page
header("Location: index.php?message=logged_out");
exit;
