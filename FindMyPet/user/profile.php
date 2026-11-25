<?php
$pageTitle = "My Profile";

require_once "../database/pdo.php";
require_once "../database/functions.php";
include "../includes/header.php";

// Check User Authentication
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    setMessage('Please Login First to View Profile.', 'error');
    redirect('login.php');
}

$userId = $_SESSION['auth_user']['id'];
$user = getUserById($pdo, $userId);
$notif_prefs = getUserNotiPreferences($pdo, $userId);

$profileErrors = $_SESSION['profile_errors'] ?? [];
$profileData = $_SESSION['profile_data'] ?? [];
unset($_SESSION['profile_errors'], $_SESSION['profile_data']);

// Fetch all cities for the dropdown menu
$cities = getAll($pdo, 'city');
$initialCityId = !empty($profileData['city_id']) ? $profileData['city_id'] : ($user['data']['city_id'] ?? '');
$townships = $initialCityId ? getTownshipsByCity($pdo, $initialCityId) : [];

function profileOld($key, $default = '')
{
    global $profileData, $user;
    if (isset($profileData[$key]) && $profileData[$key] !== '') {
        return htmlspecialchars($profileData[$key]);
    }
    if (isset($user['data'][$key])) {
        return htmlspecialchars($user['data'][$key]);
    }
    return htmlspecialchars($default);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet" />

    <style>
        .page-body {
            background-color: #f9fafb;
            font-family: 'Poppins', sans-serif;
            color: #1f2937;
        }

        .page-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }

        .page-main {
            width: 100%;
            max-width: 72rem;
            margin: 0 auto;
            padding: 2.5rem 1rem;
        }

        @media (min-width: 640px) {
            .page-main { padding: 2.5rem 1.5rem; }
        }

        @media (min-width: 1024px) {
            .page-main { padding: 2.5rem 2rem; }
        }

        .profile-layout-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .profile-layout-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .text-red-500 { color: #ef4444; }

        .form-input-readonly {
            background-color: #f9fafb;
            color: #6b7280;
            cursor: not-allowed;
        }

        .helper-text {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.375rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .helper-text-start {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.375rem;
            display: flex;
            align-items: flex-start;
            gap: 0.375rem;
        }

        .icon-mt-05 {
            margin-top: 0.125rem;
        }

        .section-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
            display: block;
        }

        .checkbox-content {
            flex: 1;
        }

        .checkbox-title {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #1f2937;
        }

        .checkbox-desc {
            display: block;
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .hidden { display: none !important; }
        .col-span-full { grid-column: 1 / -1; }

        .profile-page-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .profile-page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        @media (min-width: 768px) {
            .profile-page-title { font-size: 1.875rem; }
        }

        .profile-page-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .error-border {
            border-color: #dc3545 !important;
            background-color: #fff8f8 !important;
            outline: none;
        }
        .error-border:focus {
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15) !important;
        }
        .error-text {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 500;
        }

        .profile-card, .form-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            height: 100%;
        }
        .form-card { margin-bottom: 1.5rem; }

        .profile-header {
            background: linear-gradient(135deg, #1f7a5f 0%, #165d48 100%);
            padding: 2rem 1.5rem;
            color: white;
            text-align: center;
        }
        .profile-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0 0 0.5rem 0;
        }
        .profile-header p, .profile-header .member-since {
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            opacity: 0.9;
            margin: 0;
        }
        .profile-header .member-since {
            margin-top: 1rem;
            font-size: 0.75rem;
            opacity: 0.8;
        }
        .profile-actions { padding: 1.5rem; }
        
        .profile-action-btn {
            width: 100%;
            padding: 0.875rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            border: none;
            margin-bottom: 0.75rem;
            text-align: left;
            cursor: pointer;
            font-family: inherit;
        }
        .profile-action-btn:last-child { margin-bottom: 0; }
        .profile-action-btn.active {
            background-color: #1f7a5f !important;
            color: white !important;
            box-shadow: 0 4px 6px -1px rgba(31, 122, 95, 0.2);
        }
        .profile-action-btn:not(.active) {
            background-color: #f1f5f9;
            color: #334155;
        }
        .profile-action-btn:not(.active):hover { background-color: #e2e8f0; }
        .profile-action-btn i { font-size: 1.25rem; min-width: 24px; }

        .form-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }
        .form-header h4 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }
        .form-header h4 i { color: #1f7a5f; }
        .form-content { padding: 1.5rem; }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        .form-field { display: flex; flex-direction: column; }
        .form-field label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background-color: #ffffff;
            color: #334155;
            font-family: inherit;
        }
        .form-input:focus {
            outline: none;
            border-color: #1f7a5f;
            box-shadow: 0 0 0 3px rgba(31, 122, 95, 0.1);
        }

        .form-actions {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 1rem;
        }
        .btn-primary, .btn-secondary {
            flex: 1;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            font-family: inherit;
        }
        .btn-primary { background-color: #1f7a5f; color: white; }
        .btn-primary:hover { background-color: #165d48; }
        .btn-secondary {
            color: #475569;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
        }
        .btn-secondary:hover {
            background-color: #f1f5f9;
            border-color: #94a3b8;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            background-color: #f8fafc;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-bottom: 1rem;
        }
        .checkbox-wrapper:last-child { margin-bottom: 0; }
        .checkbox-wrapper:hover { background-color: #f1f5f9; }
        .checkbox-wrapper input[type="checkbox"] {
            margin-top: 0.25rem;
            width: 1.25rem;
            height: 1.25rem;
            accent-color: #1f7a5f;
            cursor: pointer;
        }

        .form-section { animation: fadeIn 0.35s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1024px) {
            .profile-header { padding: 1.5rem 1rem; }
            .profile-header h2 { font-size: 1.25rem; }
            .profile-actions { padding: 1rem; }
            .form-header { padding: 1rem 1.25rem; }
            .form-content { padding: 1.25rem; }
            .form-grid { gap: 1rem; }
            .form-input { padding: 0.65rem 0.85rem; font-size: 0.9rem; }
            .btn-primary, .btn-secondary { padding: 0.65rem 0.85rem; font-size: 0.9rem; }
            .checkbox-wrapper { padding: 0.75rem; gap: 0.75rem; }
        }

        @media (max-width: 768px) {
            .profile-actions { 
                display: flex; 
                flex-direction: row; 
                gap: 0.5rem; 
                padding: 1rem;
            }
            .profile-action-btn { 
                padding: 0.5rem 0.25rem; 
                font-size: 0.75rem; 
                margin-bottom: 0; 
                justify-content: center;
                text-align: center;
                flex: 1;
            }
            .profile-action-btn i { 
                font-size: 1rem; 
                min-width: 16px; 
            }
            .form-grid { grid-template-columns: 1fr; }
            .form-actions { flex-direction: column; }
            .checkbox-wrapper { 
                flex-direction: column; 
                gap: 0.5rem; 
            }
            .checkbox-wrapper input[type="checkbox"] { 
                margin-top: 0; 
            }
        }
    </style>
</head>

<body class="page-body">

    <div class="page-wrapper">
        <main class="page-main">
            
            <section>
                <!-- Page Header -->
                <div class="profile-page-header">
                    <h3 class="profile-page-title">My Profile</h3>
                    <p class="profile-page-subtitle">Manage your account information, security, and preferences.</p>
                </div>

                <?php include '../includes/alert.php'; ?>

                <!-- Layout Grid -->
                <div class="profile-layout-grid">
                    
                    <!-- Left Sidebar -->
                    <div class="profile-sidebar-col">
                        <div class="profile-card">
                            <div class="profile-header">
                                <h2><?= htmlspecialchars($user['data']['full_name']) ?></h2>
                                <p>
                                    <i class="ri-mail-line"></i> 
                                    <?= htmlspecialchars($user['data']['email']) ?>
                                </p>
                                <div class="member-since">
                                    <i class="ri-calendar-line"></i> 
                                    Member since <?= date('M d, Y', strtotime($user['data']['created_at'])) ?>
                                </div>
                            </div>

                            <div class="profile-actions">
                                <button type="button" onclick="showForm('edit-profile', this)" class="profile-action-btn active">
                                    <i class="ri-user-settings-line"></i> Edit Profile
                                </button>
                                <button type="button" onclick="showForm('change-password', this)" class="profile-action-btn">
                                    <i class="ri-lock-password-line"></i> Change Password
                                </button>
                                <button type="button" onclick="showForm('notification-preferences', this)" class="profile-action-btn">
                                    <i class="ri-notification-3-line"></i> Notifications
                                </button>
                            </div>
                        </div>
                    </div>

                     <!-- Right Content Area -->
                    <div class="profile-content-col">

                         <!-- Edit Profile Form -->
                        <div id="edit-profile" class="form-card form-section">
                            <div class="form-header">
                                <h4><i class="ri-user-settings-line"></i> Edit Profile</h4>
                            </div>
                            <form action="../code.php" method="POST" class="form-content">
                                <input type="hidden" name="action" value="update_profile">
                                <input type="hidden" name="user_id" value="<?= $user['data']['id'] ?>">

                                <div class="form-grid">
                                    <div class="form-field">
                                        <label for="full_name">Full Name <span class="text-red-500">*</span></label>
                                        <input type="text" id="full_name" name="full_name" value="<?= profileOld('full_name') ?>" class="form-input <?= profileHasError('full_name') ? 'error-border' : '' ?>">
                                        <?= profileErrorMessage('full_name') ?>
                                    </div>

                                    <div class="form-field">
                                        <label for="email">Email Address</label>
                                        <input type="email" id="email" value="<?= htmlspecialchars($user['data']['email']) ?>" readonly class="form-input form-input-readonly">
                                        <p class="helper-text"><i class="ri-shield-check-line"></i> Email cannot be changed for security reasons.</p>
                                    </div>

                                    <div class="form-field">
                                        <label for="phone">Phone Number <span class="text-red-500">*</span></label>
                                        <input type="tel" id="phone" name="phone" value="<?= profileOld('phone') ?>" class="form-input <?= profileHasError('phone') ? 'error-border' : '' ?>">
                                        <?= profileErrorMessage('phone') ?>
                                    </div>

                                    <div class="form-field">
                                        <label for="address">Address</label>
                                        <input type="text" id="address" name="address" value="<?= profileOld('address') ?>" class="form-input <?= profileHasError('address') ? 'error-border' : '' ?>">
                                        <?= profileErrorMessage('address') ?>
                                    </div>

                                    <div class="form-field">
                                        <label for="city_id">City</label>
                                        <select name="city_id" id="profile_city" class="form-input <?= profileHasError('city_id') ? 'error-border' : '' ?>">
                                            <option value="">Select City</option>
                                            <?php foreach ($cities as $city): ?>
                                                <option value="<?= $city['id'] ?>" <?= profileOld('city_id') == $city['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($city['city_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?= profileErrorMessage('city_id') ?>
                                    </div>

                                    <div class="form-field">
                                        <label for="township_id">Township <span class="text-red-500">*</span></label>
                                        <select name="township_id" id="profile_township" class="form-input <?= profileHasError('township_id') ? 'error-border' : '' ?>">
                                            <option value="">Select Township</option>
                                            <?php foreach ($townships as $township): ?>
                                                <option value="<?= $township['id'] ?>" <?= profileOld('township_id') == $township['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($township['township_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?= profileErrorMessage('township_id') ?>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-primary">Save Changes</button>
                                    <a href="profile.php" class="btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>

                         <!-- Change Password Form -->
                        <div id="change-password" class="form-card form-section hidden">
                            <div class="form-header">
                                <h4><i class="ri-lock-password-line"></i> Change Password</h4>
                            </div>
                            <form action="../code.php" method="POST" class="form-content">
                                <input type="hidden" name="action" value="change_password">
                                <input type="hidden" name="user_id" value="<?= $user['data']['id'] ?>">

                                <div class="form-grid">
                                    <div class="form-field col-span-full">
                                        <label for="current_password">Current Password <span class="text-red-500">*</span></label>
                                        <input type="password" id="current_password" name="current_password" class="form-input">
                                    </div>

                                    <div class="form-field">
                                        <label for="new_password">New Password <span class="text-red-500">*</span></label>
                                        <input type="password" id="new_password" name="new_password" class="form-input">
                                        <p class="helper-text-start">
                                            <i class="ri-information-line icon-mt-05"></i>
                                            <span>Must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.</span>
                                        </p>
                                    </div>

                                    <div class="form-field">
                                        <label for="confirm_password">Confirm New Password <span class="text-red-500">*</span></label>
                                        <input type="password" id="confirm_password" name="confirm_password" class="form-input">
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-primary">Update Password</button>
                                    <a href="profile.php" class="btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>

                         <!-- Notification Preferences Form -->
                        <div id="notification-preferences" class="form-card form-section hidden">
                            <div class="form-header">
                                <h4><i class="ri-notification-3-line"></i> Notification Preferences</h4>
                            </div>
                            <form action="../code.php" method="POST" class="form-content">
                                <input type="hidden" name="action" value="update_notification_preferences">
                                <input type="hidden" name="user_id" value="<?= $user['data']['id'] ?>">

                                <div class="form-field" style="margin-bottom: 1.5rem;">
                                    <label class="section-label">Email Notifications</label>
                                    <label class="checkbox-wrapper">
                                        <input type="checkbox" name="email_notifications" id="email_notifications" value="1" <?= $notif_prefs['email_notifications'] ? 'checked' : '' ?>>
                                        <div class="checkbox-content">
                                            <span class="checkbox-title">New posts in my township</span>
                                            <span class="checkbox-desc">Receive email alerts when new lost or found posts are created in your area.</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-field">
                                    <label class="section-label">In-App Notifications</label>

                                    <label class="checkbox-wrapper">
                                        <input type="checkbox" name="match_notifications" id="match_notifications" value="1" <?= $notif_prefs['match_notifications'] ? 'checked' : '' ?>>
                                        <div class="checkbox-content">
                                            <span class="checkbox-title">Potential matches</span>
                                            <span class="checkbox-desc">Get notified when the system finds potential matches for your posts.</span>
                                        </div>
                                    </label>

                                    <label class="checkbox-wrapper">
                                        <input type="checkbox" name="comment_notifications" id="comment_notifications" value="1" <?= $notif_prefs['comment_notifications'] ? 'checked' : '' ?>>
                                        <div class="checkbox-content">
                                            <span class="checkbox-title">New comments on my posts</span>
                                            <span class="checkbox-desc">Receive alerts when users comment or share leads on your posts.</span>
                                        </div>
                                    </label>

                                    <label class="checkbox-wrapper">
                                        <input type="checkbox" name="post_notifications" id="post_notifications" value="1" <?= $notif_prefs['post_notifications'] ? 'checked' : '' ?>>
                                        <div class="checkbox-content">
                                            <span class="checkbox-title">New posts in my location</span>
                                            <span class="checkbox-desc">Get in-app alerts for new posts in your specific township.</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-primary">Save Preferences</button>
                                    <a href="profile.php" class="btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </section>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        function showForm(formId, btnElement) {
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.add('hidden');
            });
            document.querySelectorAll('.profile-action-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.getElementById(formId).classList.remove('hidden');
            if (btnElement) {
                btnElement.classList.add('active');
            }
        }

        document.getElementById('profile_city').addEventListener('change', function() {
            const cityId = this.value;
            const townshipSelect = document.getElementById('profile_township');
            if (!cityId) {
                townshipSelect.innerHTML = '<option value="">Select Township</option>';
                return;
            }
            townshipSelect.innerHTML = '<option value="">Loading...</option>';
            townshipSelect.disabled = true;

            fetch('get_townships.php?city_id=' + cityId)
                .then(response => response.text())
                .then(htmlOptions => {
                    townshipSelect.innerHTML = '<option value="">Select Township</option>' + htmlOptions;
                    townshipSelect.disabled = false;
                })
                .catch(err => {
                    console.error('Ajax error:', err);
                    townshipSelect.innerHTML = '<option value="">Error loading townships</option>';
                    townshipSelect.disabled = false;
                });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const firstBtn = document.querySelector('.profile-action-btn');
            if (firstBtn) {
                showForm('edit-profile', firstBtn);
            }
        });
    </script>
</body>
</html>