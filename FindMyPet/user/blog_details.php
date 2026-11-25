<?php
$pageTitle = "Blog Details";
require_once "../database/pdo.php";
require_once "../database/functions.php";
include '../includes/header.php';

$blogId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($blogId <= 0) {
    setMessage('Invalid blog ID.', 'error');
    redirect('blog.php');
}

// Retrieve Blog Details
$blog = getBlogById($pdo, $blogId);

if (!$blog) {
    setMessage('Blog post not found.', 'error');
    redirect('blog.php');
}

$image = !empty($blog['data']['image']) ? $blog['data']['image'] : 'uploads/blogs/default.png';

$formattedContent = nl2br(htmlspecialchars($blog['data']['content'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/output.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet" />
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
    <style>
        .blog-content-body {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #374151;
        }

        .blog-content-body p {
            margin-bottom: 1.25rem;
        }
    </style>
</head>

<body class="bg-gray-50 font-poppins text-gray-800">

    <main class="max-w-[1200px] mx-auto my-10 px-5">

        <!-- Navigation -->
        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-600">
            <a href="blog.php" class="hover:text-primary transition flex items-center gap-1">Blog</a>
            <i class="ri-arrow-right-s-line text-gray-400"></i>
            <span class="text-gray-800 font-medium"><?= htmlspecialchars($blog['data']['title']) ?></span>
        </nav>

        <?php include '../includes/alert.php'; ?>

        <!-- Blog Container -->
        <article class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">

            <!-- Image Section -->
            <?php if (!empty($blog['data']['image'])): ?>
                <div class="relative h-64 md:h-96 bg-gray-100">
                    <img src="../<?= htmlspecialchars($image) ?>"
                        alt="<?= htmlspecialchars($blog['data']['title']) ?>"
                        class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 md:p-10 text-white">
                        <span class="inline-block px-3 py-1 bg-primary text-white text-xs font-bold uppercase rounded mb-3">
                            <?= htmlspecialchars($blog['data']['category'] ?? 'General') ?>
                        </span>
                        <h1 class="text-2xl md:text-4xl font-bold leading-tight">
                            <?= htmlspecialchars($blog['data']['title']) ?>
                        </h1>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-gradient-to-r from-primary to-primary/80 p-6 md:p-10 text-white">
                    <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-bold uppercase rounded mb-3">
                        <?= htmlspecialchars($blog['data']['category'] ?? 'General') ?>
                    </span>
                    <h1 class="text-2xl md:text-4xl font-bold leading-tight">
                        <?= htmlspecialchars($blog['data']['title']) ?>
                    </h1>
                </div>
            <?php endif; ?>

            <!-- Blog Information -->
            <div class="px-6 md:px-10 py-4 border-b border-gray-100 flex flex-wrap items-center gap-4 text-sm text-gray-600">
                <span class="flex items-center gap-2">
                    <i class="ri-user-line text-primary"></i>
                    <span class="font-medium text-gray-800"><?= htmlspecialchars($blog['data']['author_name'] ?? 'Unknown') ?></span>
                </span>
                <span class="flex items-center gap-2">
                    <i class="ri-calendar-line text-primary"></i>
                    <?= date('M d, Y', strtotime($blog['data']['created_at'])) ?>
                </span>
                <?php if (!empty($blog['data']['updated_at']) && $blog['data']['updated_at'] !== $blog['data']['created_at']): ?>
                    <span class="flex items-center gap-2 text-gray-500">
                        <i class="ri-refresh-line"></i>
                        Updated <?= date('M d, Y', strtotime($blog['data']['updated_at'])) ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Main Content Body -->
            <div class="px-6 md:px-10 py-8 md:py-10">
                <div class="blog-content-body max-w-3xl">
                    <?= $formattedContent ?>
                </div>
            </div>

        </article>

    </main>

    <?php include '../includes/footer.php'; ?>

</body>

</html>