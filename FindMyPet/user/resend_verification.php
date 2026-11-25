<?php

$pageTitle = "Resend Email Verificaion Form";
include '../includes/header.php';

if (isset($_SESSION['authenticated'])) {
    $_SESSION['status'] = ".You are already logged in.";
    header("Location: my_posts.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
</head>

<body>
    <main class="login-page">
        <section class="login-container">
            <div class="login-wrapper">
                
                <div class="login-header">
                    <h1>Resend Email Verification</h1>
                    <p>Login to manage your posts and help find lost pets.</p>
                </div>

                <?php include '../includes/alert.php'; ?>

                <form action="../code.php" method="POST" class="login-form">
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="ri-mail-line"></i>
                            <input type="email" id="email" name="email" placeholder="Enter your email address">
                        </div>
                    </div>

                    <button type="submit" name="btn_resend_verification" class="btn-login">
                        Resend Email
                    </button>
                </form>
            </div>
        </section>
    </main>
    <?php include '../includes/footer.php'; ?>

</body>

</html>