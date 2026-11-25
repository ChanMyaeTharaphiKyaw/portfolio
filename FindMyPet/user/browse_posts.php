<?php
$pageTitle = "View Lost and Found Pets";
require_once '../database/pdo.php';
require_once '../database/functions.php';

$postType = $_GET['post_type'] ?? 'all';
$petType  = $_GET['pet_type'] ?? 'all';
$city     = $_GET['city'] ?? 'all';
$township = $_GET['township'] ?? 'all';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo   = $_GET['date_to'] ?? '';
$sort     = $_GET['sort'] ?? 'newest';
$status   = $_GET['status'] ?? 'active';

// Pagination 
$itemsPerPage = 6;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, $currentPage);
$offset = ($currentPage - 1) * $itemsPerPage;

$searchQuery = trim($_GET['q'] ?? '');
$petName     = trim($_GET['pet_name'] ?? '');
$breedId     = !empty($_GET['breed']) ? (int)$_GET['breed'] : 0;

// Fetch data based on filters
$posts = getFilteredPosts($pdo, $_GET);
$totalPosts = count($posts);
$totalPages = ceil($totalPosts / $itemsPerPage);

// Get posts
$paginatedPosts = array_slice($posts, $offset, $itemsPerPage);

$petTypes = getPetTypes($pdo);
$cities = getCities($pdo);
$townships = ($city !== 'all') ? getTownshipsByCity($pdo, $city) : [];

$breeds = [];
if ($petType !== 'all') {
    $breeds = getBreedsByType($pdo, $petType);
}

$isSearch = !empty($searchQuery) || !empty($petName) || $breedId > 0;

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>

        /* --- Layout & Container --- */
        .browse-container {
            max-width: 1200px;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
            padding: 0 20px;
            margin: 50px auto;
        }

        /* --- Filter Sidebar --- */
        .filter-sidebar {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 15px;
            height: fit-content;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid #e0eee9;
            position: sticky;
            top: 20px;
        }

        .filter-sidebar h3 {
            color: #1f7a5f;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 700;
            font-family: "Poppins", sans-serif;
        }

        .filter-group {
            margin-bottom: 20px;
        }

        .filter-group > label {
            display: block;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #374151;
            font-family: "Poppins", sans-serif;
        }

        .filter-radio-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .radio-label {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            position: relative;
            margin: 0;
        }

        .radio-label input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .radio-label span {
            font-family: "Poppins", sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #4b5563;
            padding: 8px 15px;
            display: inline-block;
            white-space: nowrap;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .radio-label:hover span {
            background-color: #f3f4f6;
            color: #1f7a5f;
            border-color: #1f7a5f;
        }

        .radio-label input[type="radio"]:checked + span {
            background-color: #1f7a5f;
            color: #ffffff;
            border-color: #1f7a5f;
        }

        .filter-group select,
        .filter-group input[type="date"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background-color: #fdfdfd;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .filter-group select:focus,
        .filter-group input[type="date"]:focus {
            border-color: #1f7a5f;
            box-shadow: 0 0 5px rgba(31, 122, 95, 0.2);
        }

        .date-range-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .date-label {
            display: block;
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 4px;
        }

        /* Sidebar Action Buttons */
        .btn-filter {
            width: 100%;
            padding: 12px;
            background-color: #1f7a5f;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .btn-filter:hover {
            background-color: #165d48;
        }

        .btn-clear-filter {
            background: #f3f4f6;
            color: #333;
            text-align: center;
            display: block;
            margin-top: 10px;
            text-decoration: none;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .btn-clear-filter:hover {
            background: #e5e7eb;
            color: #1f7a5f;
            border-color: #1f7a5f;
        }

        /* --- Pet Results Section --- */
        .pet-results {
            display: flex;
            flex-direction: column;
        }

        /* Search Info Banner */
        .search-info-banner {
            background: #f0fdf4;
            border-left: 4px solid #1f7a5f;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
        }

        .search-info-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-info-title {
            color: #1f7a5f;
            margin: 0 0 0.25rem 0;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-info-text {
            margin: 0;
            color: #4b5563;
            font-size: 0.9rem;
        }

        .btn-clear-search {
            padding: 0.5rem 1rem;
            background: #1f7a5f;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.2s ease;
        }

        .btn-clear-search:hover {
            background: #165c47;
        }

        /* Status Tabs */
        .status-tabs {
            display: flex;
            gap: 20px;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 24px;
        }

        .tab-btn {
            padding: 12px 20px;
            text-decoration: none;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }

        .tab-btn:hover {
            color: #1f7a5f;
        }

        .tab-btn.active {
            color: #1f7a5f;
            border-bottom-color: #1f7a5f;
        }

        /* Results Header */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 24px;
        }

        .results-header h2 {
            color: #1f7a5f;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .sort-view-controls {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .sort-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sort-label {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
        }

        .sort-control select {
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.9rem;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .sort-control select:hover,
        .sort-control select:focus {
            border-color: #1f7a5f;
            outline: none;
            box-shadow: 0 0 0 3px rgba(31, 122, 95, 0.1);
        }

        /* --- Pet Grid & Cards --- */
        .pet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
            margin-top: 20px;
            justify-items: center; 
        }

        .pet-card {
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e0eee9;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            width: 100%;
            max-width: 340px; 
        }

        .pet-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(31, 122, 95, 0.15);
        }

        .card-image-wrapper {
            position: relative;
            height: 180px;
            width: 100%;
        }

        .card-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .status-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 4px 12px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .badge-lost { background: #ef4444; }
        .badge-found { background: #22c55e; }
        .badge-reunited { background: #3b82f6; }

        .card-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .pet-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1f7a5f;
            margin: 0 0 8px 0;
            line-height: 1.3;
        }

        .pet-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin: 15px 0;
            font-size: 0.9rem;
            color: #666;
        }

        .pet-meta i {
            color: #1f7a5f;
            width: 16px;
            text-align: center;
        }

        .no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-results-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #ccc;
        }

        /* --- Pagination Styles --- */
        .pagination-wrapper {
            padding: 0.75rem 1rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            background-color: #f9fafb;
            margin-top: 2rem;
            border-radius: 8px;
        }

        @media (min-width: 640px) {
            .pagination-wrapper {
                flex-direction: row;
            }
        }

        .pagination-info {
            font-size: 0.875rem;
            color: #4b5563;
        }

        .pagination-info .font-medium {
            font-weight: 500;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .pagination-btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            background-color: white;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: background-color 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .pagination-btn:hover {
            background-color: #f9fafb;
        }

        .pagination-btn.active {
            background-color: #1f7a5f;
            color: white;
            border-color: #1f7a5f;
        }

        .pagination-btn.disabled {
            color: #9ca3af;
            background-color: #f3f4f6;
            border-color: #e5e7eb;
            cursor: not-allowed;
        }

        .pagination-ellipsis {
            padding: 0 0.5rem;
            color: #9ca3af;
        }

        @media (max-width: 1024px) {
            .browse-container {
                grid-template-columns: 240px 1fr;
                gap: 20px;
            }

            .filter-sidebar {
                padding: 20px;
            }

            .pet-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .pet-name {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 768px) {
            .browse-container {
                grid-template-columns: 1fr;
                gap: 20px;
                margin-top: 30px;
            }

            .filter-sidebar {
                position: relative;
                top: auto;
                width: 100%;
                margin-bottom: 10px;
            }

            .pet-grid {
                grid-template-columns: 1fr;
            }

            .pet-card {
                max-width: 100%; 
            }

            .card-image-wrapper {
                height: 200px;
            }

            .results-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-info-content {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .browse-container {
                padding: 0 15px;
            }

            .filter-sidebar h3 {
                font-size: 18px;
            }

            .filter-group > label {
                font-size: 14px;
            }

            .pet-name {
                font-size: 1.05rem;
            }

            .pet-meta {
                font-size: 0.85rem;
            }

            .card-image-wrapper {
                height: 180px;
            }
        }
    </style>
</head>

<body>
    <main class="browse-container">

        <!-- Filter Sidebar -->
        <aside class="filter-sidebar">
            <h3>Filters</h3>

            <form action="" method="GET" id="filter-form">

                <!-- Post Type -->
                <div class="filter-group">
                    <label>Post Type</label>
                    <div class="filter-radio-group">
                        <label class="radio-label">
                            <input type="radio" name="post_type" value="all" <?= ($postType == 'all') ? 'checked' : '' ?>>
                            <span>All Posts</span>
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="post_type" value="lost" <?= ($postType == 'lost') ? 'checked' : '' ?>>
                            <span>Lost</span>
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="post_type" value="found" <?= ($postType == 'found') ? 'checked' : '' ?>>
                            <span>Found</span>
                        </label>
                    </div>
                </div>

                <!-- Pet Type -->
                <div class="filter-group">
                    <label>Pet Type</label>
                    <div class="filter-radio-group">
                        <label class="radio-label">
                            <input type="radio" name="pet_type" value="all" <?= ($petType == 'all') ? 'checked' : '' ?>>
                            <span>All Pets</span>
                        </label>
                        <?php foreach ($petTypes as $type): ?>
                            <label class="radio-label">
                                <input type="radio" name="pet_type" value="<?= $type['id'] ?>" <?= ($petType == $type['id']) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($type['type_name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- City -->
                <div class="filter-group">
                    <label>City</label>
                    <select name="city" id="city">
                        <option value="all" <?= ($city == 'all') ? 'selected' : '' ?>>All Cities</option>
                        <?php foreach ($cities as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($city == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['city_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Township -->
                <div class="filter-group">
                    <label>Township</label>
                    <select id="township" name="township">
                        <option value="all" <?= ($township == 'all') ? 'selected' : '' ?>>All Townships</option>
                        <?php if ($city !== 'all' && !empty($townships)): ?>
                            <?php foreach ($townships as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= ($township == $t['id']) ? 'selected' : '' ?>><?= htmlspecialchars($t['township_name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Date Range -->
                <div class="filter-group">
                    <label>Date Range</label>
                    <div class="date-range-container">
                        <div>
                            <label class="date-label">Start Date</label>
                            <input type="date" name="date_from" id="date-from" value="<?= htmlspecialchars($dateFrom) ?>">
                        </div>
                        <div>
                            <label class="date-label">End Date</label>
                            <input type="date" name="date_to" id="date-to" value="<?= htmlspecialchars($dateTo) ?>">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">

                <button type="submit" name="btn_apply_filter" class="btn-filter">Apply Filters</button>
                <a href="?" class="btn-clear-filter">Clear All</a>
            </form>
        </aside>

        <!-- Listing -->
        <section class="pet-results">
            <?php if ($isSearch): ?>
                <div class="search-info-banner">
                    <div class="search-info-content">
                        <div>
                            <h3 class="search-info-title">
                                <i class="ri-search-line"></i> Search Results
                            </h3>
                            <p class="search-info-text">
                                Found <strong><?= count($posts) ?></strong> post(s)
                                <?php if (!empty($searchQuery)): ?>
                                    matching "<strong><?= htmlspecialchars($searchQuery) ?></strong>"
                                <?php endif; ?>
                                <?php if (!empty($petName)): ?>
                                    with name "<strong><?= htmlspecialchars($petName) ?></strong>"
                                <?php endif; ?>
                            </p>
                        </div>
                        <a href="?" class="btn-clear-search">
                            <i class="ri-close-line"></i> Clear Search
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="status-tabs">
                <a href="?<?= http_build_query(array_merge($_GET, ['status' => 'active'])) ?>"
                   class="tab-btn <?= $status == 'active' ? 'active' : '' ?>">
                    Active Posts
                </a>
                <a href="?<?= http_build_query(array_merge($_GET, ['status' => 'reunited'])) ?>"
                   class="tab-btn <?= $status == 'reunited' ? 'active' : '' ?>">
                    Reunited Posts
                </a>
            </div>

            <div class="results-header">
                <h2><?= $status == 'reunited' ? 'Reunited Lost and Found Pets' : 'Active Lost and Found Pets' ?></h2>

                <div class="sort-view-controls">
                    <div class="sort-control">
                        <label for="sort-select" class="sort-label">Sort by:</label>
                        <select id="sort-select" onchange="updateUrlParam('sort', this.value)">
                            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Newest First</option>
                            <option value="oldest" <?= $sort == 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Pet Grid -->
            <div class="pet-grid">
                <?php if (empty($paginatedPosts)): ?>
                    <div class="no-results">
                        <i class="fa-solid fa-paw no-results-icon"></i>
                        <p>No <?= $status == 'reunited' ? 'reunited' : 'active' ?> posts match your current selections.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($paginatedPosts as $post):
                        $postTypeStr = $post['post_type'];
                        $petTypeStr  = $post['pet_type_name'] ?? 'Pet';
                        $breedName   = $post['breed_name'] ?? 'Unknown';
                        $title       = "$postTypeStr $petTypeStr - $breedName";

                        $statusLower = strtolower($post['status'] ?? 'active');
                        $badge_class = 'badge-' . ($statusLower === 'reunited' ? 'reunited' : strtolower($post['post_type']));
                    ?>
                        <div class="pet-card" onclick="window.location.href='post_details.php?id=<?= $post['id'] ?>'">
                            <div class="card-image-wrapper">
                                <img src="../<?= htmlspecialchars($post['image'] ?? 'uploads/pets/placeholder.jpg') ?>" alt="Pet Image">
                                <span class="status-badge <?= $badge_class ?>">
                                    <?= htmlspecialchars($statusLower === 'reunited' ? 'Reunited' : $post['post_type']) ?>
                                </span>
                            </div>

                            <div class="card-body">
                                <div class="card-header-flex">
                                    <h3 class="pet-name"><?= htmlspecialchars($title) ?></h3>
                                </div>

                                <div class="pet-meta">
                                    <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($post['township_name'] ?? 'Unknown') ?>, <?= htmlspecialchars($post['city_name'] ?? 'Myanmar') ?></span>
                                    <span><i class="fa-solid fa-calendar-days"></i> <?= $post['post_type'] === 'Found' ? 'Found on:' : 'Last seen:' ?> <?= !empty($post['last_seen_date']) ? date('M j, Y', strtotime($post['last_seen_date'])) : 'Unknown' ?></span>
                                    <span class="upload-date"><i class="fa-solid fa-clock"></i> Posted: <?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php
            renderUserPagination($currentPage, $totalPages, $totalPosts, $itemsPerPage);
            ?>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Update URL for Sorting
        function updateUrlParam(key, value) {
            const url = new URL(window.location.href);
            url.searchParams.set(key, value);
            window.location.href = url.toString();
        }

        // Township Dropdown
        document.getElementById('city').addEventListener('change', function() {
            const cityId = this.value;
            const townshipSelect = document.getElementById('township');

            if (!cityId || cityId === 'all') {
                townshipSelect.innerHTML = '<option value="all" selected>All Townships</option>';
                return;
            }

            townshipSelect.innerHTML = '<option value="">Loading townships...</option>';

            fetch('../user/get_townships.php?city_id=' + cityId)
                .then(response => response.text())
                .then(htmlOptions => {
                    townshipSelect.innerHTML = '<option value="all">All Townships</option>' + htmlOptions;
                })
                .catch(err => {
                    console.error('Ajax error:', err);
                    townshipSelect.innerHTML = '<option value="all">All Townships</option>';
                });
        });
    </script>
</body>
</html>