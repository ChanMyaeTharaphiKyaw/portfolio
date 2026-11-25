<?php
$pageTitle = "Post Management";

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to access posts.");
    redirect("login.php");
}
// Retrieve Pending Reports Count
$pendingReports = count(getReportByStatus($pdo, 'Pending'));

$filters = [
    'filter'        => $_GET['filter'] ?? 'all',
    'search_direct' => $_GET['search_direct'] ?? '',
    'post_type'     => $_GET['post_type'] ?? 'all',
    'pet_type_id'   => $_GET['pet_type_id'] ?? 'all',
    'city_id'       => $_GET['city_id'] ?? '',
    'township_id'   => $_GET['township_id'] ?? '',
    'date_from'     => $_GET['date_from'] ?? '',
    'date_to'       => $_GET['date_to'] ?? ''
];

// Retrieve Filter Parameters
$currentFilter   = $filters['filter'];
$filterPostType  = $filters['post_type'];
$filterPetType   = $filters['pet_type_id'];
$filterCity      = $filters['city_id'];
$filterTownship  = $filters['township_id'];
$filterDateFrom  = $filters['date_from'];
$filterDateTo    = $filters['date_to'];
// Format Date Range
$dateRangeDisplay = formatDateRange($filterDateFrom, $filterDateTo);

$baseFilters = $filters;
unset($baseFilters['filter']);
// Calculate Total Posts for Each Status Tab
$statusCounts = [];
foreach (['all', 'active', 'reunited'] as $status) {
    $tempFilters = $baseFilters;
    $tempFilters['filter'] = $status;
    $statusCounts[$status] = getFilteredAdminPostsCount($pdo, $tempFilters);
}
// Pagination
$perPage = 10;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

$totalRecords = $statusCounts[$currentFilter] ?? 0;
$totalPages = ceil($totalRecords / $perPage);
// Retrieve Posts
$posts = getFilteredAdminPosts($pdo, $filters, $perPage, $offset);
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
            <a href="posts.php" class="admin-sidebar-link active">
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
                    <h3 class="admin-header-title">Posts</h3>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="posts.php" class="admin-filter-form">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($currentFilter) ?>">
                <input type="hidden" name="date_from" id="hidden_date_from" value="<?= htmlspecialchars($filterDateFrom) ?>">
                <input type="hidden" name="date_to" id="hidden_date_to" value="<?= htmlspecialchars($filterDateTo) ?>">

                <div class="admin-form-grid posts-grid">
                    <!-- Search Bar -->
                    <div class="admin-form-group">
                        <label class="admin-label">Search</label>
                        <div class="search-icon-wrapper">
                            <input type="text" name="search_direct" placeholder="Search pet, owner..." class="admin-input-search" value="<?= htmlspecialchars($filters['search_direct']) ?>">
                            <i class="ri-search-line"></i>
                        </div>
                    </div>

                    <!-- Post Type Dropdown -->
                    <div class="admin-form-group">
                        <label class="admin-label">Post Type</label>
                        <select name="post_type" class="admin-select admin-filter-select">
                            <option value="all" <?= $filterPostType === 'all' ? 'selected' : '' ?>>All Types</option>
                            <option value="lost" <?= $filterPostType === 'lost' ? 'selected' : '' ?>>Lost</option>
                            <option value="found" <?= $filterPostType === 'found' ? 'selected' : '' ?>>Found</option>
                        </select>
                    </div>

                    <!-- Pet Type Dropdown -->
                    <div class="admin-form-group">
                        <label class="admin-label">Pet Type</label>
                        <select name="pet_type_id" class="admin-select admin-filter-select">
                            <option value="all">All Pets</option>
                            <?php foreach (getPetTypes($pdo) as $type): ?>
                                <option value="<?= $type['id'] ?>" <?= $filterPetType == $type['id'] ? 'selected' : '' ?>><?= htmlspecialchars($type['type_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- City Dropdown -->
                    <div class="admin-form-group">
                        <label class="admin-label">City</label>
                        <select name="city_id" id="filter_city" class="admin-select admin-filter-select">
                            <option value="">All Cities</option>
                            <?php foreach (getAll($pdo, 'city') as $city): ?>
                                <option value="<?= $city['id'] ?>" <?= $city['id'] == $filterCity ? 'selected' : '' ?>><?= htmlspecialchars($city['city_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Township Dropdown -->
                    <div class="admin-form-group">
                        <label class="admin-label">Township</label>
                        <select name="township_id" id="filter_township" class="admin-select admin-filter-select">
                            <option value="">All Townships</option>
                            <?php if ($filterCity): ?>
                                <?php foreach (getTownshipsByCity($pdo, $filterCity) as $township): ?>
                                    <option value="<?= $township['id'] ?>" <?= $township['id'] == $filterTownship ? 'selected' : '' ?>><?= htmlspecialchars($township['township_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
                    <div class="admin-form-group form-actions post-form-btn" style="grid-column: 1 / -1;">
                        <button type="submit" class="admin-btn admin-btn-primary form-action-btn">
                            <i class="ri-filter-line"></i> Apply
                        </button>
                        <a href="posts.php" class="admin-btn admin-btn-secondary form-action-btn">
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
                        <?php foreach (['all' => 'All Posts', 'active' => 'Active', 'reunited' => 'Reunited'] as $key => $label): ?>
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
                                <th>Image</th>
                                <th>Pet Name</th>
                                <th>Pet Type</th>
                                <th>Post Type</th>
                                <th>Post Owner</th>
                                <th>Contact Phone</th>
                                <th>Contact Email</th>
                                <th>Location</th>
                                <th>Last Seen</th>
                                <th>Posted Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($posts) > 0): ?>
                                <?php foreach ($posts as $postItem): ?>
                                    <?php
                                    $formattedDate = date('M d, Y', strtotime($postItem['created_at']));
                                    $lastSeen = !empty($postItem['last_seen_date']) ? date('M d, Y', strtotime($postItem['last_seen_date'])) : 'N/A';


                                    $typeClass = getPostTypeBadgeClass($postItem['post_type']);
                                    $statusClass = getPostStatusBadgeClass($postItem['status']);
                                    $statusLower = strtolower($postItem['status']);

                                    $imageSrc = !empty($postItem['pet_image']) ? '../' . $postItem['pet_image'] : '../assets/images/placeholder-pet.png';
                                    ?>
                                    <tr>
                                        <td><?= $postItem['id']; ?></td>
                                        <td>
                                            <img src="<?= htmlspecialchars($imageSrc) ?>" alt="Pet" style="width: 3rem; height: 3rem; border-radius: var(--radius-lg); object-fit: cover; border: 1px solid var(--colorBorderLight);">
                                        </td>
                                        <td class="td-name"><?= htmlspecialchars($postItem['pet_name'] ?? 'Unknown') ?></td>
                                        <td><?= htmlspecialchars($postItem['pet_type_name']) ?></td>
                                        <td>
                                            <span class="admin-tab-badge <?= $typeClass ?>" style="text-transform: uppercase;"><?= htmlspecialchars($postItem['post_type']) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($postItem['owner_name']) ?></td>
                                        <td><?= htmlspecialchars($postItem['contact_phone']) ?></td>
                                        <td class="td-email" title="<?= htmlspecialchars($postItem['contact_email']) ?>"><?= htmlspecialchars($postItem['contact_email']) ?></td>
                                        <td><?= htmlspecialchars($postItem['township_name']) ?>, <?= htmlspecialchars($postItem['city_name']) ?></td>
                                        <td class="td-date"><?= $lastSeen ?></td>
                                        <td class="td-date"><?= $formattedDate ?></td>
                                        <td>
                                            <form action="admin_code.php" method="POST" class="status-form">
                                                <input type="hidden" name="post_id" value="<?= $postItem['id']; ?>">
                                                <select name="status" onchange="this.form.submit()" class="status-select <?= $statusClass ?>">
                                                    <option value="Active" <?= $statusLower === 'active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="Reunited" <?= $statusLower === 'reunited' ? 'selected' : '' ?>>Reunited</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="admin-action-group">
                                                <a href="edit_post.php?id=<?= $postItem['id']; ?>" class="admin-action-btn edit" title="Edit"><i class="ri-pencil-line"></i></a>
                                                <a href="delete_post.php?id=<?= $postItem['id']; ?>" class="admin-action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this post?')"><i class="ri-delete-bin-6-line"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="13" class="empty-state">No Record Found</td>
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
        // Township Dropdown
        document.getElementById('filter_city').addEventListener('change', function() {
            const cityId = this.value;
            const townshipSelect = document.getElementById('filter_township');
            if (!cityId) {
                townshipSelect.innerHTML = '<option value="">All Townships</option>';
                return;
            }
            townshipSelect.innerHTML = '<option value="">Loading...</option>';
            fetch('../user/get_townships.php?city_id=' + cityId)
                .then(response => response.text())
                .then(htmlOptions => {
                    townshipSelect.innerHTML = '<option value="">All Townships</option>' + htmlOptions;
                })
                .catch(err => {
                    console.error('Ajax error:', err);
                    townshipSelect.innerHTML = '<option value="">All Townships</option>';
                });
        });
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

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.date-range-picker, .date-range-inputs')) {
                document.getElementById('date_range_inputs').classList.remove('active');
            }
        });
    </script>
</body>

</html>