<?php
$pageTitle = "Post Details";

require_once '../database/pdo.php';
require_once '../database/functions.php';
include '../includes/header.php';

$isLoggedIn = isset($_SESSION['auth_user']['id']);
$paramResult = checkParamId('id');
$post = getPostById($pdo, $paramResult);

$shareUrl = '';
$shareText = 'Help reunite lost pets with their families.';
$shareTitle = 'FindMyPet - Lost and Found Pets';
$ogImage = '';

if (is_numeric($paramResult) && isset($post['status']) && $post['status'] == 200) {
    $data = $post['data'];

    // Construct absolute URL for sharing 
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $shareUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $petName = !empty($data['name']) ? $data['name'] : 'this pet';
    $postType = $data['post_type'] ?? 'Lost';
    $location = $data['township_name'] ?? 'unknown location';

    $shareTitle = "{$postType} Pet: {$petName} in {$location}";
    $shareText = "Help find {$petName}! {$postType} pet last seen in {$location}. Please share to help reunite them with their family.";

    // Construct absolute URL for the Open Graph image
    $currentDir = dirname($_SERVER['REQUEST_URI']);
    $baseDir = dirname($currentDir);               
    
    if (!empty($data['image'])) {
        $ogImage = $protocol . "://" . $_SERVER['HTTP_HOST'] . $baseDir . "/" . ltrim($data['image'], '/');
    } else {
        $ogImage = $protocol . "://" . $_SERVER['HTTP_HOST'] . $baseDir . "/uploads/pets/default-img.jpg";
    }
}


$showCommentMessage = false;
$commentMessage = '';
$commentMessageType = 'error';

if (isset($_SESSION['comment_message'])) {
    $showCommentMessage = true;
    $commentMessage = $_SESSION['comment_message'];
    $commentMessageType = $_SESSION['comment_message_type'] ?? 'error';

    unset($_SESSION['comment_message'], $_SESSION['comment_message_type']);
}

$showReportModal = false;
$reportMessage = '';
$reportMessageType = 'error';

if (isset($_SESSION['report_message'])) {
    $showReportModal = true;
    $reportMessage = $_SESSION['report_message'];
    $reportMessageType = $_SESSION['report_message_type'] ?? 'error';

    unset($_SESSION['report_message'], $_SESSION['report_message_type']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <?php if (isset($shareTitle)): ?>
        <meta property="og:title" content="<?= htmlspecialchars($shareTitle) ?>" />
        <meta property="og:description" content="<?= htmlspecialchars($shareText) ?>" />
        <meta property="og:url" content="<?= htmlspecialchars($shareUrl) ?>" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>" />
    <?php endif; ?>

    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/output.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-50 text-gray-800">

    <main class="main-container">
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-5">
            <a href="browse_posts.php" class="hover:text-blue-600 transition font-medium">Browse Posts</a>
            <i class="ri-arrow-right-s-line"></i>
            <span class="text-gray-800 font-semibold">Pet Details</span>
        </nav>
        <?php if (!is_numeric($paramResult)): ?>
            <div class="detail-card">
                <h5 class="text-gray-500"><?= htmlspecialchars($paramResult) ?></h5>
            </div>
        <?php elseif ($post['status'] == 200): ?>
            <?php $data = $post['data']; ?>

            <!-- Post Details Container -->
            <div class="main-detail-card">
                <div class="grid grid-cols-1 lg:grid-cols-5">

                    <!-- Left Column -->
                    <div class="lg:col-span-2">
                        <div class="content-section">

                            <!-- Pet Image -->
                            <div class="image-wrapper">
                                <?php if ($data['image'] != ''): ?>
                                    <img src="../<?= htmlspecialchars($data['image']); ?>" alt="Pet Image">
                                <?php else: ?>
                                    <img src="../uploads/pets/default-img.jpg" alt="Default Pet Image">
                                <?php endif; ?>
                                <span class="status-badge <?= strtolower($data['post_type']) == 'lost' ? 'status-lost' : 'status-found' ?>">
                                    <?= htmlspecialchars($data['post_type']) ?> PET
                                </span>
                            </div>

                            <!-- Description Section -->
                            <div class="mt-5">
                                <h3 class="subsection-title">Description</h3>
                                <p class="description-text"><?= htmlspecialchars($data['description']) ?: 'No description provided.' ?></p>
                                <div class="location-badge">
                                    <i class="ri-map-pin-2-fill text-red-500"></i>
                                    <span>Last seen near <strong class="text-gray-800"><?= htmlspecialchars($data['last_seen_location'] ?? 'Unknown Location') ?></strong>, <?= htmlspecialchars($data['township_name'] ?? '') ?></span>
                                </div>
                            </div>

                            <!-- Share & Action Buttons -->
                            <div class="share-actions-section">
                                <h3 class="subsection-title"><i class="ri-share-line"></i> Share & Actions</h3>

                                <!-- Share Buttons -->
                                <div class="share-buttons-container">
                                    <?php if ($isLoggedIn): ?>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" target="_blank" rel="noopener noreferrer" class="share-btn btn-facebook">
                                            <i class="fab fa-facebook-f"></i> Facebook
                                        </a>
                                        <a href="viber://forward?text=<?= urlencode($shareText . ' ' . $shareUrl) ?>" class="share-btn btn-viber">
                                            <i class="fab fa-viber"></i> Viber
                                        </a>
                                        <button type="button" id="shareInstagramBtn" class="share-btn btn-instagram">
                                            <i class="fab fa-instagram"></i> Instagram
                                        </button>
                                    <?php else: ?>
                                        <?php $redirectUrl = urlencode('user/post_details.php?id=' . $data['id']); ?>
                                        <a href="login.php?msg=login_to_share&redirect=<?= $redirectUrl ?>" class="share-btn btn-facebook">
                                            <i class="fab fa-facebook-f"></i> Facebook
                                        </a>
                                        <a href="login.php?msg=login_to_share&redirect=<?= $redirectUrl ?>" class="share-btn btn-viber">
                                            <i class="fab fa-viber"></i> Viber
                                        </a>
                                        <a href="login.php?msg=login_to_share&redirect=<?= $redirectUrl ?>" class="share-btn btn-instagram">
                                            <i class="fab fa-instagram"></i> Instagram
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Action Buttons -->
                                <div class="action-buttons-container">
                                    <!-- Poster Button -->
                                    <?php
                                    $poster_link = $isLoggedIn ? "generate_poster.php?id=" . $data['id'] : "login.php?msg=login_to_poster&redirect=" . urlencode("user/post_details.php?id=" . $data['id']);
                                    ?>
                                    <a href="<?= $poster_link ?>" target="_blank" class="action-btn btn-generate">
                                        <i class="ri-file-image-line"></i> Generate Poster
                                    </a>

                                    <!-- Report Post Button -->
                                    <?php if ($isLoggedIn): ?>
                                        <button type="button" id="reportPostBtn" data-target-id="<?= $data['id'] ?>" data-target-type="post" class="action-btn btn-report">
                                            <i class="ri-flag-line"></i> Report Post
                                        </button>
                                    <?php else: ?>
                                        <a href="login.php?msg=login_to_report&redirect=<?= urlencode('user/post_details.php?id=' . $data['id']) ?>" class="action-btn btn-report">
                                            <i class="ri-flag-line"></i> Report Post
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="lg:col-span-3">

                        <!-- Pet Details -->
                        <div class="content-section">
                            <h2 class="details-section-title"><?= htmlspecialchars($data['post_type']) ?> Pet Details</h2>
                            <div>
                                <div class="detail-row">
                                    <span class="text-gray-600 font-medium">Name</span>
                                    <span class="text-gray-800 font-semibold"><?= htmlspecialchars($data['name']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="text-gray-600 font-medium">Pet Type</span>
                                    <span class="text-gray-800 font-semibold"><?= htmlspecialchars($data['pet_type_name']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="text-gray-600 font-medium">Breed</span>
                                    <span class="text-gray-800 font-semibold"><?= htmlspecialchars($data['breed_name']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="text-gray-600 font-medium">Gender</span>
                                    <span class="text-gray-800 font-semibold"><?= htmlspecialchars($data['gender']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="text-gray-600 font-medium">Color</span>
                                    <span class="text-gray-800 font-semibold"><?= htmlspecialchars($data['color']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="text-gray-600 font-medium">Age</span>
                                    <span class="text-gray-800 font-semibold"><?= htmlspecialchars($data['age']) ?> Months</span>
                                </div>
                                <div class="detail-row">
                                    <span class="text-gray-600 font-medium">Last Seen</span>
                                    <span class="text-gray-800 font-semibold"><?= htmlspecialchars($data['last_seen_date']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="text-gray-600 font-medium">Township</span>
                                    <span class="text-gray-800 font-semibold"><?= htmlspecialchars($data['township_name']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="text-gray-600 font-medium">City</span>
                                    <span class="text-gray-800 font-semibold"><?= htmlspecialchars($data['city_name']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="text-gray-600 font-medium">Posted On</span>
                                    <span class="text-gray-800 font-semibold"><?= htmlspecialchars(date('M d, Y h:i A', strtotime($data['post_created_at']))) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Owner Details -->
                        <div class="content-section">
                            <h2 class="details-section-title"><?= $data['post_type'] == 'Lost' ? 'Owner' : 'Finder' ?> Details</h2>
                            <div style="position: relative;">
                                <div class="<?= !$isLoggedIn ? 'blur-container' : '' ?>">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div class="contact-card">
                                            <i class="ri-user-line text-2xl" style="color: #3b82f6;"></i>
                                            <p class="contact-label">Name</p>
                                            <p class="contact-value"><?= htmlspecialchars($data['full_name']) ?></p>
                                        </div>
                                        <div class="contact-card">
                                            <i class="ri-phone-line text-2xl" style="color: #22c55e;"></i>
                                            <p class="contact-label">Phone</p>
                                            <p class="contact-value"><?= htmlspecialchars($data['contact_phone']) ?></p>
                                        </div>
                                        <div class="contact-card">
                                            <i class="ri-mail-line text-2xl" style="color: #ef4444;"></i>
                                            <p class="contact-label">Email</p>
                                            <p class="contact-value"><?= htmlspecialchars($data['contact_email']) ?></p>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!$isLoggedIn): ?>
                                    <div class="lock-overlay">
                                        <div class="lock-box">
                                            <div class="lock-icon-wrapper">
                                                <i class="ri-lock-2-line"></i>
                                            </div>
                                            <h4>Contact Info Locked</h4>
                                            <p>Please log in to view the contact details.</p>

                                            <a href="login.php?msg=login_to_view&redirect=<?= urlencode('user/post_details.php?id=' . $data['id']) ?>" class="btn-login-view">
                                                <i class="ri-login-circle-line"></i> Log In to View
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="main-detail-card">
                <div class="content-section">
                    <h3 class="subsection-title mb-4"><i class="ri-chat-3-line"></i> Comments</h3>



                    <?php if ($showCommentMessage): ?>
                        <?php
                        $icons = [
                            'success' => 'ri-checkbox-circle-line',
                            'error'   => 'ri-close-circle-line',
                            'warning' => 'ri-alert-line',
                            'info'    => 'ri-information-line'
                        ];
                        $icon = $icons[$commentMessageType] ?? $icons['info'];
                        ?>
                        <div class="form-alert alert-<?= htmlspecialchars($commentMessageType) ?>">
                            <i class="<?= htmlspecialchars($icon) ?>"></i>
                            <span><?= htmlspecialchars($commentMessage) ?></span>
                            <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                        </div>
                    <?php endif; ?>

                    <!-- Post Comment -->
                    <?php if ($isLoggedIn): ?>
                        <form action="../code.php" method="POST" class="mb-4">
                            <input type="hidden" name="post_id" value="<?= (int)$data['id']; ?>">
                            <div class="flex gap-3 items-start">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-[#1f7a5f] flex items-center justify-center font-bold flex-shrink-0 text-base">
                                    <?= htmlspecialchars(strtoupper(mb_substr($_SESSION['auth_user']['name'], 0, 1))); ?>
                                </div>
                                <div class="flex-1">
                                    <textarea name="comment" placeholder="Write a comment or share a lead..." class="comment-textarea" rows="3"></textarea>
                                    <button type="submit" name="btn_add_comment" class="btn-comment">Post Comment</button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="comment-prompt">
                            <div class="comment-prompt-icon"><i class="ri-user-line"></i></div>
                            <p class="comment-prompt-text">Have a lead on this pet? Please log in to leave a comment.</p>
                            <a href="login.php?msg=login_to_comment&redirect=<?= urlencode('user/post_details.php?id=' . $data['id']) ?>" class="btn-login-comment">
                                Log In to Comment
                            </a>
                        </div>
                    <?php endif; ?>

                    <div>
                        <!-- Comment List -->
                        <?php
                        $comments = getCommentsByPost($pdo, $paramResult);
                        if (!empty($comments)):
                            foreach ($comments as $comment):
                        ?>
                                <div class="comment-item group">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold flex-shrink-0">
                                        <?= htmlspecialchars(strtoupper(mb_substr($comment['full_name'], 0, 1))) ?>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-gray-800 text-sm"><?= htmlspecialchars($comment['full_name']) ?></span>
                                                <span class="text-xs text-gray-400"><?= htmlspecialchars(date('M d, Y h:i A', strtotime($comment['created_at']))) ?></span>
                                            </div>

                                            <?php if ($isLoggedIn): ?>
                                                <button type="button" class="report-btn btn-report-comment" data-target-id="<?= $comment['id'] ?>" data-target-type="comment" title="Report Comment">
                                                    <i class="ri-flag-2-line"></i>
                                                </button>
                                            <?php else: ?>
                                                <a href="login.php?msg=login_to_report&redirect=<?= urlencode('user/post_details.php?id=' . $data['id']) ?>" class="report-btn" title="Report Comment" style="display: inline-flex; align-items: center; text-decoration: none; color: inherit;">
                                                    <i class="ri-flag-2-line"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-gray-600 text-sm leading-relaxed"><?= htmlspecialchars($comment['comment_text']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-8 text-gray-400">
                                <i class="ri-chat-off-line text-4xl mb-2 block"></i>
                                <p class="text-sm">No comments yet. Be the first to share a lead!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="detail-card text-center">
                <h5 class="text-gray-500"><?= htmlspecialchars($post['message']) ?></h5>
            </div>
        <?php endif; ?>

        <!-- Report Modal -->
        <div id="reportModal" class="modal-overlay" style="<?= $showReportModal ? 'display:flex;' : 'display:none;' ?>">
            <div class="report-modal">
                <div class="report-header">
                    <h3>Report <span id="reportTypeTarget">Post</span></h3>
                    <button class="close-report" id="closeReport">&times;</button>
                </div>
                <div class="report-body">

                    <?php if ($showReportModal): ?>
                        <?php
                        $icons = [
                            'success' => 'ri-checkbox-circle-line',
                            'error'   => 'ri-close-circle-line',
                            'warning' => 'ri-alert-line',
                            'info'    => 'ri-information-line'
                        ];
                        $icon = $icons[$reportMessageType] ?? $icons['info'];
                        ?>
                        <div class="form-alert alert-<?= htmlspecialchars($reportMessageType) ?>">
                            <i class="<?= htmlspecialchars($icon) ?>"></i>
                            <span><?= htmlspecialchars($reportMessage) ?></span>
                            <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                        </div>
                    <?php endif; ?>

                    <p class="report-question">Why are you reporting this <span id="reportTypeText">post</span>?</p>

                    <form action="../code.php" id="reportForm" method="POST">
                        <input type="hidden" name="target_id" id="reportTargetId" value="">
                        <input type="hidden" name="target_type" id="reportTargetType" value="">

                        <div class="report-options">
                            <label class="option-item">
                                <input type="radio" name="report_reason" value="Spam" checked>
                                <span>Spam or misleading</span>
                            </label>
                            <label class="option-item">
                                <input type="radio" name="report_reason" value="False Information">
                                <span>Inaccurate information</span>
                            </label>
                            <label class="option-item">
                                <input type="radio" name="report_reason" value="Inappropriate Content">
                                <span>Inappropriate content</span>
                            </label>
                            <label class="option-item">
                                <input type="radio" name="report_reason" value="Harassment">
                                <span>Harassment or hate speech</span>
                            </label>
                            <label class="option-item">
                                <input type="radio" name="report_reason" value="Other">
                                <span>Other</span>
                            </label>
                        </div>

                        <label class="details-label">Additional details (optional)</label>
                        <textarea class="report-textarea" id="reportDetails" name="report_details" placeholder="Some information are inaccurate"></textarea>

                        <div class="report-actions">
                            <button type="button" class="btn-cancel" id="cancelReport">Cancel</button>
                            <button type="submit" name="btn_submit_report" class="btn-submit-report">Submit Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reportModal = document.getElementById('reportModal');
            const closeBtn = document.getElementById('closeReport');
            const cancelBtn = document.getElementById('cancelReport');
            const reportTypeTarget = document.getElementById('reportTypeTarget');
            const reportTypeText = document.getElementById('reportTypeText');
            const reportTargetId = document.getElementById('reportTargetId');
            const reportTargetType = document.getElementById('reportTargetType');

            function openReportModal(targetId, targetType) {
                reportTargetId.value = targetId;
                reportTargetType.value = targetType;
                if (targetType === 'post') {
                    reportTypeTarget.textContent = 'Post';
                    reportTypeText.textContent = 'post';
                } else if (targetType === 'comment') {
                    reportTypeTarget.textContent = 'Comment';
                    reportTypeText.textContent = 'comment';
                }
                reportModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeReportModal() {
                reportModal.style.display = 'none';
                document.body.style.overflow = 'auto';
                document.getElementById('reportForm').reset();
            }

            document.getElementById('reportPostBtn').addEventListener('click', function() {
                openReportModal(this.dataset.targetId, this.dataset.targetType);
            });

            document.querySelectorAll('.btn-report-comment').forEach(btn => {
                btn.addEventListener('click', function() {
                    openReportModal(this.dataset.targetId, this.dataset.targetType);
                });
            });

            closeBtn.addEventListener('click', closeReportModal);
            cancelBtn.addEventListener('click', closeReportModal);

            reportModal.addEventListener('click', function(e) {
                if (e.target === reportModal) {
                    closeReportModal();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && reportModal.style.display === 'flex') {
                    closeReportModal();
                }
            });

            <?php if ($showReportModal): ?>
                document.body.style.overflow = 'hidden';
            <?php endif; ?>
        });

        document.getElementById('shareInstagramBtn').addEventListener('click', function() {
            const shareUrl = "<?= htmlspecialchars($shareUrl) ?>";
            const shareText = "<?= htmlspecialchars($shareText) ?>";
            const shareTitle = "<?= htmlspecialchars($shareTitle) ?>";

            // 1. Try using the native Web Share API (Works perfectly on mobile devices)
            if (navigator.share) {
                navigator.share({
                    title: shareTitle,
                    text: shareText,
                    url: shareUrl
                }).catch(err => console.log('Share cancelled', err));
            }
            // 2. Fallback for Desktop: Copy to clipboard
            else {
                const fullText = shareText + '\n\n' + shareUrl;
                navigator.clipboard.writeText(fullText).then(() => {
                    alert('Post details copied to clipboard!\n\nOpen Instagram and paste this into your Story or Direct Message.');
                }).catch(() => {
                    alert('Unable to copy automatically. Please copy the URL from your browser address bar.');
                });
            }
        });
    </script>

</body>

</html>