<?php
// test_cognito_debug.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Testing Cognito Configuration</h3>";

// Test if config is loaded
require_once 'config.php';

echo "COGNITO_REGION: " . (defined('COGNITO_REGION') ? COGNITO_REGION : 'NOT DEFINED') . "<br>";
echo "COGNITO_USER_POOL_ID: " . (defined('COGNITO_USER_POOL_ID') ? COGNITO_USER_POOL_ID : 'NOT DEFINED') . "<br>";
echo "COGNITO_APP_CLIENT_ID: " . (defined('COGNITO_APP_CLIENT_ID') ? COGNITO_APP_CLIENT_ID : 'NOT DEFINED') . "<br>";

// Test AWS SDK
echo "<h4>Testing AWS SDK</h4>";
try {
    require_once 'vendor/autoload.php';
    echo "AWS SDK loaded successfully<br>";
    
    // Test creating client
    $client = new Aws\CognitoIdentityProvider\CognitoIdentityProviderClient([
        'region' => COGNITO_REGION,
        'version' => 'latest',
        'credentials' => [
            'key' => 'YOUR_AWS_ACCESS_KEY_ID',
            'secret' => 'YOUR_AWS_SECRET_ACCESS_KEY'
        ]
    ]);
    echo "Cognito client created successfully<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<h4>Testing with dummy credentials</h4>";
// Test authentication
$_POST['username'] = 'test@example.com';
$_POST['password'] = 'TestPass123!';

require_once 'cognito.php';
$result = CognitoAuth::authenticate($_POST['username'], $_POST['password']);

echo "<pre>Result: " . print_r($result, true) . "</pre>";
