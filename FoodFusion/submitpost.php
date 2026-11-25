<?php
session_start();
require_once 'pdo.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['post-title']);
    $type = $_POST['post-type'];
    $content = trim($_POST['post-content']);

    $errors = [];

    $post_image = "images/default-post.jpg";
    $uploadOk = 1;

    if (isset($_FILES['post-image']) && $_FILES['post-image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "images/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = basename($_FILES["post-image"]["name"]);
        $target_file = $target_dir . uniqid() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" &&  $imageFileType != "gif") {
            $errors[] = "Image must be JPG, PNG, or GIF.";
            $uploadOk = 0;
        }

        if ($_FILES["post-image"]["size"] > 500000) {
            $errors[] = "Image must be less than 0.5MB.";
            $uploadOk = 0;
        }

        if ($uploadOk && move_uploaded_file($_FILES["post-image"]["tmp_name"], $target_file)) {
                $post_image = $target_file;
        } else {
                $errors[] = "Failed to upload image.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['post_errors'] = $errors;
        $_SESSION['post_data'] = $_POST;
        header("Location: community.php");
        exit;
    }

    $sql = "INSERT INTO community_posts ( user_id, post_type, title, content, image_url, created_at)
            VALUES (:user_id, :post_type, :title, :content, :image_url, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':post_type' => $type,
        ':title' => $title,
        ':content' => $content,
        ':image_url' => $post_image
    ]);

    $_SESSION['success_message'] = "Your post has been shared!";
    header("Location: community.php");
    exit;
}
?>