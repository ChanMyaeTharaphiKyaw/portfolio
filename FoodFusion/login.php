<?php
session_start();
require_once 'pdo.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $target = $_POST['redirect'] ?? '';
    $errorUrl = "login.php" . ($target ? "?redirect=" . urlencode($target) : "");

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {

        if ($user['lock_time'] && time() < strtotime($user['lock_time'])) {
            $_SESSION['login_error'] = "Account locked. Try again later.";
            header("Location: " . $errorUrl);
            exit;
        }

        if (!empty($user['lock_time']) && strtotime($user['lock_time']) <= time()) {
            $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = 0, lock_time = NULL WHERE user_id = ?");
            $stmt->execute([$user['user_id']]);
            $user['failed_login_attempts'] = 0;
        }

        if (password_verify($password, $user['password'])) {
            $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = 0, lock_time = NULL WHERE user_id = ?");
            $stmt->execute([$user['user_id']]);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user'] = $email;

            $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? 'index.php';
            header("Location: " . $redirect);
            exit;
        } 
        
        else {
            $failed = $user['failed_login_attempts'] + 1;
            $lock_time = null;
            if ($failed >= 3) {
                $lock_time = date("Y-m-d H:i:s", strtotime("+3 minutes"));
                $_SESSION['login_error'] = "Account locked for 3 minutes.";
            } else {
                $remaining = 3 - $failed;
                $_SESSION['login_error'] = "Invalid login attempt. {$remaining} attempt(s) remaining.";
            }
            $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = ?, lock_time = ? WHERE email = ?");
            $stmt->execute([$failed, $lock_time, $email]);

            header("Location: " . $errorUrl);
            exit;
        }
    } 
    else {
        $_SESSION['login_error'] = "User not found.";
        header("Location: " . $errorUrl);
        exit;
    }
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FoodFusion</title>
</head>

<body>
    <div class="login-container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-box">
                <i class="ri-checkbox-circle-fill"></i>
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <form action="login.php" class="login-form" method="POST">
            <?php if (isset($_GET['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect']) ?>">
            <?php endif; ?>

            <h2>Welcome Back</h2>
            <p>Login to Your Account</p>

            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="error-box">
                    <?= htmlspecialchars($_SESSION['login_error']); ?>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
                <p class="reset-password-link" onclick="openResetPopup()">Reset Password?</p>
            </div>

            <button type="submit" class="login-form-btn">Login</button>

            <p class="register-option">Don't have an account? <a href="#" onclick="openRegisterPopup(); return false;">Register</a></p>
        </form>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            const successBox = document.querySelector('.success-box');
            if (successBox) {
                setTimeout(() => {
                    successBox.style.transition = "opacity 0.5s ease";
                    successBox.style.opacity = "0";
                    setTimeout(() => successBox.remove(), 500);
                }, 5000);
            }
        });
    </script>
</body>

</html>