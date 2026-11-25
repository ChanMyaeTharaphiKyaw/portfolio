<?php
    include 'header.php';
    require_once 'pdo.php';

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $sql = "SELECT r.*, c.cuisine_type AS cuisine, d.diet, dl.difficulty,
                   AVG(rr.rating) AS avg_rating, COUNT(rr.rating) AS total_ratings
            FROM recipes r
            JOIN cuisines c ON r.cuisine_id = c.cuisine_id
            JOIN diet d ON r.diet_id = d.diet_id
            JOIN difficulty_levels dl ON r.difficulty_id = dl.difficulty_id
            LEFT JOIN recipe_ratings rr ON rr.recipe_id = r.recipe_id
            WHERE r.recipe_id = :id
            GROUP BY r.recipe_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipe) {
        die("Recipe not found!");
    }

    function formatCookTime($totalMinutes) {
        if ($totalMinutes < 60) {
            return $totalMinutes . ' mins';
        }
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        return ($minutes > 0) ? "{$hours}hr {$minutes}m" : "{$hours}hr";
    }

    $ingredientsList = explode("\n", $recipe['ingredients']);
    $instructionsList = explode("\n", $recipe['instructions']);

    $avg = $recipe['avg_rating'] ?? 0;
    $count = $recipe['total_ratings'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipes - FoodFusion</title>
</head>
<body>
    <main class="recipe-detail-container">
        <nav class="nav-path">
            <a href="index.php">Home</a> / <a href="recipes.php">Recipes</a> / <span><?= htmlspecialchars($recipe['title']) ?></span>
        </nav>

        <section class="recipe-header">
            <div class="main-recipe-image">
                <img src="<?= htmlspecialchars($recipe['image_url'] ?: 'images/default-recipe.jpg') ?>" alt="<?= htmlspecialchars($recipe['title']) ?>">
            </div>

            <div class="recipe-header-content">
                <h1 class="recipe-title"><?= htmlspecialchars($recipe['title']) ?></h1>
                
                <div class="recipe-meta-grid">
                    <div class="meta-item">
                        <span class="meta-label">Time:</span>
                        <span class="meta-value"><?= formatCookTime($recipe['cook_time']) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Level:</span>
                        <span class="meta-value"><?= htmlspecialchars($recipe['difficulty']) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Cuisine:</span>
                        <span class="meta-value"><?= htmlspecialchars($recipe['cuisine']) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Cook:</span>
                        <span class="meta-value"><?= htmlspecialchars($recipe['diet']) ?></span>
                    </div>
                </div>

                <div class="recipe-rating-display">
                    <div class="rating-stars">
                        <?php
                        $avg = $recipe['avg_rating'] ? (float)$recipe['avg_rating'] : 0;
                        $count = (int)$recipe['total_ratings'];

                        if ($count > 0) {
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= floor($avg)) {
                                    echo '<i class="fa-solid fa-star"></i>';
                                } elseif ($i == ceil($avg) && ($avg - floor($avg)) >= 0.5) {
                                    echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                } else {
                                     echo '<i class="fa-regular fa-star"></i>';
                                }
                            }
                        } else {
                            echo str_repeat('<i class="fa-regular fa-star"></i>', 5);
                        }
                        ?>
                    </div>
                                    
                    <?php if ($count > 0): ?>
                        <span class="average-rating"><?= number_format($avg, 1) ?></span>
                        <span class="rating-count">(<?= $count ?> ratings)</span>
                    <?php else: ?>
                        <span class="rating-count">No rating yet</span>
                    <?php endif; ?>
                </div>

                <div class="recipe-description-container">
                    <h3>Description</h3>
                    <p><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
                </div>
            </div>
        </section>

        <section class="recipe-content-grid">
            <aside class="ingredients-section">
                <h3>Ingredients</h3>
                <ul class="ingredients-list">
                    <?php foreach ($ingredientsList as $item): 
                        $cleanItem = trim($item);
                        if (empty($cleanItem)) continue; 
                    ?>
                        <li>
                            <span class="ingredient-text">
                                <?= htmlspecialchars($cleanItem) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <article class="instructions-section">
                <h3>Instructions</h3>
                <div class="steps-container">
                    <?php 
                    $stepCount = 1; 
                    foreach ($instructionsList as $step): 
                        $cleanStep = trim($step);
                        if (empty($cleanStep)) continue; 
                    ?>
                        <div class="step-card">
                            <span class="step-index"><?= $stepCount++ ?>.</span>
                            <div class="step-text">
                                <p><?= htmlspecialchars($cleanStep) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>