<?php
// ============================================================
// admin/add_post.php - New Blog Post Add Karna
// ============================================================
require_once 'auth_check.php';

$conn   = getConnection();
$error  = '';
$success = '';

// ============================================================
// FORM SUBMIT - Naya post save karo
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']    ?? '');
    $category = trim($_POST['category'] ?? '');
    $author   = trim($_POST['author']   ?? '');
    $content  = trim($_POST['content']  ?? '');

    // Validation
    if (empty($title) || empty($category) || empty($author) || empty($content)) {
        $error = "Sab fields bharna zaroori hai!";
    } elseif (strlen($title) > 255) {
        $error = "Title bahut lamba hai (max 255 characters)!";
    } else {
        // Prepared statement se safe INSERT
        $stmt = $conn->prepare(
            "INSERT INTO posts (title, category, author, content) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $title, $category, $author, $content);

        if ($stmt->execute()) {
            // Success - dashboard redirect with message
            header("Location: dashboard.php?msg=post_added");
            exit;
        } else {
            $error = "Post save nahi hua: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post | <?php echo SITE_NAME; ?></title>
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
        <i class="bi bi-plus-circle text-success me-2"></i>Add New Post
    </h2>

    <!-- Error Alert -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Post Form -->
    <div class="form-card">
        <form method="POST" action="add_post.php">
            <!-- Title -->
            <div class="mb-3">
                <label class="form-label">Post Title *</label>
                <input type="text" name="title" class="form-control form-control-lg"
                       placeholder="Post ka title likhein..."
                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                       maxlength="255" required>
            </div>

            <div class="row g-3 mb-3">
                <!-- Category -->
                <div class="col-md-6">
                    <label class="form-label">Category *</label>
                    <select name="category" class="form-select" required>
                        <option value="">-- Category Select Karo --</option>
                        <?php
                        $categories = ['PHP', 'MySQL', 'Database', 'HTML', 'CSS', 'JavaScript', 'Bootstrap', 'Tools', 'Deployment', 'General'];
                        foreach ($categories as $cat):
                            $sel = (isset($_POST['category']) && $_POST['category'] === $cat) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $cat; ?>" <?php echo $sel; ?>><?php echo $cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Author -->
                <div class="col-md-6">
                    <label class="form-label">Author Name *</label>
                    <input type="text" name="author" class="form-control"
                           placeholder="Author ka naam"
                           value="<?php echo htmlspecialchars($_POST['author'] ?? 'Admin'); ?>"
                           required>
                </div>
            </div>

            <!-- Content -->
            <div class="mb-4">
                <label class="form-label">Post Content *</label>
                <textarea name="content" class="form-control" rows="10"
                          placeholder="Post ka content likhein..."
                          required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                <div class="form-text">
                    <i class="bi bi-info-circle me-1"></i>
                    Plain text likhein. Enter se new paragraph banega.
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-save me-2"></i>Publish Post
                </button>
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
