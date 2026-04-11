<?php
require_once("db-settings.php");

$horse_name = $_GET['horseId'] ?? '';  // Changed to match what JavaScript sends

if (empty($horse_name)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Horse name is required']);
    exit;
}

$query = "SELECT * FROM horse_inspection_tb WHERE horse = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $horse_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([]); // Return empty object if no records found
    exit;
}

$inspectionData = $result->fetch_assoc();
echo json_encode($inspectionData);
