<?php
// verify.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("./header.php");

// Check if we have an email to verify
if (!isset($_SESSION['verify_email'])) {
    // No email in session, redirect to registration
    header("Location: registration.php");
    exit();
}

$email = $_SESSION['verify_email'];
$name = $_SESSION['verify_name'] ?? 'User';
$message = '';
$message_type = '';
$verification_success = false;

require_once("config.php");
require_once("cognito.php");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verify code
    if (isset($_POST['verify_code'])) {
        $verification_code = trim($_POST['verification_code']);
        
        if (empty($verification_code)) {
            $message = "Please enter the verification code";
            $message_type = "error";
        } else {
            // Verify with Cognito
            $result = CognitoAuth::verifyEmail($email, $verification_code);
            
            if ($result['success']) {
                // Update database to mark email as verified
                require_once("db-settings.php");
                
                $update_sql = "UPDATE users SET cognito_verified = 1 WHERE EMAIL = ?";
                $update_stmt = $mysqli->prepare($update_sql);
                $update_stmt->bind_param("s", $email);
                $update_stmt->execute();
                
                $message = "Email verified successfully! You can now login.";
                $message_type = "success";
                $verification_success = true;
                
                // Clear session verification data
                unset($_SESSION['verify_email']);
                unset($_SESSION['verify_name']);
                
                // Auto-redirect to login after 3 seconds
                echo '<script>
                    setTimeout(function() {
                        window.location.href = "login.php";
                    }, 3000);
                </script>';
            } else {
                $message = "Invalid verification code: " . $result['error'];
                $message_type = "error";
            }
        }
    }
    
    // Resend code
    if (isset($_POST['resend_code'])) {
        $result = CognitoAuth::resendVerificationCode($email);
        
        if ($result['success']) {
            $message = "A new verification code has been sent to your email.";
            $message_type = "success";
        } else {
            $message = "Failed to resend code: " . $result['error'];
            $message_type = "error";
        }
    }
}
?>

<style>
.verify-container {
    max-width: 500px;
    margin: 100px auto 50px;
    padding: 40px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.verify-header {
    text-align: center;
    margin-bottom: 30px;
}

.verify-header h1 {
    color: #2E4053;
    font-size: 28px;
    margin-bottom: 10px;
    font-weight: 600;
}

.verify-header p {
    color: #666;
    font-size: 16px;
    line-height: 1.5;
}

.email-highlight {
    background: #f0f7ff;
    padding: 15px;
    border-radius: 6px;
    margin: 20px 0;
    text-align: center;
    border-left: 4px solid #2E4053;
}

.email-highlight strong {
    color: #2E4053;
    font-size: 18px;
    word-break: break-all;
}

.form-group {
    margin-bottom: 25px;
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
    text-align: center;
    font-size: 24px;
    letter-spacing: 8px;
    font-weight: bold;
}

.form-control:focus {
    border-color: #2E4053;
    box-shadow: 0 0 0 3px rgba(46, 64, 83, 0.1);
    outline: none;
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
}

.message-alert {
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 25px;
    text-align: center;
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

.info-box {
    background: #e8f4fd;
    border: 1px solid #b8e1ff;
    color: #0369a1;
    padding: 15px;
    border-radius: 6px;
    margin: 20px 0;
    font-size: 14px;
    text-align: left;
}

.info-box i {
    margin-right: 8px;
    color: #0284c7;
}

.resend-link {
    text-align: center;
    margin-top: 20px;
}

.resend-link button {
    background: none;
    border: none;
    color: #2E4053;
    text-decoration: underline;
    cursor: pointer;
    font-size: 14px;
}

.resend-link button:hover {
    color: #1a2634;
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

.timer {
    text-align: center;
    color: #666;
    font-size: 14px;
    margin-top: 10px;
}
</style>

<div class="verify-container">
    <div class="verify-header">
        <h1>Verify Your Email</h1>
        <p>We've sent a verification code to your email address</p>
    </div>

    <div class="email-highlight">
        <strong><?php echo htmlspecialchars($email); ?></strong>
    </div>

    <?php if ($message): ?>
        <div class="message-alert message-<?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (!$verification_success): ?>
        <div class="info-box">
            <i class="fa fa-info-circle"></i> 
            Please check your email (including spam folder) for the verification code. 
            Enter the 6-digit code below to verify your account.
        </div>

        <form method="POST" action="" id="verifyForm">
            <div class="form-group">
                <label for="verification_code">Verification Code</label>
                <input type="text" class="form-control" id="verification_code" 
                       name="verification_code" placeholder="000000" 
                       maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required>
                <div style="font-size: 13px; color: #666; margin-top: 8px; text-align: center;">
                    Enter the 6-digit code from your email
                </div>
            </div>

            <button type="submit" name="verify_code" class="btn-primary">
                <i class="fa fa-check-circle"></i> Verify Email
            </button>

            <div class="resend-link">
                <button type="submit" name="resend_code" class="resend-button">
                    <i class="fa fa-refresh"></i> Resend verification code
                </button>
            </div>

            <div class="timer" id="timer"></div>
        </form>
    <?php else: ?>
        <div style="text-align: center;">
            <i class="fa fa-check-circle" style="font-size: 60px; color: #27ae60; margin-bottom: 20px;"></i>
            <h3 style="color: #27ae60; margin-bottom: 20px;">Verification Successful!</h3>
            <p>You will be redirected to the login page in a few seconds...</p>
        </div>
    <?php endif; ?>

    <div class="login-link">
        <p><a href="login.php"><i class="fa fa-arrow-left"></i> Back to Login</a></p>
    </div>
</div>

<script>
// Auto-advance code input
const codeInput = document.getElementById('verification_code');
if (codeInput) {
    codeInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, ''); // Only numbers
        if (this.value.length === 6) {
            // Optionally auto-submit
            // document.getElementById('verifyForm').submit();
        }
    });
}

// Countdown timer for resend (optional)
function startResendTimer(seconds = 60) {
    const timer = document.getElementById('timer');
    const resendBtn = document.querySelector('.resend-button');
    
    if (resendBtn) {
        resendBtn.disabled = true;
        resendBtn.style.opacity = '0.5';
        
        const interval = setInterval(function() {
            if (seconds <= 0) {
                clearInterval(interval);
                timer.innerHTML = '';
                resendBtn.disabled = false;
                resendBtn.style.opacity = '1';
            } else {
                timer.innerHTML = `You can request a new code in ${seconds} seconds`;
                seconds--;
            }
        }, 1000);
    }
}

// Start timer if this is a fresh verification page (not after resend)
<?php if (!isset($_POST['resend_code']) && !$verification_success): ?>
    startResendTimer(60);
<?php endif; ?>

// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>