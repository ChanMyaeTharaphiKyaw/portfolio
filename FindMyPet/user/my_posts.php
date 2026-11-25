<?php
include '../includes/header.php';
require_once '../database/functions.php';

$pageTitle = "My Posts";

// Check User Authentication
if (!isset($_SESSION['authenticated'])) {
    setMessage('Please Login to Access Your Posts.', 'error');
    redirect('login.php');
}

$userId = $_SESSION['auth_user']['id'];

$currentTab = isset($_GET['tab']) && in_array($_GET['tab'], ['active', 'reunited', 'draft']) ? $_GET['tab'] : 'active';

// Pagination
$perPage = 10;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;

// Get counts for each tab
$counts = [
    'active'   => getUserPostsCountByStatus($pdo, $userId, 'active'),
    'reunited' => getUserPostsCountByStatus($pdo, $userId, 'reunited'),
    'draft'    => getUserPostsCountByStatus($pdo, $userId, 'draft')
];

$totalRecords = $counts[$currentTab];
$totalPages = ceil($totalRecords / $perPage) ?: 1;
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $perPage;

// Retrieve Posts for Current Tab
$currentPosts = getUserPostsByStatusPaginated($pdo, $userId, $currentTab, $perPage, $offset);

// Empty State
$emptyStates = [
    'active'   => ['msg' => 'You have no active posts.', 'icon' => 'ri-file-list-line', 'action' => '<a href="create_post.php" class="link-primary">Create one now!</a>'],
    'reunited' => ['msg' => 'You have no reunited posts yet.', 'icon' => 'ri-heart-line', 'action' => ''],
    'draft'    => ['msg' => 'You have no saved drafts.', 'icon' => 'ri-file-edit-line', 'action' => '']
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet" />

    <style>
        :root {
            --color-primary: #1f7a5f;
            --color-secondary: #f36f1c;
            --color-bg: #f8fafc;
        }

        /* --- Page Layout --- */
        .page-container {
            max-width: 1200px;
            margin: 2.5rem auto;
            padding: 0 1.25rem;
        }

        .page-header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 640px) {
            .page-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        @media (min-width: 640px) {
            .page-title {
                font-size: 1.875rem;
            }
        }

        .page-subtitle {
            color: #4b5563;
            margin-top: 0.25rem;
            font-size: 0.875rem;
        }

        @media (min-width: 640px) {
            .page-subtitle {
                font-size: 1rem;
            }
        }

        .btn-create {
            padding: 0.75rem 1.5rem;
            background-color: var(--color-secondary);
            color: white;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            align-self: flex-start;
        }

        .btn-create:hover {
            background-color: #ea580c;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* --- Tabs --- */
        .tabs-header {
            padding: 1rem 1rem 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            overflow-x: auto;
        }

        .tabs-wrapper {
            display: flex;
            flex-wrap: nowrap;
            gap: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .tab-item {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: background-color 0.2s, color 0.2s;
            white-space: nowrap;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        @media (min-width: 768px) {
            .tab-item {
                padding: 0.625rem 1.25rem;
                font-size: 1rem;
            }
        }

        .tab-item.active {
            background-color: var(--color-primary);
            color: white;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .tab-item.inactive {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        .tab-item.inactive:hover {
            background-color: #e5e7eb;
        }

        .tab-badge {
            margin-left: 0.5rem;
            padding: 0.125rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 9999px;
        }

        .tab-item.active .tab-badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .tab-item.inactive .tab-badge {
            background-color: #d1d5db;
            color: #374151;
        }

        /* --- Empty State --- */
        .empty-state {
            text-align: center;
            padding: 3rem;
            background-color: #f9fafb;
            border: 2px dashed #d1d5db;
            margin: 1rem;
            border-radius: 0.5rem;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #d1d5db;
            margin-bottom: 0.75rem;
            display: block;
        }

        .empty-state-text {
            color: #6b7280;
            font-style: italic;
        }

        .link-primary {
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 500;
        }

        .link-primary:hover {
            text-decoration: underline;
        }

        /* --- Post Item Layout --- */
        .post-item {
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem;
            transition: background-color 0.2s;
        }

        .post-item:last-child {
            border-bottom: none;
        }

        .post-item:hover {
            background-color: #f9fafb;
        }

        .post-layout {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        @media (min-width: 768px) {
            .post-layout {
                display: grid;
                grid-template-columns: 120px 1fr auto auto;
                gap: 1rem;
                align-items: center;
            }
        }

        .post-image-wrapper {
            width: 100%;
            height: 160px;
            position: relative;
            border-radius: 0.5rem;
            overflow: hidden;
            flex-shrink: 0;
        }

        @media (min-width: 768px) {
            .post-image-wrapper {
                width: 120px;
                height: 120px;
            }
        }

        .post-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .post-badge {
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 600;
            color: white;
        }

        .post-badge.draft {
            background-color: #eab308;
        }

        .post-badge.lost {
            background-color: #ef4444;
        }

        .post-badge.found {
            background-color: #22c55e;
        }

        .post-content {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            min-width: 0;
            width: 100%;
        }

        .post-title {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.25;
            margin: 0;
        }

        @media (min-width: 768px) {
            .post-title {
                font-size: 1.125rem;
            }
        }

        .post-location {
            font-size: 0.75rem;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            margin: 0;
        }

        @media (min-width: 768px) {
            .post-location {
                font-size: 0.875rem;
            }
        }

        .post-date {
            font-size: 0.75rem;
            color: #9ca3af;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            margin: 0;
        }

        /* --- Status & Actions --- */
        .post-status-wrapper {
            width: 100%;
        }

        @media (min-width: 768px) {
            .post-status-wrapper {
                width: auto;
            }
        }

        .status-select {
            width: 100%;
            padding: 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            outline: none;
        }

        @media (min-width: 768px) {
            .status-select {
                width: auto;
            }
        }

        .status-select.active-status {
            background-color: #dcfce7;
            color: #15803d;
        }

        .status-select.reunited-status {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .status-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            background-color: #dcfce7;
            color: #15803d;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            width: 100%;
        }

        @media (min-width: 768px) {
            .action-buttons {
                width: auto;
                justify-content: flex-start;
            }
        }

        .btn-action {
            flex: 1;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            color: white;
            transition: all 0.2s;
            text-decoration: none;
            position: relative;
        }

        @media (min-width: 768px) {
            .btn-action {
                flex: none;
                width: 2.5rem;
                height: 2.5rem;
                padding: 0;
            }
        }

        .btn-action:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-action i {
            font-size: 1.125rem;
        }

        .btn-action .btn-text {
            margin-left: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        @media (min-width: 768px) {
            .btn-action .btn-text {
                display: none;
            }
        }

        .btn-edit {
            background-color: #3b82f6;
        }

        .btn-delete {
            background-color: #ef4444;
        }

        .btn-poster {
            background-color: #a855f7;
        }

        .btn-matches {
            background-color: var(--color-primary);
        }

        

        /* --- Tooltips --- */
        .tooltip {
            position: relative;
        }

        .tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #1f2937;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
            margin-bottom: 8px;
            z-index: 10;
        }

        .tooltip:hover::after {
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-50">
    <main class="page-container">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">My Pet Posts</h1>
                <p class="page-subtitle">Manage your active listings, update their status, or finish your drafts.</p>
            </div>
            <a href="create_post.php" class="btn-create">
                <i class="ri-add-line"></i> <span>Post New Pet</span>
            </a>
        </div>

        <?php include '../includes/alert.php'; ?>

        <div class="card">

            <!-- Tabs Navigation -->
            <div class="tabs-header">
                <div class="tabs-wrapper">
                    <?php foreach (['active', 'reunited', 'draft'] as $tab): ?>
                        <a href="?tab=<?= $tab ?>" class="tab-item <?= $currentTab === $tab ? 'active' : 'inactive' ?>">
                            <span><?= ucfirst($tab) . ($tab === 'draft' ? 's' : '') ?></span>
                            <span class="tab-badge"><?= $counts[$tab] ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Post List -->
            <?php if (empty($currentPosts)): ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <i class="<?= $emptyStates[$currentTab]['icon'] ?> empty-state-icon"></i>
                    <p class="empty-state-text">
                        <?= $emptyStates[$currentTab]['msg'] ?> <?= $emptyStates[$currentTab]['action'] ?>
                    </p>
                </div>
            <?php else: ?>
                <!-- Posts List -->
                <?php foreach ($currentPosts as $post): ?>
                    <?php
                    $statusLower = strtolower($post['status'] ?? '');
                    $statusClass = ($statusLower === 'active') ? 'active-status' : 'reunited-status';
                    $isDraft = ($currentTab === 'draft');
                    $isReunited = ($statusLower === 'reunited');
                    $badgeClass = $isDraft ? 'draft' : (strtolower($post['post_type']) === 'lost' ? 'lost' : 'found');
                    ?>

                    <div class="post-item">
                        <div class="post-layout">

                            <!-- Post Image & Type Badge -->
                            <div class="post-image-wrapper">
                                <img src="../<?= htmlspecialchars($post['image'] ?: 'placeholder.jpg') ?>" alt="Pet Image" class="post-image">
                                <span class="post-badge <?= $badgeClass ?>">
                                    <?= strtoupper($isDraft ? 'DRAFT' : $post['post_type']) ?>
                                </span>
                            </div>

                            <!-- Post Details -->
                            <div class="post-content">
                                <h3 class="post-title">
                                    <?= $isDraft
                                        ? htmlspecialchars($post['name'] ?: 'Unnamed Pet')
                                        : strtoupper(htmlspecialchars($post['post_type'])) . ' - ' . strtoupper(htmlspecialchars($post['pet_type_name'])) . ' - ' . htmlspecialchars($post['name'])
                                    ?>
                                </h3>
                                <p class="post-location">
                                    <i class="ri-map-pin-2-line"></i> <?= htmlspecialchars($post['township_name'] ?: 'N/A') ?>, <?= htmlspecialchars($post['city_name'] ?: 'N/A') ?>
                                </p>
                                <p class="post-date">
                                    <i class="ri-calendar-line"></i> <?= $isDraft ? 'Saved' : 'Posted' ?>: <?= date('M d, Y', strtotime($post['created_at'])) ?>
                                </p>
                            </div>

                            <!-- Status Update Form -->
                            <div class="post-status-wrapper">
                                <?php if (!$isDraft && !$isReunited): ?>
                                    <form action="../code.php" method="POST">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="status-select <?= $statusClass ?>">
                                            <option value="Active" <?= $statusLower === 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="Reunited" <?= $statusLower === 'reunited' ? 'selected' : '' ?>>Reunited</option>
                                        </select>
                                    </form>
                                <?php elseif ($isReunited): ?>
                                    <span class="status-badge">Reunited</span>
                                <?php endif; ?>
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <a href="edit_post.php?id=<?= $post['id']; ?>"
                                    class="tooltip btn-action btn-edit"
                                    data-tooltip="Edit">
                                    <i class="ri-edit-line"></i>
                                    <span class="btn-text">Edit</span>
                                </a>

                                <a href="delete_post.php?id=<?= $post['id']; ?>"
                                    class="tooltip btn-action btn-delete"
                                    data-tooltip="Delete"
                                    onclick="return confirm('Are you sure you want to delete this post?')">
                                    <i class="ri-delete-bin-line"></i>
                                    <span class="btn-text">Delete</span>
                                </a>

                                <?php if (!$isDraft): ?>
                                    <a href="generate_poster.php?id=<?= $post['id'] ?>"
                                        target="_blank"
                                        class="tooltip btn-action btn-poster"
                                        data-tooltip="Poster">
                                        <i class="ri-file-image-line"></i>
                                        <span class="btn-text">Poster</span>
                                    </a>

                                    <?php if (!$isReunited): ?>
                                        <a href="potential_matches.php?id=<?= $post['id'] ?>"
                                            class="tooltip btn-action btn-matches"
                                            data-tooltip="Matches">
                                            <i class="ri-heart-line"></i>
                                            <span class="btn-text">Matches</span>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Pagination -->
            <?php
            if ($totalPages > 1) {
                renderUserPagination($currentPage, $totalPages, $totalRecords, $perPage, ['tab' => $currentTab]);
            }
            ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>

</html>