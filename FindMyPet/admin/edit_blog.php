<?php
$pageTitle = "Edit Blog";

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to access blogs.");
    redirect("login.php");
}

// Retrieve Pending Reports Count
$pendingReports = count(getReportByStatus($pdo, 'Pending'));

// Retrieve Blog 
$blogId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$blogResult = getBlogById($pdo, $blogId);

include "admin_code.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet" />
</head>

<body class="admin-body">

    <div id="sidebar-overlay" class="is-hidden"></div>

    <!-- Left Sidebar -->
    <aside id="sidebar">
        <div>
            <div class="logo-container">
                <img src="../uploads/logo.png" alt="Logo" class="sidebar-logo">
                <span>FindMyPet</span>
            </div>
        </div>

        <nav>
            <a href="dashboard.php" class="admin-sidebar-link">
                <i class="ri-dashboard-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="users.php" class="admin-sidebar-link">
                <i class="ri-user-3-line"></i>
                <span>Users</span>
            </a>
            <a href="posts.php" class="admin-sidebar-link">
                <i class="ri-file-list-line"></i>
                <span>Posts</span>
            </a>
            <a href="reports.php" class="admin-sidebar-link">
                <i class="ri-alarm-warning-line"></i>
                <span>Content Reports</span>
                <?php if ($pendingReports > 0): ?>
                    <span class="badge"><?= $pendingReports ?></span>
                <?php endif; ?>
            </a>
            <a href="matches.php" class="admin-sidebar-link">
                <i class="ri-puzzle-2-line"></i>
                <span>Matches</span>
            </a>
            <a href="comments.php" class="admin-sidebar-link">
                <i class="ri-chat-3-line"></i>
                <span>Comments</span>
            </a>
            <a href="messages.php" class="admin-sidebar-link">
                <i class="ri-mail-open-line"></i>
                <span>Contact Messages</span>
            </a>
            <a href="announcements.php" class="admin-sidebar-link">
                <i class="ri-megaphone-line"></i>
                <span>Announcements</span>
            </a>
            <a href="blogs.php" class="admin-sidebar-link active">
                <i class="ri-article-line"></i>
                <span>Blogs</span>
            </a>
            <a href="pet_types.php" class="admin-sidebar-link">
                <i class="ri-edit-box-line"></i>
                <span>Pet Types</span>
            </a>
            <a href="breeds.php" class="admin-sidebar-link">
                <i class="ri-shape-2-line"></i>
                <span>Breeds</span>
            </a>
            <a href="cities.php" class="admin-sidebar-link">
                <i class="ri-map-pin-line"></i>
                <span>Cities</span>
            </a>
            <a href="townships.php" class="admin-sidebar-link">
                <i class="ri-building-4-line"></i>
                <span>Townships</span>
            </a>
        </nav>

        <div>
            <a href="logout.php" class="admin-sidebar-link">
                <i class="ri-logout-box-r-line"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="admin-main">
        <section>
            <!-- Page Header -->
            <div class="admin-header">
                <div class="admin-header-left">
                    <button id="sidebar-toggle" class="sidebar-toggle-btn">
                        <i class="ri-menu-line"></i>
                    </button>
                    <h3 class="admin-header-title">Edit Blog</h3>
                </div>
                <div class="admin-header-right">
                    <a href="blogs.php" class="admin-btn admin-btn-secondary">
                        <i class="ri-arrow-left-long-line"></i>
                        <span>Back</span>
                    </a>
                </div>
            </div>

            <!-- Form Card -->
            <div class="table-card">
                <div class="blog-form-container">
                    <?php include '../includes/alert.php'; ?>

                    <?php if (isset($blogResult['status']) && $blogResult['status'] == 200): ?>
                        <?php $blogData = $blogResult['data']; ?>

                        <form action="admin_code.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="blog_id" value="<?= htmlspecialchars($blogData['id']) ?>">

                            <div class="admin-form-grid blog-form-grid">

                                <!-- Title -->
                                <div class="admin-form-group blog-form-group-full">
                                    <label class="admin-label" for="title">Title <span class="text-color-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="admin-input" placeholder="Enter blog title" value="<?= htmlspecialchars($blogData['title']) ?>" required>
                                </div>

                                <!-- Category -->
                                <div class="admin-form-group blog-form-group-full">
                                    <label class="admin-label" for="category">Category <span class="text-color-danger">*</span></label>
                                    <select name="category" id="category" class="admin-select admin-filter-select" required>
                                        <option value="Emergency" <?= strtolower($blogData['category']) === 'emergency' ? 'selected' : '' ?>>Emergency</option>
                                        <option value="Health" <?= strtolower($blogData['category']) === 'health' ? 'selected' : '' ?>>Health</option>
                                        <option value="Prevention" <?= strtolower($blogData['category']) === 'prevention' ? 'selected' : '' ?>>Prevention</option>
                                        <option value="Community" <?= strtolower($blogData['category']) === 'community' ? 'selected' : '' ?>>Community</option>
                                    </select>
                                </div>
                                <!-- Image -->
                                <div class="admin-form-group blog-form-group-full">
                                    <label class="admin-label" for="image">Featured Image</label>

                                    <!-- Current Image Preview -->
                                    <?php if (!empty($blogData['image'])): ?>
                                        <div class="current-image-preview">
                                            <img src="../<?= htmlspecialchars($blogData['image']) ?>" alt="Current Image">
                                            <div>
                                                <p class="current-image-preview-text">Current Image</p>
                                                <p class="current-image-preview-hint">Upload a new file below to replace it.</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <input type="file" name="image" id="image" accept="image/*" class="admin-input">
                                </div>

                                <!-- Content -->
                                <div class="admin-form-group blog-form-group-full">
                                    <label class="admin-label" for="content">Content <span class="text-color-danger">*</span></label>
                                    <textarea name="content" id="content" rows="10" class="admin-input admin-textarea" placeholder="Write your blog content here..." required><?= htmlspecialchars($blogData['content']) ?></textarea>
                                </div>

                                <!-- Form Actions -->
                                <div class="admin-form-group form-actions blog-form-actions">
                                    <a href="blogs.php" class="admin-btn admin-btn-secondary">Cancel</a>
                                    <button type="submit" name="btn_update_blog" class="admin-btn admin-btn-primary primary-btn">Update Blog</button>
                                </div>
                            </div>
                        </form>

                    <?php else: ?>
                        <!-- Error State -->
                        <div class="empty-state text-color-danger">
                            <?= htmlspecialchars($blogResult['message'] ?? 'Blog not found or invalid ID.') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Sidebar Toggle 
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
                sidebarOverlay.classList.toggle('is-hidden');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.add('is-hidden');
            });
        }
    </script>
</body>

</html>