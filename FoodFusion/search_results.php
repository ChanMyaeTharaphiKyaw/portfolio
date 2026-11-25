<?php
session_start();
include 'header.php';
require_once 'pdo.php';

function formatCookTime($totalMinutes)
{
    if ($totalMinutes < 60) {
        return $totalMinutes . ' mins';
    }
    $hours = floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;

    return ($minutes > 0) ? "{$hours}h {$minutes}m" : "{$hours}h";
}

$searchTerm = isset($_GET['query']) ? trim($_GET['query']) : '';
$results = [];

if (!empty($searchTerm)) {
    try {
        $sql = "SELECT r.*, c.cuisine_type 
                FROM recipes r 
                JOIN cuisines c ON r.cuisine_id = c.cuisine_id 
                WHERE r.title LIKE :term 
                   OR r.description LIKE :term 
                   OR c.cuisine_type LIKE :term
                ORDER BY r.uploaded_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['term' => '%' . $searchTerm . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Search failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results - FoodFusion</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="search-page-container">
        <section class="results-header">
            <h2>Results for: "<?= htmlspecialchars($searchTerm) ?>"</h2>
            <p><?= count($results) ?> recipe(s) found</p>
        </section>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <section class="latest-recipe-section"> <div class="latest-container">
                <div class="latest-recipe-grid">
                    <?php if (!empty($results)): ?>
                        <?php foreach ($results as $recipe): ?>
                            <div class="recipe-item">
                                <div class="image-container">
                                    <img src="<?= htmlspecialchars($recipe['image_url'] ?: 'images/default-recipe.jpg') ?>" alt="Recipe">
                                    <div class="image-overlay">
                                        <span class="cuisine-type"><?= htmlspecialchars($recipe['cuisine_type']) ?></span>
                                    </div>
                                </div>
                                <div class="recipe-info">
                                    <div class="time-top">
                                        <span class="cook-time"><?= formatCookTime($recipe['cook_time']) ?></span>
                                        <a href="recipe_details.php?id=<?= $recipe['recipe_id'] ?>" class="view-link">View</a>
                                    </div>
                                    <h3><?= htmlspecialchars($recipe['title']) ?></h3>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-results">
                            <span><i class="fa-solid fa-utensils"></i></span>
                            <p>No recipes matched your search. Try searching for "Pasta", "Vegetarian", or "Italian".</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>