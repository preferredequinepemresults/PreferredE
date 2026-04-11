<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

header('Content-Type: application/json');

// S3 Client with static caching
function getS3Client($region) {
    static $s3Client = null;
    
    if ($s3Client === null) {
        $s3Client = new S3Client([
            'region' => $region,
            'version' => 'latest',
            'suppress_php_deprecation_warning' => true
        ]);
    }
    
    return $s3Client;
}

// Secrets Manager Client with static caching  
function getSecretsManagerClient($region) {
    static $secretsClient = null;
    
    if ($secretsClient === null) {
        $secretsClient = new SecretsManagerClient([
            'version' => 'latest',
            'region'  => $region,
        ]);
    }
    
    return $secretsClient;
}

// Validate request
if (!isset($_POST['imageUrl'])) {
    echo json_encode(['success' => false, 'error' => 'Missing image URL']);
    exit;
}

$imageUrl = $_POST['imageUrl'];

// Parse S3 key from URL
$parsedUrl = parse_url($imageUrl);
if (!isset($parsedUrl['path'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid image URL']);
    exit;
}
$path = ltrim($parsedUrl['path'], '/'); // S3 object key

// Fetch AWS credentials and config from Secrets Manager
$secretName = 'MyApp/S3Credentials'; // Replace with your secret name or env var
$region = 'us-east-1';               // Replace with your AWS region or env var

try {
    $secretsClient = getSecretsManagerClient($region);

    $result = $secretsClient->getSecretValue([
        'SecretId' => $secretName,
    ]);

    if (isset($result['SecretString'])) {
        $secretData = json_decode($result['SecretString'], true);

        // Expecting secret JSON like:
        // {
        //   "AWS_ACCESS_KEY_ID": "...",
        //   "AWS_SECRET_ACCESS_KEY": "...",
        //   "AWS_BUCKET": "...",
        //   "AWS_REGION": "..."
        // }

        $bucket = $secretData['AWS_BUCKET'] ?? null;
        $region = $secretData['AWS_REGION'] ?? $region;

        if (!$bucket) {
            throw new Exception("Incomplete AWS credentials or bucket info in secret");
        }
    } else {
        throw new Exception("SecretString not found in secret");
    }
} catch (AwsException $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve secrets: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
    exit;
}

// Delete from S3
try {
    $s3 = getS3Client($region);
    $s3->deleteObject([
        'Bucket' => $bucket,
        'Key' => $path,
    ]);
} catch (AwsException $e) {
    echo json_encode(['success' => false, 'error' => 'S3 delete failed: ' . $e->getAwsErrorMessage()]);
    exit;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'S3 delete error: ' . $e->getMessage()]);
    exit;
}

// Delete from database
require 'db-settings.php'; // $mysqli should be defined here

$stmt = $mysqli->prepare("DELETE FROM horse_images WHERE image_url = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Database prepare failed']);
    exit;
}
$stmt->bind_param("s", $path);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database entry not found']);
}

$stmt->close();
$mysqli->close();