<?php
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');
require_once 'functions.php';
require 'db-settings.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $horseName = $_POST['horseId'] ?? null;

    if (empty($horseName)) {
        echo json_encode(['success' => false, 'error' => 'Horse name is required']);
        exit;
    }

    // Build the data array from known expected fields
    $expectedFields = [
        'YEARFOAL', 'SEX', 'Sire', 'DAM', 'DATEFOAL', 'TYPE', 'COLOR', 'GAIT', 
        'BREDTO', 'FARMNAME', 'inspector_name', 'inspection_date', 'SALECODE', 
        'req_day', 'dr_psd', 'day_rating_indicator', 'arr_num', 'farm_barn_id', 
        'farm_id', 'HORSE', 'Sireofdam', 'TATTOO', 'pedigree', 'consignor', 
        'biomechanics', 'BREED', 'in_foal_to', 'LASTBRED', 'brd_status', 'PRICE',
        'SALEYEAR', 'salesection', 'salestall', 'PEMCODE', 'FARMCODE', 'rating2_sb',
        'rating6_le', 'rating7_guest', 'PURFNAME', 'PURLNAME', 
        'purchasers_comments_post_sale', 'broker_comments', 'initial_trainer_first_name',
        'initial_trainer_last_name', 'initial_trainer_location', 'SALEDATE',
        'trainer_comments_as_two_year_old'
    ];
    
    $data = [];

    foreach ($expectedFields as $field) {
        if (isset($_POST[$field])) {
            $data[$field] = $_POST[$field];
        }
    }

    // Call the update function
    $result = updateHorseDetailsTb($horseName, $data);

    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}