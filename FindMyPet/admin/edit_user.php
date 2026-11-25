<?php

$pageTitle = "Edit User";

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to edit users.");
    redirect("login.php");
}

// Retrieve Pending Reports Count
$pendingReports = count(getReportByStatus($pdo, 'Pending'));

// Retrieve User
$userId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$userResult = getUserById($pdo, $userId);

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
                    <h3 class="admin-header-title">Edit User</h3>
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

                    <?php if (isset($userResult['status']) && $userResult['status'] == 200): ?>
                        <?php $userData = $userResult['data']; ?>

                                                <form action="admin_code.php" method="POST">
                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($userData['id']) ?>">

                            <div class="admin-form-grid user-form-grid">
                                
                                <!-- Full Name -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="full_name">Full Name <span class="text-color-danger">*</span></label>
                                    <input type="text" name="full_name" id="full_name" class="admin-input <?= hasError('full_name', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['full_name'] ?? $userData['full_name']) ?>" placeholder="Enter full name" >
                                    <small class="error-text"><?= getError('full_name', $formErrors) ?></small>
                                </div>

                                <!-- Email Address -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="email">Email Address <span class="text-color-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="admin-input <?= hasError('email', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['email'] ?? $userData['email']) ?>" placeholder="Enter email address" >
                                    <small class="error-text"><?= getError('email', $formErrors) ?></small>
                                </div>

                                <!-- Phone Number -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="phone">Phone Number <span class="text-color-danger">*</span></label>
                                    <input type="text" name="phone" id="phone" class="admin-input <?= hasError('phone', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['phone'] ?? $userData['phone']) ?>" placeholder="Enter phone number" >
                                    <small class="error-text"><?= getError('phone', $formErrors) ?></small>
                                </div>

                                <!-- Password -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="password">Password <span class="text-color-muted">(Leave blank to keep current)</span></label>
                                    <input type="password" name="password" id="password" class="admin-input <?= hasError('password', $formErrors) ? 'error-border' : '' ?>" placeholder="Enter new password">
                                    <small class="error-text"><?= getError('password', $formErrors) ?></small>
                                </div>

                                <!-- City -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="city">City <span class="text-color-danger">*</span></label>
                                    <select name="city" id="city" class="admin-select admin-filter-select <?= hasError('city', $formErrors) ? 'error-border' : '' ?>" >
                                        <option value="">Select City</option>
                                        <?php
                                        $citiesList = getAll($pdo, 'city');
                                        foreach ($citiesList as $cityItem) {
                                            $savedCity = $formData['city'] ?? $userData['city_id'];
                                            $isSelected = ($cityItem['id'] == $savedCity) ? 'selected' : '';
                                            echo '<option value="' . $cityItem['id'] . '" ' . $isSelected . '>' . htmlspecialchars($cityItem['city_name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <small class="error-text"><?= getError('city', $formErrors) ?></small>
                                </div>

                                <!-- Township -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="township">Township <span class="text-color-danger">*</span></label>
                                    <select name="township" id="township" class="admin-select admin-filter-select <?= hasError('township', $formErrors) ? 'error-border' : '' ?>"  data-old-value="<?= htmlspecialchars($formData['township'] ?? $userData['township_id'] ?? '') ?>">
                                        <option value="">Select A City First</option>
                                        <?php
                                        $currentCityId = $formData['city'] ?? ($userData['city_id'] ?? null);
                                        if ($currentCityId) {
                                            $townshipsList = getTownshipsByCity($pdo, $currentCityId);
                                            foreach ($townshipsList as $townshipItem) {
                                                $savedTownship = $formData['township'] ?? $userData['township_id'];
                                                $isSelected = ($townshipItem['id'] == $savedTownship) ? 'selected' : '';
                                                echo '<option value="' . $townshipItem['id'] . '" ' . $isSelected . '>' . htmlspecialchars($townshipItem['township_name']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <small class="error-text"><?= getError('township', $formErrors) ?></small>
                                </div>

                                <!-- Is Verified -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="is_verified">Verification Status <span class="text-color-danger">*</span></label>
                                    <select name="is_verified" id="is_verified" class="admin-select admin-filter-select <?= hasError('is_verified', $formErrors) ? 'error-border' : '' ?>" required>
                                        <option value="0" <?= (isset($formData['is_verified']) ? $formData['is_verified'] == '0' : (isset($userData['is_verified']) && $userData['is_verified'] == 0)) ? 'selected' : '' ?>>Pending</option>
                                        <option value="1" <?= (isset($formData['is_verified']) ? $formData['is_verified'] == '1' : (isset($userData['is_verified']) && $userData['is_verified'] == 1)) ? 'selected' : '' ?>>Verified</option>
                                    </select>
                                    <small class="error-text"><?= getError('is_verified', $formErrors) ?></small>
                                </div>

                                <!-- Account Status -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="status">Account Status <span class="text-color-danger">*</span></label>
                                    <select name="status" id="status" class="admin-select admin-filter-select <?= hasError('status', $formErrors) ? 'error-border' : '' ?>" required>
                                        <?php 
                                        $currentStatus = isset($formData['status']) ? strtolower($formData['status']) : (isset($userData['status']) ? strtolower($userData['status']) : 'active');
                                        ?>
                                        <option value="active" <?= ($currentStatus == 'active') ? 'selected' : '' ?>>Active</option>
                                        <option value="suspended" <?= ($currentStatus == 'suspended') ? 'selected' : '' ?>>Suspended</option>
                                        <option value="banned" <?= ($currentStatus == 'banned') ? 'selected' : '' ?>>Banned</option>
                                    </select>
                                    <small class="error-text"><?= getError('status', $formErrors) ?></small>
                                </div>

                                <!-- Address -->
                                <div class="admin-form-group user-form-group-full">
                                    <label class="admin-label" for="address">Address <span class="text-color-danger">*</span></label>
                                    <input type="text" name="address" id="address" class="admin-input <?= hasError('address', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['address'] ?? $userData['address']) ?>" placeholder="Enter detailed address" >
                                    <small class="error-text"><?= getError('address', $formErrors) ?></small>
                                </div>

                                <!-- Form Actions -->
                                <div class="admin-form-group form-actions user-form-actions">
                                    <a href="users.php" class="admin-btn admin-btn-secondary">Cancel</a>
                                    <button type="submit" name="btn_update_user" class="admin-btn admin-btn-primary primary-btn">Update User</button>
                                </div>
                            </div>
                        </form>

                    <?php else: ?>
                        <!-- Error State -->
                        <div class="empty-state text-color-danger">
                            <?= htmlspecialchars($userResult['message'] ?? 'User not found or invalid ID.') ?>
                        </div>
                    <?php endif; ?>
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