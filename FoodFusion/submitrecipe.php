<?php
session_start();
require_once 'pdo.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $errors = [];
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['recipe-title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $hours = (int)$_POST['cook-hours'];
    $minutes = (int)$_POST['cook-minutes'];
    $cook_time = $hours * 60 + $minutes;

    $cuisine = !empty($_POST['cuisine']) ? $_POST['cuisine'] : null;
    $diet = !empty($_POST['diet']) ? $_POST['diet'] : null;
    $difficulty = !empty($_POST['difficulty']) ? $_POST['difficulty'] : null;
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');

    if ($cook_time <= 0) {
        $errors[] = "Cook time must be at least 1 minute.";
    }

    $recipe_image = "images/default-recipe.jpg";
    $uploadOk = 1;

    if (isset($_FILES['recipe-image']) && $_FILES['recipe-image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "images/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = basename($_FILES["recipe-image"]["name"]);
        $target_file = $target_dir . uniqid() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" &&  $imageFileType != "gif") {
            $errors[] = "Image must be JPG, JPEG, PNG, or GIF.";
            $uploadOk = 0;
        }

        if ($_FILES["recipe-image"]["size"] > 500000) {
            $errors[] = "Image must be less than 0.5MB.";
            $uploadOk = 0;
        }


        if ($uploadOk && move_uploaded_file($_FILES["recipe-image"]["tmp_name"], $target_file)) {
                $recipe_image = $target_file;
        } else {
                $errors[] = "Failed to upload image.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: community.php");
        exit();
    }

    $sql = "INSERT INTO recipes ( user_id, cuisine_id, diet_id, difficulty_id, title, description, ingredients, cook_time, instructions, image_url, uploaded_at) 
            VALUES (:user_id, :cuisine, :diet, :difficulty, :title, :description, :ingredients, :cook_time, :instructions, :image_url, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':cuisine' => $cuisine,
        ':diet' => $diet,
        ':difficulty' => $difficulty,
        ':title' => $title,
        ':description' => $description,
        ':ingredients' => $ingredients,
        ':cook_time' => $cook_time,
        ':instructions' => $instructions,
        ':image_url' => $recipe_image
    ]);

    $_SESSION['success_message'] = "Recipe shared successfully!";
    header("Location: community.php");
    exit;
}
