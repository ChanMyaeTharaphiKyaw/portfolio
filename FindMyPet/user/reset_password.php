<?php
require_once '../database/pdo.php';
require_once '../database/functions.php';

$pageTitle = "Reset Password";

$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (empty($token)) {
    setMessage("Invalid reset link.", "error");
    redirect('login.php');
}

// Fetch user
$stmt = $pdo->prepare("SELECT id, full_name, reset_token, reset_expire FROM users WHERE reset_token = :token LIMIT 1");
$stmt->execute(['token' => $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Validate Token and Expiration
if (!$user || strtotime($user['reset_expire']) < time()) {
    setMessage("This reset link is invalid or has expired.", "error");
    redirect('login.php');
}

include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
</head>
<body>
    <main class="login-page">
        <section class="login-container">
            <div class="login-wrapper">
                <div class="login-header">
                    <h1>Reset Password</h1>
                    <p>Create a new secure password for your account.</p>
                </div>

                <?php include '../includes/alert.php'; ?>

                <form action="../code.php" method="POST" class="login-form">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <div class="input-with-icon">
                            <i class="ri-lock-2-line"></i>
                            <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="input-with-icon">
                            <i class="ri-lock-password-line"></i>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                        </div>
                    </div>

                    <button type="submit" name="btn_reset_password" class="btn-login">Update Password</button>

                    <div class="login-footer">
                        <p><a href="login.php">Back to Login</a></p>
                    </div>
                </form>
            </div>
        </section>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>