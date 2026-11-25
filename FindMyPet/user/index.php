<?php

$pageTitle = "Home Page";
require_once "../database/pdo.php";
require_once "../database/functions.php";
include '../includes/header.php';

// Retrieve Latest Posts and Blogs
$recentPosts = getLatestPosts($pdo, 4);
$blogs = getLatestBlogs($pdo, 3);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>
    
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        :root {
            --color-primary: #1f7a5f;
            --color-primary-dark: #165d48;
            --color-orange: #f36f1c;
            --color-orange-hover: #d65a10;
            --color-bg: #f8fafc;
            --color-text: #1f2937;
            --color-text-muted: #6b7280;
        }

        .hero-section {
            background-color: var(--color-primary);
            padding: 80px 0;
            color: #ffffff;
            position: relative;
        }

        .hero-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 30px;
        }

        .hero-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
        }

        .hero-text { flex: 1; }
        .hero-image { flex: 1; }

        .hero-content h1 {
            font-size: 48px;
            font-family: "Poppins", sans-serif;
            line-height: 1.2;
            margin-bottom: 20px;
            font-weight: 800;
        }

        .highlight {
            color: var(--color-orange);
        }

        .hero-content p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .hero-image img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 20px;
        }

        /* Feature Section */
        .features-section {
            padding: 80px 20px;
            background-color: #f9f9f9;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 36px;
            font-weight: 800;
            font-family: "Poppins", sans-serif;
            margin-bottom: 10px;
            color: var(--color-primary);
            text-align: center;
        }

        .section-subtitle {
            font-size: 16px;
            color: var(--color-primary);
            margin-bottom: 45px;
            text-align: center;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 35px;
            text-align: center;
        }

        .feature-card {
            background-color: #ffffff;
            padding: 25px 20px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }

        .feature-icon {
            font-size: 36px;
            color: var(--color-orange);
            margin-bottom: 18px;
        }

        .feature-card h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .feature-card p {
            font-size: 14px;
            line-height: 1.6;
            color: #555;
        }

        /* Recent Posts Section */
        .recent-posts {
            padding: 80px 20px;
            background-color: #ffffff;
        }

        .recent-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .recent-posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 40px;
        }

        .recent-post-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            border: 1px solid #eee;
        }

        .recent-post-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .post-image-container {
            position: relative;
            height: 230px;
            overflow: hidden;
        }

        .post-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .status-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            color: white;
            z-index: 2;
        }

        .status-badge.lost { background: #ef4444; }
        .status-badge.found { background: #3b82f6; }

        .post-content {
            padding: 1rem 1.2rem;
        }

        .post-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .post-info {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.65rem;
            color: var(--color-text-muted);
            font-size: 0.9rem;
        }

        .post-info i {
            color: var(--color-primary);
            font-size: 1.1rem;
            width: 20px;
        }

        .center-btn {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }

        .browse-all-btn {
            padding: 12px 30px;
            border-radius: 30px;
            background-color: var(--color-primary);
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .browse-all-btn:hover {
            background-color: var(--color-primary-dark);
        }

        /* Latest Blog Section */
        .blog-section {
            padding: 80px 20px;
            background-color: var(--color-bg);
        }

        .blog-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .blog-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .blog-img {
            height: 200px;
            overflow: hidden;
        }

        .blog-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .blog-card:hover .blog-img img {
            transform: scale(1.05);
        }

        .blog-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .blog-tag {
            display: inline-block;
            background-color: #e6f4f1;
            color: var(--color-primary);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            width: fit-content;
        }

        .blog-content h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }

        .blog-content p {
            color: var(--color-text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .read-more {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--color-primary);
            font-weight: 600;
            text-decoration: none;
            transition: gap 0.2s ease;
        }

        .read-more:hover {
            gap: 0.75rem;
        }

        /* Call To Action Buttons */
        .cta-section {
            background-color: var(--color-primary);
            padding: 80px 20px;
            text-align: center;
            color: #ffffff;
        }

        .cta-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .cta-container h2 {
            font-size: 36px;
            font-family: "Poppins", sans-serif;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .cta-container p {
            font-size: 18px;
            margin-bottom: 35px;
            opacity: 0.95;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .cta-lost-btn, .cta-found-btn {
            padding: 14px 32px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: opacity 0.3s ease, transform 0.2s ease;
        }

        .cta-lost-btn {
            background-color: var(--color-orange);
            color: #ffffff;
        }

        .cta-lost-btn:hover {
            background-color: var(--color-orange-hover);
        }

        .cta-found-btn {
            background-color: transparent;
            border: 2px solid #ffffff;
            color: #ffffff;
        }

        .cta-found-btn:hover {
            background-color: #ffffff;
            color: var(--color-primary);
        }

        .empty-state-message {
            text-align: center;
            grid-column: 1 / -1;
            color: #666;
            font-style: italic;
            padding: 2rem;
        }

        .text-link {
            color: var(--color-primary);
            text-decoration: underline;
        }
        @media (max-width: 992px) {
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .recent-posts-grid { grid-template-columns: repeat(2, 1fr); }
            .hero-content { flex-direction: column; text-align: center; gap: 30px; }
            .hero-content h1 { font-size: 36px; }
        }

        @media (max-width: 768px) {
            .features-grid { grid-template-columns: 1fr; }
            .section-title { font-size: 28px; }
            .section-subtitle { font-size: 14px; }
            .feature-card { padding: 25px 15px; }
            .recent-posts-grid { grid-template-columns: 1fr; }
            .blog-grid { grid-template-columns: 1fr; }
            .recent-posts { padding: 60px 15px; }
        }

        @media (max-width: 576px) {
            .cta-container h2 { font-size: 28px; }
            .cta-container p { font-size: 16px; }
            .cta-buttons { flex-direction: column; align-items: center; }
            .cta-lost-btn, .cta-found-btn { width: 100%; max-width: 280px; text-align: center; }
        }
    </style>
</head>

<body>
    <main class="home-page-container">

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-container">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1>Find Your Lost Pets <span class="highlight">Home</span></h1>
                        <p>
                            A centralized lost and found pet platform dedicated to Myanmar.
                            Report sightings instantly, browse community listings, and increase the
                            chances of a happy reunion.
                        </p>
                    </div>

                    <div class="hero-image">
                        <img src="../uploads/hero-dog-and-cat.jpg" alt="Happy Dog and Cat" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature Section -->
        <section class="features-section">
            <div class="features-container">
                <h2 class="section-title">
                    How FindMyPet <span class="highlight">Works</span>
                </h2>
                <p class="section-subtitle">
                    A simple process to report, search, and reunite lost pets across Myanmar.
                </p>

                <div class="features-grid">
                    <div class="feature-card">
                        <i class="ri-file-text-line feature-icon"></i>
                        <h3>Post a Pet</h3>
                        <p>Post details of a lost or found pet with photos, location, and date.</p>
                    </div>
                    <div class="feature-card">
                        <i class="ri-search-line feature-icon"></i>
                        <h3>Search & Filter</h3>
                        <p>Use advanced search to filter by location, pet type, or name.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fa-regular fa-bell feature-icon"></i>
                        <h3>Get Notified</h3>
                        <p>Receive instant notifications for matching lost or found posts.</p>
                    </div>
                    <div class="feature-card">
                        <i class="ri-team-line feature-icon"></i>
                        <h3>Community Support</h3>
                        <p>Engage with fellow pet lovers, comment, and share posts to help reunite pets faster.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Posts Section -->
        <section class="recent-posts">
            <div class="recent-container">
                <div class="section-header">
                    <h2 class="section-title">
                        Recent <span class="highlight">Lost & Found</span> Pets
                    </h2>
                    <p class="section-subtitle">
                        Latest reports from our community. Help reunite them with their families.
                    </p>
                </div>

                <div class="recent-posts-grid">
                    <?php if (empty($recentPosts)): ?>
                        <p class="empty-state-message">
                            No recent posts found. Be the first to <a href="create_post.php" class="text-link">post a pet</a>!
                        </p>
                    <?php else: ?>
                        <?php foreach ($recentPosts as $post):
                            $postTypeLower = strtolower($post['post_type']);
                            $badgeClass     = ($postTypeLower == 'lost') ? 'lost' : 'found';
                            $dateLabel      = ($postTypeLower == 'lost') ? 'Last seen:' : 'Found on:';
                            
                            $imagePath = !empty($post['pet_image']) ? $post['pet_image'] : 'placeholder.jpg';
                        ?>
                            <div class="recent-post-card" onclick="window.location.href='post_details.php?id=<?= $post['id'] ?>'">
                                <div class="post-image-container">

                                    <img src="../<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($post['pet_name']) ?>" />
                                    <span class="status-badge <?= $badgeClass ?>"><?= strtoupper($post['post_type']) ?></span>
                                </div> 
                                <div class="post-content">
                                    <h3 class="post-title">
                                        <?= ucfirst($postTypeLower) ?> <?= htmlspecialchars($post['pet_type_name']) ?> - <?= htmlspecialchars($post['pet_name']) ?>
                                    </h3>
                                    <div class="post-info">
                                        <i class="ri-map-pin-line"></i>
                                        <span><?= htmlspecialchars($post['township_name']) ?>, <?= htmlspecialchars($post['city_name']) ?></span>
                                    </div>
                                    <div class="post-info">
                                        <i class="ri-calendar-line"></i>
                                        <span><?= $dateLabel ?> <?= formatDate($post['last_seen_date']) ?></span>
                                    </div>
                                    <div class="post-info">
                                        <i class="ri-time-line"></i>
                                        <span>Posted: <?= formatDate($post['created_at']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="center-btn">
                    <a href="browse_posts.php" class="browse-all-btn">View All Posts</a>
                </div>
            </div>
        </section>

        <!-- Blog Sections -->
        <section class="blog-section">
            <div class="blog-container">
                <div class="section-header">
                    <h2 class="section-title">
                        Pet <span class="highlight">Safety Guides</span>
                    </h2>
                    <p class="section-subtitle">
                        Expert advice on finding lost pets and keeping your furry friends safe in Myanmar.
                    </p>
                </div>

                <div class="blog-grid">
                    <?php if (empty($blogs)): ?>
                        <p class="empty-state-message">
                            No articles published yet. Check back soon!
                        </p>
                    <?php else: ?>
                        <?php foreach ($blogs as $blog): 
                            $blogImagePath = !empty($blog['image']) ? $blog['image'] : 'placeholder.jpg';
                        ?>
                            <article class="blog-card">
                                <div class="blog-img">
                                    <img src="../<?= htmlspecialchars($blogImagePath) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" />
                                </div>
                                <div class="blog-content">
                                    <span class="blog-tag"><?= htmlspecialchars($blog['category'] ?? 'General') ?></span>
                                    <h3><?= htmlspecialchars($blog['title']) ?></h3>
                                    <p>
                                        <?= htmlspecialchars(substr(strip_tags($blog['content']), 0, 120)) ?>...
                                    </p>
                                    <a href="blog_details.php?id=<?= $blog['id'] ?>" class="read-more">Read Article <i class="ri-arrow-right-line"></i></a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="cta-section">
            <div class="cta-container">
                <h2>Lost or Found a Pet?</h2>
                <p>
                    Don't wait. Every minute matters. Post now and help reunite pets with their families.
                </p>

                <div class="cta-buttons">
                    <a href="create_post.php" class="cta-lost-btn">Post Lost Pet</a>
                    <a href="create_post.php" class="cta-found-btn">Post Found Pet</a>
                </div>
            </div>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>

</body>
</html>