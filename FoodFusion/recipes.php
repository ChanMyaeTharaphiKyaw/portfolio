<?php
    session_start();
    include 'header.php';
    require_once 'pdo.php';

    // 1. Initialize variables
    $recipesPerPage = 12;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $recipesPerPage;

    $searchItem = $_GET['search-item'] ?? '';
    $cuisineFilter = $_GET['cuisine'] ?? 'all';
    $dietFilter = $_GET['diet'] ?? 'all';
    $difficultyFilter = $_GET['difficulty'] ?? 'all';

    // 2. Build the WHERE clause (used for both Count and Main Query)
    $whereClauses = " WHERE 1=1";
    $params = [];

    if (!empty($searchItem)) {
        $whereClauses .= " AND r.title LIKE :search";
        $params[':search'] = '%' . $searchItem . '%';
    }
    if ($cuisineFilter !== 'all') {
        $whereClauses .= " AND c.cuisine_type = :cuisine";
        $params[':cuisine'] = $cuisineFilter;
    }
    if ($dietFilter !== 'all') {
        $whereClauses .= " AND d.diet = :diet";
        $params[':diet'] = $dietFilter;
    }
    if ($difficultyFilter !== 'all') {
        $whereClauses .= " AND dl.difficulty = :diff";
        $params[':diff'] = $difficultyFilter;
    }

    // 3. COUNT QUERY (To know how many pages exist)
    $countSql = "SELECT COUNT(DISTINCT r.recipe_id) 
                 FROM recipes r
                 JOIN cuisines c ON r.cuisine_id = c.cuisine_id
                 JOIN diet d ON r.diet_id = d.diet_id
                 JOIN difficulty_levels dl ON r.difficulty_id = dl.difficulty_id 
                 $whereClauses";
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalRecipes = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecipes / $recipesPerPage);

    // 4. MAIN DATA QUERY
    $sql = "
        SELECT
            r.recipe_id, r.title, r.description, r.image_url, r.cook_time,
            c.cuisine_type AS cuisine, d.diet AS diet, dl.difficulty AS difficulty,
            AVG(rr.rating) AS avg_rating, COUNT(rr.rating) AS total_ratings
        FROM recipes r
        JOIN cuisines c ON r.cuisine_id = c.cuisine_id
        JOIN diet d ON r.diet_id = d.diet_id
        JOIN difficulty_levels dl ON r.difficulty_id = dl.difficulty_id
        LEFT JOIN recipe_ratings rr ON rr.recipe_id = r.recipe_id
        $whereClauses
        GROUP BY r.recipe_id, r.title, r.description, r.image_url, 
                 r.cook_time, c.cuisine_type, d.diet, dl.difficulty
        ORDER BY r.uploaded_at DESC
        LIMIT $recipesPerPage OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipes Collection - FoodFusion</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <main>
        <div class="recipe-container">
            <section class="recipe-hero-section">
                <div class="recipe-hero">
                    <h1 class="recipe-hero-title">Discover Recipes from Around the World</h1>
                    <p class="recipe-hero-text">Discover a thoughtfully curated collection of recipes inspired by cuisines from around the world. From comforting classics to bold international flavors, each recipe is selected to be approachable, reliable, and enjoyable for home cooks of all skill levels.</p>
                </div>
                <div class="recipe-hero-image">
                    <img src="images/recipe-hero.jpg" alt="">
                </div>
            </section>

            <form action="" method="GET">
                <div class="recipe-filter-bar">
                    <div class="filter-gp search-gp">
                        <input type="text" name="search-item" class="filter-selection recipe-search-input" placeholder="Search Recipes..." value="<?= htmlspecialchars($searchItem)?>">
                    </div>

                    <div class="filter-gp">
                        <select name="cuisine" class="filter-selection">
                            <option value="all">All Cuisines</option>
                            <?php
                            $stmt = $pdo->query("SELECT cuisine_type FROM cuisines");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $type = $row['cuisine_type'];
                                $selected = ($cuisineFilter == $type) ? 'selected' : '';
                                
                                echo '<option value="' . htmlspecialchars($type) . '" ' . $selected . '>' . 
                                    htmlspecialchars($type) . 
                                    '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="filter-gp">
                        <select name="diet" class="filter-selection">
                            <option value="all">All Diets</option>
                            <?php
                            $stmt = $pdo->query("SELECT diet FROM diet");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $type = $row['diet'];
                                $selected = ($dietFilter == $type) ? 'selected' : '';
                                
                                echo '<option value="' . htmlspecialchars($type) . '" ' . $selected . '>' . 
                                    htmlspecialchars($type) . 
                                    '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="filter-gp">
                        <select name="difficulty" class="filter-selection">
                            <option value="all">Any Difficulty</option>
                            <?php
                            $stmt = $pdo->query("SELECT difficulty FROM difficulty_levels");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $type = $row['difficulty'];
                                $selected = ($difficultyFilter == $type) ? 'selected' : '';
                                
                                echo '<option value="' . htmlspecialchars($type) . '" ' . $selected . '>' . 
                                    htmlspecialchars($type) . 
                                    '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="filter-btn">Search</button>
                    <a href="recipes.php" class="clear-btn">Clear All</a>
                </div>
            </form>
            
            <section class="recipe-grid-section">
                <?php if (empty($recipes)): ?>
                    <div class="no-recipe-result">
                        <i class="fa-solid fa-utensils"></i>
                        <h3>No recipes found matching your criteria.</h3>
                        <p>Try clearing your filters or searching for a different keyword!</p>
                        <a href="recipes.php" class="all-recipe-btn">View All Recipes</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($recipes as $recipe): ?>
                        <div class="recipe-card">
                            <a href="recipe_details.php?id=<?= $recipe['recipe_id']; ?>" class="recipe-image-link">
                                <img
                                    src="<?= htmlspecialchars($recipe['image_url'] ?: 'images/default-recipe.jpg'); ?>"
                                    alt="<?= htmlspecialchars($recipe['title']); ?>"
                                    class="recipe-image"
                                >
                            </a>

                            <div class="recipe-card-content">
                                <h3 class="recipe-card-title"><?= htmlspecialchars($recipe['title']); ?></h3>

                                <p class="recipe-card-description">
                                    <?php
                                    $desc = $recipe['description'];
                                    $firstPeriodPos = strpos($desc, '.');
                                    echo htmlspecialchars($firstPeriodPos !== false ? substr($desc, 0, $firstPeriodPos + 1) : $desc);
                                    ?>
                                </p>

                                <div class="recipe-card-tags">
                                    <span><?= htmlspecialchars($recipe['difficulty']); ?></span>
                                    <span><?= htmlspecialchars($recipe['diet']); ?></span>
                                    <span><?= htmlspecialchars($recipe['cuisine']); ?></span>
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

                                <a href="recipe_details.php?id=<?= $recipe['recipe_id']; ?>" class="recipe-card-btn">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php 
                    $queryParams = $_GET; 
                    ?>

                    <?php if ($page > 1): ?>
                        <?php $prevParams = array_merge($queryParams, ['page' => $page - 1]); ?>
                        <a href="?<?= http_build_query($prevParams) ?>" class="controls-btn">
                            <i class="ri-arrow-left-s-line"></i> Prev
                        </a>
                    <?php endif; ?>

                    <?php 
                    $range = 2;
                    for ($i = 1; $i <= $totalPages; $i++): 
                        if ($i == 1 || $i == $totalPages || ($i >= $page - $range && $i <= $page + $range)): 
                            $numberParams = array_merge($queryParams, ['page' => $i]);
                    ?>
                            <a href="?<?= http_build_query($numberParams) ?>" 
                            class="controls-btn <?= ($i == $page) ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php elseif ($i == $page - $range - 1 || $i == $page + $range + 1): ?>
                            <span class="pagination-dots">...</span>
                        <?php endif; 
                    endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <?php $nextParams = array_merge($queryParams, ['page' => $page + 1]); ?>
                        <a href="?<?= http_build_query($nextParams) ?>" class="controls-btn">
                            Next <i class="ri-arrow-right-s-line"></i>
                        </a>
                    <?php endif; ?>

                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>