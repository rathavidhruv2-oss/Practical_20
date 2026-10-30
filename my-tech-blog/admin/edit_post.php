<?php
// ============================================================
// admin/edit_post.php - Existing Post Edit Karna
// ============================================================
require_once 'auth_check.php';

$conn  = getConnection();
$error = '';

// URL se post ID lo
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($post_id <= 0) {
    header("Location: dashboard.php");
    exit;
}

// ============================================================
// EXISTING POST FETCH
// ============================================================
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post   = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    header("Location: dashboard.php");
    exit;
}

// ============================================================
// FORM SUBMIT - Post update karo
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']    ?? '');
    $category = trim($_POST['category'] ?? '');
    $author   = trim($_POST['author']   ?? '');
    $content  = trim($_POST['content']  ?? '');

    if (empty($title) || empty($category) || empty($author) || empty($content)) {
        $error = "Sab fields bharna zaroori hai!";
    } else {
        // Prepared statement se safe UPDATE
        $stmt2 = $conn->prepare(
            "UPDATE posts SET title=?, category=?, author=?, content=? WHERE id=?"
        );
        $stmt2->bind_param("ssssi", $title, $category, $author, $content, $post_id);

        if ($stmt2->execute()) {
            header("Location: dashboard.php?msg=post_updated");
            exit;
        } else {
            $error = "Update nahi hua: " . $conn->error;
        }
        $stmt2->close();
    }

    // Form mein updated values dikhao
    $post['title']    = $title;
    $post['category'] = $category;
    $post['author']   = $author;
    $post['content']  = $content;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post | <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
</head>
<body style="background:#f0f2f5;">

<!-- Admin Navbar -->
<nav class="navbar navbar-dark bg-dark shadow">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="bi bi-code-slash text-warning me-2"></i><?php echo SITE_NAME; ?> Admin
        </a>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
        </a>
    </div>
</nav>

<div class="container py-4" style="max-width:750px;">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-pencil-square text-primary me-2"></i>Edit Post
        <small class="text-muted fs-6">ID: #<?php echo $post_id; ?></small>
    </h2>

    <!-- Error Alert -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Edit Form -->
    <div class="form-card">
        <form method="POST" action="edit_post.php?id=<?php echo $post_id; ?>">

            <!-- Title -->
            <div class="mb-3">
                <label class="form-label">Post Title *</label>
                <input type="text" name="title" class="form-control form-control-lg"
                       value="<?php echo htmlspecialchars($post['title']); ?>"
                       maxlength="255" required>
            </div>

            <div class="row g-3 mb-3">
                <!-- Category -->
                <div class="col-md-6">
                    <label class="form-label">Category *</label>
                    <select name="category" class="form-select" required>
                        <?php
                        $categories = ['PHP', 'MySQL', 'Database', 'HTML', 'CSS', 'JavaScript', 'Bootstrap', 'Tools', 'Deployment', 'General'];
                        foreach ($categories as $cat):
                            $sel = ($post['category'] === $cat) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $cat; ?>" <?php echo $sel; ?>><?php echo $cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Author -->
                <div class="col-md-6">
                    <label class="form-label">Author Name *</label>
                    <input type="text" name="author" class="form-control"
                           value="<?php echo htmlspecialchars($post['author']); ?>"
                           required>
                </div>
            </div>

            <!-- Content -->
            <div class="mb-4">
                <label class="form-label">Post Content *</label>
                <textarea name="content" class="form-control" rows="12"
                          required><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save me-2"></i>Update Post
                </button>
                <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $post_id; ?>"
                   target="_blank" class="btn btn-outline-info btn-lg">
                    <i class="bi bi-eye me-2"></i>Preview
                </a>
                <a href="dashboard.php" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
