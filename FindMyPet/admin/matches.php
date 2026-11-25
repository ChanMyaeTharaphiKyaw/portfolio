<?php
$pageTitle = "Match Management";

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to access matches.");
    redirect("login.php");
}

// Retrieve Pending Reports Count
$pendingReports = count(getReportByStatus($pdo, 'Pending'));

// Retrieve Filter Parameters
$currentStatus   = $_GET['match_status'] ?? 'all';

$filterPetType   = $_GET['pet_type_id'] ?? 'all';
$filterCity      = $_GET['city_id'] ?? '';
$filterTownship  = $_GET['township_id'] ?? '';
$filterDateFrom  = $_GET['date_from'] ?? '';
$filterDateTo    = $_GET['date_to'] ?? '';
$searchDirect    = $_GET['search_direct'] ?? '';

// Format Date Range
$dateRangeDisplay = formatDateRange($filterDateFrom, $filterDateTo);

$baseFilters = [
    'search_direct' => $searchDirect,
    'pet_type_id'   => $filterPetType,
    'city_id'       => $filterCity,
    'township_id'   => $filterTownship,
    'date_from'     => $filterDateFrom,
    'date_to'       => $filterDateTo
];

// Calculate Total Matches for Each Status Tab
$statusCounts = getFilteredMatchCounts($pdo, $baseFilters);

$filters = $baseFilters;
$filters['match_status'] = $currentStatus;

// Pagination
$perPage = 10;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

$totalRecords = $statusCounts[$currentStatus] ?? 0;
$totalPages = ceil($totalRecords / $perPage) ?: 1;

if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
    $offset = ($currentPage - 1) * $perPage;
}

// Retrieve Matches
$matches = getFilteredMatches($pdo, $filters, $perPage, $offset);
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
            <a href="matches.php" class="admin-sidebar-link active">
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
                    <h3 class="admin-header-title">Matches</h3>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="matches.php" class="admin-filter-form">
                <input type="hidden" name="match_status" value="<?= htmlspecialchars($currentStatus) ?>">
                <input type="hidden" name="date_from" id="hidden_date_from" value="<?= htmlspecialchars($filterDateFrom) ?>">
                <input type="hidden" name="date_to" id="hidden_date_to" value="<?= htmlspecialchars($filterDateTo) ?>">

                <div class="admin-form-grid matches-grid">
                    <!-- Search Bar -->
                    <div class="admin-form-group">
                        <label class="admin-label">Search</label>
                        <div class="search-icon-wrapper">
                            <input type="text" name="search_direct" placeholder="Pet, owner, ID..." class="admin-input-search" value="<?= htmlspecialchars($searchDirect) ?>">
                            <i class="ri-search-line"></i>
                        </div>
                    </div>

                    <!-- Pet Type Dropdown -->
                    <div class="admin-form-group">
                        <label class="admin-label">Pet Type</label>
                        <select name="pet_type_id" class="admin-select admin-filter-select">
                            <option value="all">All Pets</option>
                            <?php foreach (getPetTypes($pdo) as $type): ?>
                                <option value="<?= $type['id'] ?>" <?= $filterPetType == $type['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($type['type_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- City Dropdown -->
                    <div class="admin-form-group">
                        <label class="admin-label">City</label>
                        <select name="city_id" id="filter_city" class="admin-select admin-filter-select">
                            <option value="">All Cities</option>
                            <?php foreach (getAll($pdo, 'city') as $city): ?>
                                <option value="<?= $city['id'] ?>" <?= $city['id'] == $filterCity ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($city['city_name']) ?>
                                </option>
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
                                    <option value="<?= $township['id'] ?>" <?= $township['id'] == $filterTownship ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($township['township_name']) ?>
                                    </option>
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

                    <!-- Filter Action Buttons -->
                    <div class="admin-form-group form-actions post-form-btn">
                        <button type="submit" class="admin-btn admin-btn-primary form-action-btn">
                            <i class="ri-filter-line"></i> Apply
                        </button>
                        <a href="matches.php" class="admin-btn admin-btn-secondary form-action-btn">
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
                        <?php foreach (['all' => 'All Matches', 'pending' => 'Pending', 'accepted' => 'Accepted', 'rejected' => 'Rejected'] as $key => $label): ?>
                            <?php
                            $tabParams = $_GET;
                            $tabParams['match_status'] = $key;
                            $tabParams['page'] = 1;
                            ?>
                            <a href="?<?= http_build_query($tabParams) ?>" class="admin-tab <?= $currentStatus === $key ? 'active' : '' ?>">
                                <?= $label ?>
                                <span class="admin-tab-badge"><?= $statusCounts[$key] ?? 0 ?></span>
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
                                <th>Post A (Original)</th>
                                <th>Post B (Matched)</th>
                                <th>Score</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($matches) > 0): ?>
                                <?php foreach ($matches as $matchItem): ?>
                                    <?php
                                    $scoreColor = function_exists('getMatchScoreColor') ? getMatchScoreColor($matchItem['match_score']) : 'color: var(--colorPrimary);';

                                    $statusClass = getMatchStatusBadgeClass($matchItem['match_status']);
                                    ?>
                                    <tr>
                                        <td class="font-mono text-xs text-gray-500"><?= $matchItem['match_id'] ?></td>

                                        <!-- Post A Details -->
                                        <td>
                                            <div class="match-post-row">
                                                <img src="<?= htmlspecialchars(!empty($matchItem['orig_image']) ? '../' . $matchItem['orig_image'] : '../assets/images/placeholder-pet.png') ?>"
                                                    class="match-post-img" alt="Post A">
                                                <div>
                                                    <div class="match-post-name"><?= htmlspecialchars($matchItem['orig_pet_name'] ?? 'Unknown') ?></div>
                                                    <div class="match-post-meta"><?= htmlspecialchars($matchItem['orig_post_type']) ?> • <?= htmlspecialchars($matchItem['orig_owner']) ?></div>
                                                    <div class="match-post-id">ID: <?= $matchItem['post_id'] ?></div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Post B Details -->
                                        <td>
                                            <div class="match-post-row">
                                                <img src="<?= htmlspecialchars(!empty($matchItem['match_image']) ? '../' . $matchItem['match_image'] : '../assets/images/placeholder-pet.png') ?>"
                                                    class="match-post-img" alt="Post B">
                                                <div>
                                                    <div class="match-post-name"><?= htmlspecialchars($matchItem['match_pet_name'] ?? 'Unknown') ?></div>
                                                    <div class="match-post-meta"><?= htmlspecialchars($matchItem['match_post_type']) ?> • <?= htmlspecialchars($matchItem['match_owner']) ?></div>
                                                    <div class="match-post-id">ID: <?= $matchItem['matched_post_id'] ?></div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Match Score -->
                                        <td class="match-score" style="<?= $scoreColor ?>">
                                            <?= number_format($matchItem['match_score'], 0) ?>%
                                        </td>

                                        <!-- Match Status -->
                                        <td>
                                            <span class="admin-tab-badge <?= $statusClass ?> text-uppercase">
                                                <?= htmlspecialchars(ucfirst($matchItem['match_status'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="empty-state">No Matches Found</td>
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