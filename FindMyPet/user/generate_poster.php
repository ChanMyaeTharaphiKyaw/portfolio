<?php

require_once '../database/pdo.php';
require_once '../database/functions.php';

// Check User Authentication
if (!isset($_SESSION['authenticated'])) {
    setMessage('Please login first to generate poster.', 'warning');
    redirect('login.php');
}

// Retrieve Post ID
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate Post ID
if ($postId <= 0) {
    setMessage('Invalid post selected.', 'error');
    redirect('post_details.php?id=' . $postId);
}

// Retrieve Post Details
$postResponse = getPostById($pdo, $postId);

if ($postResponse['status'] != 200) {
     setMessage('The requested post could not be found.', 'error');
    redirect('post_details.php?id=' . $postId);
}
$post = $postResponse['data'];

// Generate Poster HTML
$html = generatePosterHtml($post);

// Display HTML Poster
if (isset($_GET['render']) && $_GET['render'] == 'html') {
    header('Content-Type: text/html; charset=utf-8');
    echo $html;
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poster Preview - Lost & Found Pets</title>
    <link rel="stylesheet" href="../assets/css/output.css">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center py-10 px-4">
    <!-- Page Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Your Poster is Ready to Download!</h1>
        <p class="text-gray-600">Review the preview below. Click the button to save the PDF to your device.</p>
    </div>

    <!-- Poster Preview -->
    <div class="bg-white p-4 rounded-lg shadow-xl border border-gray-200 mb-8 w-full max-w-3xl">
        <iframe 
            src="generate_poster.php?id=<?= $postId ?>&render=html" 
            class="w-full h-[800px] border-0 rounded"
            title="Poster Preview">
        </iframe>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4">
        <!-- Download Poster -->
        <a href="../code.php?download_poster=1&post_id=<?= $postId ?>" 
           class="inline-flex items-center justify-center px-8 py-3 text-lg font-semibold text-white bg-[#1f7a5f] rounded-lg shadow-md hover:bg-[#165c47] transition duration-200 focus:outline-none focus:ring-2 focus:ring-[#1f7a5f] focus:ring-offset-2">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download PDF Poster
        </a>
        <!-- Back to Post -->
        <a href="post_details.php?id=<?= $postId ?>" 
           class="inline-flex items-center justify-center px-8 py-3 text-lg font-semibold text-[#1f7a5f] bg-white border-2 border-[#1f7a5f] rounded-lg shadow-sm hover:bg-[#f0fdf4] transition duration-200 focus:outline-none focus:ring-2 focus:ring-[#1f7a5f] focus:ring-offset-2">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Post
        </a>
    </div>

</body>
</html>