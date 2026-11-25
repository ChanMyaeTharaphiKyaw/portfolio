<?php
$pageTitle = "Potential Matches";

require_once "../database/pdo.php";
require_once "../database/functions.php";
include "../includes/header.php";

// Check User Authentication
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    setMessage('Please login first to view matches.', 'error');
    redirect('login.php');
}

$userId = $_SESSION['auth_user']['id'];

// Validate Post ID 
$paramResult = checkParamId('id');
if (!is_numeric($paramResult)) {
    setMessage($paramResult, 'error');
    redirect('my_posts.php');
}

$postId = $paramResult;
$filterStatus = $_GET['status'] ?? 'all';

// Verify Post Authorization
$ownership = verifyPostOwnership($pdo, $postId, $userId);
if (!$ownership['authorized']) {
    setMessage($ownership['message'], 'error');
    redirect('my_posts.php');
}
$postData = $ownership['data'];

// Retrieve Match Count
$statusCounts = getMatchStatusCounts($pdo, $postId);
$pendingCount = $statusCounts['pending'];
$acceptedCount = $statusCounts['accepted'];
$rejectedCount = $statusCounts['rejected'];
$totalCount = $statusCounts['total'];

// Pagination 
$perPage = 9;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

$totalRecords = getPotentialMatchesCount($pdo, $postId, $filterStatus);
$totalPages = ceil($totalRecords / $perPage);

if ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
    $offset = ($currentPage - 1) * $perPage;
}

// Retrieve Match
$matches = getPotentialMatchesPaginated($pdo, $postId, $filterStatus, $perPage, $offset);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1f7a5f',
                        secondary: '#f36f1c',
                        background: '#f8fafc'
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet" />
</head>

<body class="bg-background font-poppins text-gray-800">

    <main class="max-w-[1200px] mx-auto my-10 px-5">
        <section>

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Potential Matches</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Review and manage matches suggested for your post.
                    </p>
                </div>
                <a href="my_posts.php" class="btn flex items-center gap-2 px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="ri-arrow-left-line"></i>
                    <span>Back to My Posts</span>
                </a>
            </div>

            <?php include '../includes/alert.php'; ?>

            <!-- Post Details -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border-l-4 border-primary">
                <div class="flex items-start gap-2 mb-4">
                    <i class="ri-pushpin-fill text-primary text-lg"></i>
                    <h4 class="text-base font-bold text-gray-800">Your Post (Reference)</h4>
                </div>
                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <div class="w-full md:w-40 h-40 relative rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                        <?php if (!empty($postData['image'])): ?>
                            <img src="../<?= htmlspecialchars($postData['image']) ?>" alt="Pet Image" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <i class="ri-image-line text-4xl"></i>
                            </div>
                        <?php endif; ?>
                        <span class="absolute top-2 left-2 text-xs px-2 py-1 rounded font-semibold 
                            <?= strtolower($postData['post_type']) == 'lost' ? 'bg-red-500 text-white' : 'bg-green-500 text-white' ?>">
                            <?= strtoupper($postData['post_type']) ?>
                        </span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">
                            <?= strtoupper(htmlspecialchars($postData['post_type'])) ?> -
                            <?= strtoupper(htmlspecialchars($postData['pet_type_name'])) ?> -
                            <?= htmlspecialchars($postData['name']) ?>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-600">
                            <p><i class="ri-shape-line text-primary"></i> Breed: <span class="font-medium text-gray-800"><?= htmlspecialchars($postData['breed_name'] ?? 'N/A') ?></span></p>
                            <p><i class="ri-palette-line text-primary"></i> Color: <span class="font-medium text-gray-800"><?= htmlspecialchars($postData['color'] ?? 'N/A') ?></span></p>
                            <p><i class="ri-user-line text-primary"></i> Gender: <span class="font-medium text-gray-800"><?= htmlspecialchars($postData['gender'] ?? 'N/A') ?></span></p>
                            <p><i class="ri-map-pin-line text-primary"></i> Location: <span class="font-medium text-gray-800"><?= htmlspecialchars($postData['township_name'] ?? 'N/A') ?>, <?= htmlspecialchars($postData['city_name'] ?? 'N/A') ?></span></p>

                            <?php
                            $lastSeenDate = !empty($postData['last_seen_date']) ? date('M d, Y', strtotime($postData['last_seen_date'])) : 'N/A';
                            $postedDate = !empty($postData['post_created_at']) ? date('M d, Y', strtotime($postData['post_created_at'])) : 'N/A';
                            ?>

                            <p><i class="ri-calendar-line text-primary"></i> Last Seen: <span class="font-medium text-gray-800"><?= $lastSeenDate ?></span></p>
                            <p><i class="ri-calendar-event-line text-primary"></i> Posted: <span class="font-medium text-gray-800"><?= $postedDate ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Matches Container -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">

                <!-- Filter Tabs -->
                <div class="px-4 pt-4 pb-3 border-b border-gray-200 overflow-x-auto">
                    <div class="flex flex-nowrap md:flex-wrap gap-2 pb-2 md:pb-0">
                        <?php
                        $tabs = [
                            'all'       => ['label' => 'All Matches', 'count' => $totalCount],
                            'pending'   => ['label' => 'Pending',     'count' => $pendingCount],
                            'accepted'  => ['label' => 'Accepted',    'count' => $acceptedCount],
                            'rejected'  => ['label' => 'Rejected',    'count' => $rejectedCount],
                        ];

                        foreach ($tabs as $key => $tab):
                            $isActive = ($filterStatus === $key);
                        ?>
                            <a href="<?= buildMatchTabUrl($key, $postId) ?>" class="px-4 md:px-5 py-2 md:py-2.5 rounded-lg font-medium transition whitespace-nowrap text-sm md:text-base <?= $isActive ? 'bg-primary text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                                <?= $tab['label'] ?>
                                <span class="ml-2 px-2 py-0.5 text-xs rounded-full <?= $isActive ? 'bg-white/20 text-white' : 'bg-gray-300 text-gray-700' ?>">
                                    <?= $tab['count'] ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Matches List -->
                <div class="p-4 md:p-6">
                    <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="ri-heart-fill text-red-500"></i>
                        <?php if ($filterStatus == 'all'): ?>
                            All Matched Posts
                        <?php else: ?>
                            <?= ucfirst($filterStatus) ?> Matches
                        <?php endif; ?>
                    </h4>

                    <?php if (empty($matches)): ?>
                        <!-- Empty List -->
                        <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <i class="ri-search-line text-5xl text-gray-300 mb-3 block"></i>
                            <h5 class="text-base font-semibold text-gray-700 mb-2">
                                <?php if ($filterStatus == 'all'): ?>
                                    No potential matches found yet
                                <?php else: ?>
                                    No <?= $filterStatus ?> matches
                                <?php endif; ?>
                            </h5>
                            <p class="text-sm text-gray-500">
                                <?php if ($filterStatus == 'all'): ?>
                                    The system hasn't found any matching posts for your pet yet. Check back later!
                                <?php else: ?>
                                    There are no <?= $filterStatus ?> matches at this time.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <!-- Matches Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            <?php foreach ($matches as $match):
                                $currentStatus = strtolower($match['match_status']);
                            ?>
                                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-shadow flex flex-col">

                                    <!-- Match Image & Badges -->
                                    <div class="relative h-48 bg-gray-100">
                                        <?php if (!empty($match['image'])): ?>
                                            <img src="../<?= htmlspecialchars($match['image']) ?>" alt="Match Image" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <i class="ri-image-line text-4xl"></i>
                                            </div>
                                        <?php endif; ?>
                                        <span class="absolute top-3 left-3 text-xs px-2.5 py-1 rounded font-bold 
                                            <?= strtolower($match['post_type']) == 'lost' ? 'bg-red-500 text-white' : 'bg-green-500 text-white' ?>">
                                            <?= strtoupper($match['post_type']) ?>
                                        </span>
                                        <span class="absolute top-3 right-3 text-xs px-2.5 py-1 rounded font-bold border <?= getMatchStatusBadge($match['match_status']) ?>">
                                            <?= strtoupper($match['match_status']) ?>
                                        </span>
                                    </div>

                                    <!-- Match Details -->
                                    <div class="p-4 flex-1 flex flex-col">
                                        <div class="flex justify-between items-start mb-3">
                                            <h3 class="text-base font-bold text-gray-900 leading-tight flex-1">
                                                <?= htmlspecialchars($match['pet_name'] ?: 'Unknown') ?>
                                                <span class="text-xs font-normal text-gray-500">(<?= htmlspecialchars($match['breed_name']) ?>)</span>
                                            </h3>
                                            <a href="post-details.php?id=<?= $match['post_id'] ?>" class="ml-2 flex-shrink-0 px-3 py-1.5 bg-primary text-white rounded-lg hover:bg-opacity-90 transition text-xs font-medium">
                                                <i class="ri-eye-line mr-1"></i>View
                                            </a>
                                        </div>

                                        <div class="space-y-1.5 text-xs text-gray-600 mb-3 flex-1">
                                            <p class="flex items-center gap-2"><i class="ri-palette-line text-gray-400"></i> <?= htmlspecialchars($match['color']) ?> • <?= htmlspecialchars($match['gender']) ?></p>
                                            <p class="flex items-center gap-2"><i class="ri-map-pin-line text-gray-400"></i> <?= htmlspecialchars($match['township_name']) ?>, <?= htmlspecialchars($match['city_name']) ?></p>
                                            <?php if (!empty($match['last_seen_location'])): ?>
                                                <p class="flex items-start gap-2"><i class="ri-navigation-line text-gray-400 mt-0.5"></i> <span class="line-clamp-2"><?= htmlspecialchars($match['last_seen_location']) ?></span></p>
                                            <?php endif; ?>
                                            <?php
                                            $dateLabel = (strtolower($match['post_type']) == 'lost') ? 'Last Seen:' : 'Found:';
                                            $dateValue = !empty($match['last_seen_date']) ? date('M d, Y', strtotime($match['last_seen_date'])) : 'Unknown';
                                            ?>
                                            <p class="flex items-center gap-2"><i class="ri-calendar-line text-gray-400"></i> <?= $dateLabel ?> <?= $dateValue ?></p>
                                        </div>

                                        <!-- Contact Information -->
                                        <div class="bg-gray-50 rounded-lg p-2.5 mb-3 border border-gray-100">
                                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1.5">Contact Owner</p>
                                            <div class="flex flex-col gap-1 text-xs">
                                                <span class="flex items-center gap-2 text-gray-700"><i class="ri-phone-line text-primary"></i> <?= htmlspecialchars($match['contact_phone']) ?></span>
                                                <span class="flex items-center gap-2 text-gray-700"><i class="ri-mail-line text-primary"></i> <?= htmlspecialchars($match['contact_email']) ?></span>
                                            </div>
                                        </div>

                                        <!-- Match Confidence Score -->
                                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-3 mb-3 border border-gray-200">
                                            <div class="flex justify-between items-center">
                                                <span class="text-xs font-semibold text-gray-600 uppercase">Match Confidence</span>
                                                <span class="text-lg font-bold <?= getMatchScoreColor($match['match_score']) ?>"><?= number_format($match['match_score'], 0) ?>%</span>
                                            </div>
                                            <div class="mt-1 text-xs font-medium <?= getMatchScoreColor($match['match_score']) ?>">
                                                <?= getMatchScoreLabel($match['match_score']) ?>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <?php if ($currentStatus === 'pending'): ?>
                                            <div class="flex gap-2">
                                                <form action="../code.php" method="POST" class="flex w-full gap-2">
                                                    <input type="hidden" name="match_id" value="<?= $match['match_id'] ?>">
                                                    <input type="hidden" name="post_id" value="<?= $postId ?>">
                                                    <input type="hidden" name="return_status" value="<?= htmlspecialchars($filterStatus) ?>">
                                                    <input type="hidden" name="return_page" value="<?= $currentPage ?>">

                                                    <!-- Accept Button -->
                                                    <button type="submit" name="update_match_status" value="accepted"
                                                        class="flex-1 px-3 py-2 text-sm font-medium rounded-lg transition bg-green-100 text-green-700 hover:bg-green-500 hover:text-white">
                                                        <i class="ri-check-line mr-1"></i>Accept
                                                    </button>

                                                    <!-- Reject Button -->
                                                    <button type="submit" name="update_match_status" value="rejected"
                                                        class="flex-1 px-3 py-2 text-sm font-medium rounded-lg transition bg-red-100 text-red-700 hover:bg-red-500 hover:text-white">
                                                        <i class="ri-close-line mr-1"></i>Reject
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php 
                renderUserPagination($currentPage, $totalPages, $totalRecords, $perPage, $_GET);
                ?>
                
            </div>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>

</html>