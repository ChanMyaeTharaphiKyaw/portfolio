<?php
$pageTitle = "Pet Types Management";

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to access pet types.");
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

$totalRecords = getPetTypesCount($pdo, $search);
$totalPages = ceil($totalRecords / $perPage);

// Retrieve Pet Types
$petTypes = getFilteredPetTypes($pdo, $search, $perPage, $offset);
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
            <a href="blogs.php" class="admin-sidebar-link">
                <i class="ri-article-line"></i>
                <span>Blogs</span>
            </a>
            <a href="pet_types.php" class="admin-sidebar-link active">
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
                    <h3 class="admin-header-title">Pet Types</h3>
                </div>
                <div class="admin-header-right">
                    <button type="button" onclick="document.getElementById('addModal').classList.remove('is-hidden')" class="admin-btn admin-btn-primary primary-btn">
                        <i class="ri-add-line"></i>
                        <span>Add New Type</span>
                    </button>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="pet_types.php" class="admin-filter-form">
                <div class="admin-form-grid search-grid">
                    <!-- Search Bar -->
                    <div class="admin-form-group">
                        <label class="admin-label">Search</label>
                        <div class="search-icon-wrapper">
                            <input type="text" name="search" placeholder="Search pet type..." class="admin-input-search" value="<?= htmlspecialchars($search) ?>">
                            <i class="ri-search-line"></i>
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

            <!-- Table -->
            <div class="table-card">
                <div class="admin-table-wrapper">
                    <?php include '../includes/alert.php'; ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pet Type Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($petTypes) > 0): ?>
                                <?php foreach ($petTypes as $typeItem): ?>
                                    <tr>
                                        <td><?= $typeItem['id']; ?></td>
                                        <td class="td-name"><?= htmlspecialchars($typeItem['type_name']); ?></td>
                                        <td>
                                            <div class="admin-action-group">
                                                <button type="button" 
                                                        class="admin-action-btn edit review-btn" 
                                                        title="Edit"
                                                        onclick="openEditModal(<?= $typeItem['id'] ?>, '<?= htmlspecialchars($typeItem['type_name'], ENT_QUOTES) ?>')">
                                                    <i class="ri-pencil-line"></i>
                                                </button>
                                                <a href="delete_pet_type.php?id=<?= $typeItem['id']; ?>" 
                                                   class="admin-action-btn delete" 
                                                   title="Delete" 
                                                   onclick="return confirm('Are you sure you want to delete this data?')">
                                                    <i class="ri-delete-bin-6-line"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Empty State -->
                                <tr>
                                    <td colspan="3" class="empty-state">No Record Found</td>
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

    <!-- Add Pet Type Modal -->
    <div id="addModal" class="review-modal is-hidden">
        <div class="review-modal-overlay" onclick="document.getElementById('addModal').classList.add('is-hidden')"></div>
        <div class="review-modal-content">
            <form action="admin_code.php" method="POST">
                <div class="review-modal-header">
                    <h3 class="admin-header-title">Add New Pet Type</h3>
                </div>
                <div class="review-modal-body">
                    <div class="admin-form-group">
                        <label class="admin-label" for="type_name">Pet Type Name</label>
                        <input type="text" name="type_name" id="type_name" required class="admin-input" placeholder="e.g. Dog, Cat, Bird">
                    </div>
                </div>
                <div class="review-modal-footer">
                    <button type="button" onclick="document.getElementById('addModal').classList.add('is-hidden')" class="admin-btn admin-btn-secondary">Cancel</button>
                    <button type="submit" name="btn_add_type" class="admin-btn admin-btn-primary">Add Type</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Pet Type Modal -->
    <div id="editModal" class="review-modal is-hidden">
        <div class="review-modal-overlay" onclick="document.getElementById('editModal').classList.add('is-hidden')"></div>
        <div class="review-modal-content">
            <form action="admin_code.php" method="POST">
                <input type="hidden" name="type_id" id="edit_type_id">
                <div class="review-modal-header">
                    <h3 class="admin-header-title">Edit Pet Type</h3>
                </div>
                <div class="review-modal-body">
                    <div class="admin-form-group">
                        <label class="admin-label" for="edit_type_name">Pet Type Name</label>
                        <input type="text" name="type_name" id="edit_type_name" required class="admin-input">
                    </div>
                </div>
                <div class="review-modal-footer">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('is-hidden')" class="admin-btn admin-btn-secondary">Cancel</button>
                    <button type="submit" name="btn_update_type" class="admin-btn admin-btn-primary">Update Type</button>
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

        // Modal Functions
        function openEditModal(id, name) {
            document.getElementById('edit_type_id').value = id;
            document.getElementById('edit_type_name').value = name;
            document.getElementById('editModal').classList.remove('is-hidden');
        }
    </script>
</body>
</html>