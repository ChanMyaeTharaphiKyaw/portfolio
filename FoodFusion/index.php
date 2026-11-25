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

// Fetch Featured Recipe (Highest Score)
try {
    $feat_sql = "SELECT r.*, c.cuisine_type, 
                AVG(rr.rating) AS avg_rating, 
                COUNT(DISTINCT rr.rating_id) AS total_ratings
                FROM recipes r
                JOIN cuisines c ON r.cuisine_id = c.cuisine_id
                LEFT JOIN recipe_ratings rr ON r.recipe_id = rr.recipe_id
                LEFT JOIN recipe_likes rl ON r.recipe_id = rl.recipe_id
                GROUP BY r.recipe_id
                ORDER BY ((AVG(rr.rating) * 10) + COUNT(DISTINCT rl.rlike_id)) DESC
                LIMIT 1";

    $feat_stmt = $pdo->prepare($feat_sql);
    $feat_stmt->execute();
    $featuredRecipe = $feat_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featuredRecipe = null;
}

// Fetch Popular Recipes (Top 4)
try {
    $pop_sql = "SELECT r.*, c.cuisine_type, 
                AVG(rr.rating) AS avg_rating, 
                COUNT(DISTINCT rr.rating_id) AS total_ratings,
                ((AVG(rr.rating) * 10) + COUNT(DISTINCT rl.rlike_id)) AS score
                FROM recipes r
                JOIN cuisines c ON r.cuisine_id = c.cuisine_id
                LEFT JOIN recipe_ratings rr ON r.recipe_id = rr.recipe_id
                LEFT JOIN recipe_likes rl ON r.recipe_id = rl.recipe_id
                GROUP BY r.recipe_id
                ORDER BY score DESC
                LIMIT 4";

    $pop_stmt = $pdo->prepare($pop_sql);
    $pop_stmt->execute();
    $popularRecipes = $pop_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $popularRecipes = [];
}

// Fetch Latest Recipes (Newest 6)
try {
    $latest_sql = "SELECT r.*, c.cuisine_type 
                   FROM recipes r 
                   JOIN cuisines c ON r.cuisine_id = c.cuisine_id 
                   ORDER BY r.uploaded_at DESC 
                   LIMIT 6";

    $latest_stmt = $pdo->prepare($latest_sql);
    $latest_stmt->execute();
    $latestRecipes = $latest_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $latestRecipes = [];
}

// Fetch Upcoming Events
try {
    $event_sql = "SELECT *
                  FROM events
                  WHERE event_date >= CURDATE()
                  AND event_status = 'open'
                  ORDER BY event_date ASC";

    $event_stmt = $pdo->prepare($event_sql);
    $event_stmt->execute();
    $events = $event_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $events = [];
}

$userRegisteredEvents = [];
if (isset($_SESSION['user_id'])) {
    try {
        $reg_sql = "SELECT event_id 
                    FROM event_registrations 
                    WHERE user_id = :user_id";
        $reg_stmt = $pdo->prepare($reg_sql);
        $reg_stmt->execute(['user_id' => $_SESSION['user_id']]);
        $userRegisteredEvents = $reg_stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $userRegisteredEvents = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page - FoodFusion</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <main class="home-page-container">
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">A Home for Every Food Lover</h1>
                <p class="hero-subtitle">FoodFusion brings food lovers together to share delicious home-cooked recipes, learn culinary tips, and connect with a vibrant food community.</p>
                <div class="hero-search-container">
                    <form action="search_results.php" method="GET">
                        <input type="text" name="query" placeholder="Search recipes" required>
                        <button type="submit">Search</button>
                    </form>
                </div>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <button type="submit" class="join-btn" onclick="openJoinusPopup(); return false">Join Us</button>
                <?php endif; ?>
            </div>

            <div class="joinus-popup-container" id="joinusPopup">
                <div class="joinus-form">
                    <span class="close-btn" onclick="closeJoinusPopup()"><i class="ri-close-circle-fill"></i></span>
                    <h2>Join FoodFusion</h2>

                    <?php if (isset($_SESSION['register_errors'])): ?>
                        <div class="error-box">
                            <ul>
                                <?php foreach ($_SESSION['register_errors'] as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="register.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first-name" value="<?= htmlspecialchars($_SESSION['register_data']['first-name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last-name" value="<?= htmlspecialchars($_SESSION['register_data']['last-name'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['register_data']['email'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>

                        <div class="form-group">
                            <label>Profile Picture</label>
                            <input type="file" name="profile" id="profile">
                        </div>

                        <div class="popup-btn">
                            <button type="button" class="cancel-btn" onclick="closeJoinusPopup()">Cancel</button>
                            <button type="submit" class="join-form-btn">Join Us</button>
                        </div>
                        <p class="login-option">Already have an account? <a href="login.php">Login</a></p>

                        <input type="hidden" name="form_source" value="join_us">
                    </form>
                </div>
            </div>
        </section>

        <?php if ($featuredRecipe): ?>
            <section class="featured-section">
                <div class="featured-container">
                    <div class="featured-banner">
                        <div class="featured-image-side">
                            <img src="<?= htmlspecialchars($featuredRecipe['image_url'] ?: 'images/default-recipe.jpg') ?>" alt="Featured">
                            <div class="trending-badge">
                                Trending Now
                            </div>
                        </div>

                        <div class="featured-content-side">
                            <span class="feat-cuisine"><?= htmlspecialchars($featuredRecipe['cuisine_type']) ?></span>
                            <h2 class="feat-title"><?= htmlspecialchars($featuredRecipe['title']) ?></h2>
                            <p class="feat-description">
                                <?= htmlspecialchars(substr($featuredRecipe['description'], 0, 160)) ?>...
                            </p>

                            <div class="feat-rating">
                                <i class="fa-solid fa-star"></i>
                                <span><?= number_format($featuredRecipe['avg_rating'], 1) ?> (<?= $featuredRecipe['total_ratings'] ?> Ratings)</span>
                            </div>

                            <a href="recipe_details.php?id=<?= $featuredRecipe['recipe_id'] ?>" class="feat-view-btn">
                                View Full Recipe
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <section class="category-section">
            <div class="category-container">
                <h2 class="category-section-title">Browse by Cuisine</h2>
                <div class="category-grid">
                    <a href="recipes.php?cuisine=Chinese" class="category-card">
                        <div class="category-icon"><i class="fa-solid fa-bowl-rice"></i></div>
                        <span>Chinese</span>
                    </a>
                    <a href="recipes.php?cuisine=French" class="category-card">
                        <div class="category-icon"><i class="ri-cake-3-fill"></i></div>
                        <span>French</span>
                    </a>
                    <a href="recipes.php?cuisine=Italian" class="category-card">
                        <div class="category-icon"><i class="fa-solid fa-pizza-slice"></i></div>
                        <span>Italian</span>
                    </a>
                    <a href="recipes.php?cuisine=Japanese" class="category-card">
                        <div class="category-icon"><i class="fa-solid fa-shrimp"></i></div>
                        <span>Japanese</span>
                    </a>
                    <a href="recipes.php?cuisine=Korean" class="category-card">
                        <div class="category-icon"><i class="fa-solid fa-bowl-food"></i></div>
                        <span>Korean</span>
                    </a>
                    <a href="recipes.php?cuisine=Mexican" class="category-card">
                        <div class="category-icon"><i class="fa-solid fa-stroopwafel"></i></div>
                        <span>Mexican</span>
                    </a>
                </div>
            </div>
        </section>

        <section class="popular-recipe-section">
            <div class="popular-container">
                <h2 class="popular-section-title">Popular Recipes</h2>
                <div class="popular-recipe-grid">
                    <?php foreach ($popularRecipes as $recipe): ?>
                        <div class="popular-recipe-row">
                            <div class="popular-recipe-image">
                                <img src="<?= htmlspecialchars($recipe['image_url'] ?: 'images/default-recipe.jpg') ?>" alt="Recipe">
                            </div>

                            <div class="popular-content-right">
                                <h3><?= htmlspecialchars($recipe['title']) ?></h3>

                                <p class="popular-description">
                                    <?php
                                    $pos = strpos($recipe['description'], ".");
                                    echo htmlspecialchars(($pos !== false) ? substr($recipe['description'], 0, $pos + 1) : $recipe['description']);
                                    ?>
                                </p>

                                <div class="popular-interaction">
                                    <div class="rating-stars">
                                        <?php
                                        $avg = $recipe['avg_rating'] ? (float)$recipe['avg_rating'] : 0;
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= floor($avg)) echo '<i class="fa-solid fa-star"></i>';
                                            elseif ($i == ceil($avg) && ($avg - floor($avg)) >= 0.5) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                            else echo '<i class="fa-regular fa-star"></i>';
                                        }
                                        ?>
                                    </div>
                                    <a href="recipe_details.php?id=<?= $recipe['recipe_id'] ?>" class="view-link">View Recipe</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="latest-recipe-section">
            <div class="latest-container">
                <h2 class="latest-recipe-title">Latest Recipes</h2>
                <div class="latest-recipe-grid">
                    <?php foreach ($latestRecipes as $recipe): ?>
                        <div class="recipe-item">
                            <div class="image-container">
                                <img src="<?= htmlspecialchars($recipe['image_url'] ?: 'images/default-recipe.jpg') ?>" alt="<?= htmlspecialchars($recipe['title']) ?>">
                                <div class="image-overlay">
                                    <span class="cuisine-type"><?= htmlspecialchars($recipe['cuisine_type']) ?></span>
                                </div>
                            </div>

                            <div class="recipe-info">
                                <div class="time-top">
                                    <span class="cook-time"><?= formatCookTime($recipe['cook_time']) ?></span>
                                    <a href="recipe_details.php?id=<?= $recipe['recipe_id'] ?>" class="view-link">View Recipe</a>
                                </div>
                                <h3><?= htmlspecialchars($recipe['title']) ?></h3>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="events-section" id="events">
            <div class="events-container">
                <h2 class="event-section-title">Upcoming Events</h2>

                <?php if (isset($_SESSION['event_success'])): ?>
                    <div class="success-box">
                        <i class="ri-checkbox-circle-fill"></i>
                        <?= htmlspecialchars($_SESSION['event_success']) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['event_error'])): ?>
                    <div class="error-box">
                        <?= htmlspecialchars($_SESSION['event_error']); ?>
                    </div>
                <?php endif; ?>

                <div class="carousel-wrapper">
                    <button class="carousel-prev" onclick="moveCarousel(-1)">&#10094;</button>
                    <button class="carousel-next" onclick="moveCarousel(1)">&#10095;</button>

                    <div class="carousel-viewport">
                        <div class="carousel-track" id="eventTrack">
                            <?php if (empty($events)): ?>
                                <p style="text-align: center; width: 100%;">No upcoming events at the moment.</p>
                            <?php else: ?>

                                <?php foreach ($events as $event): ?>
                                    <div class="event-card">
                                        <div class="event-image">
                                            <img src="images/<?= htmlspecialchars($event['event_image_url'] ?: 'default-event.jpg') ?>"
                                                alt="<?= htmlspecialchars($event['event_title']) ?>">
                                        </div>
                                        <div class="event-content">
                                            <span class="event-date">
                                                <?= strtoupper(date('F d, Y', strtotime($event['event_date']))) ?>
                                            </span>
                                            <h3 class="event-title"><?= htmlspecialchars($event['event_title']) ?></h3>
                                            <p class="event-description"><?= htmlspecialchars($event['event_description']) ?></p>

                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                <?php if (in_array($event['event_id'], $userRegisteredEvents)): ?>
                                                    <button class="event-reg-btn" disabled>Already Registered</button>
                                                <?php else: ?>
                                                    <form method="POST" action="register_event.php">
                                                        <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                                                        <button type="submit" class="event-reg-btn">Register Now</button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="login.php?redirect=index.php%23events" class="event-log-btn">Login to Register</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <section class="signup-section">
                <div class="signup-banner">
                    <div class="text-side">
                        <h2>Ready to share your own recipes?</h2>
                        <p>Sign up now to upload your own recipes and join the FoodFusion community.</p>
                    </div>
                    <div class="button-side">
                        <button class="signup-now-btn" onclick="openSignupPopup()">Sign Up Now</button>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <div class="signup-popup-container" id="signupPopup">
            <div class="signup-form">
                <span class="close-btn" onclick="closeSignupPopup()"><i class="ri-close-circle-fill"></i></span>
                <h2>Sign Up Now</h2>
                <?php if (isset($_SESSION['register_errors'])): ?>
                    <div class="error-box">
                        <ul>
                            <?php foreach ($_SESSION['register_errors'] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first-name" value="<?= htmlspecialchars($_SESSION['register_data']['first-name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last-name" value="<?= htmlspecialchars($_SESSION['register_data']['last-name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['register_data']['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>

                    <div class="form-group">
                        <label>Profile Picture</label>
                        <input type="file" name="profile" id="profile">
                    </div>

                    <div class="popup-btn">
                        <button type="button" class="cancel-btn" onclick="closeSignupPopup()">Cancel</button>
                        <button type="submit" class="register-form-btn">Sign Up</button>
                    </div>
                    <p class="login-option">Already have an account? <a href="login.php?redirect=<?= urlencode($currentPage) ?>">Login</a></p>

                    <input type="hidden" name="form_source" value="sign_up_now">
                </form>

            </div>
        </div>

    </main>

    <?php include 'footer.php'; ?>

    <script>
        function openJoinusPopup() {
            const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;

            document.getElementById("joinusPopup").style.display = "flex";
            document.body.style.overflow = "hidden";

            document.body.style.paddingRight = scrollBarWidth + "px";
        }

        function closeJoinusPopup() {
            document.getElementById("joinusPopup").style.display = "none";
            document.body.style.overflow = "auto";
            document.body.style.paddingRight = "0px";
        }

        function openSignupPopup() {
            const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;

            document.getElementById("signupPopup").style.display = "flex";
            document.body.style.overflow = "hidden";

            document.body.style.paddingRight = scrollBarWidth + "px";
        }

        function closeSignupPopup() {
            document.getElementById("signupPopup").style.display = "none";
            document.body.style.overflow = "auto";
            document.body.style.paddingRight = "0px";
        }

        window.addEventListener('load', () => {
            <?php if (isset($_SESSION['register_errors'])): ?>
                <?php
                $source = $_SESSION['register_source'] ?? 'header_register';
                if ($source === 'join_us'): ?>
                    openJoinusPopup();
                <?php elseif ($source === 'sign_up_now'): ?>
                    openSignupPopup();
                <?php elseif ($source === 'header_register'): ?>
                <?php endif; ?>
            <?php endif; ?>
        });


        window.onclick = function(event) {
            let signupModal = document.getElementById("signupPopup");
            let joinusModal = document.getElementById("joinusPopup");

            if (event.target == signupModal) closeSignupPopup();
            if (event.target == joinusModal) closeJoinusPopup();
        }

        let currentEventIndex = 0;

        function moveCarousel(direction) {
            const track = document.getElementById('eventTrack');
            const cards = document.querySelectorAll('.event-card');
            const cardCount = cards.length;

            let visibleCards;
            if (window.innerWidth > 1024) visibleCards = 3;
            else if (window.innerWidth > 768) visibleCards = 2;
            else visibleCards = 1;

            const maxIndex = cardCount - visibleCards;

            currentEventIndex += direction;

            if (currentEventIndex < 0) {
                currentEventIndex = maxIndex;
            } else if (currentEventIndex > maxIndex) {
                currentEventIndex = 0;
            }

            const cardWidth = cards[0].offsetWidth;
            const gap = 20;
            const moveDistance = currentEventIndex * (cardWidth + gap);

            track.style.transform = `translateX(-${moveDistance}px)`;
        }

        window.addEventListener('resize', () => {
            currentEventIndex = 0;
            document.getElementById('eventTrack').style.transform = 'translateX(0)';
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
unset($_SESSION['register_errors']);
unset($_SESSION['register_data']);
unset($_SESSION['register_source']);
unset($_SESSION['event_success']);
unset($_SESSION['event_error']);
?>