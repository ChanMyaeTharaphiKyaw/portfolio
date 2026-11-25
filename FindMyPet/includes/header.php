<?php
require_once '../database/functions.php';

$pageTitle = isset($pageTitle) ? $pageTitle : 'FindMyPet';

date_default_timezone_set('Asia/Yangon');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
</head>

<body>
    <header>
        <nav class="nav-bar">
            <div class="nav-container">
                <div class="nav-left">
                    <!-- Menu Toggle Button -->
                    <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Toggle navigation menu">
                        <i class="ri-menu-line" id="menu-icon"></i>
                        <i class="ri-close-line" id="close-icon"></i>
                    </button>
                    
                    <!-- Logo -->
                    <a href="../user/index.php" class="logo">
                        <img src="../uploads/logo.png" alt="FindMyPet Logo" class="logo-img">
                        <span class="logo-text">FindMyPet</span>
                    </a>

                    <!-- Search Bar -->
                    <form action="../user/browse_posts.php" method="GET">
                        <div class="search-container">
                            <input type="text" name="q" placeholder="Search pets..."
                                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="search-bar">
                            <button type="submit" class="search-btn">
                                <i class="ri-search-2-line"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="nav-right">
                    <!-- Navigation Links -->
                    <div class="nav-links">
                        <a href="../user/create_post.php">Create Lost or Found Post</a>
                        <a href="../user/browse_posts.php">Browse Posts</a>
                        <a href="../user/blog.php">Blog</a>
                        <a href="../user/about.php">About Us</a>
                        <a href="../user/contact.php">Contact Us</a>
                    </div>

                    <?php if (isset($_SESSION['authenticated'])): 
                        $userId = $_SESSION['auth_user']['id'];
                        $userName = $_SESSION['auth_user']['name'];
                        $userEmail = $_SESSION['auth_user']['email'] ?? '';

                        $unreadCount = getUnreadNotificationCount($pdo, $userId);
                    ?>

                        <div class="nav-actions">
                            <!-- Notification Bell Icon -->
                            <div class="notification-wrapper">
                                <i class="ri-notification-2-line" id="notification-bell"></i>
                                <span class="notification-count <?= $unreadCount > 0 ? '' : 'hidden' ?>">
                                    <?= $unreadCount ?>
                                </span>
                                <div class="notification-dropdown">
                                    <div class="noti-header">
                                        <h3>Notifications</h3>
                                    </div>
                                    <div class="noti-list noti-list-divider" id="noti-list">
                                        <div class="noti-empty hidden" id="noti-empty">
                                            No new notifications
                                        </div>
                                    </div>
                                    <a href="../user/notifications.php" class="noti-footer">View All Notifications</a>
                                </div>
                            </div>
                            <!-- User Profile Dropdown -->
                            <div class="user-profile-nav">
                                <div class="user-avatar"><?= htmlspecialchars(strtoupper(mb_substr($userName, 0, 1))); ?></div>
                                <div class="profile-dropdown">
                                    <div class="profile-dropdown-header">
                                        <div class="profile-dropdown-name"><?= htmlspecialchars($userName) ?></div>
                                        <div class="profile-dropdown-email"><?= htmlspecialchars($userEmail) ?></div>
                                    </div>
                                    <a href="../user/profile.php"><i class="ri-user-line"></i> My Profile</a>
                                    <a href="../user/my_posts.php"><i class="ri-file-list-line"></i> My Posts</a>
                                    <hr>
                                    <a href="../user/logout.php" class="logout-link"><i class="ri-logout-box-r-line"></i> Logout</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <a href="../user/login.php" class="btn btn-outline">Login</a>
                            <a href="../user/register.php" class="btn btn-secondary">Register</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Menu Toggle
            const menuToggle = document.getElementById('mobile-menu-toggle');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');
            const navLinks = document.querySelector('.nav-links');

            if (menuToggle && navLinks) {
                menuToggle.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                    if (navLinks.classList.contains('active')) {
                        menuIcon.style.display = 'none';
                        closeIcon.style.display = 'block';
                    } else {
                        menuIcon.style.display = 'block';
                        closeIcon.style.display = 'none';
                    }
                });
            }

            // Notification
            const notiWrapper = document.querySelector('.notification-wrapper');
            const notiDropdown = document.querySelector('.notification-dropdown');
            const notiCountEl = document.querySelector('.notification-count');
            const notiList = document.getElementById('noti-list');
            const notiEmpty = document.getElementById('noti-empty');

            const currentUserId = <?= json_encode($userId ?? 0) ?>;
            let unreadCount = <?= json_encode($unreadCount ?? 0) ?>;

            if (notiWrapper && notiDropdown) {
                notiWrapper.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notiDropdown.classList.toggle('active');
                    notiWrapper.classList.toggle('active');
                });
                document.addEventListener('click', function(e) {
                    if (!notiWrapper.contains(e.target)) {
                        notiDropdown.classList.remove('active');
                        notiWrapper.classList.remove('active');
                    }
                });
            }

            // Load Notifications
            async function loadInitialNotifications() {
                if (currentUserId == 0) return;
                try {
                    // Fetch notifications from server
                    const response = await fetch('/cproj/includes/get_notifications.php');
                    const data = await response.json();

                    // Display notifications
                    if (data.length > 0) {
                        notiEmpty.classList.add('hidden');
                        data.reverse().forEach(notif => {
                            prependNotification(notif, false);
                        });
                    } else {
                        notiEmpty.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error loading notifications:', error);
                }
            }

            // WebSocket
            function connectWebSocket() {
                if (currentUserId == 0) return;

                // Establish WebSocket connection
                const ws = new WebSocket(`ws://localhost:8080?user_id=${currentUserId}`);
                ws.onopen = () => console.log("Connected to notification server.");

                // Handle new notifications
                ws.onmessage = (e) => {
                    const data = JSON.parse(e.data);
                    console.log("New Notification Received:", data);
                    prependNotification(data, true);
                };

                // Handle connection errors
                ws.onerror = (e) => console.error("WebSocket error:", e);

                // Handle disconnections with auto-reconnect
                ws.onclose = () => {
                    console.log("Disconnected. Reconnecting in 5s...");
                    setTimeout(connectWebSocket, 5000);
                };
            }

            // Render Notifications
            function prependNotification(notif, isNew) {
                notiEmpty.classList.add('hidden');

                // Create notification element
                const item = document.createElement('a');
                item.href = notif.link || '#';
                item.dataset.notifId = notif.id;

                const isUnread = isNew || (notif.is_read == 0);

                let iconClass = 'ri-megaphone-line';
                let iconBgClass = 'icon-bg-red';
                if (notif.type === 'match') {
                    iconClass = 'ri-puzzle-2-line';
                    iconBgClass = 'icon-bg-pink';
                } else if (notif.type === 'comment') {
                    iconClass = 'ri-chat-3-line';
                    iconBgClass = 'icon-bg-blue';
                } else if (notif.type === 'new_post') {
                    iconClass = 'ri-file-list-line';
                    iconBgClass = 'icon-bg-green';
                }

                // Time formatting
                const timeAgo = notif.time || 'Just now';

                item.className = `noti-item ${isUnread ? 'is-unread' : ''}`;
                item.innerHTML = `
                    <div class="noti-icon-wrapper ${iconBgClass}">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="noti-content">
                        <p class="noti-message ${isUnread ? 'noti-text-unread' : 'noti-text-read'}">
                            ${notif.message}
                        </p>
                        <span class="noti-time">
                            ${timeAgo}
                        </span>
                    </div>
                `;

                // Handle unread notification
                if (isUnread) {
                    item.addEventListener('click', function(e) {
                        fetch(`/cproj/includes/get_notifications.php?mark_read=1&id=${notif.id}`);

                        item.classList.remove('is-unread');
                        const p = item.querySelector('.noti-message');
                        p.classList.remove('noti-text-unread');
                        p.classList.add('noti-text-read');

                        if (unreadCount > 0) {
                            unreadCount--;
                            notiCountEl.textContent = unreadCount;
                            if (unreadCount === 0) {
                                notiCountEl.classList.add('hidden');
                            }
                        }
                    });
                }

                notiList.insertBefore(item, notiList.firstChild);

                // Handle new notifications
                if (isNew) {
                    unreadCount++;
                    notiCountEl.textContent = unreadCount;
                    notiCountEl.classList.remove('hidden');
                    item.classList.add('new-noti-highlight');
                    setTimeout(() => item.classList.remove('new-noti-highlight'), 3000);
                }
            }

            // Initialize notification 
            loadInitialNotifications();
            connectWebSocket();
        });
    </script>
</body>
</html>