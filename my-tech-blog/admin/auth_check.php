<?php
// ============================================================
// auth_check.php - Authentication Guard
// Ye file har admin page ke top par include hoti hai
// Admin login nahi hai to login page par redirect karti hai
// ============================================================
require_once dirname(__DIR__) . '/config.php';

// Login check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: " . SITE_URL . "/login.php?msg=please_login");
    exit;
}
// Agar login hai to script aage chalti rehti hai
?>
