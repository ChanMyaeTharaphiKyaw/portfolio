<?php
$pageTitle = "All Notifications";

require_once '../database/pdo.php';
require_once '../database/functions.php';
include '../includes/header.php';

// Check User Authentication
if (!isset($_SESSION['authenticated'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    setMessage('Please Login First.');
    redirect('login.php');
}

$userId = $_SESSION['auth_user']['id'];

// Pagination
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, $currentPage);
$offset = ($currentPage - 1) * $itemsPerPage;

// Get Total Notifications Count
$totalNotifications = getNotificationCount($pdo, $userId);
$totalPages = ceil($totalNotifications / $itemsPerPage);

// Retrieve Notifications
$notifications = getNotifications($pdo, $userId, 'all', $itemsPerPage, $offset);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />

    <style>
        .page-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 40px;
        }

        /* Heading */
        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .breadcrumb-link {
            color: #6b7280;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .breadcrumb-link:hover {
            color: #1f7a5f;
        }

        .breadcrumb-current {
            color: #1f2937;
            font-weight: 600;
        }

        .page-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f7a5f;
            margin: 0;
        }

        /* Notification Container */
        .notification-container {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #f3f4f6;
            overflow: hidden;
        }

        /* Empty Style */
        .empty-state {
            padding: 64px 16px;
            text-align: center;
            color: #6b7280;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
            display: block;
            color: #d1d5db;
        }

        .empty-state-text {
            font-size: 18px;
            font-weight: 500;
            color: #4b5563;
            margin: 0;
        }

        /* Notification List */
        .notification-list {
            border-top: 1px solid #f3f4f6;
        }

        .notification-list> :first-child {
            border-top: none;
        }

        /* Notification Item */
        .notification-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 20px;
            text-decoration: none;
            color: inherit;
            transition: background-color 0.2s ease;
        }

        .notification-item.is-read {
            background-color: #ffffff;
        }

        .notification-item.is-read:hover {
            background-color: #f9fafb;
        }

        .notification-item.is-unread {
            background-color: #f0f9ff;
        }

        .notification-item.is-unread:hover {
            background-color: #e0f2fe;
        }

        .notification-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .notification-item:hover .notification-icon-wrapper {
            transform: scale(1.1);
        }

        .notification-icon-wrapper i {
            font-size: 24px;
        }

        .icon-bg-blue {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .icon-bg-pink {
            background-color: #fce7f3;
            color: #db2777;
        }

        .icon-bg-red {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .icon-bg-green {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-message {
            font-size: 15px;
            line-height: 1.625;
            margin-bottom: 4px;
            word-wrap: break-word;
        }

        .notification-message.is-unread {
            font-weight: 600;
            color: #111827;
        }

        .notification-message.is-read {
            color: #374151;
        }

        .notification-time {
            font-size: 12px;
            color: #9ca3af;
        }

        Pagination .pagination-wrapper {
            padding: 0.75rem 1rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            background-color: #f9fafb;
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

        @media (max-width: 768px) {
            .page-container {
                padding: 0 20px;
                margin: 20px auto;
            }

            .page-title {
                font-size: 20px;
            }

            .notification-item {
                padding: 16px;
            }

            .notification-icon-wrapper {
                width: 40px;
                height: 40px;
            }

            .notification-icon-wrapper i {
                font-size: 20px;
            }

            .notification-message {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    <main class="page-container">

        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb-nav">
            <a href="../index.php" class="breadcrumb-link">Home</a>
            <i class="ri-arrow-right-s-line"></i>
            <span class="breadcrumb-current">Notifications</span>
        </nav>

        <!-- Page Heading -->
        <div class="page-heading">
            <h1 class="page-title">Notifications</h1>
        </div>

        <!-- Notification List Container -->
        <div class="notification-container">

            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <i class="ri-notification-off-line empty-state-icon"></i>
                    <p class="empty-state-text">No notifications yet</p>
                </div>
            <?php else: ?>
                <!-- Display Notifications -->
                <div class="notification-list">
                    <?php foreach ($notifications as $notif):
                        // Icon for Different Notification Type
                        $icon = 'ri-megaphone-line';
                        $iconBg = 'icon-bg-red';

                        if ($notif['type'] === 'match') {
                            $icon = 'ri-puzzle-2-line';
                            $iconBg = 'icon-bg-pink';
                        } elseif ($notif['type'] === 'comment') {
                            $icon = 'ri-chat-3-line';
                            $iconBg = 'icon-bg-blue';
                        } elseif ($notif['type'] === 'new_post') {
                            $icon = 'ri-file-list-line';
                            $iconBg = 'icon-bg-green';
                        }

                        // Format Notification Date
                        $timeAgo = date('M d, g:i A', strtotime($notif['created_at']));
                        $link = $notif['link'] ? htmlspecialchars($notif['link']) : '#';

                        // Check Read Status
                        $isUnread = ($notif['is_read'] == 0);
                        $statusClass = $isUnread ? 'is-unread' : 'is-read';
                        $textClass = $isUnread ? 'is-unread' : 'is-read';
                    ?>

                        <!-- Notification Item -->
                        <a href="<?= $link ?>"
                            data-notif-id="<?= $notif['id'] ?>"
                            class="notification-item <?= $statusClass ?>">

                            <!-- Notification Icon -->
                            <div class="notification-icon-wrapper <?= $iconBg ?>">
                                <i class="<?= $icon ?>"></i>
                            </div>

                            <!-- Notification Content -->
                            <div class="notification-content">
                                <p class="notification-message <?= $textClass ?>">
                                    <?= htmlspecialchars($notif['message']) ?>
                                </p>
                                <span class="notification-time">
                                    <?= $timeAgo ?>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php
            renderUserPagination($currentPage, $totalPages, $totalNotifications, $itemsPerPage);
            ?>
        </div>

    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationLinks = document.querySelectorAll('a[data-notif-id]');

            notificationLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (this.classList.contains('is-unread')) {
                        const notifId = this.dataset.notifId;
                        const url = '/cproj/includes/get_notifications.php?mark_read=1&id=' + notifId;

                        if (navigator.sendBeacon) {
                            navigator.sendBeacon(url);
                        } else {
                            fetch(url, {
                                keepalive: true
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>