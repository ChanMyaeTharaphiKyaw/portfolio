<?php
$pageTitle = "Contact Messages Management";

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to access contact messages.");
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

$totalRecords = getContactMessagesCount($pdo, $search);
$totalPages = ceil($totalRecords / $perPage);

// Retrieve Messages
$messages = getFilteredContactMessages($pdo, $search, $perPage, $offset);
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
            <a href="messages.php" class="admin-sidebar-link active">
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
                    <h3 class="admin-header-title">Contact Messages</h3>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="messages.php" class="admin-filter-form">
                <div class="admin-form-grid" style="grid-template-columns: 1fr auto;">
                    <!-- Search Bar -->
                    <div class="admin-form-group">
                        <label class="admin-label">Search</label>
                        <div class="search-icon-wrapper">
                            <input type="text" name="search" placeholder="Search name, email, subject..." class="admin-input-search" value="<?= htmlspecialchars($search) ?>">
                            <i class="ri-search-line"></i>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="admin-form-group form-actions">
                        <button type="submit" class="admin-btn admin-btn-primary form-action-btn">
                            <i class="ri-filter-line"></i> Apply
                        </button>
                        <a href="messages.php" class="admin-btn admin-btn-secondary form-action-btn">
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
                                <th>Sender Name</th>
                                <th>Sender Email</th>
                                <th>Status</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($messages) > 0): ?>
                                <?php foreach ($messages as $msg): ?>
                                    <?php
                                    // Format Date
                                    $formattedDate = date('M d, Y', strtotime($msg['created_at']));
                                    $isRegistered = !empty($msg['user_id']);
                                    ?>
                                    <tr>
                                        <td><?= $msg['id']; ?></td>
                                        <td>
                                            <div class="font-medium"><?= htmlspecialchars($msg['full_name']); ?></div>
                                        </td>
                                        <td><div class="text-sm"><?= htmlspecialchars($msg['email']); ?></div></td>
                                        <td><span class="admin-tab-badge <?= getUserTypeBadgeClass($isRegistered) ?> text-uppercase">
                                                <?= $isRegistered ? 'Registered' : 'Guest' ?>
                                            </span></td>
                                        <td class="font-medium"><?= htmlspecialchars($msg['subject']); ?></td>
                                        <td class="td-comment" title="<?= htmlspecialchars($msg['message']) ?>">
                                            <?php 
                                            $shortMsg = strlen($msg['message']) > 60 ? substr($msg['message'], 0, 60) . '...' : $msg['message'];
                                            echo htmlspecialchars($shortMsg); 
                                            ?>
                                        </td>
                                        <td class="td-date"><?= htmlspecialchars($formattedDate) ?></td>
                                        <td>
                                            <div class="admin-action-group">
                                                <button type="button" 
                                                        class="admin-action-btn edit review-btn" 
                                                        title="View"
                                                        onclick="openViewModal('<?= htmlspecialchars($msg['full_name'], ENT_QUOTES) ?>', 
                                                                               '<?= htmlspecialchars($msg['email'], ENT_QUOTES) ?>', 
                                                                               '<?= htmlspecialchars($msg['subject'], ENT_QUOTES) ?>', 
                                                                               '<?= htmlspecialchars($msg['message'], ENT_QUOTES) ?>')">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Empty State -->
                                <tr>
                                    <td colspan="6" class="empty-state">No Record Found</td>
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

    <!-- View Modal -->
    <div id="viewModal" class="review-modal is-hidden">
        <div class="review-modal-overlay" onclick="document.getElementById('viewModal').classList.add('is-hidden')"></div>
        <div class="review-modal-content">
            <div class="review-modal-header">
                <h3 class="admin-header-title" id="modal-subject">Message Subject</h3>
            </div>
            
            <div class="review-modal-body">
                <div class="report-alert-box">
                    <div class="report-alert-text">
                        <span class="font-weight-medium" id="modal-name">Sender Name</span>
                    </div>
                    <div class="report-alert-text margin-top-sm">
                        <span class="text-color-muted" id="modal-email">sender@example.com</span>
                    </div>
                    <div class="margin-top-sm">
                        <span class="admin-tab-badge badge-registered text-uppercase" id="modal-user-type">Registered</span>
                    </div>
                </div>

                <div class="margin-bottom-md">
                    <h4 class="section-title">Message Content:</h4>
                    <div class="content-preview-box" id="modal-message"></div>
                </div>
            </div>

            <div class="review-modal-footer">
                <button type="button" onclick="document.getElementById('viewModal').classList.add('is-hidden')" class="admin-btn admin-btn-secondary">
                    Close
                </button>
            </div>
        </div>
    </div>

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

        // View Message Modal Functionality
        function openViewModal(name, email, subject, message) {
            document.getElementById('modal-name').textContent = name;
            document.getElementById('modal-email').textContent = email;
            document.getElementById('modal-subject').textContent = subject;
            document.getElementById('modal-message').textContent = message;
            
            // Update user type badge
            const userTypeBadge = document.getElementById('modal-user-type');
            if (name.includes('Guest')) {
                userTypeBadge.textContent = 'Guest';
                userTypeBadge.className = 'admin-tab-badge badge-guest text-uppercase';
            } else {
                userTypeBadge.textContent = 'Registered';
                userTypeBadge.className = 'admin-tab-badge badge-registered text-uppercase';
            }
            
            document.getElementById('viewModal').classList.remove('is-hidden');
        }
    </script>
</body>
</html>