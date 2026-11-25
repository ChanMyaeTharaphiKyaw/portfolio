<?php
$pageTitle = "Comments Management";

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to access comments.");
    redirect("login.php");
}

// Retrieve Pending Reports Count
$pendingReports = count(getReportByStatus($pdo, 'Pending'));

// Retrieve Search Parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination
$perPage = 10;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

$totalRecords = getFilteredCommentsCount($pdo, $search);
$totalPages = ceil($totalRecords / $perPage);

// Retrieve Comments
$comments = getFilteredComments($pdo, $search, $perPage, $offset);
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
            <a href="comments.php" class="admin-sidebar-link active">
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
            <a href="blogs.php" class="admin-sidebar-link">
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
                    <h3 class="admin-header-title">Comments</h3>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="comments.php" class="admin-filter-form">
                <div class="admin-form-grid" style="grid-template-columns: 1fr auto;">
                    <!-- Search Bar -->
                    <div class="admin-form-group">
                        <label class="admin-label">Search</label>
                        <div class="search-icon-wrapper">
                            <input type="text" name="search" placeholder="Search comment or user name..." class="admin-input-search" value="<?= htmlspecialchars($search) ?>">
                            <i class="ri-search-line"></i>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="admin-form-group" style="align-self: flex-end; display: flex; gap: 0.5rem;">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-filter-line"></i> Apply
                        </button>
                        <a href="comments.php" class="admin-btn admin-btn-secondary">
                            Clear
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="table-card">
                <div class="admin-table-wrapper">
                    <?php include '../includes/alert.php'; ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Comment</th>
                                <th>Commented By</th>
                                <th>Post Type</th>
                                <th>Post Owner</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($comments) > 0): ?>
                                <?php foreach ($comments as $commentItem): ?>
                                    <?php
                                    // Format Registration Date
                                    $formattedDate = date('M d, Y', strtotime($commentItem['created_at']));
                                    $typeClass = getPostTypeBadgeClass($commentItem['post_type']);
                                    ?>
                                    <tr>
                                        <td><?= $commentItem['id']; ?></td>
                                        <td class="td-comment" title="<?= htmlspecialchars($commentItem['comment_text']) ?>">
                                            <?= htmlspecialchars($commentItem['comment_text']); ?>
                                        </td>
                                        <td class="td-name"><?= htmlspecialchars($commentItem['commenter_name']); ?></td>
                                        <td>
                                            <span class="admin-tab-badge <?= $typeClass ?> text-uppercase">
                                                <?= htmlspecialchars($commentItem['post_type']); ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($commentItem['post_owner_name']); ?></td>
                                        <td class="td-date"><?= htmlspecialchars($formattedDate) ?></td>
                                        <td>
                                            <div class="admin-action-group">
                                                <a href="delete_comment.php?id=<?= $commentItem['id']; ?>" class="admin-action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this comment?')">
                                                    <i class="ri-delete-bin-6-line"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Empty State -->
                                <tr>
                                    <td colspan="7" class="empty-state">No Record Found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php
                if ($totalPages > 1) {
                    adminPagination($currentPage, $totalPages, $totalRecords, $perPage);
                }
                ?>
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