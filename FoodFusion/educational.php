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
    <title>Educational Resources - FoodFusion</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <main class="edu-page">
        <section class="edu-header-section">
            <div class="edu-header">
                <h1>Powering a Sustainable Future</h1>
                <p>Access expert-led tutorials, technical deep-dives, and resources designed to simplify the complexities of renewable energy.</p>
            </div>
        </section>

        <div class="resource-filter">
            <a href="#downloads" class="resource-filter-btn">Downloadable Resources</a>
            <a href="#infographics" class="resource-filter-btn">Infographics</a>
            <a href="#videos" class="resource-filter-btn">Videos</a>
        </div>

        <section class="edu-resource-section" id="downloads">
            <div class="edu-resource-container">
                <h3 class="resource-header">Downloadable Resources</h3>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="error-box">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="edu-resource-grid">
                    <div class="edu-card">
                        <img src="images/guide-to-sustainable-cooking.jpg" class="edu-card-image" alt="">
                        <div class="edu-card-content">
                            <h3 class="edu-card-title">Sustainable Cooking Technologies</h3>
                            <p class="edu-card-description">They are modern cooking solutions that reduce smoke and environmental damage caused by traditional fuels.</p>
                            <div class="edu-stats">
                                <span><strong>Format:</strong> PDF Guide</span>
                                <span><strong>Pages: </strong> 2</span>
                            </div>
                            <div class="edu-card-action">
                                <a href="download.php?file=Clean_and_Sustainable_Cooking_Guide.pdf" class="edu-download-btn"><i class="ri-download-2-line"></i> Download</a>
                            </div>
                        </div>
                    </div>

                    <div class="edu-card">
                        <img src="images/renewable energy.jpg" class="edu-card-image" alt="">
                        <div class="edu-card-content">
                            <h3 class="edu-card-title">Renewable Energy for Cooking</h3>
                            <p class="edu-card-description">It refers to the use of natural, sustainable energy sources such as solar energy and renewable electricity to prepare food.</p>
                            <div class="edu-stats">
                                <span><strong>Format:</strong> PDF Guide</span>
                                <span><strong>Pages: </strong> 2</span>
                            </div>
                            <div class="edu-card-action">
                                <a href="download.php?file=Renewable_Energy_for_Cooking.pdf" class="edu-download-btn"><i class="ri-download-2-line"></i> Download</a>
                            </div>
                        </div>
                    </div>

                    <div class="edu-card">
                        <img src="images/solar_cooking.jpg" class="edu-card-image" alt="">
                        <div class="edu-card-content">
                            <h3 class="edu-card-title">Solar Electric Cooking</h3>
                            <p class="edu-card-description">Solar Electric Cooking (PV-eCook) is a modern solution that uses Solar Photovoltaic panels to generate electricity.</p>
                            <div class="edu-stats">
                                <span><strong>Format:</strong> PDF Guide</span>
                                <span><strong>Pages: </strong> 5</span>
                            </div>
                            <div class="edu-card-action">
                                <a href="download.php?file=Solar_Electric_Cooking.pdf" class="edu-download-btn"><i class="ri-download-2-line"></i> Download</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="edu-infographic-section" id="infographics">
            <div class="edu-infographic-container">
                <h3 class="infographic-header">Educational Infographics</h3>
                <div class="edu-resource-grid">
                    <div class="edu-card">
                        <img src="images/carbon_comparison.png" class="info-image" alt="Carbon Comparison">
                        <div class="edu-card-content">
                            <h3 class="edu-card-title">The Carbon Impact Map</h3>
                            <p class="edu-card-description">A visual comparison of CO2 emissions between charcoal, wood, and electric cooking.</p>
                            <a href="view_infographic.php?file=carbon_comparison.png" class="view-info-btn" target="_blank">
                                <i class="ri-eye-line"></i> View Infographic
                            </a>
                        </div>
                    </div>

                    <div class="edu-card">
                        <img src="images/anatomy_of_pv_system.png" class="info-image" alt="PV System Diagram">
                        <div class="edu-card-content">
                            <h3 class="edu-card-title">Anatomy of a PV-eCook System</h3>
                            <p class="edu-card-description">Understand how energy flows from the sun to your dinner plate through solar components.</p>
                            <a href="view_infographic.php?file=anatomy_of_pv_system.png" class="view-info-btn" target="_blank">
                                <i class="ri-eye-line"></i> View Infographic
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="edu-video-section" id="videos">
            <div class="edu-video-container">
                <h3 class="video-header">Videos</h3>
                <div class="edu-video-grid">
                    <?php
                    $videoIds = ["44Wp3WE1AHs", "RJa0ekhTFCs"];

                    foreach ($videoIds as $id): ?>
                        <div class="edu-video-item">
                            <div class="video-wrapper">
                                <iframe src="https://www.youtube.com/embed/<?= $id ?>"
                                    frameborder="0"
                                    allow="autoplay; encrypted-media"
                                    allowfullscreen loading="lazy">
                                </iframe>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    </main>
    <?php include 'footer.php'; ?>
</body>

</html>