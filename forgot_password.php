<?php
session_start();
include("./header.php");

require_once("config.php");
require_once("cognito.php");
require_once("db-settings.php");

$message = '';
$message_type = ''; // success, error, info
$show_reset_form = false;
$email = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // STEP 1: Send reset code
    if (isset($_POST['send_code'])) {
        $email = trim($_POST['email']);
        
        if (empty($email)) {
            $message = "Please enter your email address";
            $message_type = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Please enter a valid email address";
            $message_type = "error";
        } else {
            try {
                // Check if user exists in database
                $user_check = $mysqli->prepare("SELECT * FROM users WHERE EMAIL = ?");
                $user_check->bind_param("s", $email);
                $user_check->execute();
                $user_result = $user_check->get_result();
                
                if ($user_result->num_rows === 0) {
                    $message = "No account found with this email address";
                    $message_type = "error";
                } else {
                    $user_data = $user_result->fetch_assoc();
                    
                    // Check if user is verified in Cognito
                    if ($user_data['cognito_verified'] == 0) {
                        $message = "Your email is not verified. Please verify your email first before resetting your password.";
                        $message_type = "error";
                        
                        // Store email in session for verification redirect
                        $_SESSION['verify_email'] = $email;
                        
                        // Add verification link
                        echo '<script>
                            setTimeout(function() {
                                if(confirm("Your email is not verified. Would you like to go to the verification page?")) {
                                    window.location.href = "verify.php";
                                }
                            }, 100);
                        </script>';
                    } else {
                        // User is verified, proceed with password reset
                        
                        // Try Cognito first - IAM role provides credentials automatically
                        require_once 'vendor/autoload.php';
                        
                        // Client uses IAM role from EC2 instance
                        $client = new Aws\CognitoIdentityProvider\CognitoIdentityProviderClient([
                            'region' => COGNITO_REGION,
                            'version' => 'latest'
                        ]);
                        
                        try {
                            // Attempt to send reset code via Cognito
                            $client->forgotPassword([
                                'ClientId' => COGNITO_APP_CLIENT_ID,
                                'Username' => $email
                            ]);
                            
                            $_SESSION['reset_email'] = $email;
                            $_SESSION['reset_method'] = 'cognito';
                            $message = "Reset code sent to your email! Please check your inbox.";
                            $message_type = "success";
                            $show_reset_form = true;
                            
                        } catch (Exception $cognitoError) {
                            $errorMessage = $cognitoError->getMessage();
                            error_log("Cognito forgotPassword error: " . $errorMessage);
                            
                            // FALLBACK: Manual reset via database
                            
                            // Generate 6-digit reset code
                            $reset_code = sprintf("%06d", mt_rand(1, 999999));
                            
                            // Set expiry (1 hour from now)
                            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                            
                            // Store reset code in database
                            $update_code = $mysqli->prepare("UPDATE users SET reset_code = ?, reset_code_expiry = ? WHERE EMAIL = ?");
                            $update_code->bind_param("sss", $reset_code, $expiry, $email);
                            
                            if ($update_code->execute()) {
                                // Send email with reset code
                                $to = $email;
                                $subject = "Password Reset Code";
                                
                                $headers = "MIME-Version: 1.0" . "\r\n";
                                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                                $headers .= "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
                                $headers .= "Reply-To: support@" . $_SERVER['HTTP_HOST'] . "\r\n";
                                
                                $email_body = "
                                <!DOCTYPE html>
                                <html>
                                <head>
                                    <style>
                                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                                        .header { background: #2E4053; color: white; padding: 20px; text-align: center; }
                                        .content { padding: 30px; background: #f9f9f9; }
                                        .code { 
                                            font-size: 32px; 
                                            font-weight: bold; 
                                            color: #2E4053; 
                                            text-align: center; 
                                            padding: 20px; 
                                            background: white; 
                                            border-radius: 5px;
                                            margin: 20px 0;
                                            letter-spacing: 5px;
                                        }
                                        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                                    </style>
                                </head>
                                <body>
                                    <div class='container'>
                                        <div class='header'>
                                            <h2>Password Reset Request</h2>
                                        </div>
                                        <div class='content'>
                                            <p>Hello,</p>
                                            <p>We received a request to reset your password. Use the code below to complete your password reset:</p>
                                            <div class='code'>{$reset_code}</div>
                                            <p>This code will expire in <strong>1 hour</strong>.</p>
                                            <p>If you didn't request this password reset, please ignore this email or contact support.</p>
                                        </div>
                                        <div class='footer'>
                                            <p>&copy; " . date('Y') . " Preferred Equine. All rights reserved.</p>
                                        </div>
                                    </div>
                                </body>
                                </html>";
                                
                                if (mail($to, $subject, $email_body, $headers)) {
                                    $_SESSION['reset_email'] = $email;
                                    $_SESSION['reset_method'] = 'manual';
                                    
                                    $message = "Reset code sent to your email! Please check your inbox.";
                                    $message_type = "success";
                                    $show_reset_form = true;
                                } else {
                                    $message = "Failed to send reset code email. Please try again.";
                                    $message_type = "error";
                                }
                            } else {
                                $message = "System error. Please try again later.";
                                $message_type = "error";
                            }
                        }
                    }
                }
                
            } catch (Exception $e) {
                $message = "System Error: " . $e->getMessage();
                $message_type = "error";
                error_log("Password reset error: " . $e->getMessage());
            }
        }
    }
    
    // STEP 2: Reset password with code
    if (isset($_POST['reset_password'])) {
        $email = $_SESSION['reset_email'] ?? '';
        $code = trim($_POST['code']);
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $reset_method = $_SESSION['reset_method'] ?? 'cognito';
        
        $errors = [];
        
        // Validation
        if (empty($email)) {
            $errors[] = "Email session expired. Please start over.";
        }
        if (empty($code)) {
            $errors[] = "Reset code is required";
        }
        if (empty($new_password)) {
            $errors[] = "New password is required";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        }
        
        // Password strength validation
        if (strlen($new_password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        }
        if (!preg_match('/[A-Z]/', $new_password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        if (!preg_match('/[a-z]/', $new_password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        if (!preg_match('/[0-9]/', $new_password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        if (empty($errors)) {
            try {
                
                if ($reset_method === 'manual') {
                    // MANUAL RESET: Verify code from database
                    
                    // Check if code exists and not expired
                    $verify_stmt = $mysqli->prepare("SELECT * FROM users WHERE EMAIL = ? AND reset_code = ? AND reset_code_expiry > NOW()");
                    $verify_stmt->bind_param("ss", $email, $code);
                    $verify_stmt->execute();
                    $verify_result = $verify_stmt->get_result();
                    
                    if ($verify_result->num_rows > 0) {
                        // Code is valid - update password in database
                        $update_stmt = $mysqli->prepare("UPDATE users SET PASSWORD = ?, reset_code = NULL, reset_code_expiry = NULL WHERE EMAIL = ?");
                        $update_stmt->bind_param("ss", $new_password, $email);
                        
                        if ($update_stmt->execute()) {
                            
                            // Also try to update in Cognito using IAM role
                            try {
                                require_once 'vendor/autoload.php';
                                
                                // Use IAM role from EC2
                                $client = new Aws\CognitoIdentityProvider\CognitoIdentityProviderClient([
                                    'region' => COGNITO_REGION,
                                    'version' => 'latest'
                                ]);
                                
                                // Admin set password in Cognito
                                $client->adminSetUserPassword([
                                    'UserPoolId' => COGNITO_USER_POOL_ID,
                                    'Username' => $email,
                                    'Password' => $new_password,
                                    'Permanent' => true
                                ]);
                                
                                // Mark as verified in Cognito
                                $client->adminUpdateUserAttributes([
                                    'UserPoolId' => COGNITO_USER_POOL_ID,
                                    'Username' => $email,
                                    'UserAttributes' => [
                                        [
                                            'Name' => 'email_verified',
                                            'Value' => 'true'
                                        ]
                                    ]
                                ]);
                                
                                // Update database cognito_verified flag
                                $update_verified = $mysqli->prepare("UPDATE users SET cognito_verified = 1 WHERE EMAIL = ?");
                                $update_verified->bind_param("s", $email);
                                $update_verified->execute();
                                
                            } catch (Exception $cogError) {
                                // Log Cognito error but don't fail the reset
                                error_log("Cognito update failed for user {$email}: " . $cogError->getMessage());
                            }
                            
                            // Clear session
                            unset($_SESSION['reset_email']);
                            unset($_SESSION['reset_method']);
                            
                            $message = "Password reset successfully! You can now login with your new password.";
                            $message_type = "success";
                            $show_reset_form = false;
                            
                            // Redirect to login after 3 seconds
                            echo '<script>
                                setTimeout(function() {
                                    window.location.href = "login.php";
                                }, 3000);
                            </script>';
                            
                        } else {
                            $message = "Failed to update password. Please try again.";
                            $message_type = "error";
                            $show_reset_form = true;
                        }
                        
                    } else {
                        $message = "Invalid or expired reset code. Please request a new code.";
                        $message_type = "error";
                        $show_reset_form = true;
                    }
                    
                } else {
                    // COGNITO RESET: Use Cognito's confirmForgotPassword
                    require_once 'vendor/autoload.php';
                    
                    // Use IAM role from EC2
                    $client = new Aws\CognitoIdentityProvider\CognitoIdentityProviderClient([
                        'region' => COGNITO_REGION,
                        'version' => 'latest'
                    ]);
                    
                    // Confirm password reset in Cognito
                    $client->confirmForgotPassword([
                        'ClientId' => COGNITO_APP_CLIENT_ID,
                        'Username' => $email,
                        'ConfirmationCode' => $code,
                        'Password' => $new_password
                    ]);
                    
                    // Update password in database
                    $update_stmt = $mysqli->prepare("UPDATE users SET PASSWORD = ? WHERE EMAIL = ?");
                    $update_stmt->bind_param("ss", $new_password, $email);
                    $update_stmt->execute();
                    
                    // Ensure cognito_verified is set to 1
                    $update_verified = $mysqli->prepare("UPDATE users SET cognito_verified = 1 WHERE EMAIL = ?");
                    $update_verified->bind_param("s", $email);
                    $update_verified->execute();
                    
                    // Clear session
                    unset($_SESSION['reset_email']);
                    unset($_SESSION['reset_method']);
                    
                    $message = "Password reset successfully! You can now login with your new password.";
                    $message_type = "success";
                    $show_reset_form = false;
                    
                    // Redirect to login after 3 seconds
                    echo '<script>
                        setTimeout(function() {
                            window.location.href = "login.php";
                        }, 3000);
                    </script>';
                }
                
            } catch (Exception $e) {
                $message = "Error: " . $e->getMessage();
                $message_type = "error";
                $show_reset_form = true;
                error_log("Password reset confirmation error: " . $e->getMessage());
            }
        } else {
            $message = implode("<br>", $errors);
            $message_type = "error";
            $show_reset_form = true;
        }
    }
}

// Get email from session if available
if (!$email && isset($_SESSION['reset_email'])) {
    $email = $_SESSION['reset_email'];
    $show_reset_form = true;
}

// Resend code handler
if (isset($_GET['resend']) && $_GET['resend'] == 1 && isset($_SESSION['reset_email'])) {
    $email = $_SESSION['reset_email'];
    $reset_method = $_SESSION['reset_method'] ?? 'manual';
    
    if ($reset_method === 'manual') {
        // Generate new code
        $reset_code = sprintf("%06d", mt_rand(1, 999999));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $update_code = $mysqli->prepare("UPDATE users SET reset_code = ?, reset_code_expiry = ? WHERE EMAIL = ?");
        $update_code->bind_param("sss", $reset_code, $expiry, $email);
        
        if ($update_code->execute()) {
            // Send email
            $to = $email;
            $subject = "New Password Reset Code";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
            
            $email_body = "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #2E4053; color: white; padding: 20px; text-align: center; }
                    .content { padding: 30px; background: #f9f9f9; }
                    .code { 
                        font-size: 32px; 
                        font-weight: bold; 
                        color: #2E4053; 
                        text-align: center; 
                        padding: 20px; 
                        background: white; 
                        border-radius: 5px;
                        margin: 20px 0;
                        letter-spacing: 5px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>New Password Reset Code</h2>
                    </div>
                    <div class='content'>
                        <p>Hello,</p>
                        <p>Here is your new password reset code:</p>
                        <div class='code'>{$reset_code}</div>
                        <p>This code will expire in <strong>1 hour</strong>.</p>
                    </div>
                </div>
            </body>
            </html>";
            
            mail($to, $subject, $email_body, $headers);
            
            $message = "New reset code sent to your email!";
            $message_type = "success";
        }
    }
}
?>

<style>
/* Forgot Password Styles */
.forgot-container {
    max-width: 500px;
    margin: 100px auto 50px;
    padding: 40px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.forgot-header {
    text-align: center;
    margin-bottom: 30px;
}

.forgot-header h1 {
    color: #2E4053;
    font-size: 28px;
    margin-bottom: 10px;
    font-weight: 600;
}

.forgot-header p {
    color: #666;
    font-size: 16px;
    line-height: 1.5;
}

.form-group {
    margin-bottom: 25px;
    position: relative;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #444;
    font-size: 15px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 15px;
    transition: all 0.3s;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #2E4053;
    box-shadow: 0 0 0 3px rgba(46, 64, 83, 0.1);
    outline: none;
}

/* Password field with toggle */
.password-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.password-wrapper .form-control {
    padding-right: 45px;
}

.toggle-password {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    font-size: 18px;
    padding: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.3s;
    z-index: 10;
}

.toggle-password:hover {
    color: #2E4053;
}

.toggle-password:focus {
    outline: none;
}

.password-hint {
    font-size: 13px;
    color: #666;
    margin-top: 5px;
    margin-bottom: 15px;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 4px;
    border-left: 3px solid #2E4053;
}

.btn-primary {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #2E4053 0%, #3a506b 100%);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #3a506b 0%, #2E4053 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(46, 64, 83, 0.2);
}

.btn-secondary {
    width: 100%;
    padding: 14px;
    background: #6c757d;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.btn-link {
    background: none;
    border: none;
    color: #2E4053;
    text-decoration: underline;
    cursor: pointer;
    font-size: 14px;
    margin-top: 10px;
}

.message-alert {
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
}

.message-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.message-info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.message-alert i {
    margin-right: 10px;
    font-size: 18px;
}

.login-link {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    color: #666;
}

.login-link a {
    color: #2E4053;
    font-weight: 600;
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}

.steps {
    display: flex;
    justify-content: space-between;
    margin: 30px 0;
    position: relative;
}

.steps:before {
    content: '';
    position: absolute;
    top: 20px;
    left: 10%;
    right: 10%;
    height: 2px;
    background: #e0e0e0;
    z-index: 1;
}

.step {
    text-align: center;
    position: relative;
    z-index: 2;
    flex: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    background: #e0e0e0;
    color: #666;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-weight: bold;
    font-size: 18px;
}

.step.active .step-number {
    background: #2E4053;
    color: white;
}

.step-label {
    font-size: 14px;
    color: #666;
}

.step.active .step-label {
    color: #2E4053;
    font-weight: 600;
}

.code-input {
    text-align: center;
    font-size: 20px;
    letter-spacing: 5px;
    font-weight: bold;
}

.resend-link {
    text-align: center;
    margin-top: 15px;
}

.resend-link a {
    color: #2E4053;
    text-decoration: none;
    font-size: 14px;
}

.resend-link a:hover {
    text-decoration: underline;
}

.timer {
    text-align: center;
    color: #666;
    font-size: 14px;
    margin-top: 10px;
}
</style>

<div class="forgot-container">
    <div class="forgot-header">
        <h1>Reset Your Password</h1>
        <p>Enter your email address and we'll send you a code to reset your password.</p>
    </div>

    <!-- Progress Steps -->
    <div class="steps">
        <div class="step <?php echo !$show_reset_form ? 'active' : ''; ?>">
            <div class="step-number">1</div>
            <div class="step-label">Enter Email</div>
        </div>
        <div class="step <?php echo $show_reset_form ? 'active' : ''; ?>">
            <div class="step-number">2</div>
            <div class="step-label">Reset Password</div>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="message-alert message-<?php echo $message_type; ?>">
            <i class="fa fa-<?php echo $message_type === 'success' ? 'check-circle' : ($message_type === 'info' ? 'info-circle' : 'exclamation-circle'); ?>"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Step 1: Request Reset Code -->
    <?php if (!$show_reset_form): ?>
    <form method="POST" action="" id="requestForm">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" 
                   placeholder="Enter your email address" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        
        <button type="submit" name="send_code" class="btn-primary">
            <i class="fa fa-paper-plane"></i> Send Reset Code
        </button>
    </form>
    <?php endif; ?>

    <!-- Step 2: Reset Password Form -->
    <?php if ($show_reset_form): ?>
    <form method="POST" action="" id="resetForm">
        <div class="form-group">
            <label>Email Address</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly>
            <?php if (isset($_SESSION['reset_method']) && $_SESSION['reset_method'] === 'manual'): ?>
                <small style="color: #e67e22; font-size: 13px; display: block; margin-top: 5px;">
                    <i class="fa fa-info-circle"></i> Using manual reset (Cognito temporarily unavailable)
                </small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="code">Reset Code</label>
            <input type="text" class="form-control code-input" id="code" name="code" 
                   placeholder="Enter 6-digit code" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric">
            <small style="color: #666; font-size: 13px;">Check your email for the 6-digit reset code</small>
        </div>
        
        <div class="form-group">
            <label for="new_password">New Password</label>
            <div class="password-wrapper">
                <input type="password" class="form-control" id="new_password" name="new_password" 
                       placeholder="Enter new password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('new_password', this)" tabindex="-1">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <div class="password-wrapper">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                       placeholder="Confirm new password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', this)" tabindex="-1">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
        </div>
        
        <div class="password-hint">
            <strong>Password Requirements:</strong><br>
            <i class="fa fa-check-circle" id="req-length"></i> Minimum 8 characters<br>
            <i class="fa fa-check-circle" id="req-uppercase"></i> At least one uppercase letter<br>
            <i class="fa fa-check-circle" id="req-lowercase"></i> At least one lowercase letter<br>
            <i class="fa fa-check-circle" id="req-number"></i> At least one number
        </div>
        
        <button type="submit" name="reset_password" class="btn-primary" id="resetBtn">
            <i class="fa fa-key"></i> Reset Password
        </button>
        
        <button type="button" onclick="window.location.href='forgot_password.php'" class="btn-secondary">
            <i class="fa fa-redo"></i> Start Over
        </button>
        
        <div class="resend-link">
            <a href="?resend=1"><i class="fa fa-refresh"></i> Resend reset code</a>
        </div>
    </form>
    <?php endif; ?>

    <div class="login-link">
        <p>Remember your password? <a href="login.php">Back to Login</a></p>
    </div>
</div>

<script>
// Toggle password visibility
function togglePassword(fieldId, button) {
    const field = document.getElementById(fieldId);
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password strength checker
if (document.getElementById('new_password')) {
    const passwordInput = document.getElementById('new_password');
    const confirmInput = document.getElementById('confirm_password');
    const lengthReq = document.getElementById('req-length');
    const upperReq = document.getElementById('req-uppercase');
    const lowerReq = document.getElementById('req-lowercase');
    const numberReq = document.getElementById('req-number');
    
    function checkPasswordStrength() {
        const password = passwordInput.value;
        
        // Check length
        if (password.length >= 8) {
            lengthReq.style.color = '#27ae60';
            lengthReq.className = 'fa fa-check-circle';
        } else {
            lengthReq.style.color = '#e74c3c';
            lengthReq.className = 'fa fa-times-circle';
        }
        
        // Check uppercase
        if (/[A-Z]/.test(password)) {
            upperReq.style.color = '#27ae60';
            upperReq.className = 'fa fa-check-circle';
        } else {
            upperReq.style.color = '#e74c3c';
            upperReq.className = 'fa fa-times-circle';
        }
        
        // Check lowercase
        if (/[a-z]/.test(password)) {
            lowerReq.style.color = '#27ae60';
            lowerReq.className = 'fa fa-check-circle';
        } else {
            lowerReq.style.color = '#e74c3c';
            lowerReq.className = 'fa fa-times-circle';
        }
        
        // Check number
        if (/[0-9]/.test(password)) {
            numberReq.style.color = '#27ae60';
            numberReq.className = 'fa fa-check-circle';
        } else {
            numberReq.style.color = '#e74c3c';
            numberReq.className = 'fa fa-times-circle';
        }
        
        // Check if passwords match
        if (confirmInput.value) {
            if (password === confirmInput.value) {
                confirmInput.style.borderColor = '#27ae60';
            } else {
                confirmInput.style.borderColor = '#e74c3c';
            }
        }
    }
    
    passwordInput.addEventListener('input', checkPasswordStrength);
    confirmInput.addEventListener('input', checkPasswordStrength);
}

// Auto-advance code input
if (document.getElementById('code')) {
    const codeInput = document.getElementById('code');
    codeInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, ''); // Only numbers
        if (this.value.length === 6) {
            document.getElementById('new_password').focus();
        }
    });
}

// Form validation
if (document.getElementById('requestForm')) {
    document.getElementById('requestForm').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        if (!email || !email.includes('@') || !email.includes('.')) {
            e.preventDefault();
            alert('Please enter a valid email address');
        }
    });
}

if (document.getElementById('resetForm')) {
    document.getElementById('resetForm').addEventListener('submit', function(e) {
        const code = document.getElementById('code').value;
        const newPass = document.getElementById('new_password').value;
        const confirmPass = document.getElementById('confirm_password').value;
        const errors = [];
        
        if (!code || code.length < 6) {
            errors.push('Please enter the 6-digit reset code');
        }
        if (!newPass) {
            errors.push('New password is required');
        }
        if (newPass !== confirmPass) {
            errors.push('Passwords do not match');
        }
        if (newPass.length < 8) {
            errors.push('Password must be at least 8 characters');
        }
        if (!/[A-Z]/.test(newPass)) {
            errors.push('Password must contain at least one uppercase letter');
        }
        if (!/[a-z]/.test(newPass)) {
            errors.push('Password must contain at least one lowercase letter');
        }
        if (!/[0-9]/.test(newPass)) {
            errors.push('Password must contain at least one number');
        }
        
        if (errors.length > 0) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
        }
    });
}

// Countdown timer for resend (optional)
if (document.querySelector('.resend-link')) {
    let seconds = 60;
    const resendLink = document.querySelector('.resend-link a');
    const originalText = resendLink.innerHTML;
    
    function updateTimer() {
        if (seconds > 0) {
            resendLink.innerHTML = `<i class="fa fa-hourglass-half"></i> Resend in ${seconds}s`;
            resendLink.style.pointerEvents = 'none';
            resendLink.style.opacity = '0.5';
            seconds--;
            setTimeout(updateTimer, 1000);
        } else {
            resendLink.innerHTML = originalText;
            resendLink.style.pointerEvents = 'auto';
            resendLink.style.opacity = '1';
        }
    }
    
    // Start timer when page loads with reset form
    if (document.querySelector('.reset-form')) {
        updateTimer();
    }
}
</script>