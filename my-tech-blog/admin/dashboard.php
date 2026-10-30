<?php
// ============================================================
// admin/dashboard.php - Admin Dashboard
// Saare posts ki table with Edit/Delete options
// ============================================================
require_once 'auth_check.php';  // Login check (agar login nahi to redirect)

$conn = getConnection();

// Success/Delete message
$msg = $_GET['msg'] ?? '';

// Saare posts fetch karo, comments count ke saath
$sql = "SELECT p.id, p.title, p.category, p.author, p.created_at,
               COUNT(c.id) AS comments_count
        FROM posts p
        LEFT JOIN comments c ON c.post_id = p.id
        GROUP BY p.id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
</head>
<body style="background:#f0f2f5;">

<!-- Admin Navbar -->
<nav class="navbar navbar-dark bg-dark shadow">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="<?php echo SITE_URL; ?>/index.php">
            <i class="bi bi-code-slash text-warning me-2"></i><?php echo SITE_NAME; ?>
            <small class="text-muted ms-2 fs-6">Admin</small>
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white small">
                <i class="bi bi-person-circle me-1"></i>
                <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
            </span>
            <a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-eye me-1"></i>View Site
            </a>
            <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-warning btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-speedometer2 me-2 text-warning"></i>Admin Dashboard
        </h2>
        <a href="add_post.php" class="btn btn-success">
            <i class="bi bi-plus-circle me-2"></i>Add New Post
        </a>
    </div>

    <!-- Alerts -->
    <?php if ($msg === 'post_added'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>Post successfully add ho gaya!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'post_updated'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>Post successfully update ho gaya!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'post_deleted'): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="bi bi-trash me-2"></i>Post delete ho gaya!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-left:4px solid #ffc107!important;">
                <h3 class="fw-bold text-warning"><?php echo $result ? $result->num_rows : 0; ?></h3>
                <p class="mb-0 text-muted"><i class="bi bi-file-text me-1"></i>Total Posts</p>
            </div>
        </div>
        <?php
        // Comments count
        $c_result = $conn->query("SELECT COUNT(*) AS total FROM comments");
        $c_count  = $c_result->fetch_assoc()['total'];
        ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3">
                <h3 class="fw-bold text-primary"><?php echo $c_count; ?></h3>
                <p class="mb-0 text-muted"><i class="bi bi-chat-dots me-1"></i>Total Comments</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3">
                <h3 class="fw-bold text-success">1</h3>
                <p class="mb-0 text-muted"><i class="bi bi-person-check me-1"></i>Admin Users</p>
            </div>
        </div>
    </div>

    <!-- Posts Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i>All Posts</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Date</th>
                            <th>Comments</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php $sr = 1; while ($post = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $sr++; ?></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $post['id']; ?>"
                                           target="_blank" class="text-decoration-none fw-semibold">
                                            <?php echo htmlspecialchars(mb_substr($post['title'], 0, 45)); ?>
                                            <?php if (mb_strlen($post['title']) > 45) echo '...'; ?>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-warning text-dark"><?php echo htmlspecialchars($post['category']); ?></span></td>
                                    <td><?php echo htmlspecialchars($post['author']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($post['created_at'])); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark"><?php echo $post['comments_count']; ?></span>
                                    </td>
                                    <td class="table-actions">
                                        <!-- Edit -->
                                        <a href="edit_post.php?id=<?php echo $post['id']; ?>"
                                           class="btn btn-sm btn-primary me-1">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <!-- Delete (confirm dialog) -->
                                        <a href="delete_post.php?id=<?php echo $post['id']; ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Kya aap sach mein ye post delete karna chahte hain?');">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Koi post nahi hai. <a href="add_post.php">Pehla post add karo!</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>
