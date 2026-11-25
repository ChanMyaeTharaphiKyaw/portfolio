<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: login.php?redirect=$redirect");
    exit();
}

include 'header.php';
require_once 'pdo.php';

$sql = "
        SELECT 
            r.recipe_id AS id, 
            'recipe' AS post_type, 
            r.title, 
            r.description AS body, 
            r.image_url, 
            r.uploaded_at AS created_at, 
            r.cook_time, 
            c.cuisine_type AS cuisine, 
            u.first_name, u.last_name, u.profile,
            (SELECT COUNT(*) FROM recipe_likes rl WHERE rl.recipe_id = r.recipe_id) AS likes,
            (SELECT COUNT(*) FROM recipe_comments rc WHERE rc.recipe_id = r.recipe_id) AS comments,
            (SELECT AVG(rating) FROM recipe_ratings rr WHERE rr.recipe_id = r.recipe_id) AS rating
        FROM recipes r
        JOIN users u ON r.user_id = u.user_id
        JOIN cuisines c ON r.cuisine_id = c.cuisine_id
        
        UNION ALL
        
        SELECT 
            cp.post_id AS id, 
            'community' AS post_type,
            cp.title, 
            cp.content AS body, 
            cp.image_url, 
            cp.created_at AS created_at, 
            NULL AS cook_time, 
            NULL AS cuisine, 
            u.first_name, u.last_name, u.profile,
            (SELECT COUNT(*) FROM post_likes pl WHERE pl.post_id = cp.post_id) AS likes,
            (SELECT COUNT(*) FROM post_comments pc WHERE pc.post_id = cp.post_id) AS comments,
            NULL AS rating
        FROM community_posts cp
        JOIN users u ON cp.user_id = u.user_id
        
        ORDER BY created_at DESC
    ";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$feeds = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Cookbook - FoodFusion</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <main class="community-page">
        <div class="community-container">

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-box">
                    <i class="ri-checkbox-circle-fill"></i>
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <div class="community-header">
                <h1>Foodie Community</h1>
                <p>Discover recipes, tips, and culinary experiences shared by our members.</p>
            </div>
            <hr class="header-divider">
            <div class="community-post-container">
                <?php foreach ($feeds as $post): ?>
                    <a href="community_details.php?id=<?= $post['id'] ?>&type=<?= $post['post_type'] ?>" class="post-card-link">
                        <div class="post-card">
                            <div class="card-image-container">
                                <img src="<?= htmlspecialchars($post['image_url'] ?? 'images/default-image.jpg') ?>" alt="<?= htmlspecialchars($recipe['title']); ?>">
                            </div>

                            <div class="post-card-content">
                                <span class="category-tag tag-<?= strtolower($post['post_type']) ?>">
                                    <?= ucfirst(htmlspecialchars($post['post_type'])) ?>
                                </span>

                                <h3 class="post-card-title"><?= htmlspecialchars($post['title']) ?></h3>

                                <p class="post-card-description">
                                    <?php
                                    $desc = $post['body'];
                                    $sentences = preg_split('/(?<=[.!?])\s+/', $desc, 3);

                                    $twoSentences = implode(' ', array_slice($sentences, 0, 2));
                                    echo htmlspecialchars($twoSentences);
                                    ?>
                                </p>

                                <div class="stats-row">
                                    <span class="stat-item"><i class="ri-heart-fill"></i> <?= $post['likes'] ?></span>
                                    <span class="stat-item"><i class="ri-chat-3-line"></i> <?= $post['comments'] ?></span>
                                    <?php if ($post['post_type'] === 'recipe'): ?>
                                        <span class="stat-item rating"><i class="ri-star-fill"></i> <?= number_format($post['rating'] ?? 0, 1) ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="author-row">
                                    <img src="<?= htmlspecialchars($post['profile'] ?? 'images/default-profile.jpg') ?>" class="author-img">
                                    <span class="author-name"><?= htmlspecialchars($post['first_name'] . ' ' . $post['last_name']) ?></span>
                                    <span class="date-divider">|</span>
                                    <span class="post-date"><?= date('d F Y', strtotime($post['created_at'])) ?></span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="community-floating-actions">
                <button type="button" class="float-btn" onclick="openRecipeForm()">
                    <i class="ri-restaurant-line"></i> <span>Share a Recipe</span>
                </button>
                <button type="button" class="float-btn" onclick="openPostForm()">
                    <i class="ri-edit-line"></i> <span>Share a Post</span>
                </button>
            </div>

            <div class="community-form-container">
                <form action="submitrecipe.php" method="POST" id="recipeForm" class="community-form" enctype="multipart/form-data">
                    <span onclick="closeForm()" class="close-btn"><i class="ri-close-circle-fill"></i></span>
                    <h2>Share Your Recipe</h2>
                    <?php if (isset($_SESSION['form_errors'])): ?>
                        <div class="error-box">
                            <ul>
                                <?php foreach ($_SESSION['form_errors'] as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="recipe-title">Recipe Title</label>
                        <input type="text" id="recipe-title" name="recipe-title" placeholder="Enter recipe title" required value="<?= htmlspecialchars($_SESSION['form_data']['recipe-title'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="description">Short Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Enter short description of recipe" required><?=
                                                                                        htmlspecialchars($_SESSION['form_data']['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Cook Time</label>
                        <div class="cook-time-inputs">
                            <input type="number" name="cook-hours" min="0" value="<?= htmlspecialchars($_SESSION['form_data']['cook-hours'] ?? 0) ?>" placeholder="Hours">
                            <input type="number" name="cook-minutes" min="0" max="59" value="<?= htmlspecialchars($_SESSION['form_data']['cook-minutes'] ?? 0) ?>" placeholder="Minutes">
                        </div>
                    </div>

                    <div class="dropdown-selection">
                        <div class="form-group">
                            <label for="cuisine">Cuisine</label>
                            <select name="cuisine" id="cuisine">
                                <?php
                                $stmt = $pdo->query("SELECT cuisine_id, cuisine_type FROM cuisines");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = (isset($_SESSION['form_data']['cuisine']) && $_SESSION['form_data']['cuisine'] == $row['cuisine_id']) ? 'selected' : '';
                                    echo '<option value="' . $row['cuisine_id'] . '" ' . $selected . '>' . htmlspecialchars($row['cuisine_type']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="diet">Diet</label>
                            <select name="diet" id="diet">
                                <?php
                                $stmt = $pdo->query("SELECT diet_id, diet FROM diet");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = (isset($_SESSION['form_data']['diet']) && $_SESSION['form_data']['diet'] == $row['diet_id']) ? 'selected' : '';
                                    echo '<option value="' . $row['diet_id'] . '" ' . $selected . '>' . htmlspecialchars($row['diet']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="difficulty">Difficulty Level</label>
                            <select name="difficulty" id="difficulty">
                                <?php
                                $stmt = $pdo->query("SELECT difficulty_id, difficulty FROM difficulty_levels");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = (isset($_SESSION['form_data']['difficulty']) && $_SESSION['form_data']['difficulty'] == $row['difficulty_id']) ? 'selected' : '';
                                    echo '<option value="' . $row['difficulty_id'] . '" ' . $selected . '>' . htmlspecialchars($row['difficulty']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ingredients">Ingredients</label>
                        <textarea id="ingredients" name="ingredients" rows="4" placeholder="Enter ingredients of recipe" required><?= htmlspecialchars($_SESSION['form_data']['ingredients'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="instructions">Instructions</label>
                        <textarea id="instructions" name="instructions" rows="4" placeholder="Enter step by step instructions" required><?= htmlspecialchars($_SESSION['form_data']['instructions'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="recipe-image">Recipe Image</label>
                        <input type="file" name="recipe-image" id="recipe-image">
                    </div>
                    <button type="submit" class="submit-btn">Share Recipe</button>
                </form>

                <form action="submitpost.php" method="POST" id="postForm" class="community-form" enctype="multipart/form-data">
                    <span onclick="closeForm()" class="close-btn"><i class="ri-close-circle-fill"></i></span>
                    <h2>Share Your Tip or Experience</h2>

                    <?php if (isset($_SESSION['post_errors'])): ?>
                        <div class="error-box">
                            <ul>
                                <?php foreach ($_SESSION['post_errors'] as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="post-title">Post Title</label>
                        <input type="text" id="post-title" name="post-title" placeholder="Enter post title" required
                            value="<?= htmlspecialchars($_SESSION['post_data']['post-title'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="post-type">Post Type</label>
                        <select name="post-type" id="post-type">
                            <?php
                            $stmt = $pdo->query("SHOW COLUMNS FROM community_posts LIKE 'post_type'");
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            preg_match("/^enum\('(.*)'\)$/", $row['Type'], $matches);
                            $enum_values = explode("','", $matches[1]);
                            foreach ($enum_values as $value) {
                                $selected = (isset($_SESSION['post_data']['post-type']) && $_SESSION['post_data']['post-type'] == $value) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($value) . '" ' . $selected . '>' . ucfirst(htmlspecialchars($value)) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="post-content">Content</label>
                        <textarea id="post-content" name="post-content" rows="4" placeholder="Enter post details" required><?= htmlspecialchars($_SESSION['post_data']['post-content'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="post-image">Upload Image</label>
                        <input type="file" name="post-image" id="post-image">
                    </div>

                    <button type="submit" class="submit-btn">Share Post</button>
                </form>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        function openRecipeForm() {
            const container = document.querySelector(".community-form-container");
            const recipeForm = document.getElementById("recipeForm");
            const postForm = document.getElementById("postForm");

            container.style.display = "flex";
            recipeForm.style.display = "block";
            postForm.style.display = "none";
        }

        function openPostForm() {
            const container = document.querySelector(".community-form-container");
            const recipeForm = document.getElementById("recipeForm");
            const postForm = document.getElementById("postForm");

            container.style.display = "flex";
            postForm.style.display = "block";
            recipeForm.style.display = "none";
        }

        function closeForm() {
            document.querySelector(".community-form-container").style.display = "none";
            document.getElementById("recipeForm").style.display = "none";
            document.getElementById("postForm").style.display = "none";
        }

        document.querySelector('.community-form-container').addEventListener('click', e => {
            if (e.target === e.currentTarget) closeForm();
        });

        window.addEventListener('load', () => {
            <?php if (isset($_SESSION['form_errors'])): ?>
                openRecipeForm();
            <?php endif; ?>

            <?php if (isset($_SESSION['post_errors'])): ?>
                openPostForm();
            <?php endif; ?>
        });

        window.addEventListener('DOMContentLoaded', (event) => {
            const successBox = document.querySelector('.success-box');
            if (successBox) {
                setTimeout(() => {
                    successBox.style.transition = "opacity 0.5s ease";
                    successBox.style.opacity = "0";
                    setTimeout(() => successBox.remove(), 500);
                }, 5000);
            }
        });
    </script>
</body>

</html>

<?php
unset($_SESSION['form_errors']);
unset($_SESSION['form_data']);

unset($_SESSION['post_errors']);
unset($_SESSION['post_data']);
?>