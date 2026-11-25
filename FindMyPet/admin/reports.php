<?php
$pageTitle = "Content Reports";

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to access reports.");
    redirect("login.php");
}

// Retrieve Pending Reports Count
$pendingReports = count(getReportByStatus($pdo, 'Pending'));
$statusCounts = getReportStatusCounts($pdo);
// Retrieve Filter Parameters
$currentFilter   = $_GET['filter'] ?? 'all';
$searchQuery     = $_GET['search'] ?? '';
$targetContent   = $_GET['target'] ?? 'all';
$filterDateFrom  = $_GET['date_from'] ?? '';
$filterDateTo    = $_GET['date_to'] ?? '';

$filters = [
    'filter'    => $currentFilter,
    'search'    => $searchQuery,
    'target'    => $targetContent,
    'date_from' => $filterDateFrom,
    'date_to'   => $filterDateTo
];
// Format Date Range
$dateRangeDisplay = formatDateRange($filterDateFrom, $filterDateTo);
// Pagination
$perPage = 10;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

$totalRecords = getFilteredReportsCount($pdo, $filters);
$totalPages = ceil($totalRecords / $perPage);
// Retrieve Reports
$reports = getFilteredReports($pdo, $filters, $perPage, $offset);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet Admin</title>
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
            <a href="reports.php" class="admin-sidebar-link active">
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
                    <h3 class="admin-header-title">Content Reports</h3>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="reports.php" class="admin-filter-form">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($currentFilter) ?>">
                <input type="hidden" name="date_from" id="hidden_date_from" value="<?= htmlspecialchars($filterDateFrom) ?>">
                <input type="hidden" name="date_to" id="hidden_date_to" value="<?= htmlspecialchars($filterDateTo) ?>">

                <div class="admin-form-grid reports-grid">
                    <!-- Search Bar -->
                    <div class="admin-form-group search-group">
                        <label class="admin-label">Search</label>
                        <div class="search-icon-wrapper">
                            <input type="text" name="search" placeholder="Search reporter or reason..." class="admin-input-search" value="<?= htmlspecialchars($searchQuery) ?>">
                            <i class="ri-search-line"></i>
                        </div>
                    </div>

                    <!-- Target Dropdown -->
                    <div class="admin-form-group">
                        <label class="admin-label">Target Content</label>
                        <select name="target" class="admin-select admin-filter-select">
                            <option value="all" <?= $targetContent === 'all' ? 'selected' : '' ?>>All Targets</option>
                            <option value="post" <?= $targetContent === 'post' ? 'selected' : '' ?>>Posts Only</option>
                            <option value="comment" <?= $targetContent === 'comment' ? 'selected' : '' ?>>Comments Only</option>
                        </select>
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
                        <a href="reports.php" class="admin-btn admin-btn-secondary form-action-btn">
                            Clear
                        </a>
                    </div>
                </div>
            </form>

            <!-- Status Tab & Table -->
            <div class="table-card">

                <!-- Status Filter Tabs -->
                <div class="admin-tabs-container">
                    <div class="admin-tabs-wrapper">
                        <?php foreach (['all' => 'All Reports', 'Pending' => 'Pending', 'Resolved' => 'Resolved', 'Dismissed' => 'Dismissed'] as $key => $label): ?>
                            <?php
                            $tabParams = $_GET;
                            $tabParams['filter'] = $key;
                            $tabParams['page'] = 1;
                            $tabUrl = '?' . http_build_query($tabParams);
                            ?>
                            <a href="<?= $tabUrl ?>" class="admin-tab <?= $currentFilter === $key ? 'active' : '' ?>">
                                <?= $label ?>
                                <span class="admin-tab-badge"><?= $statusCounts[$key] ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Table -->
                <div class="admin-table-wrapper">
                    <?php include '../includes/alert.php'; ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reporter</th>
                                <th>Target Content</th>
                                <th>Reason</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($reports) > 0): ?>
                                <?php foreach ($reports as $reportItem): ?>
                                    <?php
                                    $formattedDate = date('M d, Y', strtotime($reportItem['created_at']));
                                    $statusClass = getReportStatusBadgeClass($reportItem['status']);
                                    $targetType = !empty($reportItem['post_id']) ? 'post' : 'comment';
                                    $targetId = !empty($reportItem['post_id']) ? $reportItem['post_id'] : $reportItem['comment_id'];
                                    $ownerId = $reportItem['owner_id'] ?? 0;

                                    if ($targetType === 'post') {
                                        $targetBadge = '<span class="admin-tab-badge badge-post text-uppercase">Post (' . htmlspecialchars($reportItem['reported_post_type']) . ')</span>';
                                        $viewLink = 'edit_post.php?id=' . $reportItem['post_id'];
                                        $contentPreview = $reportItem['reported_post_type'];
                                    } else {
                                        $targetBadge = '<span class="admin-tab-badge badge-comment text-uppercase">Comment</span>';
                                        $viewLink = !empty($reportItem['comment_post_id']) ? '../user/post_details.php?id=' . $reportItem['comment_post_id'] : '#';
                                        $contentPreview = $reportItem['reported_comment_text'];
                                    }
                                    ?>
                                    <tr>
                                        <td><?= $reportItem['id']; ?></td>
                                        <td class="td-name"><?= htmlspecialchars($reportItem['reporter_name'] ?? 'Unknown User'); ?></td>
                                        <td><?= $targetBadge ?></td>
                                        <td>
                                            <div class="td-email" title="<?= htmlspecialchars($reportItem['reason'] . ': ' . $reportItem['details']) ?>">
                                                <span class="font-weight-medium"><?= htmlspecialchars($reportItem['reason']); ?></span>
                                                <?php if (!empty($reportItem['details'])): ?>
                                                    <span class="text-color-muted"> - <?= htmlspecialchars($reportItem['details']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="td-date"><?= $formattedDate ?></td>
                                        <td>
                                            <span class="admin-tab-badge <?= $statusClass ?> text-uppercase">
                                                <?= htmlspecialchars($reportItem['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="admin-action-group admin-action-group-end">
                                                <button type="button"
                                                    class="admin-action-btn edit review-btn"
                                                    title="Review & Take Action"
                                                    data-report-id="<?= $reportItem['id'] ?>"
                                                    data-reporter="<?= htmlspecialchars($reportItem['reporter_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') ?>"
                                                    data-reason="<?= htmlspecialchars($reportItem['reason'], ENT_QUOTES, 'UTF-8') ?>"
                                                    data-details="<?= htmlspecialchars($reportItem['details'], ENT_QUOTES, 'UTF-8') ?>"
                                                    data-target-type="<?= $targetType ?>"
                                                    data-target-id="<?= $targetId ?>"
                                                    data-content-preview="<?= htmlspecialchars($contentPreview, ENT_QUOTES, 'UTF-8') ?>"
                                                    data-view-link="<?= htmlspecialchars($viewLink, ENT_QUOTES, 'UTF-8') ?>"
                                                    data-owner-id="<?= $ownerId ?>">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
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

    <!-- Review Modal -->
    <div id="reviewModal" class="review-modal is-hidden">
        <div class="review-modal-overlay" onclick="document.getElementById('reviewModal').classList.add('is-hidden')"></div>
        <div class="review-modal-content">
            <form action="admin_code.php" method="POST">
                <input type="hidden" name="report_id" id="modal_report_id">
                <input type="hidden" name="target_type" id="modal_target_type">
                <input type="hidden" name="target_id" id="modal_target_id">
                <input type="hidden" name="owner_id" id="modal_owner_id">

                <div class="review-modal-header">
                    <h3 class="admin-header-title modal-title-danger">
                        <i class="ri-alarm-warning-line"></i> Review Report #<span id="modal_report_num"></span>
                    </h3>
                </div>

                <div class="review-modal-body">
                    <div class="report-alert-box">
                        <p class="report-alert-text">Reported by: <span id="modal_reporter" class="font-weight-normal"></span></p>
                        <p class="report-alert-text margin-top-sm">Reason: <span id="modal_reason" class="font-weight-normal text-color-danger"></span></p>
                        <p class="text-color-muted margin-top-sm" id="modal_details"></p>
                    </div>

                    <div class="margin-bottom-md">
                        <h4 class="section-title">Reported Content:</h4>
                        <div class="content-preview-box" id="modal_content_preview"></div>
                        <a href="#" id="modal_view_link" target="_blank" class="view-link">
                            <i class="ri-external-link-line"></i> View full content in new tab
                        </a>
                    </div>

                    <h4 class="section-title-bordered">Admin Actions:</h4>
                    <div class="action-grid">
                        <label class="action-option">
                            <input type="radio" name="admin_action" value="dismiss" class="radio-input" required checked>
                            <div>
                                <span class="font-weight-medium">Dismiss</span>
                                <p class="text-small-muted">Content is fine. No action.</p>
                            </div>
                        </label>
                        <label class="action-option">
                            <input type="radio" name="admin_action" value="warn" class="radio-input">
                            <div>
                                <span class="text-color-warning">Warn User</span>
                                <p class="text-small-muted">Keep content, warn user.</p>
                            </div>
                        </label>
                        <label class="action-option">
                            <input type="radio" name="admin_action" value="remove" class="radio-input">
                            <div>
                                <span class="text-color-danger">Remove Content</span>
                                <p class="text-small-muted">Delete post/comment.</p>
                            </div>
                        </label>
                    </div>

                    <div id="action_details" class="action-details">
                        <label class="admin-label">Message to User (Optional)</label>
                        <textarea name="admin_message" rows="3" class="admin-input" placeholder="Explain why the action was taken..."></textarea>
                    </div>
                </div>

                <div class="review-modal-footer">
                    <button type="button" onclick="document.getElementById('reviewModal').classList.add('is-hidden')" class="admin-btn admin-btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" name="btn_handle_report" class="admin-btn admin-btn-primary">
                        Submit Action
                    </button>
                </div>
            </form>
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

        // Date Range Picker
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

        // Review Modal
        document.querySelectorAll('.review-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                openReviewModal(
                    this.dataset.reportId,
                    this.dataset.reporter,
                    this.dataset.reason,
                    this.dataset.details,
                    this.dataset.targetType,
                    this.dataset.targetId,
                    this.dataset.contentPreview,
                    this.dataset.viewLink,
                    this.dataset.ownerId
                );
            });
        });

        function openReviewModal(reportId, reporter, reason, details, targetType, targetId, contentPreview, viewLink, ownerId) {
            document.getElementById('modal_report_id').value = reportId;
            document.getElementById('modal_report_num').textContent = reportId;
            document.getElementById('modal_reporter').textContent = reporter;
            document.getElementById('modal_reason').textContent = reason;
            document.getElementById('modal_details').textContent = details || 'No additional details provided.';

            document.getElementById('modal_target_type').value = targetType;
            document.getElementById('modal_target_id').value = targetId;
            document.getElementById('modal_owner_id').value = ownerId;

            let previewHtml = '';
            if (targetType === 'post') {
                previewHtml = `<span class="text-color-blue">Post (${contentPreview}):</span> Click the link below to view the full post details.`;
            } else {
                previewHtml = `<span class="text-color-purple">Comment:</span> "${contentPreview}"`;
            }
            document.getElementById('modal_content_preview').innerHTML = previewHtml;
            document.getElementById('modal_view_link').href = viewLink;

            document.querySelector('input[name="admin_action"][value="dismiss"]').checked = true;
            document.querySelector('textarea[name="admin_message"]').value = '';

            document.getElementById('reviewModal').classList.remove('is-hidden');
        }

        document.querySelectorAll('input[name="admin_action"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const detailsDiv = document.getElementById('action_details');
                const textarea = document.querySelector('textarea[name="admin_message"]');

                if (this.value === 'dismiss') {
                    detailsDiv.classList.remove('is-visible');
                    detailsDiv.style.display = 'none';
                } else {
                    detailsDiv.style.display = 'block';
                    detailsDiv.offsetHeight;
                    detailsDiv.classList.add('is-visible');

                    if (this.value === 'remove') {
                        textarea.placeholder = "Explain why their content was removed (e.g., fake information, spam)...";
                    } else {
                        textarea.placeholder = "Write a warning message to the user...";
                    }

                    const modalBody = document.querySelector('.review-modal-body');
                    setTimeout(() => {
                        modalBody.scrollTop = modalBody.scrollHeight;
                    }, 100);
                }
            });
        });
    </script>
</body>

</html>