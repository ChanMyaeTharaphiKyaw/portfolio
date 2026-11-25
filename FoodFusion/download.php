<?php
$dir = __DIR__ . "/resources/";

if (isset($_GET['file'])) {
    $filename = basename($_GET['file']);
    $filepath = $dir . $filename;

    if (!empty($filename) && file_exists($filepath)) {

        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));

        readfile($filepath);
        exit;
    } else {
        $_SESSION['error'] = "File not found.";
        exit;
    }
} else {
    $_SESSION['error'] = "No file specified.";
    exit;
}
