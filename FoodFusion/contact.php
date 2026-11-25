<?php
    session_start();
    include 'header.php';
    require_once 'pdo.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - FoodFusion</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <section class="contact-page">
        <div class="contact-container">
            <div class="contact-header">
                <h2>Contact Us</h2>
                <p>Have questions about a recipe or want to share feedback?</p>
            </div>

            <div class="contact-form-container">

                <?php if (isset($_SESSION['contact-success'])): ?>
                    <div class="success-box">
                        <p><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($_SESSION['contact-success']) ?></p>
                    </div>
                    <?php unset($_SESSION['contact-success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['contact-error'])): ?>
                    <div class="error-box">
                        <p><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($_SESSION['contact-error']) ?></p>
                    </div>
                    <?php unset($_SESSION['contact-error']); ?>
                <?php endif; ?>

                <form action="submitcontact.php" method="POST" class="contact-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" id="full-name" placeholder="Enter your full name" required>
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" id="email" placeholder="Enter your email address" required>
                        </div>
                    </div>
                        
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select name="subject" id="subject" required>
                            <option value="" disabled selected>Select a topic</option>
                            <option value="General Enquiry">General Enquiry</option>
                            <option value="Recipe Request">Recipe Request</option>
                            <option value="Feedback">Feedback</option>
                            <option value="Partnership">Partnership</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" rows="6" placeholder="How can we help you?" required></textarea>
                    </div>

                    <button type="submit" class="submit-contact-btn">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>