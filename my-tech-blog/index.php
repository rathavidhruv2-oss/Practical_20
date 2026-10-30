<?php
// ============================================================
// index.php - Homepage (All Blog Posts)
// Database se saare posts cards mein dikhata hai
// ============================================================
$pageTitle = "Home - All Posts";
require_once 'header.php';

// Database connection lo
$conn = getConnection();

// Saare posts fetch karo, latest pehle
// LEFT JOIN se comments count bhi aata hai
$sql = "SELECT p.id, p.title, p.category, p.author, p.created_at,
               LEFT(p.content, 180) AS short_content,
               COUNT(c.id) AS comments_count
        FROM posts p
        LEFT JOIN comments c ON c.post_id = p.id
        GROUP BY p.id
        ORDER BY p.created_at DESC";

$result = $conn->query($sql);
?>

<!-- ===== HERO SECTION ===== -->
<div class="hero-section">
    <div class="container text-center">
        <h1><i class="bi bi-code-slash text-warning me-2"></i>Welcome to My Tech Blog</h1>
        <p class="mt-2 mb-0">PHP, MySQL, Web Development & Deployment Tutorials in Hindi/English</p>
    </div>
</div>

<!-- ===== BLOG POSTS GRID ===== -->
<div class="container mb-5">
    <h2 class="section-heading"><i class="bi bi-journal-richtext me-2"></i>Latest Posts</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($post = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card blog-card">
                        <!-- Card Header: Category -->
                        <div class="card-header">
                            <span class="badge-category">
                                <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($post['category']); ?>
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <!-- Post Title -->
                            <h5 class="card-title mb-2">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </h5>

                            <!-- Post Meta: Author & Date -->
                            <p class="post-meta mb-2">
                                <i class="bi bi-person-fill"></i> <?php echo htmlspecialchars($post['author']); ?>
                                &nbsp;|&nbsp;
                                <i class="bi bi-calendar3"></i> <?php echo date('d M Y', strtotime($post['created_at'])); ?>
                                &nbsp;|&nbsp;
                                <span class="comments-count-badge">
                                    <i class="bi bi-chat-dots"></i> <?php echo $post['comments_count']; ?> Comments
                                </span>
                            </p>

                            <!-- Short Content Preview -->
                            <p class="card-text flex-grow-1">
                                <?php echo htmlspecialchars($post['short_content']); ?>...
                            </p>

                            <!-- Read More Button -->
                            <div class="mt-3">
                                <a href="post.php?id=<?php echo $post['id']; ?>" class="btn-read-more">
                                    Read More <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    <?php else: ?>
        <!-- Koi post nahi hai tab -->
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle me-2"></i>
            Abhi koi post nahi hai.
            <a href="login.php" class="alert-link">Admin login karke post add karein.</a>
        </div>
    <?php endif; ?>
</div>

<?php
$conn->close();
require_once 'footer.php';
?>
