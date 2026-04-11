<?php
// cognito.php - Modified to NOT auto-confirm users
require_once 'vendor/autoload.php';

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Exception\AwsException;

class CognitoAuth {
    
    public static function authenticate($username, $password) {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
            ]);
            
            $result = $client->initiateAuth([
                'AuthFlow' => 'USER_PASSWORD_AUTH',
                'ClientId' => COGNITO_APP_CLIENT_ID,
                'AuthParameters' => [
                    'USERNAME' => $username,
                    'PASSWORD' => $password
                ]
            ]);
            
            return ['success' => true];
            
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => 'Invalid username or password'
            ];
        }
    }
    
    public static function register($email, $password, $first_name, $last_name, $user_role) {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
            ]);
            
            // Sign up user (creates unconfirmed user)
            $result = $client->signUp([
                'ClientId' => COGNITO_APP_CLIENT_ID,
                'Username' => $email,
                'Password' => $password,
                'UserAttributes' => [
                    ['Name' => 'email', 'Value' => $email],
                    ['Name' => 'given_name', 'Value' => $first_name],
                    ['Name' => 'family_name', 'Value' => $last_name]
                ]
            ]);
            
            // REMOVED THE AUTO-CONFIRM LINE
            
            return [
                'success' => true,
                'message' => 'User created successfully. Please verify your email.',
                'userSub' => $result['UserSub']
            ];
            
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getAwsErrorMessage(),
                'error_code' => $e->getAwsErrorCode()
            ];
        }
    }
    
    // Add this new method to verify email
    public static function verifyEmail($email, $verification_code) {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
            ]);
            
            $result = $client->confirmSignUp([
                'ClientId' => COGNITO_APP_CLIENT_ID,
                'Username' => $email,
                'ConfirmationCode' => $verification_code
            ]);
            
            return [
                'success' => true,
                'message' => 'Email verified successfully'
            ];
            
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getAwsErrorMessage(),
                'error_code' => $e->getAwsErrorCode()
            ];
        }
    }
    
    // Add this method to resend verification code
    public static function resendVerificationCode($email) {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
            ]);
            
            $result = $client->resendConfirmationCode([
                'ClientId' => COGNITO_APP_CLIENT_ID,
                'Username' => $email
            ]);
            
            return [
                'success' => true,
                'message' => 'Verification code resent successfully'
            ];
            
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getAwsErrorMessage()
            ];
        }
    }
}