<?php
session_start();
include 'header.php';
require_once 'pdo.php';

$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = $_GET['type'] ?? '';

if (!$id || !in_array($type, ['recipe', 'community'])) {
    echo "Invalid request.";
    exit;
}

if ($type === 'recipe') {

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
                d.diet,
                dl.difficulty,
                r.ingredients,
                r.instructions,
                u.first_name,
                u.last_name,
                u.profile
            FROM recipes r
            JOIN users u ON r.user_id = u.user_id
            JOIN cuisines c ON r.cuisine_id = c.cuisine_id
            JOIN diet d ON r.diet_id = d.diet_id
            JOIN difficulty_levels dl ON r.difficulty_id = dl.difficulty_id
            WHERE r.recipe_id = :id
            LIMIT 1
        ";
} else {

    $sql = "
            SELECT 
                cp.post_id AS id,
                'community' AS post_type,
                cp.title,
                cp.content AS body,
                cp.image_url,
                cp.created_at AS created_at,
                NULL AS cook_time,
                NULL AS cuisine,
                NULL AS diet,
                NULL AS difficulty,
                NULL AS ingredients,
                NULL AS instructions,
                u.first_name,
                u.last_name,
                u.profile
            FROM community_posts cp
            JOIN users u ON cp.user_id = u.user_id
            WHERE cp.post_id = :id
            LIMIT 1
        ";
}

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Post not found.";
    exit;
}

function formatCookTime($totalMinutes)
{
    if ($totalMinutes < 60) {
        return $totalMinutes . ' mins';
    }
    $hours = floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;

    return ($minutes > 0) ? "{$hours}h {$minutes}m" : "{$hours}h";
}
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
    <main class="community-details-page">
        <div class="community-details-container">
            <div class="community-post" data-type="<?= $post['post_type'] ?>">
                <div class="post-header">
                    <div class="back-navigation">
                        <a href="community.php" class="back-btn">
                            <i class="ri-arrow-left-line"></i>
                        </a>
                    </div>

                    <img class="profile-pic"
                        src="<?= htmlspecialchars($post['profile'] ?? 'images/default-profile.jpg') ?>"
                        alt="User">

                    <div class="user-information">
                        <span class="username">
                            <?= htmlspecialchars($post['first_name'] . ' ' . $post['last_name']) ?>
                        </span>
                        <span class="time">
                            <?= date('M d, Y • h:i A', strtotime($post['created_at'])) ?>
                        </span>
                    </div>
                </div>

                <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>

                <?php
                if ($post['post_type'] === 'recipe') {
                    $like_sql = "SELECT COUNT(*) FROM recipe_likes WHERE recipe_id = :id";
                } else {
                    $like_sql = "SELECT COUNT(*) FROM post_likes WHERE post_id = :id";
                }
                $stmt = $pdo->prepare($like_sql);
                $stmt->execute([':id' => $post['id']]);
                $like_count = $stmt->fetchColumn();

                $is_liked = false;
                if (isset($_SESSION['user_id'])) {
                    if ($post['post_type'] === 'recipe') {
                        $check = $pdo->prepare("SELECT 1 FROM recipe_likes WHERE recipe_id = ? AND user_id = ?");
                    } else {
                        $check = $pdo->prepare("SELECT 1 FROM post_likes WHERE post_id = ? AND user_id = ?");
                    }
                    $check->execute([$post['id'], $_SESSION['user_id']]);
                    $is_liked = $check->fetch() ? true : false;
                }

                if ($post['post_type'] === 'recipe') {
                    $comment_sql = "SELECT COUNT(*) FROM recipe_comments WHERE recipe_id = :id";
                } else {
                    $comment_sql = "SELECT COUNT(*) FROM post_comments WHERE post_id = :id";
                }
                $stmt = $pdo->prepare($comment_sql);
                $stmt->execute([':id' => $post['id']]);
                $comment_count = $stmt->fetchColumn();

                $avg_rating = 0;
                $rating_count = 0;

                if ($post['post_type'] === 'recipe') {
                    $stmt = $pdo->prepare("
                        SELECT 
                            ROUND(AVG(rating), 1) AS avg_rating,
                            COUNT(*) AS rating_count
                        FROM recipe_ratings
                        WHERE recipe_id = :id
                    ");
                    $stmt->execute([':id' => $post['id']]);
                    $rating = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($rating) {
                        $avg_rating   = (float) $rating['avg_rating'];
                        $rating_count = (int) $rating['rating_count'];
                    }
                }
                ?>

                <div class="post-actions">
                    <button class="like-btn <?= $is_liked ? 'liked' : ''; ?>"
                        onclick="toggleLike(<?= $post['id']; ?>, '<?= $post['post_type']; ?>', this)">
                        <i class="<?= $is_liked ? 'ri-heart-3-fill' : 'ri-heart-3-line'; ?>"></i>
                        <span class="like-count"><?= $like_count; ?></span>
                    </button>

                    <button class="comment-btn">
                        <i class="ri-chat-3-line"></i>
                        <span class="comment-count"><?= $comment_count; ?></span>
                    </button>

                    <?php if ($post['post_type'] === 'recipe'): ?>
                        <div class="recipe-rating-display">
                            <div class="rating-stars">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= floor($avg_rating)) {
                                        echo '<i class="fa-solid fa-star"></i>';
                                    } elseif ($i == ceil($avg_rating) && ($avg_rating - floor($avg_rating)) >= 0.5) {
                                        echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                    } else {
                                        echo '<i class="fa-regular fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <span class="average-rating"><?= number_format($avg_rating, 1) ?></span>
                            <span class="rating-count">(<?= $rating_count ?>)</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="post-content">
                    <?= nl2br(htmlspecialchars($post['body'])) ?>
                </div>

                <?php
                $display_image = !empty($post['image_url']) ? $post['image_url'] : 'images/default-image.jpg';
                ?>

                <div class="post-image-container">
                    <img class="post-image"
                        src="<?= htmlspecialchars($display_image) ?>"
                        alt="<?= htmlspecialchars($post['title']) ?>">
                </div>

                <?php if ($post['post_type'] === 'recipe'): ?>
                    <div class="recipe-meta-grid">
                        <div class="meta-item">
                            <span class="meta-label">Time:</span>
                            <span class="meta-value"><?= formatCookTime($post['cook_time']) ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Level:</span>
                            <span class="meta-value"><?= htmlspecialchars($post['difficulty']) ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Cuisine:</span>
                            <span class="meta-value"><?= htmlspecialchars($post['cuisine']) ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Cook:</span>
                            <span class="meta-value"><?= htmlspecialchars($post['diet']) ?></span>
                        </div>
                    </div>

                    <div class="post-grid">
                        <div class="post-ingredients">
                            <h3>Ingredients</h3>
                            <ul class="ingredients-list">
                                <?php
                                $ingredients = explode("\n", trim($post['ingredients']));
                                foreach ($ingredients as $item):
                                    if (empty(trim($item))) continue; ?>
                                    <li><?= htmlspecialchars($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="post-instructions">
                            <h3>Instructions</h3>
                            <div class="steps-container">
                                <?php
                                $stepCount = 1;
                                $instructions = explode("\n", trim($post['instructions']));
                                foreach ($instructions as $step):
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
                        </div>
                    </div>

                <?php endif; ?>

                <div class="rating-container">
                    <?php if ($post['post_type'] === 'recipe'): ?>
                        <div class="recipe-rating">
                            <h4>Rating:</h4>
                            <?php
                            $user_rating = 0;
                            if (isset($_SESSION['user_id'])) {
                                $stmt = $pdo->prepare("SELECT rating FROM recipe_ratings WHERE recipe_id = ? AND user_id = ?");
                                $stmt->execute([$post['id'], $_SESSION['user_id']]);
                                $user_rating = $stmt->fetchColumn() ?: 0;
                            }

                            ?>
                            <form method="POST" action="rate_recipe.php">
                                <input type="hidden" name="recipe_id" value="<?= $post['id'] ?>">

                                <div class="star-rating">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="star-<?= $i ?>-<?= $post['id'] ?>" name="rating" value="<?= $i ?>" <?= $user_rating == $i ? 'checked' : '' ?>>
                                        <label for="star-<?= $i ?>-<?= $post['id'] ?>"><i class="fa-solid fa-star"></i></label>
                                    <?php endfor; ?>
                                </div>

                                <button type="submit">Submit Rating</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="comment-container">
                    <h4>Comments</h4>
                    <div class="comment-list">
                        <?php
                        if ($post['post_type'] === 'recipe') {
                            $sql = "
                                        SELECT rc.comment_text, rc.created_at, u.first_name, u.last_name, u.profile
                                        FROM recipe_comments rc
                                        JOIN users u ON rc.user_id = u.user_id
                                        WHERE rc.recipe_id = :post_id
                                        ORDER BY rc.created_at ASC
                                    ";
                        } else {
                            $sql = "
                                        SELECT pc.comment_text, pc.created_at, u.first_name, u.last_name, u.profile
                                        FROM post_comments pc
                                        JOIN users u ON pc.user_id = u.user_id
                                        WHERE pc.post_id = :post_id
                                        ORDER BY pc.created_at ASC
                                    ";
                        }
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([':post_id' => $post['id']]);
                        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($comments) {
                            foreach ($comments as $c): ?>
                                <div class="user-comment">
                                    <img src="<?= htmlspecialchars($c['profile'] ?? 'images/default-profile.jpg'); ?>" class="comment-profile" alt="User">
                                    <div class="comment-content">
                                        <strong><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']); ?></strong>
                                        <p><?= nl2br(htmlspecialchars($c['comment_text'])); ?></p>
                                        <small><?= date('M d, Y h:i A', strtotime($c['created_at'])); ?></small>
                                    </div>
                                </div>
                        <?php endforeach;
                        } else {
                            echo "<p>No comments yet.</p>";
                        }
                        ?>
                    </div>
                </div>
                <div class="comment-section">
                    <form method="POST" action="comment.php" class="comment-form">
                        <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
                        <input type="hidden" name="post_type"
                            value="<?= $post['post_type'] === 'recipe' ? 'recipe' : 'community'; ?>">
                        <textarea name="comment" required placeholder="Add a comment..."></textarea>
                        <button type="submit">Post</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        function toggleLike(id, type, btnElement) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('type', type === 'recipe' ? 'recipe' : 'community');

            fetch('like.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const countSpan = btnElement.querySelector('.like-count');
                        const heartIcon = btnElement.querySelector('i');

                        countSpan.innerText = data.new_count;

                        if (data.action === 'liked') {
                            heartIcon.classList.replace('ri-heart-3-line', 'ri-heart-3-fill');
                            btnElement.classList.add('liked');
                        } else {
                            heartIcon.classList.replace('ri-heart-3-fill', 'ri-heart-3-line');
                            btnElement.classList.remove('liked');
                        }
                    } else {
                        alert(data.message); 
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>

</html>