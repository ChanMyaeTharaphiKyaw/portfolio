<?php
require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage('Please login first to delete city.', 'error');
    redirect('login.php');
}

// Validate City ID Parameter
$paramResult = checkParamId('id');

if (is_numeric($paramResult)) {
    $cityId = $paramResult;

    // Retrieve City Record
    $city = getById($pdo, 'city', $cityId);

    // Check City Exists
    if ($city['status'] == 200) {

        // Check Whether City Is Used By Existing Townships
        $checkSql = "SELECT COUNT(*) FROM township WHERE city_id = :city_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            'city_id' => $cityId
        ]);

        $townshipCount = $checkStmt->fetchColumn();

        // Prevent Deletion If City Has Townships
        if ($townshipCount > 0) {
            setMessage('Cannot delete this city because it contains existing townships.', 'error');
        } else {
            $cityDeleteResult = deleteQuery($pdo, 'city', $cityId);
            if ($cityDeleteResult) {
                setMessage('City Deleted Successfully.', 'success');
            } else {
                setMessage('Failed to delete city.', 'error');
            }
        }
    } else {
        setMessage($city['message'], 'error');
    }
} else {
    setMessage($paramResult, 'error');
}
redirect('cities.php');