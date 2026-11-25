<?php
$pageTitle = "Blog Page";
require_once "../database/pdo.php";
require_once "../database/functions.php";
include '../includes/header.php';

// Pagination 
$perPage = 9;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

// Category Filtering
$category = $_GET['category'] ?? 'all';
$allowedCategories = ['emergency', 'health', 'prevention', 'community'];

// Validate category
if ($category !== 'all' && !in_array($category, $allowedCategories)) {
    $category = 'all';
}

// Retrieve Blog
$totalRecords = getBlogsCount($pdo, $category);
$totalPages = ceil($totalRecords / $perPage) ?: 1;

if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
    $offset = ($currentPage - 1) * $perPage;
}

$blogs = getBlogs($pdo, $category, $perPage, $offset);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet" />
    
    <style>

        .blog-hero {
            background-color: #ffffff;
            padding: 60px 20px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 40px;
        }

        .blog-hero-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-title {
            font-size: 36px;
            font-weight: 800;
            color: #1f7a5f;
            margin-bottom: 15px;
            font-family: 'Poppins', sans-serif;
        }

        .page-subtitle {
            color: #64748b;
            max-width: 600px;
            margin: 0 auto 30px;
            line-height: 1.6;
            font-size: 16px;
        }

        /* Category Filters */
        .blog-filters {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            border-radius: 30px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background-color: #1f7a5f;
            color: #ffffff;
            border-color: #1f7a5f;
        }

        /* Blog Grid Section */
        .blog-grid-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 24px;
        }

        @media (min-width: 768px) {
            .blog-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .blog-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 64px 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .empty-state-icon {
            font-size: 3.5rem;
            color: #d1d5db;
            margin-bottom: 16px;
        }

        .empty-state-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .empty-state-text {
            color: #6b7280;
        }

        /* Blog Card */
        .blog-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .blog-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .blog-card-image-wrapper {
            position: relative;
            height: 192px;
            background-color: #f3f4f6;
            overflow: hidden;
        }

        .blog-card-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .blog-card:hover .blog-card-image {
            transform: scale(1.05);
        }

        .blog-card-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            padding: 4px 12px;
            background-color: rgba(255, 255, 255, 0.9);
            color: #1f7a5f;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 4px;
        }

        .blog-card-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .blog-card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .blog-card-excerpt {
            color: #4b5563;
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 16px;
            flex-grow: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .blog-card-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            color: #6b7280;
            margin-bottom: 16px;
            padding-top: 12px;
            border-top: 1px solid #f3f4f6;
        }

        .blog-card-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .blog-card-link {
            display: inline-flex;
            align-items: center;
            color: #1f7a5f;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .blog-card-link i {
            margin-left: 4px;
            transition: transform 0.2s ease;
        }

        .blog-card-link:hover {
            color: #f36f1c;
        }

        .blog-card-link:hover i {
            transform: translateX(4px);
        }

        /* Pagination Wrapper */
        .pagination-wrapper {
            margin-top: 48px;
            padding: 12px 16px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        @media (min-width: 640px) {
            .pagination-wrapper {
                flex-direction: row;
            }
        }

        .pagination-info {
            font-size: 0.875rem;
            color: #4b5563;
        }

        .pagination-info .font-medium {
            font-weight: 500;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .pagination-btn {
            padding: 6px 12px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            background-color: white;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            transition: background-color 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .pagination-btn:hover {
            background-color: #f9fafb;
        }

        .pagination-btn.active {
            background-color: #1f7a5f;
            color: white;
            border-color: #1f7a5f;
        }

        .pagination-btn.disabled {
            color: #9ca3af;
            background-color: #f3f4f6;
            border-color: #e5e7eb;
            cursor: not-allowed;
        }

        .pagination-ellipsis {
            padding: 0 8px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <main class="blog-page">
        
        <!-- Hero Section -->
        <section class="blog-hero">
            <div class="blog-hero-container">
                <h1 class="page-title">Pet Safety Guides</h1>
                <p class="page-subtitle">Expert advice on finding lost pets and keeping your furry friends safe in Myanmar.</p>
                
                <!-- Category Filters -->
                <div class="blog-filters">
                    <a href="?category=all&page=1" class="filter-btn <?= $category === 'all' ? 'active' : '' ?>">All Articles</a>
                    <a href="?category=emergency&page=1" class="filter-btn <?= $category === 'emergency' ? 'active' : '' ?>">Emergency</a>
                    <a href="?category=prevention&page=1" class="filter-btn <?= $category === 'prevention' ? 'active' : '' ?>">Prevention</a>
                    <a href="?category=health&page=1" class="filter-btn <?= $category === 'health' ? 'active' : '' ?>">Health</a>
                    <a href="?category=community&page=1" class="filter-btn <?= $category === 'community' ? 'active' : '' ?>">Community</a>
                </div>
            </div>
        </section>
        
        <!-- Blog Grid Section -->
        <section class="blog-grid-container">

            <?php if (empty($blogs)): ?>
                <!-- Empty Result -->
                <div class="empty-state">
                    <i class="ri-article-line empty-state-icon"></i>
                    <h3 class="empty-state-title">No articles found</h3>
                    <p class="empty-state-text">There are no articles in this category yet.</p>
                </div>
            <?php else: ?>
                <!-- Blog Cards Grid -->
                <div class="blog-grid">
                    <?php foreach ($blogs as $blog): ?>
                        <article class="blog-card">
                            
                            <!-- Blog Image & Category Badge -->
                            <div class="blog-card-image-wrapper">
                                <img src="../<?= htmlspecialchars($blog['image'] ?? 'uploads/placeholder.jpg') ?>"
                                     alt="<?= htmlspecialchars($blog['title']) ?>"
                                     class="blog-card-image">
                                <span class="blog-card-badge">
                                    <?= htmlspecialchars(ucfirst($blog['category'])) ?>
                                </span>
                            </div>

                            <!-- Blog Content -->
                            <div class="blog-card-content">
                                <h3 class="blog-card-title">
                                    <?= htmlspecialchars($blog['title']) ?>
                                </h3>
                                <p class="blog-card-excerpt">
                                    <?= htmlspecialchars(substr(strip_tags($blog['content']), 0, 120)) ?>...
                                </p>

                                <!-- Date & Author Name -->
                                <div class="blog-card-meta">
                                    <span><i class="ri-calendar-line"></i> <?= date('M d, Y', strtotime($blog['created_at'])) ?></span>
                                    <span><i class="ri-user-line"></i> <?= htmlspecialchars($blog['author_name']) ?></span>
                                </div>

                                <!-- Read More Link -->
                                <a href="blog_details.php?id=<?= $blog['id'] ?>" class="blog-card-link">
                                    Read Article <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php 
                renderUserPagination($currentPage, $totalPages, $totalRecords, $perPage, $_GET);
                ?>
            <?php endif; ?>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>