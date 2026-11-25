<?php

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to delete breed.", 'error');
    redirect('login.php');
}

// Validate Breed ID
$paramResult = checkParamId('id');

if (is_numeric($paramResult)) {
    $breedId = $paramResult;

    // Retrieve Breed Record
    $breed = getById($pdo, 'breed', $breedId);

    // Check Breed Exists
    if ($breed['status'] == 200) {

        // Check Whether Breed Is Used By Existing Pets
        $checkSql = "SELECT COUNT(*) FROM pet WHERE breed_id = :breed_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            'breed_id' => $breedId
        ]);

        $petCount = $checkStmt->fetchColumn();

        // Prevent Deletion If Breed Is In Use
        if ($petCount > 0) {
            setMessage('Cannot delete this breed because it is currently used by existing pets.', 'error');
        } else {

            // Delete Breed Record
            $breedDeleteResult = deleteQuery($pdo, 'breed', $breedId);
            if ($breedDeleteResult) {
                setMessage('Breed deleted successfully.', 'success');
            } else {
                setMessage('Failed to delete breed.', 'error');
            }
        }
    } else {
        setMessage($breed['message'], 'error');
    }
} else {
    setMessage($paramResult, 'error');
}
redirect('breeds.php');