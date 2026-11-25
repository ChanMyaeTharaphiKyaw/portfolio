<?php
require_once 'pdo.php';
?>

<footer class="main-footer">
    <div class="footer-top">
        <div class="footer-container">
            <div class="footer-column about-column">
                <div class="footer-logo">
                    <a href="index.php" class="logo">FoodFusion</a>
                </div>
                <p>
                    Promoting home cooking and culinary creativity. Join our vibrant food community
                    to share recipes, tips, and explore global cuisines.
                </p>

                <div class="contact-info">
                    <p><i class="ri-phone-line"></i> 09778324363</p>
                    <p><i class="ri-mail-line"></i> support@foodfusion.com</p>
                    <p><i class="ri-map-pin-line"></i> Mandalay, Myanmar</p>
                </div>
            </div>

            <div class="footer-column">
                <h3>Getting Started</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="recipes.php">Recipes Collection</a></li>
                    <li><a href="community.php">Community Cookbook</a></li>
                    <li><a href="culinary.php">Culinary Resources</a></li>
                    <li><a href="educational.php">Educational Resources</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Information</h3>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Support</h3>
                <ul>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                    <li><a href="cookie.php">Cookie Policy</a></li>
                    <li><a href="terms_of_service.php">Terms of Service</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="footer-container">
            <p class="copyright">&copy; 2026 FoodFusion. All rights reserved.</p>
            <div class="bottom-links">
                <a href="terms_of_service.php">Terms of Service</a>
                <a href="privacy.php">Privacy Policy</a>
                <a href="cookie.php">Cookie Policy</a>
            </div>
            <div class="footer-socials">
                <a href="#"><i class="ri-facebook-circle-fill"></i></a>
                <a href="#"><i class="ri-instagram-fill"></i></a>
                <a href="#"><i class="ri-twitter-x-fill"></i></a>
                <a href="#"><i class="ri-youtube-fill"></i></a>
            </div>
        </div>
    </div>
</footer>

<div id="cookie-banner" class="cookie-banner">
    <p>We use cookies to ensure you get the best experience and personalized content on our culinary platform.
        <a href="cookie.php">Learn more</a>
    </p>
    <button onclick="acceptCookies()" class="cookie-btn">Accept All</button>
</div>

<script>
    window.addEventListener('load', function() {
        if (!localStorage.getItem('cookiesAccepted')) {
            document.getElementById('cookie-banner').classList.add('show');
        }
    });

    function acceptCookies() {
        localStorage.setItem('cookiesAccepted', 'true');
        document.getElementById('cookie-banner').classList.remove('show');
    }
</script>