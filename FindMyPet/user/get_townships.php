<?php

require_once '../database/pdo.php';
require_once '../database/functions.php'; 

if (isset($_GET['city_id'])) {
    $city_id = $_GET['city_id'];
    $townships = getTownshipsByCity($pdo, $city_id);
    
    if ($townships) {
        // echo '<option value="">Select Township</option>';
        foreach ($townships as $row) {
            echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['township_name']) . '</option>';
        }
    } else {
        echo '<option value="">No Townships Found</option>';
    }
}
