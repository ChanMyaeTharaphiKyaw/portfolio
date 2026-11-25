<?php

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage('Please login first to delete pet type.', 'error');
    redirect('login.php');
}

// Validate Pet Type ID Parameter
$paramResult = checkParamId('id');

if (is_numeric($paramResult)) {
    $typeId = $paramResult;

    // Retrieve Pet Type Record
    $type = getById($pdo, 'pet_type', $typeId);

    // Check Pet Type Exists
    if ($type['status'] == 200) {

        // Check Whether Pet Type Is Used By Existing Breeds
        $checkSql = "SELECT COUNT(*) FROM breed WHERE pet_type_id = :type_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            'type_id' => $typeId
        ]);

        $breedCount = $checkStmt->fetchColumn();

        // Prevent Deletion If Pet Type Has Related Breeds
        if ($breedCount > 0) {

            setMessage('Cannot delete this pet type because it contains existing breeds.', 'error');
        } else {

            // Delete Pet Type Record
            $typeDeleteResult = deleteQuery($pdo, 'pet_type', $typeId);

            if ($typeDeleteResult) {
                setMessage('Pet type deleted successfully.', 'success');
            } else {
                setMessage('Failed to delete pet type.', 'error');
            }
        }
    } else {

        // Pet Type ID Does Not Exist
        setMessage($type['message'], 'error');
    }
} else {

    // Invalid Pet Type ID
    setMessage($paramResult, 'error');
}
redirect('pet_types.php');
