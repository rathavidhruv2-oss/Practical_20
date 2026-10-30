<?php
// ============================================================
// admin/delete_post.php - Post Delete Karna
// Foreign key ON DELETE CASCADE se comments bhi delete honge
// ============================================================
require_once 'auth_check.php';

$conn    = getConnection();
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate ID
if ($post_id <= 0) {
    header("Location: dashboard.php");
    exit;
}

// Post exist karti hai check karo
$stmt = $conn->prepare("SELECT id, title FROM posts WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post   = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    // Post nahi mili
    header("Location: dashboard.php");
    exit;
}

// ============================================================
// DELETE - Prepared Statement
// Comments automatically delete ho jayenge (ON DELETE CASCADE)
// ============================================================
$stmt2 = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt2->bind_param("i", $post_id);
$stmt2->execute();
$stmt2->close();
$conn->close();

// Dashboard redirect with success message
header("Location: dashboard.php?msg=post_deleted");
exit;
?>
