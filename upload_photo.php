<?php
ob_start();

require 'vendor/autoload.php';
require 'db-settings.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;


header('Content-Type: application/json');

// Simple static caching for S3 client
function getS3Client() {
    static $s3Client = null;
    static $counter = 0;
    
    $counter++;
    $pid = getmypid();
    
    error_log("📞 S3 Client call #$counter in PID: $pid");
    
    if ($s3Client === null) {
        error_log("❌ S3 Client is NULL - creating new instance");
        $start = microtime(true);
        
        $s3Client = new S3Client([
            'region' => 'us-east-1',
            'version' => 'latest',
            'suppress_php_deprecation_warning' => true
        ]);
        
        $time = round(microtime(true) - $start, 2);
        error_log("⏰ S3 Client creation took: {$time}s");
    } else {
        error_log("✅ S3 Client EXISTS - reusing instance");
    }
    
    return $s3Client;
}

try {
    $bucket = 'horse-list-photos-and-details'; // bucket name

    $s3 = getS3Client();

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK || !isset($_POST['horseId'])) {
        throw new Exception('Missing file or horse ID.');
    }

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $originalName = $_FILES['file']['name'];
    $fileMimeType = mime_content_type($fileTmpPath);
    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Extension & MIME type check
    $allowedExtensions = ['pdf', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff'];
    $allowedMimeTypes = [
        'application/pdf',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/bmp',
        'image/tiff'
    ];

    if (!in_array($fileExtension, $allowedExtensions)) {
        throw new Exception("Invalid file extension: .$fileExtension");
    }

    if (!in_array($fileMimeType, $allowedMimeTypes)) {
        throw new Exception("Unsupported MIME type: $fileMimeType");
    }

    $horseId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['horseId']);
    $uniqueFilename = "uploads/horse_" . $horseId . "_" . time() . "." . $fileExtension;

    $s3->putObject([
        'Bucket' => $bucket,
        'Key'    => $uniqueFilename,
        'Body'   => fopen($fileTmpPath, 'rb'),
        'ContentType' => $fileMimeType
    ]);

    // Presigned URL valid for 5 minutes
    $cmd = $s3->getCommand('GetObject', [
        'Bucket' => $bucket,
        'Key'    => $uniqueFilename
    ]);
    $request = $s3->createPresignedRequest($cmd, '+5 minutes');

    // DB insert (uses global $mysqli from db-settings.php)
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO horse_images (horse_id, image_url, uploaded_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $horseId, $uniqueFilename);

    if (!$stmt->execute()) {
        throw new Exception("Database error: " . $stmt->error);
    }

    $fileId = $stmt->insert_id;
    $stmt->close();

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'url' => (string) $request->getUri(),
        'id' => $fileId,
        'name' => $originalName
    ]);
    exit;
} catch (AwsException $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'error' => 'AWS Error: ' . ($e->getAwsErrorMessage() ?: $e->getMessage())
    ]);
    exit;
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}
