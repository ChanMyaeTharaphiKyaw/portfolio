<?php
session_start();
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - FoodFusion</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
</head>

<body>
    <main class="about-page">
        <section class="about-hero-section">
            <div class="about-hero-container">
                <h1>About FoodFusion</h1>
            </div>
        </section>

        <section class="vision-section">
            <div class="vision-container">
                <div class="about-image">
                    <img src="images/vision.jpg" alt="FoodFusion Vision">
                </div>
                <div class="vision-content">
                    <span class="tagline">OUR VISION</span>
                    <h2>Redefining the Home <br>Cooking Experience</h2>
                    <p>To become the world's most interactive culinary hub, where the joy of home cooking meets a commitment to a sustainable and energy-efficient future.</p>
                </div>
            </div>
        </section>

        <section class="philosophy-section">
            <div class="philosophy-container">
                <div class="philosophy-content">
                    <span class="tagline">OUR PHILOSOPHY</span>
                    <h2>Sustainably Sourced, <br>Communally Shared</h2>
                    <p>At FoodFusion, we believe a recipe is more than instructions—it's an opportunity to foster a vibrant community. We bridge the gap between traditional culinary arts and modern renewable energy.</p>
                    <ul class="check-list">
                        <li><i class="ri-checkbox-circle-fill"></i> Sustainable Tech Focus</li>
                        <li><i class="ri-checkbox-circle-fill"></i> Globally Diverse Recipes</li>
                        <li><i class="ri-checkbox-circle-fill"></i> Expert Kitchen Hacks</li>
                        <li><i class="ri-checkbox-circle-fill"></i> 24/7 Community Support</li>
                    </ul>
                </div>
                <div class="about-image">
                    <img src="images/philosophy.jpg" alt="FoodFusion Philosophy">
                </div>
            </div>
        </section>

        <section class="value-section">
            <div class="value-container">
                <span class="value-tagline">OUR CORE VALUES</span>
                <h2>The Ingredients of Our Success</h2>
                <div class="value-content">
                    <div class="value-box">
                        <i class="ri-restaurant-fill"></i>
                        <h3>Culinary Creativity</h3>
                        <p>We believe home cooking is an art form that should be accessible, experimental, and fun for everyone.</p>
                    </div>
                    <div class="value-box">
                        <i class="ri-team-fill"></i>
                        <h3>Community First</h3>
                        <p>FoodFusion is built by you. We value collaborative sharing through our Community Cookbook and vibrant member discussions.</p>
                    </div>
                    <div class="value-box">
                        <i class="ri-restaurant-2-fill"></i>
                        <h3>Kitchen Confidence</h3>
                        <p>We provide rigorously tested tutorials to ensure that every member, regardless of skill level, feels like a chef in their own home.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="team-section">
            <div class="team-container">
                <span class="team-tagline">OUR TEAM</span>
                <h2>Team Behind FoodFusion</h2>
                <p>A passionate team of chefs, nutritionists, and renewable energy experts working to revolutionize your kitchen.</p>

                <div class="team-grid">
                    <div class="team-member">
                        <div class="member-image"><img src="images/chan_profile.jpg" alt="Full-Stack Developer"></div>
                        <h4>Chan</h4>
                        <p>Full-Stack Developer</p>
                    </div>
                    <div class="team-member">
                        <div class="member-image"><img src="images/wendy_profile.jpg" alt="Creative Content Designer"></div>
                        <h4>Wendy</h4>
                        <p>Creative Content Designer</p>
                    </div>
                    <div class="team-member">
                        <div class="member-image"><img src="images/david_profile.jpg" alt="Senior Web Architect"></div>
                        <h4>David</h4>
                        <p>Senior Web Architect</p>
                    </div>
                </div>
            </div>
        </section>
              
    </main>

    <?php include 'footer.php'; ?>

</body>

</html>