<?php
$pageTitle = "Edit Post";

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to edit posts.");
    redirect("login.php");
}

// Retrieve Pending Reports Count
$pendingReports = count(getReportByStatus($pdo, 'Pending'));

// Retrieve Post
$postId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$postResult = getPostById($pdo, $postId);

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
                    <h3 class="admin-header-title">Edit Post</h3>
                </div>
                <div class="admin-header-right">
                    <a href="posts.php" class="admin-btn admin-btn-secondary">
                        <i class="ri-arrow-left-long-line"></i>
                        <span>Back</span>
                    </a>
                </div>
            </div>

            <!-- Form Card -->
            <div class="table-card">
                <div class="post-form-container">
                    <?php include '../includes/alert.php'; ?>

                    <?php if (isset($postResult['status']) && $postResult['status'] == 200): ?>
                        <?php $postData = $postResult['data']; ?>

                        <form action="admin_code.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="post_id" value="<?= htmlspecialchars($postData['id']) ?>">
                            <input type="hidden" name="pet_id" value="<?= htmlspecialchars($postData['pet_id']) ?>">

                            <div class="admin-form-grid post-form-grid">

                                <!-- Post Type -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="post_type">Post Type <span class="text-color-danger">*</span></label>
                                    <select name="post_type" id="post_type" class="admin-select admin-filter-select <?= hasError('post_type', $formErrors) ? 'error-border' : '' ?>" required>
                                        <option value="Lost" <?= ($formData['post_type'] ?? $postData['post_type']) == 'Lost' ? 'selected' : '' ?>>Lost</option>
                                        <option value="Found" <?= ($formData['post_type'] ?? $postData['post_type']) == 'Found' ? 'selected' : '' ?>>Found</option>
                                    </select>
                                    <small class="error-text"><?= getError('post_type', $formErrors) ?></small>
                                </div>

                                <!-- Status -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="status">Status <span class="text-color-danger">*</span></label>
                                    <select name="status" id="status" class="admin-select admin-filter-select <?= hasError('status', $formErrors) ? 'error-border' : '' ?>" required>
                                        <option value="Active" <?= ($formData['status'] ?? $postData['status']) == 'Active' ? 'selected' : '' ?>>Active</option>
                                        <option value="Reunited" <?= ($formData['status'] ?? $postData['status']) == 'Reunited' ? 'selected' : '' ?>>Reunited</option>
                                    </select>
                                    <small class="error-text"><?= getError('status', $formErrors) ?></small>
                                </div>

                                <!-- Pet Type -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="pet_type_id">Pet Type <span class="text-color-danger">*</span></label>
                                    <select name="pet_type_id" id="pet_type_id" class="admin-select admin-filter-select <?= hasError('pet_type_id', $formErrors) ? 'error-border' : '' ?>" required>
                                        <option value="">Select Pet Type</option>
                                        <?php foreach (getPetTypes($pdo) as $type): ?>
                                            <option value="<?= $type['id'] ?>" <?= ($formData['pet_type_id'] ?? $postData['pet_type_id']) == $type['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($type['type_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="error-text"><?= getError('pet_type_id', $formErrors) ?></small>
                                </div>

                                <!-- Breed -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="breed_id">Breed <span class="text-color-danger">*</span></label>
                                    <select name="breed_id" id="breed_id" class="admin-select admin-filter-select <?= hasError('breed_id', $formErrors) ? 'error-border' : '' ?>" required data-old-value="<?= htmlspecialchars($formData['breed_id'] ?? $postData['breed_id'] ?? '') ?>">
                                        <option value="">Select Breed</option>
                                        <?php
                                        $currentPetTypeId = $formData['pet_type_id'] ?? ($postData['pet_type_id'] ?? null);
                                        if ($currentPetTypeId) {
                                            $breedsList = getBreedsByType($pdo, $currentPetTypeId);
                                            if ($breedsList) {
                                                foreach ($breedsList as $breed):
                                        ?>
                                                    <option value="<?= $breed['id'] ?>" <?= ($formData['breed_id'] ?? $postData['breed_id']) == $breed['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($breed['breed_name']) ?>
                                                    </option>
                                        <?php
                                                endforeach;
                                            }
                                        }
                                        ?>
                                    </select>
                                    <small class="error-text"><?= getError('breed_id', $formErrors) ?></small>
                                </div>

                                <!-- Pet Name -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="pet_name">Pet Name</label>
                                    <input type="text" name="pet_name" id="pet_name" class="admin-input <?= hasError('pet_name', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['pet_name'] ?? $postData['name'] ?? '') ?>" placeholder="Enter pet name">
                                    <small class="error-text"><?= getError('pet_name', $formErrors) ?></small>
                                </div>

                                <!-- Pet Color -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="pet_color">Pet Color</label>
                                    <select name="pet_color" id="pet_color" class="admin-select <?= hasError('pet_color', $formErrors) ? 'error-border' : '' ?>">
                                        <option value="">Select Color</option>
                                        <option value="Black" <?= ($formData['pet_color'] ?? $postData['color'] ?? '') == 'Black' ? 'selected' : '' ?>>Black</option>
                                        <option value="White" <?= ($formData['pet_color'] ?? $postData['color'] ?? '') == 'White' ? 'selected' : '' ?>>White</option>
                                        <option value="Brown" <?= ($formData['pet_color'] ?? $postData['color'] ?? '') == 'Brown' ? 'selected' : '' ?>>Brown</option>
                                        <option value="Gray" <?= ($formData['pet_color'] ?? $postData['color'] ?? '') == 'Gray' ? 'selected' : '' ?>>Gray</option>
                                        <option value="Cream" <?= ($formData['pet_color'] ?? $postData['color'] ?? '') == 'Cream' ? 'selected' : '' ?>>Cream</option>
                                        <option value="Tan" <?= ($formData['pet_color'] ?? $postData['color'] ?? '') == 'Tan' ? 'selected' : '' ?>>Tan</option>
                                        <option value="Golden" <?= ($formData['pet_color'] ?? $postData['color'] ?? '') == 'Golden' ? 'selected' : '' ?>>Golden</option>
                                        <option value="Orange" <?= ($formData['pet_color'] ?? $postData['color'] ?? '') == 'Orange' ? 'selected' : '' ?>>Orange</option>
                                        <option value="Red" <?= ($formData['pet_color'] ?? $postData['color'] ?? '') == 'Red' ? 'selected' : '' ?>>Red / Rust / Mahogany</option>
                                        <option value="Other" <?= ($formData['pet_color'] ?? $postData['color'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                    <small class="error-text"><?= getError('pet_color', $formErrors) ?></small>
                                </div>

                                <!-- Pet Age -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="pet_age">Pet Age</label>
                                    <input type="number" name="pet_age" id="pet_age" class="admin-input <?= hasError('pet_age', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['pet_age'] ?? $postData['age'] ?? '') ?>" placeholder="Enter age">
                                    <small class="error-text"><?= getError('pet_age', $formErrors) ?></small>
                                </div>

                                <!-- Pet Gender -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="pet_gender">Pet Gender</label>
                                    <select name="pet_gender" id="pet_gender" class="admin-select admin-filter-select <?= hasError('pet_gender', $formErrors) ? 'error-border' : '' ?>">
                                        <option value="Male" <?= ($formData['pet_gender'] ?? $postData['gender']) == 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= ($formData['pet_gender'] ?? $postData['gender']) == 'Female' ? 'selected' : '' ?>>Female</option>
                                        <option value="Unknown" <?= ($formData['pet_gender'] ?? $postData['gender']) == 'Unknown' || empty($formData['pet_gender'] ?? $postData['gender']) ? 'selected' : '' ?>>Unknown</option>
                                    </select>
                                    <small class="error-text"><?= getError('pet_gender', $formErrors) ?></small>
                                </div>

                                <!-- Contact Phone -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="contact_phone">Contact Phone <span class="text-color-danger">*</span></label>
                                    <input type="text" name="contact_phone" id="contact_phone" class="admin-input <?= hasError('contact_phone', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['contact_phone'] ?? $postData['contact_phone'] ?? '') ?>" placeholder="Enter contact phone" required>
                                    <small class="error-text"><?= getError('contact_phone', $formErrors) ?></small>
                                </div>

                                <!-- Contact Email -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="contact_email">Contact Email <span class="text-color-danger">*</span></label>
                                    <input type="email" name="contact_email" id="contact_email" class="admin-input <?= hasError('contact_email', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['contact_email'] ?? $postData['contact_email'] ?? '') ?>" placeholder="Enter contact email" required>
                                    <small class="error-text"><?= getError('contact_email', $formErrors) ?></small>
                                </div>

                                <!-- Last Seen Date -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="last_seen_date">Last Seen Date</label>
                                    <input type="date" name="last_seen_date" id="last_seen_date" class="admin-input <?= hasError('last_seen_date', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['last_seen_date'] ?? $postData['last_seen_date'] ?? '') ?>">
                                    <small class="error-text"><?= getError('last_seen_date', $formErrors) ?></small>
                                </div>

                                <!-- Last Seen Time -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="last_seen_time">Last Seen Time</label>
                                    <input type="time" name="last_seen_time" id="last_seen_time" class="admin-input <?= hasError('last_seen_time', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['last_seen_time'] ?? $postData['last_seen_time'] ?? '') ?>">
                                    <small class="error-text"><?= getError('last_seen_time', $formErrors) ?></small>
                                </div>

                                <!-- Last Seen Location -->
                                <div class="admin-form-group post-form-group-full">
                                    <label class="admin-label" for="last_seen_location">Last Seen Location</label>
                                    <input type="text" name="last_seen_location" id="last_seen_location" class="admin-input <?= hasError('last_seen_location', $formErrors) ? 'error-border' : '' ?>" value="<?= htmlspecialchars($formData['last_seen_location'] ?? $postData['last_seen_location'] ?? '') ?>" placeholder="Enter detailed location">
                                    <small class="error-text"><?= getError('last_seen_location', $formErrors) ?></small>
                                </div>

                                <!-- City -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="city_id">City <span class="text-color-danger">*</span></label>
                                    <select name="city_id" id="city_id" class="admin-select admin-filter-select <?= hasError('city_id', $formErrors) ? 'error-border' : '' ?>" required>
                                        <option value="">Select City</option>
                                        <?php foreach (getAll($pdo, 'city') as $city): ?>
                                            <option value="<?= $city['id'] ?>" <?= ($formData['city_id'] ?? $postData['city_id']) == $city['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($city['city_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="error-text"><?= getError('city_id', $formErrors) ?></small>
                                </div>

                                <!-- Township -->
                                <div class="admin-form-group">
                                    <label class="admin-label" for="township_id">Township <span class="text-color-danger">*</span></label>
                                    <select name="township_id" id="township_id" class="admin-select admin-filter-select <?= hasError('township_id', $formErrors) ? 'error-border' : '' ?>" required data-old-value="<?= htmlspecialchars($formData['township_id'] ?? $postData['township_id'] ?? '') ?>">
                                        <option value="">Select Township</option>
                                        <?php
                                        $currentCityId = $formData['city_id'] ?? ($postData['city_id'] ?? null);
                                        if ($currentCityId) {
                                            $townshipsList = getTownshipsByCity($pdo, $currentCityId);
                                            if ($townshipsList) {
                                                foreach ($townshipsList as $township):
                                        ?>
                                                    <option value="<?= $township['id'] ?>" <?= ($formData['township_id'] ?? $postData['township_id']) == $township['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($township['township_name']) ?>
                                                    </option>
                                        <?php
                                                endforeach;
                                            }
                                        }
                                        ?>
                                    </select>
                                    <small class="error-text"><?= getError('township_id', $formErrors) ?></small>
                                </div>

                                <!-- Image -->
                                <div class="admin-form-group post-form-group-full">
                                    <label class="admin-label">Current Image</label>
                                    <?php if (!empty($postData['image'])): ?>
                                        <div class="current-image-preview">
                                            <img src="../<?= htmlspecialchars($postData['image']) ?>" alt="Pet Image">
                                            <div>
                                                <p class="current-image-preview-text">Current Image</p>
                                                <p class="current-image-preview-hint">Upload a new file below to replace it.</p>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-color-muted" style="margin-bottom: 0.75rem;">No image currently uploaded.</p>
                                    <?php endif; ?>

                                    <label class="admin-label" for="pet_image">Change Image</label>
                                    <input type="file" name="pet_image" id="pet_image" accept="image/*" class="admin-input <?= hasError('pet_image', $formErrors) ? 'error-border' : '' ?>">
                                    <small class="error-text"><?= getError('pet_image', $formErrors) ?></small>
                                </div>

                                <!-- Description -->
                                <div class="admin-form-group post-form-group-full">
                                    <label class="admin-label" for="description">Description</label>
                                    <textarea name="description" id="description" rows="4" class="admin-input admin-textarea <?= hasError('description', $formErrors) ? 'error-border' : '' ?>" placeholder="Enter post description"><?= htmlspecialchars($formData['description'] ?? $postData['description'] ?? '') ?></textarea>
                                    <small class="error-text"><?= getError('description', $formErrors) ?></small>
                                </div>

                                <!-- Form Actions -->
                                <div class="admin-form-group form-actions post-form-actions">
                                    <a href="posts.php" class="admin-btn admin-btn-secondary">Cancel</a>
                                    <button type="submit" name="btn_update_post" class="admin-btn admin-btn-primary primary-btn">Update Post</button>
                                </div>
                            </div>
                        </form>

                    <?php else: ?>
                        <!-- Error State -->
                        <div class="empty-state text-color-danger">
                            <?= htmlspecialchars($postResult['message'] ?? 'Post not found or invalid ID.') ?>
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

        // Dynamic Breed Dropdown
        document.getElementById('pet_type_id').addEventListener('change', function() {
            const petTypeId = this.value;
            const breedSelect = document.getElementById('breed_id');
            const oldBreed = breedSelect.getAttribute('data-old-value') || '';

            if (!petTypeId) {
                breedSelect.innerHTML = '<option value="">Select Breed</option>';
                return;
            }

            breedSelect.innerHTML = '<option value="">Loading breeds...</option>';
            fetch('../user/get_breeds.php?pet_type_id=' + petTypeId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.text();
                })
                .then(htmlOptions => {
                    breedSelect.innerHTML = '<option value="">Select Breed</option>' + htmlOptions;
                    if (oldBreed) {
                        breedSelect.value = oldBreed;
                    }
                })
                .catch(err => {
                    console.error('Ajax error fetching breeds:', err);
                    breedSelect.innerHTML = '<option value="">Select Breed</option>';
                });
        });

        // Dynamic Township Dropdown
        document.getElementById('city_id').addEventListener('change', function() {
            const cityId = this.value;
            const townshipSelect = document.getElementById('township_id');
            const oldTownship = townshipSelect.getAttribute('data-old-value') || '';

            if (!cityId) {
                townshipSelect.innerHTML = '<option value="">Select Township</option>';
                return;
            }

            townshipSelect.innerHTML = '<option value="">Loading townships...</option>';

            fetch('../user/get_townships.php?city_id=' + cityId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.text();
                })
                .then(htmlOptions => {
                    townshipSelect.innerHTML = '<option value="">Select Township</option>' + htmlOptions;
                    if (oldTownship) {
                        townshipSelect.value = oldTownship;
                    }
                })
                .catch(err => {
                    console.error('Ajax error fetching townships:', err);
                    townshipSelect.innerHTML = '<option value="">Select Township</option>';
                });
        });
        window.addEventListener('DOMContentLoaded', () => {
            const petTypeSelect = document.getElementById('pet_type_id');
            if (petTypeSelect.value) {
                petTypeSelect.dispatchEvent(new Event('change'));
            }

            const citySelect = document.getElementById('city_id');
            if (citySelect.value) {
                citySelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>

</html>