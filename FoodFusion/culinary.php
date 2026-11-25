<?php
session_start();
include 'header.php';
require_once 'pdo.php';
$testFile = "files/Recipe_1.pdf";
if (file_exists($testFile)) {
    echo "";
} else {
    echo "";
}

$targetIds = [1, 2, 3]; 
$placeholders = implode(',', array_fill(0, count($targetIds), '?'));

$sql = "
    SELECT 
        r.recipe_id, r.title, r.description, r.image_url, r.cook_time,
        c.cuisine_type AS cuisine, 
        d.diet AS diet, 
        dl.difficulty AS difficulty
    FROM recipes r
    JOIN cuisines c ON r.cuisine_id = c.cuisine_id
    JOIN diet d ON r.diet_id = d.diet_id
    JOIN difficulty_levels dl ON r.difficulty_id = dl.difficulty_id
    WHERE r.recipe_id IN ($placeholders)
    ORDER BY FIELD(r.recipe_id, 1, 2, 3)
";

$stmt = $pdo->prepare($sql);
$stmt->execute($targetIds);
$featuredRecipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culinary Resources - FoodFusion</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <main class="culinary-page">
        <section class="culinary-header-section">
            <div class="culinary-header">
                <h1>Culinary Resources</h1>
                <p>Sharpen your skills with our curated collection of tutorials, downloadable cards, and expert kitchen hacks.</p>
            </div>
        </section>

        <section class="resource-card-section">
            <div class="resource-card-container">
                <h3 class="resource-header">Downloadable Recipe Cards</h3>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="error-box">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <div class="culinary-resource-grid">
                    <?php foreach ($featuredRecipes as $recipe): ?>
                        <div class="resource-card">
                            <img 
                                src="<?= htmlspecialchars($recipe['image_url'] ?: 'images/default-recipe.jpg'); ?>" 
                                alt="<?= htmlspecialchars($recipe['title']); ?>" 
                                class="resource-card-image">

                            <div class="resource-card-content">
                                <h3 class="resource-card-title"><?= htmlspecialchars($recipe['title']); ?></h3>

                                <div class="resource-card-tags">
                                    <span><?= htmlspecialchars($recipe['cuisine']); ?></span>
                                    <span><?= htmlspecialchars($recipe['diet']); ?></span>
                                    <span><?= htmlspecialchars($recipe['cook_time']); ?> mins</span>
                                </div>

                                <div class="resource-btn">
                                    <a href="recipe_details.php?id=<?= $recipe['recipe_id']; ?>" class="view-btn">View Recipe</a>
                                    
                                    <a href="download.php?file=Recipe_<?= $recipe['recipe_id']; ?>.pdf" class="download-btn"><i class="ri-download-2-fill"></i> Download</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="tutorial-video-section">
            <div class="tutorial-video-container">
                <h3 class="tutorial-header">Cooking Tutorials</h3>
                <div class="tutorial-video-grid">
                    <?php
                    $videoIds = ["zonTHxroL30", "Apv9PVWZ390", "tyBkdp89ZHA"];
                    foreach ($videoIds as $id): ?>
                        <div class="tutorial-video-item">
                            <div class="video-wrapper">
                                <iframe src="https://www.youtube.com/embed/<?= $id ?>"
                                    frameborder="0" allowfullscreen loading="lazy">
                                </iframe>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="instruction-video-section">
            <div class="instruction-video-container">
                <h3 class="instruction-header">Kitchen Instructional Videos</h3>
                <div class="instruction-video-grid">
                    <?php
                    $videoIds = ["hOd-iZjpSa0", "YrHpeEwk_-U"];
                    foreach ($videoIds as $id): ?>
                        <div class="instruction-video-item">
                            <div class="video-wrapper">
                                <iframe src="https://www.youtube.com/embed/<?= $id ?>"
                                    frameborder="0" allowfullscreen loading="lazy">
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