<?php

require_once '../database/pdo.php';
require_once '../database/functions.php'; 

if (isset($_GET['pet_type_id'])) {
    $pet_type_id = $_GET['pet_type_id'];
    $breeds = getBreedsByType($pdo, $pet_type_id);

    if ($breeds) {
        // echo '<option value="">Select Breed</option>';
        foreach ($breeds as $row) {
            echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['breed_name']) . '</option>';
        }
    } else {
        echo '<option value="">No Breeds Found</option>';
    }
}