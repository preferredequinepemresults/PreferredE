<?php
header('Content-Type: application/json');
require_once 'db-settings.php';  // Include your DB connection
require_once 'functions.php';    // Include your functions file with getHorseDetails()

if (isset($_GET['horseId'])) {
    $horseId = $_GET['horseId'];
    $data = getHorseDetailsTb($horseId);
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Missing horseId']);
}
