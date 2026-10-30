<?php
// ============================================================
// post.php - Single Post Page
// Poora post dikhata hai + comment form + saare comments
// ============================================================
require_once 'header.php';

$conn = getConnection();
$error   = '';
$success = '';

// URL se post ID lo (GET parameter)
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate: ID must be positive
if ($post_id <= 0) {
    header("Location: index.php");
    exit;
}

// ============================================================
// POST FETCH - Prepared Statement (SQL injection se bachao)
// ============================================================
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post   = $result->fetch_assoc();
$stmt->close();

// Agar post nahi mila
if (!$post) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Post not found! <a href="index.php">Go Home</a></div></div>';
    require_once 'footer.php';
    exit;
}

$pageTitle = $post['title'];

// ============================================================
// COMMENT SUBMIT - Form process karo
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    // Input sanitize karo
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $comment = trim($_POST['comment'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($comment)) {
        $error = "Sab fields bharna zaroori hai!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Valid email address daalo!";
    } elseif (strlen($comment) < 5) {
        $error = "Comment thoda bada likho!";
    } else {
        // Prepared statement se safe insert
        $stmt2 = $conn->prepare(
            "INSERT INTO comments (post_id, name, email, comment) VALUES (?, ?, ?, ?)"
        );
        $stmt2->bind_param("isss", $post_id, $name, $email, $comment);

        if ($stmt2->execute()) {
            $success = "Aapka comment successfully submit ho gaya! Thank you!";
        } else {
            $error = "Comment save nahi hua. Please try again.";
        }
        $stmt2->close();
    }
}

// ============================================================
// COMMENTS FETCH - Is post ke saare comments
// ============================================================
$stmt3 = $conn->prepare(
    "SELECT name, email, comment, created_at FROM comments WHERE post_id = ? ORDER BY created_at ASC"
);
$stmt3->bind_param("i", $post_id);
$stmt3->execute();
$comments_result = $stmt3->get_result();
$stmt3->close();
?>

<!-- ===== POST HERO ===== -->
<div class="post-hero">
    <div class="container">
        <span class="badge-category mb-3 d-inline-block">
            <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($post['category']); ?>
        </span>
        <h1 class="fw-bold"><?php echo htmlspecialchars($post['title']); ?></h1>
        <p class="post-meta mt-2" style="color:rgba(255,255,255,0.8)">
            <i class="bi bi-person-fill me-1"></i><?php echo htmlspecialchars($post['author']); ?>
            &nbsp;|&nbsp;
            <i class="bi bi-calendar3 me-1"></i><?php echo date('d M Y, h:i A', strtotime($post['created_at'])); ?>
        </p>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <!-- Main Content Column -->
        <div class="col-lg-8">

            <!-- ===== POST CONTENT ===== -->
            <div class="post-content mb-4">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>

            <!-- ===== COMMENTS LIST ===== -->
            <h3 class="section-heading mt-4">
                <i class="bi bi-chat-dots me-2"></i>
                Comments (<?php echo $comments_result->num_rows; ?>)
            </h3>

            <?php if ($comments_result->num_rows > 0): ?>
                <?php while ($c = $comments_result->fetch_assoc()): ?>
                    <div class="comment-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="comment-author">
                                <i class="bi bi-person-circle me-1 text-warning"></i>
                                <?php echo htmlspecialchars($c['name']); ?>
                            </span>
                            <span class="comment-date">
                                <i class="bi bi-clock me-1"></i>
                                <?php echo date('d M Y', strtotime($c['created_at'])); ?>
                            </span>
                        </div>
                        <p class="mt-2 mb-0" style="color:#555;">
                            <?php echo nl2br(htmlspecialchars($c['comment'])); ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">Abhi koi comment nahi. Pehle comment karo!</p>
            <?php endif; ?>

            <!-- ===== COMMENT FORM ===== -->
            <h3 class="section-heading mt-5">
                <i class="bi bi-pencil-square me-2"></i>Leave a Comment
            </h3>

            <!-- Success/Error alerts -->
            <?php if ($success): ?>
                <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-card">
                <form method="POST" action="post.php?id=<?php echo $post_id; ?>">
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label">Your Name *</label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Apna naam likhein"
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                   required>
                        </div>
                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="your@email.com"
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   required>
                        </div>
                        <!-- Comment -->
                        <div class="col-12">
                            <label class="form-label">Your Comment *</label>
                            <textarea name="comment" class="form-control" rows="4"
                                      placeholder="Apna comment likhein..."
                                      required><?php echo htmlspecialchars($_POST['comment'] ?? ''); ?></textarea>
                        </div>
                        <!-- Submit -->
                        <div class="col-12">
                            <button type="submit" name="submit_comment" class="btn btn-primary">
                                <i class="bi bi-send me-2"></i>Submit Comment
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Back button -->
            <div class="mt-4">
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to All Posts
                </a>
            </div>

        </div><!-- /col-lg-8 -->

        <!-- Sidebar Column -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="form-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-warning"></i>Post Info</h5>
                <ul class="list-unstyled text-muted small">
                    <li class="mb-2"><i class="bi bi-tag me-2"></i><strong>Category:</strong> <?php echo htmlspecialchars($post['category']); ?></li>
                    <li class="mb-2"><i class="bi bi-person me-2"></i><strong>Author:</strong> <?php echo htmlspecialchars($post['author']); ?></li>
                    <li class="mb-2"><i class="bi bi-calendar me-2"></i><strong>Published:</strong> <?php echo date('d M Y', strtotime($post['created_at'])); ?></li>
                    <?php if ($post['updated_at'] !== $post['created_at']): ?>
                        <li class="mb-2"><i class="bi bi-pencil me-2"></i><strong>Updated:</strong> <?php echo date('d M Y', strtotime($post['updated_at'])); ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div><!-- /row -->
</div><!-- /container -->

<?php
$conn->close();
require_once 'footer.php';
?>
