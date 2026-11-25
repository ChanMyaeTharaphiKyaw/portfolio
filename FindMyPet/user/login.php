<?php
$pageTitle = "Login Form";

require_once '../database/pdo.php';
require_once '../database/functions.php';

$errors = $_SESSION['errors'] ?? [];
$input = $_SESSION['input'] ?? [];

unset($_SESSION['errors']);
unset($_SESSION['input']);

if (isset($_GET['redirect'])) {
    $_SESSION['redirect_url'] = $_GET['redirect'];
}

// Handle specific warning messages 
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'login_to_comment') {
        setMessage('Please login first to comment.', 'warning');
    } elseif ($_GET['msg'] === 'login_to_poster') {
        setMessage('Please login first to generate poster.', 'warning');
    } elseif ($_GET['msg'] === 'login_to_view') {
        setMessage('Please login first to view contact details.', 'warning');
    } elseif ($_GET['msg'] === 'login_to_report') {
        setMessage('Please login first to report content.', 'warning');
    }
    elseif ($_GET['msg'] === 'login_to_share') { 
        setMessage('Please login first to share this post.', 'warning');
    }
}

// Redirect if already authenticated
if (isset($_SESSION['authenticated'])) {
    $url = $_SESSION['redirect_url'] ?? 'index.php';
    unset($_SESSION['redirect_url']);
    redirect($url);
}

include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
</head>

<body>
    <main class="login-page">
        <section class="login-container">
            <div class="login-wrapper">
                <div class="login-header">
                    <h1>Welcome Back</h1>
                    <p>Login to manage your posts and help find lost pets.</p>
                </div>

                <?php include '../includes/alert.php'; ?>

                <!-- Login Form -->
                <form action="../code.php" method="POST" class="login-form">

                    <!-- Email Address -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="ri-mail-line"></i>
                            <input type="email" id="email" name="email"
                                class="<?= hasError('email', $errors) ? 'error-border' : '' ?>"
                                value="<?= htmlspecialchars($input['email'] ?? '') ?>"
                                placeholder="Enter your email">
                        </div>
                        <?php if (hasError('email', $errors)): ?>
                            <small class="error-text"><?= getError('email', $errors) ?></small>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div class="label-row">
                            <label for="password">Password</label>
                        </div>
                        <div class="input-with-icon">
                            <i class="ri-lock-2-line"></i>
                            <input type="password" id="password" name="password"
                                class="<?= hasError('password', $errors) ? 'error-border' : '' ?>"
                                placeholder="Enter your password">
                        </div>
                        <?php if (hasError('password', $errors)): ?>
                            <small class="error-text"><?= getError('password', $errors) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="forgot-link-container">
                        <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" name="btn_login" class="btn-login">
                        Login
                    </button>

                    <div class="login-footer">
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
                        <p class="resend-verification">Verificatin link expire? <a href="resend_verification.php" class="forgot-link">Resend Email</a>
                        </p>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>

</html>