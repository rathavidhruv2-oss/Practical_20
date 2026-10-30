<?php
// ============================================================
// config.php - Database Configuration File
// Practical-20: My Tech Blog
// ============================================================
// IMPORTANT: Live server par deploy karte waqt sirf ye 4
// values change karni hain apni hosting ki values se.
// ============================================================

// --- LOCAL (XAMPP) Settings ---
define('DB_HOST', 'localhost');      // Database host (usually localhost)
define('DB_USER', 'root');           // XAMPP default user is 'root'
define('DB_PASS', '');               // XAMPP default password is empty ''
define('DB_NAME', 'mytechblog');     // Database name jo apne banaya

// --- LIVE SERVER Settings (comment out above, uncomment below) ---
// define('DB_HOST', 'localhost');
// define('DB_USER', 'your_cpanel_db_user');    // e.g., username_dbuser
// define('DB_PASS', 'your_db_password');
// define('DB_NAME', 'your_cpanel_db_name');    // e.g., username_mytechblog

// ============================================================
// Database Connection Function
// MySQLi + Prepared Statements use karte hain security ke liye
// ============================================================
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Connection error check
    if ($conn->connect_error) {
        // Production mein ye line comment kar dena aur log me save karna
        die("
        <div style='font-family:Arial; padding:30px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; border-radius:8px; max-width:600px; margin:50px auto;'>
            <h3>&#9888; Database Connection Error</h3>
            <p><strong>Error:</strong> " . htmlspecialchars($conn->connect_error) . "</p>
            <p>Please check your <code>config.php</code> file settings.</p>
        </div>");
    }

    // Character set set karo - UTF-8 for Hindi/special characters
    $conn->set_charset("utf8mb4");

    return $conn;
}

// Site configuration
define('SITE_NAME', 'My Tech Blog');
define('SITE_URL',  'http://localhost/my-tech-blog'); // Change on live server

// Session start (ek baar hi karo)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
