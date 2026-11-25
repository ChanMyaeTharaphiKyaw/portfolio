<?php
$pageTitle = "Announcements Management";

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Verify Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to access announcements.");
    redirect("login.php");
}

// Retrieve Pending Reports Count
$pendingReports = count(getReportByStatus($pdo, 'Pending'));

// Retrieve Filter Parameters
$searchQuery = $_GET['search'] ?? '';
$filterDateFrom = $_GET['date_from'] ?? '';
$filterDateTo = $_GET['date_to'] ?? '';

// Format Date Range
$dateRangeDisplay = formatDateRange($filterDateFrom, $filterDateTo);

$baseFilters = [
    'search'    => $searchQuery,
    'date_from' => $filterDateFrom,
    'date_to'   => $filterDateTo
];

// Pagination
$perPage = 10;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

$totalRecords = getFilteredAnnouncementsCount($pdo, $baseFilters);
$totalPages = ceil($totalRecords / $perPage);

// Retrieve Announcements
$announcements = getFilteredAnnouncements($pdo, $baseFilters, $perPage, $offset);
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
            <a href="comments.php" class="admin-sidebar-link">
                <i class="ri-chat-3-line"></i>
                <span>Comments</span>
            </a>
            <a href="messages.php" class="admin-sidebar-link">
                <i class="ri-mail-open-line"></i>
                <span>Contact Messages</span>
            </a>
            <a href="announcements.php" class="admin-sidebar-link active">
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
                    <h3 class="admin-header-title">Announcements</h3>
                </div>
                <div class="admin-header-right">
                    <button type="button" onclick="document.getElementById('addModal').classList.remove('is-hidden')" class="admin-btn admin-btn-primary primary-btn">
                        <i class="ri-megaphone-line"></i>
                        <span>Send Announcement</span>
                    </button>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="announcements.php" class="admin-filter-form">
                <input type="hidden" name="date_from" id="hidden_date_from" value="<?= htmlspecialchars($filterDateFrom) ?>">
                <input type="hidden" name="date_to" id="hidden_date_to" value="<?= htmlspecialchars($filterDateTo) ?>">

                <div class="admin-form-grid">
                    <!-- Search Bar -->
                    <div class="admin-form-group">
                        <label class="admin-label">Search</label>
                        <div class="search-icon-wrapper">
                            <input type="text" name="search" placeholder="Search announcements..." class="admin-input-search" value="<?= htmlspecialchars($searchQuery) ?>">
                            <i class="ri-search-line"></i>
                        </div>
                    </div>

                    <!-- Date Range Picker -->
                    <div class="admin-form-group date-range-picker-wrapper">
                        <label class="admin-label">Date Range</label>
                        <div class="date-range-picker" onclick="toggleDateRange(event)">
                            <div class="date-range-display">
                                <i class="ri-calendar-line"></i>
                                <span id="date_range_display"><?= htmlspecialchars($dateRangeDisplay) ?></span>
                            </div>
                            <i class="ri-arrow-down-s-line"></i>
                        </div>

                        <div class="date-range-inputs" id="date_range_inputs">
                            <div class="date-range-specific-wrapper">
                                <h4 class="date-range-title">Specific Date Range</h4>
                                <div class="date-inputs-wrapper">
                                    <div>
                                        <label class="date-label">From</label>
                                        <input type="date" id="date_from" value="<?= htmlspecialchars($filterDateFrom) ?>" class="admin-input">
                                    </div>
                                    <div>
                                        <label class="date-label">To</label>
                                        <input type="date" id="date_to" value="<?= htmlspecialchars($filterDateTo) ?>" class="admin-input">
                                    </div>
                                </div>
                            </div>
                            <div class="date-actions">
                                <button type="button" onclick="clearDateRange()" class="admin-btn admin-btn-secondary date-action-btn">Clear</button>
                                <button type="button" onclick="applyDateRange()" class="admin-btn admin-btn-primary date-action-btn">Apply</button>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="admin-form-group form-actions">
                        <button type="submit" class="admin-btn admin-btn-primary form-action-btn">
                            <i class="ri-filter-line"></i> Apply
                        </button>
                        <a href="announcements.php" class="admin-btn admin-btn-secondary form-action-btn">
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
                                <th>Message</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($announcements) > 0): ?>
                                <?php foreach ($announcements as $announcementItem): ?>
                                    <?php 
                                    $formattedDate = date('M d, Y', strtotime($announcementItem['created_at'])); 
                                    $encodedMessage = htmlspecialchars(json_encode($announcementItem['message']), ENT_QUOTES, 'UTF-8');
                                    $shortMessage = strlen($announcementItem['message']) > 60 
                                        ? htmlspecialchars(substr($announcementItem['message'], 0, 60)) . '...' 
                                        : htmlspecialchars($announcementItem['message']);
                                    ?>
                                    <tr>
                                        <td><?= $announcementItem['id']; ?></td>
                                        <td class="td-name">
                                            <?= $shortMessage ?>
                                        </td>
                                        <td class="td-date"><?= $formattedDate ?></td>
                                        <td>
                                            <div class="admin-action-group">
                                                <button type="button" 
                                                    class="admin-action-btn edit review-btn view-ann-btn" 
                                                    title="View Details"
                                                    data-message='<?= $encodedMessage ?>'
                                                    data-date="<?= $formattedDate ?>">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Empty State -->
                                <tr>
                                    <td colspan="4" class="empty-state">No Announcements Found</td>
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

    <!-- Send Announcement Modal -->
    <div id="addModal" class="review-modal is-hidden">
        <div class="review-modal-overlay" onclick="document.getElementById('addModal').classList.add('is-hidden')"></div>
        <div class="review-modal-content">
            <form action="admin_code.php" method="POST">
                <div class="review-modal-header">
                    <h3 class="admin-header-title">Send Announcement to All Users</h3>
                </div>
                <div class="review-modal-body">
                    <div class="report-alert-box">
                        <p class="report-alert-text text-color-warning">
                            <i class="ri-information-line"></i> This announcement will appear in all users' notification center instantly.
                        </p>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label" for="title">Title</label>
                        <input type="text" name="title" id="title" required maxlength="255" class="admin-input" placeholder="e.g., System Maintenance Notice">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label" for="message">Message</label>
                        <textarea name="message" id="message" rows="6" required class="admin-input" placeholder="Write your announcement message here..."></textarea>
                    </div>
                </div>
                <div class="review-modal-footer">
                    <button type="button" onclick="document.getElementById('addModal').classList.add('is-hidden')" class="admin-btn admin-btn-secondary">Cancel</button>
                    <button type="submit" name="btn_send_announcement" class="admin-btn admin-btn-primary">Send Announcement</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Announcement Modal -->
    <div id="viewModal" class="review-modal is-hidden">
        <div class="review-modal-overlay" onclick="document.getElementById('viewModal').classList.add('is-hidden')"></div>
        <div class="review-modal-content">
            <div class="review-modal-header">
                <h3 class="admin-header-title">Announcement Details</h3>
            </div>
            <div class="review-modal-body">
                <p class="text-color-muted margin-bottom-md" id="view_date"></p>
                <div class="content-preview-box" id="view_message" style="white-space: pre-wrap;"></div>
            </div>
            <div class="review-modal-footer">
                <button type="button" onclick="document.getElementById('viewModal').classList.add('is-hidden')" class="admin-btn admin-btn-secondary">Close</button>
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

        // View Announcement
        document.querySelectorAll('.view-ann-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('view_date').textContent = 'Sent on ' + this.dataset.date;
                document.getElementById('view_message').textContent = JSON.parse(this.dataset.message);
                document.getElementById('viewModal').classList.remove('is-hidden');
            });
        });

        // Date Range Picker Logic
        function toggleDateRange(event) {
            event.stopPropagation();
            document.getElementById('date_range_inputs').classList.toggle('active');
        }

        function applyDateRange() {
            document.getElementById('hidden_date_from').value = document.getElementById('date_from').value;
            document.getElementById('hidden_date_to').value = document.getElementById('date_to').value;
            updateDateRangeDisplay(document.getElementById('date_from').value, document.getElementById('date_to').value);
            document.getElementById('date_range_inputs').classList.remove('active');
        }

        function clearDateRange() {
            document.getElementById('date_from').value = '';
            document.getElementById('date_to').value = '';
            document.getElementById('hidden_date_from').value = '';
            document.getElementById('hidden_date_to').value = '';
            document.getElementById('date_range_display').textContent = 'All Time';
            document.getElementById('date_range_inputs').classList.remove('active');
        }

        function updateDateRangeDisplay(from, to) {
            const display = document.getElementById('date_range_display');
            if (!from && !to) {
                display.textContent = 'All Time';
                return;
            }
            display.textContent = (from ? formatDate(from) : 'Start') + ' - ' + (to ? formatDate(to) : 'Present');
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.date-range-picker, .date-range-inputs')) {
                document.getElementById('date_range_inputs').classList.remove('active');
            }
        });
    </script>
</body>
</html>