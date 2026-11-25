<?php
$pageTitle = "Add New User";

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to access users.");
    redirect("login.php");
}

// Retrieve Pending Reports Count
$pendingReports = count(getReportByStatus($pdo, 'Pending'));

$formErrors = $_SESSION['errors'] ?? [];
$formData = $_SESSION['input'] ?? [];

if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}
if (isset($_SESSION['input'])) {
    unset($_SESSION['input']);
}
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
                    <h3 class="admin-header-title">Add New User</h3>
                </div>
                <div class="admin-header-right">
                    <a href="users.php" class="admin-btn admin-btn-secondary">
                        <i class="ri-arrow-left-long-line"></i>
                        <span>Back</span>
                    </a>
                </div>
            </div>

            <!-- Form Card -->
            <div class="table-card">
                <div class="user-form-container">
                    <?php include '../includes/alert.php'; ?>

                    <form action="admin_code.php" method="POST">
                        <div class="admin-form-grid user-form-grid">
                            
                            <!-- Full Name -->
                            <div class="admin-form-group">
                                <label class="admin-label" for="full_name">Full Name <span class="text-color-danger">*</span></label>
                                <input type="text" name="full_name" id="full_name" class="admin-input <?= hasError('full_name', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['full_name'] ?? '') ?>" placeholder="Enter full name">
                                <?php if (hasError('full_name', $formErrors)): ?>
                                    <small class="error-text"><?= getError('full_name', $formErrors) ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Email Address -->
                            <div class="admin-form-group">
                                <label class="admin-label" for="email">Email Address <span class="text-color-danger">*</span></label>
                                <input type="email" name="email" id="email" class="admin-input <?= hasError('email', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['email'] ?? '') ?>" placeholder="Enter email address">
                                <?php if (hasError('email', $formErrors)): ?>
                                    <small class="error-text"><?= getError('email', $formErrors) ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Phone Number -->
                            <div class="admin-form-group">
                                <label class="admin-label" for="phone">Phone Number <span class="text-color-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="admin-input <?= hasError('phone', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['phone'] ?? '') ?>" placeholder="Enter phone number">
                                <?php if (hasError('phone', $formErrors)): ?>
                                    <small class="error-text"><?= getError('phone', $formErrors) ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Password -->
                            <div class="admin-form-group">
                                <label class="admin-label" for="password">Password <span class="text-color-danger">*</span></label>
                                <input type="password" name="password" id="password" class="admin-input <?= hasError('password', $formErrors) ? 'error-border' : '' ?>" placeholder="Enter a strong password">
                                <?php if (hasError('password', $formErrors)): ?>
                                    <small class="error-text"><?= getError('password', $formErrors) ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- City -->
                            <div class="admin-form-group">
                                <label class="admin-label" for="city">City <span class="text-color-danger">*</span></label>
                                <select name="city" id="city" class="admin-select admin-filter-select <?= hasError('city', $formErrors) ? 'error-border' : '' ?>">
                                    <option value="">Select City</option>
                                    <?php
                                    $citiesList = getAll($pdo, 'city');
                                    foreach ($citiesList as $cityItem) {
                                        $isSelected = (isset($formData['city']) && $formData['city'] == $cityItem['id']) ? 'selected' : '';
                                        echo '<option value="' . $cityItem['id'] . '" ' . $isSelected . '>' . htmlspecialchars($cityItem['city_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                                <?php if (hasError('city', $formErrors)): ?>
                                    <small class="error-text"><?= getError('city', $formErrors) ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Township -->
                            <div class="admin-form-group">
                                <label class="admin-label" for="township">Township <span class="text-color-danger">*</span></label>
                                <select name="township" id="township" class="admin-select admin-filter-select <?= hasError('township', $formErrors) ? 'error-border' : '' ?>" data-old-value="<?= htmlspecialchars($formData['township'] ?? '') ?>">
                                    <option value="">Select A City First</option>
                                </select>
                                <?php if (hasError('township', $formErrors)): ?>
                                    <small class="error-text"><?= getError('township', $formErrors) ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Address (Full Width) -->
                            <div class="admin-form-group user-form-group-full">
                                <label class="admin-label" for="address">Address <span class="text-color-danger">*</span></label>
                                <textarea name="address" id="address" rows="2" class="admin-input admin-textarea <?= hasError('address', $formErrors) ? 'error-border' : '' ?>" placeholder="Enter detailed address"><?= htmlspecialchars($formData['address'] ?? '') ?></textarea>
                                <?php if (hasError('address', $formErrors)): ?>
                                    <small class="error-text"><?= getError('address', $formErrors) ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Form Actions -->
                            <div class="admin-form-group form-actions user-form-actions">
                                <a href="users.php" class="admin-btn admin-btn-secondary">Cancel</a>
                                <button type="submit" name="btn_add_user" class="admin-btn admin-btn-primary primary-btn">Add User</button>
                            </div>
                        </div>
                    </form>
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

        // Dynamic Township Dropdown
        document.getElementById('city').addEventListener('change', function() {
            const cityId = this.value;
            const townshipSelect = document.getElementById('township');
            const oldTownship = townshipSelect.getAttribute('data-old-value') || '';

            if (!cityId) {
                townshipSelect.innerHTML = '<option value="">Select A City First</option>';
                return;
            }

            townshipSelect.innerHTML = '<option value="">Loading townships...</option>';

            fetch('../user/get_townships.php?city_id=' + cityId)
                .then(response => response.text())
                .then(htmlOptions => {
                    townshipSelect.innerHTML = htmlOptions;
            
                    if (oldTownship) {
                        townshipSelect.value = oldTownship;
                    }
                })
                .catch(err => {
                    console.error('Ajax error:', err);
                    townshipSelect.innerHTML = '<option value="">Error loading data</option>';
                });
        });
        window.addEventListener('DOMContentLoaded', () => {
            const citySelect = document.getElementById('city');
            if (citySelect.value) {
                citySelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>