<?php
require_once '../database/functions.php'; 
require_once '../database/pdo.php';


$pageTitle = "Forgot Password";

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    setMessage("You are already logged in.", "info");
    redirect('my_posts.php');
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
                    <h1>Forgot Password?</h1>
                    <p>Enter your email to receive a reset link.</p>
                </div>

                <?php include '../includes/alert.php'; ?>

                <form action="../code.php" method="POST" class="login-form">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="ri-mail-line"></i>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <button type="submit" name="btn_forgot_password" class="btn-login">Send Reset Link</button>

                    <div class="login-footer">
                        <p>Remember your password? <a href="login.php">Login</a></p>
                    </div>
                </form>
            </div>
        </section>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>