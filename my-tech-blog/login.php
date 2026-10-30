<?php
// ============================================================
// login.php - Admin Login Page
// Session-based login with bcrypt password verification
// ============================================================
require_once 'config.php';

$error = '';

// Agar already logged in hai to dashboard redirect karo
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin/dashboard.php");
    exit;
}

// ============================================================
// FORM SUBMIT PROCESS
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic validation
    if (empty($username) || empty($password)) {
        $error = "Username aur Password dono zaroori hain!";
    } else {
        $conn = getConnection();

        // Prepared statement - SQL injection se bachao
        $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin  = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

        // Password verify karo (bcrypt)
        if ($admin && password_verify($password, $admin['password'])) {
            // Login success - session set karo
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $admin['id'];
            $_SESSION['admin_username']  = $admin['username'];

            // Dashboard redirect
            header("Location: admin/dashboard.php");
            exit;
        } else {
            // Wrong credentials
            $error = "Invalid username ya password!";
        }
    }
}

$pageTitle = "Admin Login";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
</head>
<body style="background: linear-gradient(135deg, #1a1a2e, #0f3460); min-height: 100vh;">

<div class="login-wrapper">
    <div class="login-card">
        <!-- Title -->
        <div class="login-title">
            <i class="bi bi-shield-lock-fill"></i>
            <h2>Admin Login</h2>
            <p class="text-muted small"><?php echo SITE_NAME; ?></p>
        </div>

        <!-- Error Alert -->
        <?php if ($error): ?>
            <div class="alert alert-danger py-2">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="login.php">
            <!-- Username -->
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control"
                           placeholder="admin"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           autofocus required>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control"
                           placeholder="••••••••" required>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </button>
        </form>

        <!-- Back to site -->
        <div class="text-center mt-3">
            <a href="index.php" class="text-muted small">
                <i class="bi bi-arrow-left me-1"></i>Back to Website
            </a>
        </div>

        <!-- Demo credentials hint -->
        <div class="alert alert-info mt-3 py-2 text-center small">
            <i class="bi bi-info-circle me-1"></i>
            Default: <strong>admin</strong> / <strong>admin123</strong>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
