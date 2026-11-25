<?php

$pageTitle = "About Page";
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

    <main class="about-page">
        <section class="about-hero">
            <div class="container">
                <h1>Our Mission: <span class="highlight">Reuniting Families</span></h1>
                <p>FindMyPet is Myanmar's dedicated platform helping lost pets find their way back home through community power and technology.</p>
            </div>
        </section>

        <section class="story-section">
            <div class="about-container">
                <div class="story-content">
                    <div class="story-text">
                        <h2 class="section-title">Why We <span class="highlight-teal">Started</span></h2>
                        <p>In Myanmar, the first 24 hours of a pet going missing are often filled with panic and disorganized social media posts. We saw a need for a centralized, searchable database where every second counts.</p>
                        <p>Founded in 2026, FindMyPet was built to bridge the gap between those who have lost a pet and the kind neighbors who have found them, specifically focusing on townships across Yangon and beyond.</p>
                    </div>
                    <div class="story-image">
                        <img src="../uploads/about_mission.jpg" alt="Pet reunion story">
                    </div>
                </div>
            </div>
        </section>

        <section class="values-section">
            <div class="about-container">
                <h2 class="center-title">Our Core Values</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <div class="value-icon"><i class="ri-community-line"></i></div>
                        <h3>Community First</h3>
                        <p>We believe that a vigilant neighborhood is a pet's best safety net.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon"><i class="ri-flashlight-line"></i></div>
                        <h3>Speed & Efficiency</h3>
                        <p>Our matching algorithms work 24/7 to find potential matches instantly.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon"><i class="ri-shield-check-line"></i></div>
                        <h3>Safe Reunions</h3>
                        <p>We provide guides on how to safely verify ownership before handing over pets.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-cta">
            <div class="cta-content">
                <h2>Help us keep Myanmar's pets safe.</h2>
                <p>Whether you've lost a pet or found one, your post could be the key to a happy ending.</p>
                <div class="cta-btns">
                    <a href="post.php" class="btn-post-pet">Post a Pet</a>
                    <a href="browse.php" class="btn-browse-listing">Browse Listings</a>
                </div>
            </div>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>

</body>

</html>