<?php
session_start();

$dir = "images/"; 

if (isset($_GET['file'])) {
    $filename = basename($_GET['file']); 
    $filepath = $dir . $filename;
    
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (file_exists($filepath) && in_array($file_extension, $allowed_extensions)) {
        
        while (ob_get_level()) {
            ob_end_clean();
        }

        $mimeType = mime_content_type($filepath);

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: public, max-age=3600'); 

        readfile($filepath);
        exit;
    } else {
        $_SESSION['error'] = "Image not found or invalid format.";
        header("Location: culinary.php");
        exit;
    }
}