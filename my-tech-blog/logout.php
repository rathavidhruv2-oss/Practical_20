<?php
// ============================================================
// logout.php - Admin Logout
// Session destroy karke home page redirect
// ============================================================
require_once 'config.php';

// Session destroy karo
$_SESSION = [];                  // Session data clear
session_destroy();               // Session file delete

// Cookie bhi clear karo (optional)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Homepage redirect
header("Location: index.php?logout=1");
exit;
?>
