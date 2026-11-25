<?php
session_start();
require_once '../database/pdo.php';
require_once '../database/functions.php';
include '../includes/header.php';

// Check User Authentication
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    setMessage('Please Login First to Edit Post.', 'error');
    redirect('login.php');
}

$pageTitle = "Edit Post";
$userId = $_SESSION['auth_user']['id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />

    <style>
        :root {
            --color-primary: #1f7a5f;
            --color-primary-hover: rgba(31, 122, 95, 0.9);
            --color-bg: #f8fafc;
            --color-text: #1f2937;
            --color-text-muted: #4b5563;
            --color-border: #d1d5db;
            --color-border-light: #e5e7eb;
        }

        .page-container {
            max-width: 1024px;
            margin: 2.5rem auto;
            padding: 0 1rem;
        }

        @media (min-width: 640px) {
            .page-container {
                padding: 0 1.5rem;
            }
        }

        .page-header {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 640px) {
            .page-header {
                flex-direction: row;
                align-items: center;
            }
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-text);
            margin: 0;
        }

        @media (min-width: 640px) {
            .page-title {
                font-size: 1.875rem;
            }
        }

        .page-subtitle {
            color: var(--color-text-muted);
            margin-top: 0.25rem;
            font-size: 0.875rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 550;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .btn-primary {
            background-color: var(--color-primary);
            color: white;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .form-card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid var(--color-border-light);
            padding: 1.5rem;
        }

        @media (min-width: 640px) {
            .form-card {
                padding: 2rem;
            }
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-section-title i {
            color: var(--color-primary);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }

            .form-grid .col-span-2 {
                grid-column: 1 / -1;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.625rem 1rem;
            border: 1px solid var(--color-border);
            border-radius: 0.5rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: white;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 2px rgba(31, 122, 95, 0.2);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-file {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--color-border);
            border-radius: 0.5rem;
            background-color: white;
            font-size: 0.875rem;
            color: #374151;
        }

        .form-file::file-selector-button {
            margin-right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: none;
            font-size: 0.875rem;
            font-weight: 600;
            background-color: var(--color-primary);
            color: white;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .form-file::file-selector-button:hover {
            opacity: 0.9;
        }

        .helper-text {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }

        .image-preview-container {
            margin-bottom: 1rem;
        }

        .image-preview {
            width: 10rem;
            height: 10rem;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 2px solid var(--color-border-light);
        }

        .image-placeholder {
            width: 10rem;
            height: 10rem;
            background-color: #f3f4f6;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            border: 2px dashed var(--color-border);
        }

        .image-placeholder i {
            font-size: 2.25rem;
        }

        .new-image-preview {
            margin-top: 0.5rem;
            border-color: var(--color-primary);
        }

        .form-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--color-border-light);
        }

        @media (min-width: 640px) {
            .form-actions {
                flex-direction: row;
                justify-content: flex-end;
            }
        }

        .alert-box {
            padding: 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
    </style>
</head>

<body class="bg-gray-50">

    <div class="page-container">
        <!-- Validate Post ID -->
        <?php
        $paramResult = checkParamId('id');

        if (!is_numeric($paramResult)) {
            echo '<div class="alert-box alert-error">' . htmlspecialchars($paramResult) . '</div>';
        } else {
            $post = getPostById($pdo, $paramResult);

            if (isset($post['status']) && $post['status'] == 200) {
                $data = $post['data'];
                $isDraft = (strtolower($data['status']) === 'draft');
                $pageTitle = $isDraft ? "Publish Post" : "Edit Post";
        ?>

                <!-- Header -->
                <div class="page-header">
                    <div>
                        <h1 class="page-title"><?= $pageTitle ?></h1>
                        <p class="page-subtitle">Update your pet post information</p>
                    </div>
                    <a href="my_posts.php" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Back to My Posts
                    </a>
                </div>

                <!-- Edit Form -->
                <div class="form-card">
                    <?php include '../includes/alert.php'; ?>

                    <form action="../code.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($data['id']) ?>">
                        <input type="hidden" name="pet_id" value="<?= htmlspecialchars($data['pet_id']) ?>">

                        <!-- Pet Details -->
                        <div class="form-section">
                            <h2 class="form-section-title">
                                <i class="ri-paw-fill"></i> Pet Details
                            </h2>
                            <div class="form-grid">
                                <!-- Pet Name -->
                                <div class="form-group">
                                    <label class="form-label" for="pet_name">Pet Name</label>
                                    <input type="text" id="pet_name" name="pet_name" value="<?= htmlspecialchars($data['name']) ?>" class="form-input" placeholder="Enter pet name">
                                </div>

                                <!-- Pet Type -->
                                <div class="form-group">
                                    <label class="form-label" for="pet_type">Pet Type *</label>
                                    <select name="pet_type_id" id="pet_type" class="form-select" required>
                                        <option value="">Select Pet Type</option>
                                        <?php foreach (getPetTypes($pdo) as $type): ?>
                                            <option value="<?= $type['id'] ?>" <?= $type['id'] == $data['pet_type_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($type['type_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Breed Name -->
                                <div class="form-group">
                                    <label class="form-label" for="breed">Breed *</label>
                                    <select name="breed_id" id="breed" class="form-select" required>
                                        <option value="">Select Breed</option>
                                        <?php
                                        $breeds = getBreedsByType($pdo, $data['pet_type_id']);
                                        foreach ($breeds as $breed):
                                        ?>
                                            <option value="<?= $breed['id'] ?>" <?= $breed['id'] == $data['breed_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($breed['breed_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Pet Color -->
                                <div class="form-group">
                                    <label class="form-label" for="pet_color">Pet Color *</label>
                                    <select name="pet_color" id="pet_color" class="form-select" required>
                                        <option value="">Select Color</option>
                                        <option value="Black" <?= $data['color'] == 'Black' ? 'selected' : '' ?>>Black</option>
                                        <option value="White" <?= $data['color'] == 'White' ? 'selected' : '' ?>>White</option>
                                        <option value="Brown" <?= $data['color'] == 'Brown' ? 'selected' : '' ?>>Brown</option>
                                        <option value="Gray" <?= $data['color'] == 'Gray' ? 'selected' : '' ?>>Gray</option>
                                        <option value="Cream" <?= $data['color'] == 'Cream' ? 'selected' : '' ?>>Cream</option>
                                        <option value="Tan" <?= $data['color'] == 'Tan' ? 'selected' : '' ?>>Tan</option>
                                        <option value="Golden" <?= $data['color'] == 'Golden' ? 'selected' : '' ?>>Golden</option>
                                        <option value="Orange" <?= $data['color'] == 'Orange' ? 'selected' : '' ?>>Orange</option>
                                        <option value="Red" <?= $data['color'] == 'Red' ? 'selected' : '' ?>>Red / Rust / Mahogany</option>
                                        <option value="Other" <?= $data['color'] == 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>

                                <!-- Pet Age -->
                                <div class="form-group">
                                    <label class="form-label" for="pet_age">Pet Age</label>
                                    <input type="text" id="pet_age" name="pet_age" value="<?= htmlspecialchars($data['age']) ?>" class="form-input" placeholder="e.g., 2 years">
                                </div>

                                <!-- Pet Gender -->
                                <div class="form-group">
                                    <label class="form-label" for="pet_gender">Pet Gender</label>
                                    <select name="pet_gender" id="pet_gender" class="form-select">
                                        <option value="Male" <?= $data['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= $data['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                        <option value="Unknown" <?= $data['gender'] == 'Unknown' || empty($data['gender']) ? 'selected' : '' ?>>Unknown</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Post Details -->
                        <div class="form-section">
                            <h2 class="form-section-title">
                                <i class="ri-file-list-fill"></i> Post Details
                            </h2>
                            <div class="form-grid">
                                <!-- Post Type -->
                                <div class="form-group">
                                    <label class="form-label" for="post_type">Post Type *</label>
                                    <select name="post_type" id="post_type" class="form-select" required>
                                        <option value="Lost" <?= $data['post_type'] == 'Lost' ? 'selected' : '' ?>>Lost</option>
                                        <option value="Found" <?= $data['post_type'] == 'Found' ? 'selected' : '' ?>>Found</option>
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="form-group">
                                    <label class="form-label" for="status">Status *</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="Active" <?= ($data['status'] == 'Active' || $isDraft) ? 'selected' : '' ?>>Active</option>
                                        <option value="Reunited" <?= $data['status'] == 'Reunited' ? 'selected' : '' ?>>Reunited</option>
                                        <?php if ($isDraft): ?>
                                            <option value="Draft" selected>Draft</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Last Seen Date -->
                                <div class="form-group">
                                    <label class="form-label" for="last_seen_date">Last Seen Date</label>
                                    <input type="date" id="last_seen_date" name="last_seen_date" value="<?= htmlspecialchars($data['last_seen_date']) ?>" class="form-input">
                                </div>

                                <!-- Last Seen Time -->
                                <div class="form-group">
                                    <label class="form-label" for="last_seen_time">Last Seen Time</label>
                                    <input type="time" id="last_seen_time" name="last_seen_time" value="<?= htmlspecialchars($data['last_seen_time']) ?>" class="form-input">
                                </div>

                                <!-- City -->
                                <div class="form-group">
                                    <label class="form-label" for="city">City *</label>
                                    <select name="city_id" id="city" class="form-select" required>
                                        <option value="">Select City</option>
                                        <?php foreach (getAll($pdo, 'city') as $city): ?>
                                            <option value="<?= $city['id'] ?>" <?= $city['id'] == $data['city_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($city['city_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Township -->
                                <div class="form-group">
                                    <label class="form-label" for="township">Township *</label>
                                    <select name="township_id" id="township" class="form-select" required>
                                        <option value="">Select Township</option>
                                        <?php
                                        $townships = getTownshipsByCity($pdo, $data['city_id']);
                                        foreach ($townships as $township):
                                        ?>
                                            <option value="<?= $township['id'] ?>" <?= $township['id'] == $data['township_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($township['township_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Last Seen Location -->
                                <div class="form-group col-span-2">
                                    <label class="form-label" for="last_seen_location">Last Seen Location</label>
                                    <input type="text" id="last_seen_location" name="last_seen_location" value="<?= htmlspecialchars($data['last_seen_location']) ?>" class="form-input" placeholder="Enter detailed location">
                                </div>

                                <!-- Description -->
                                <div class="form-group col-span-2">
                                    <label class="form-label" for="description">Description</label>
                                    <textarea name="description" id="description" rows="4" class="form-textarea" placeholder="Enter post description"><?= htmlspecialchars($data['description']) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="form-section">
                            <h2 class="form-section-title">
                                <i class="ri-image-fill"></i> Post Image
                            </h2>
                            <div class="form-group">
                                <label class="form-label">Current Image</label>
                                <div class="image-preview-container">
                                    <?php if (!empty($data['image'])): ?>
                                        <img src="../<?= htmlspecialchars($data['image']) ?>" alt="Pet Image" class="image-preview" id="current-image">
                                    <?php else: ?>
                                        <div class="image-placeholder">
                                            <i class="ri-image-line"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <label class="form-label" for="image-upload">Change Image (Optional)</label>
                                <input type="file" name="pet_image" id="image-upload" accept="image/jpeg, image/png, image/webp" class="form-file">
                                <p class="helper-text">Leave empty to keep current image. Supports JPG, PNG, WebP (Max 5MB).</p>
                                <div id="preview-container"></div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="form-section">
                            <h2 class="form-section-title">
                                <i class="ri-contacts-fill"></i> Contact Information
                            </h2>
                            <div class="form-grid">
                                <!-- Contact Email -->
                                <div class="form-group">
                                    <label class="form-label" for="contact_email">Contact Email *</label>
                                    <input type="email" id="contact_email" name="contact_email" value="<?= htmlspecialchars($data['contact_email']) ?>" class="form-input" required>
                                </div>

                                <!-- Contact Phone -->
                                <div class="form-group">
                                    <label class="form-label" for="contact_phone">Contact Phone *</label>
                                    <input type="tel" id="contact_phone" name="contact_phone" value="<?= htmlspecialchars($data['contact_phone']) ?>" class="form-input" required>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="my_posts.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" name="btn_update_own_post" class="btn btn-update">
                                <?= $isDraft ? 'Publish Post' : 'Update Post' ?>
                            </button>
                        </div>
                    </form>
                </div>

        <?php
            } else {
                echo '<div class="alert-box alert-error">' . htmlspecialchars($post['message'] ?? 'Post not found.') . '</div>';
            }
        }
        ?>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Image Preview
        document.getElementById('image-upload').addEventListener('change', function() {
            const previewContainer = document.getElementById('preview-container');
            previewContainer.innerHTML = '';
            const file = this.files[0];

            if (file && file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'image-preview new-image-preview';
                img.onload = () => URL.revokeObjectURL(img.src);
                previewContainer.appendChild(img);
            }
        });

        // Breed Dropdown
        document.getElementById('pet_type').addEventListener('change', function() {
            const petTypeId = this.value;
            const breedSelect = document.getElementById('breed');
            if (!petTypeId) {
                breedSelect.innerHTML = '<option value="">Select Breed</option>';
                return;
            }
            breedSelect.innerHTML = '<option value="">Loading breeds...</option>';
            fetch('get_breeds.php?pet_type_id=' + petTypeId)
                .then(response => response.text())
                .then(htmlOptions => {
                    breedSelect.innerHTML = '<option value="">Select Breed</option>' + htmlOptions;
                })
                .catch(err => {
                    console.error('Ajax error:', err);
                    breedSelect.innerHTML = '<option value="">Error loading data</option>';
                });
        });

        // Township Dropdown
        document.getElementById('city').addEventListener('change', function() {
            const cityId = this.value;
            const townshipSelect = document.getElementById('township');
            if (!cityId) {
                townshipSelect.innerHTML = '<option value="">Select A City First</option>';
                return;
            }
            townshipSelect.innerHTML = '<option value="">Loading townships...</option>';
            fetch('get_townships.php?city_id=' + cityId)
                .then(response => response.text())
                .then(htmlOptions => {
                    townshipSelect.innerHTML = '<option value="">Select Township</option>' + htmlOptions;
                })
                .catch(err => {
                    console.error('Ajax error:', err);
                    townshipSelect.innerHTML = '<option value="">Error loading data</option>';
                });
        });
    </script>
</body>

</html>