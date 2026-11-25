<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to submit rating.";
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $user_id   = $_SESSION['user_id'];
    $recipe_id = (int)$_POST['recipe_id'];
    $rating    = (int)$_POST['rating'];

    if ($rating < 1 || $rating > 5) {
        $_SESSION['rating_error'] = "Invalid rating. Please select 1 to 5 stars.";
        header("Location: community_details.php?id=" . $recipe_id . "&type=recipe");
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM recipe_ratings 
                           WHERE user_id = :uid AND recipe_id = :rid");
    $stmt->execute([
        ':uid' => $user_id,
        ':rid' => $recipe_id
    ]);

    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE recipe_ratings 
                               SET rating = :rating 
                               WHERE user_id = :uid AND recipe_id = :rid");
        $stmt->execute([
            ':rating' => $rating,
            ':uid' => $user_id,
            ':rid' => $recipe_id
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO recipe_ratings (recipe_id, user_id, rating)
                               VALUES (:rid, :uid, :rating)");
        $stmt->execute([
            ':rid' => $recipe_id,
            ':uid' => $user_id,
            ':rating' => $rating
        ]);

    }
    
    header("Location: community_details.php?id=" . $recipe_id . "&type=recipe");
    exit;
}
?>
