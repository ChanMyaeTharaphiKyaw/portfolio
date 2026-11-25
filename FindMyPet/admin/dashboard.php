<?php

require_once "../database/pdo.php";
require_once "../database/functions.php";

if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to access the dashboard.");
    redirect("login.php");
}

$adminName = $_SESSION['auth_admin']['name'] ?? 'Admin';

$dateFrom = $_GET['date_from'] ?? null;
$dateTo = $_GET['date_to'] ?? null;

$dateConditionP = getDateCondition($dateFrom, $dateTo, 'p');
$dateConditionU = getDateCondition($dateFrom, $dateTo, 'u');

// Fetch dashboard data
$kpis = getDashboardStatistics($pdo, $dateFrom, $dateTo);
$postsTimeData = getPostsTimeSeriesData($pdo, 'monthly', $dateConditionP);
$usersTimeData = getUsersTimeSeriesData($pdo, 'monthly', $dateConditionU);
$distributionData = getPostsDistributionData($pdo, $dateFrom, $dateTo);
$engagementData = getEngagementAndReportsData($pdo, $dateFrom, $dateTo);

$totalUsers = $kpis['total_users'] ?? 0;
$activeUsers = $kpis['active_users'] ?? 0;
$newUsersPeriod = $kpis['new_users_period'] ?? 0;
$totalPosts = $kpis['total_posts'] ?? 0;
$pendingReportsCount = $kpis['pending_reports'] ?? 0;
$successRate = $kpis['success_rate'] ?? 0;
$matchAccuracy = $kpis['match_accuracy'] ?? 0;

$postsByType = $distributionData['posts_by_type'] ?? [];
$userStatusDist = $distributionData['user_status_dist'] ?? [];
$postsByCity = $distributionData['posts_by_city'] ?? [];
$topTownships = $distributionData['top_townships'] ?? [];
$postsByPetType = $distributionData['posts_by_pet_type'] ?? [];
$reportCategories = $engagementData['report_categories'] ?? [];
$reportStatus = $engagementData['report_status'] ?? [];
$engagedPosts = $engagementData['engaged_posts'] ?? [];

// Prepare JSON data for Chart.js
$postsLabelsJson = json_encode($postsTimeData['labels'] ?? []);
$postsLostJson = json_encode($postsTimeData['lost'] ?? []);
$postsFoundJson = json_encode($postsTimeData['found'] ?? []);
$postsReunitedJson = json_encode($postsTimeData['reunited'] ?? []);

$userLabelsJson = json_encode($usersTimeData['labels'] ?? []);
$userCountsJson = json_encode($usersTimeData['counts'] ?? []);

$typeLabelsJson = json_encode(array_keys($postsByType));
$typeDataJson = json_encode(array_values($postsByType));

$userStatusLabelsJson = json_encode(array_keys($userStatusDist));
$userStatusDataJson = json_encode(array_values($userStatusDist));

$cityLabelsJson = json_encode(array_column($postsByCity, 'city_name'));
$cityDataJson = json_encode(array_column($postsByCity, 'count'));

$townshipLabelsJson = json_encode(array_column($topTownships, 'township_name'));
$townshipDataJson = json_encode(array_column($topTownships, 'count'));

$petTypeLabelsJson = json_encode(array_column($postsByPetType, 'type_name'));
$petTypeDataJson = json_encode(array_column($postsByPetType, 'count'));

$reportCatLabelsJson = json_encode(array_column($reportCategories, 'reason'));
$reportCatDataJson = json_encode(array_column($reportCategories, 'count'));

$reportStatusLabelsJson = json_encode(array_keys($reportStatus));
$reportStatusDataJson = json_encode(array_values($reportStatus));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | FindMyPet</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="dashboard.php" class="admin-sidebar-link active">
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
                <?php if ($pendingReportsCount > 0): ?>
                    <span class="badge"><?= $pendingReportsCount ?></span>
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

        <!-- Dashboard Header with Filter -->
        <header class="dashboard-header">
            <form method="GET" action="dashboard.php">
                <div class="admin-header">
                    <div class="admin-header-left">
                        <button type="button" id="sidebar-toggle" class="sidebar-toggle-btn">
                            <i class="ri-menu-line"></i>
                        </button>
                        <div>
                            <h1 class="dashboard-title">Analytics Dashboard</h1>
                            <p class="dashboard-subtitle">Welcome back, <?= htmlspecialchars($adminName) ?>.</p>
                        </div>
                    </div>
                    <div class="admin-header-right">
                        <div class="dashboard-filter-form">
                            <span class="dashboard-filter-label">Date from</span>
                            <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom ?? '') ?>" class="dashboard-filter-input">
                            <span class="dashboard-filter-label">to</span>
                            <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo ?? '') ?>" class="dashboard-filter-input">
                            <button type="submit" class="admin-btn admin-btn-primary">Apply</button>
                            <a href="dashboard.php" class="admin-btn admin-btn-secondary">Clear</a>
                        </div>
                    </div>
                </div>
            </form>
        </header>

        <!-- KPI Cards Row 1 -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div>
                    <p class="kpi-label">Total Users</p>
                    <p class="kpi-value"><?= number_format($totalUsers) ?></p>
                </div>
                <div class="kpi-icon bg-blue"><i class="ri-user-line"></i></div>
            </div>

            <div class="kpi-card">
                <div>
                    <p class="kpi-label">Active Users</p>
                    <p class="kpi-value"><?= number_format($activeUsers) ?></p>
                </div>
                <div class="kpi-icon bg-indigo"><i class="ri-user-follow-line"></i></div>
            </div>

            <div class="kpi-card">
                <div>
                    <p class="kpi-label"><?= ($dateFrom || $dateTo) ? 'Users in Period' : 'New (Month)' ?></p>
                    <p class="kpi-value"><?= number_format($newUsersPeriod) ?></p>
                </div>
                <div class="kpi-icon bg-purple"><i class="ri-user-add-line"></i></div>
            </div>

            <div class="kpi-card">
                <div>
                    <p class="kpi-label">Total Posts</p>
                    <p class="kpi-value"><?= number_format($totalPosts) ?></p>
                </div>
                <div class="kpi-icon bg-primary"><i class="ri-file-list-line"></i></div>
            </div>
        </div>

        <!-- KPI Cards Row 2 -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div>
                    <p class="kpi-label">Pending Reports</p>
                    <p class="kpi-value text-red"><?= number_format($pendingReportsCount) ?></p>
                </div>
                <div class="kpi-icon bg-red"><i class="ri-alarm-warning-line"></i></div>
            </div>

            <div class="kpi-card">
                <div>
                    <p class="kpi-label">Success Rate</p>
                    <p class="kpi-value text-primary"><?= $successRate ?>%</p>
                </div>
                <div class="kpi-icon bg-green"><i class="ri-trophy-line"></i></div>
            </div>

            <div class="kpi-card">
                <div>
                    <p class="kpi-label">Match Accuracy</p>
                    <p class="kpi-value text-purple"><?= $matchAccuracy ?>%</p>
                </div>
                <div class="kpi-icon bg-purple"><i class="ri-checkbox-circle-line"></i></div>
            </div>
            
            <div class="kpi-card" style="visibility: hidden;"></div>
        </div>

        <!-- Charts Row 1 -->
        <div class="chart-grid">
            <div class="chart-card">
                <h3 class="chart-card-title">Number of Posts Over Time</h3>
                <div class="chart-container">
                    <canvas id="postsOverTimeChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3 class="chart-card-title">User Growth Over Time</h3>
                <div class="chart-container">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="chart-grid">
            <div class="chart-card">
                <h3 class="chart-card-title">Posts by Pet Type</h3>
                <div class="chart-container-small">
                    <canvas id="postsByPetTypeChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3 class="chart-card-title">User Status Distribution</h3>
                <div class="chart-container-small">
                    <canvas id="userStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 3 -->
        <div class="chart-card">
            <h3 class="chart-card-title">Top 5 Cities</h3>
            <div class="chart-container">
                <canvas id="postsByCityChart"></canvas>
            </div>
        </div>

        <!-- Charts Row 4 -->
        <div class="chart-grid">
            <div class="chart-card">
                <h3 class="chart-card-title">Top Townships</h3>
                <div class="chart-container-small">
                    <canvas id="topTownshipsChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3 class="chart-card-title">Report Categories</h3>
                <div class="chart-container-small">
                    <canvas id="reportCategoriesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 5 -->
        <div class="chart-grid">
            <div class="chart-card">
                <h3 class="chart-card-title">Posts by Type (Lost vs Found)</h3>
                <div class="chart-container-small">
                    <canvas id="postsByTypePieChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3 class="chart-card-title">Report Status</h3>
                <div class="chart-container-small">
                    <canvas id="reportStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="data-table-card">
            <div class="data-table-header">
                <h3 class="data-table-title">Most Engaged Posts</h3>
                <p class="data-table-subtitle">Top 5 by comments</p>
            </div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Post ID</th>
                            <th>Pet Name</th>
                            <th>Type</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($engagedPosts)): ?>
                            <tr>
                                <td colspan="4" class="empty-state">No engaged posts yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($engagedPosts as $ep): ?>
                                <tr>
                                    <td class="font-mono text-xs text-gray-500">#<?= $ep['id'] ?></td>
                                    <td class="td-name"><?= htmlspecialchars($ep['pet_name'] ?? 'Unknown') ?></td>
                                    <td>
                                        <span class="admin-tab-badge <?= strtolower($ep['post_type']) == 'lost' ? 'badge-lost' : 'badge-found' ?> text-uppercase">
                                            <?= htmlspecialchars(ucfirst($ep['post_type'])) ?>
                                        </span>
                                    </td>
                                    <td class="td-date" style="font-weight: 600;"><?= $ep['comment_count'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

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

        // Chart.js Configurations
        Chart.defaults.font.family = "'Poppins', sans-serif";
        Chart.defaults.color = '#4b5563';

        new Chart(document.getElementById('postsOverTimeChart'), {
            type: 'line',
            data: {
                labels: <?= $postsLabelsJson ?>,
                datasets: [
                    { label: 'Lost Posts', data: <?= $postsLostJson ?>, borderColor: '#f36f1c', backgroundColor: 'rgba(243, 111, 28, 0.1)', fill: false, tension: 0.4, pointRadius: 4 },
                    { label: 'Found Posts', data: <?= $postsFoundJson ?>, borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.1)', fill: false, tension: 0.4, pointRadius: 4 },
                    { label: 'Reunited Posts', data: <?= $postsReunitedJson ?>, borderColor: '#1f7a5f', backgroundColor: 'rgba(31, 122, 95, 0.1)', fill: true, tension: 0.4, pointRadius: 4 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { usePointStyle: true, padding: 20 } } },
                scales: { y: { beginAtZero: true, grid: { color: '#e5e7eb' } }, x: { grid: { display: false } } }
            }
        });

        new Chart(document.getElementById('userGrowthChart'), {
            type: 'line',
            data: {
                labels: <?= $userLabelsJson ?>,
                datasets: [{
                    label: 'New Users', data: <?= $userCountsJson ?>,
                    borderColor: '#8b5cf6', backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true, tension: 0.4, pointBackgroundColor: '#8b5cf6'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: '#e5e7eb' } }, x: { grid: { display: false } } }
            }
        });

        new Chart(document.getElementById('postsByPetTypeChart'), {
            type: 'bar',
            data: {
                labels: <?= $petTypeLabelsJson ?>,
                datasets: [{ label: 'Posts', data: <?= $petTypeDataJson ?>, backgroundColor: '#f36f1c', borderRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: '#e5e7eb' } }, x: { grid: { display: false } } }
            }
        });

        new Chart(document.getElementById('userStatusChart'), {
            type: 'bar',
            data: {
                labels: <?= $userStatusLabelsJson ?>,
                datasets: [{ label: 'Users', data: <?= $userStatusDataJson ?>, backgroundColor: ['#16a34a', '#6b7280', '#dc2626'], borderRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: '#e5e7eb' } }, x: { grid: { display: false } } }
            }
        });

        new Chart(document.getElementById('postsByCityChart'), {
            type: 'bar',
            data: {
                labels: <?= $cityLabelsJson ?>,
                datasets: [{ label: 'Posts', data: <?= $cityDataJson ?>, backgroundColor: '#8b5cf6', borderRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: '#e5e7eb' } }, x: { grid: { display: false } } }
            }
        });

        new Chart(document.getElementById('topTownshipsChart'), {
            type: 'bar',
            data: {
                labels: <?= $townshipLabelsJson ?>,
                datasets: [{ label: 'Posts', data: <?= $townshipDataJson ?>, backgroundColor: '#06b6d4', borderRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: '#e5e7eb' } }, x: { grid: { display: false } } }
            }
        });

        new Chart(document.getElementById('postsByTypePieChart'), {
            type: 'pie',
            data: {
                labels: <?= $typeLabelsJson ?>,
                datasets: [{ data: <?= $typeDataJson ?>, backgroundColor: ['#f36f1c', '#1f7a5f'], borderWidth: 2, borderColor: '#ffffff' }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } }
            }
        });

        new Chart(document.getElementById('reportStatusChart'), {
            type: 'doughnut',
            data: {
                labels: <?= $reportStatusLabelsJson ?>,
                datasets: [{ data: <?= $reportStatusDataJson ?>, backgroundColor: ['#ca8a04', '#16a34a'], borderWidth: 2, borderColor: '#ffffff' }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } },
                cutout: '70%'
            }
        });

        new Chart(document.getElementById('reportCategoriesChart'), {
            type: 'bar',
            data: {
                labels: <?= $reportCatLabelsJson ?>,
                datasets: [{ label: 'Reports', data: <?= $reportCatDataJson ?>, backgroundColor: '#dc2626', borderRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: '#e5e7eb' } }, x: { grid: { display: false } } }
            }
        });
    </script>
</body>
</html>