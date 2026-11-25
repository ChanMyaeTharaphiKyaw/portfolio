<?php
$pageTitle = "Users Management";

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to access users.");
    redirect("login.php");
}

// Retrieve Pending Reports Count
$pendingReports = count(getReportByStatus($pdo, 'Pending'));

// Retrieve Filter Parameters
$currentFilter     = $_GET['filter'] ?? 'all';
$filterCity        = $_GET['city_id'] ?? '';
$filterTownship    = $_GET['township_id'] ?? '';
$filterDateFrom    = $_GET['date_from'] ?? '';
$filterDateTo      = $_GET['date_to'] ?? '';
$searchDirect      = $_GET['search_direct'] ?? '';

// Format Date Range
$dateRangeDisplay = formatDateRange($filterDateFrom, $filterDateTo);

$baseFilters = [
    'search_direct' => $searchDirect,
    'city_id'       => $filterCity,
    'township_id'   => $filterTownship,
    'date_from'     => $filterDateFrom,
    'date_to'       => $filterDateTo
];

// Calculate Total Users for Each Status Tab
$statusCounts = [];
foreach (['all', 'active', 'suspended', 'banned'] as $status) {
    $tempFilters = $baseFilters;
    $tempFilters['filter'] = $status;
    $statusCounts[$status] = getFilteredUsersCount($pdo, $tempFilters);
}

$filters = $baseFilters;
$filters['filter'] = $currentFilter;

// Pagination
$perPage = 10;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

$totalRecords = $statusCounts[$currentFilter];
$totalPages = ceil($totalRecords / $perPage);
// Retrieve Users
$users = getFilteredUsers($pdo, $filters, $perPage, $offset);
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
            <a href="users.php" class="admin-sidebar-link active">
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
                    <h3 class="admin-header-title">Users</h3>
                </div>
                <div class="admin-header-right">
                    <a href="add_user.php" class="admin-btn admin-btn-primary">
                        <i class="ri-add-line"></i>
                        <span>Add New User</span>
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="users.php" class="admin-filter-form">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($currentFilter) ?>">
                <input type="hidden" name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <input type="hidden" name="date_from" id="hidden_date_from" value="<?= htmlspecialchars($filterDateFrom) ?>">
                <input type="hidden" name="date_to" id="hidden_date_to" value="<?= htmlspecialchars($filterDateTo) ?>">

                <div class="admin-form-grid users-grid">
                    <!-- Search Bar -->
                    <div class="admin-form-group">
                        <label class="admin-label">Search</label>
                        <div class="search-icon-wrapper">
                            <input type="text" name="search_direct" placeholder="Search name, email..." class="admin-input-search" value="<?= htmlspecialchars($searchDirect) ?>">
                            <i class="ri-search-line"></i>
                        </div>
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
                        <!-- Date Range Dropdown -->
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
                        <a href="users.php" class="admin-btn admin-btn-secondary form-action-btn">
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
                        <?php foreach (['all' => 'All Users', 'active' => 'Active', 'suspended' => 'Suspended', 'banned' => 'Banned'] as $key => $label): ?>
                            <a href="<?= buildTabUrl($key) ?>" class="admin-tab <?= $currentFilter === $key ? 'active' : '' ?>">
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
                                <th>Name</th>
                                <th>Email Address</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Township</th>
                                <th>City</th>
                                <th>Is Verified</th>
                                <th>Registered Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($users) > 0): ?>
                                <?php foreach ($users as $userItem): ?>
                                                          <?php
                                    // Format Registration Date
                                    $formattedDate = date('M d, Y', strtotime($userItem['created_at']));

                                    $statusClass = getUserStatusBadgeClass($userItem['status']);
                                    $statusLower = strtolower($userItem['status']);
                                    ?>
                                    <tr>
                                        <td><?= $userItem['id']; ?></td>
                                        <td class="td-name"><?= htmlspecialchars($userItem['full_name']); ?></td>
                                        <td class="td-email" title="<?= htmlspecialchars($userItem['email']); ?>"><?= htmlspecialchars($userItem['email']); ?></td>
                                        <td><?= htmlspecialchars($userItem['phone']); ?></td>
                                        <td class="td-address" title="<?= htmlspecialchars($userItem['address']); ?>"><?= htmlspecialchars($userItem['address']); ?></td>
                                        <td><?= htmlspecialchars($userItem['township_name']); ?></td>
                                        <td><?= htmlspecialchars($userItem['city_name']); ?></td>
                                        <td>
                                            <?php if ($userItem['is_verified'] == 1): ?>
                                                <span class="status-verified"><i class="ri-checkbox-circle-fill"></i> Verified</span>
                                            <?php else: ?>
                                                <span class="status-pending"><i class="ri-time-fill"></i> Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="td-date"><?= htmlspecialchars($formattedDate) ?></td>
                                        <td>
                                            <form action="admin_code.php" method="POST" class="status-form">
                                                <input type="hidden" name="user_id" value="<?= $userItem['id']; ?>">
                                                <select name="status" onchange="this.form.submit()" class="status-select <?= $statusClass ?>">
                                                    <option value="Active" <?= $statusLower === 'active' ? 'selected' : '' ?>>
                                                        Active
                                                    </option>
                                                    <option value="Suspended" <?= $statusLower === 'suspended' ? 'selected' : '' ?>>
                                                        Suspended
                                                    </option>
                                                    <option value="Banned" <?= $statusLower === 'banned' ? 'selected' : '' ?>>
                                                        Banned
                                                    </option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="admin-action-group">
                                                <a href="edit_user.php?id=<?= $userItem['id']; ?>" class="admin-action-btn edit" title="Edit"><i class="ri-pencil-line"></i></a>
                                                <a href="delete_user.php?id=<?= $userItem['id']; ?>" class="admin-action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this data?')"><i class="ri-delete-bin-6-line"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="empty-state">No Record Found</td>
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