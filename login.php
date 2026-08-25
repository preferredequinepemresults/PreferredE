<?php
session_start();
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("./header.php");

require_once("config.php");
require_once("db-settings.php");
require_once("auth.php");

// If user is already logged in, redirect
if (isset($_SESSION['UserName'])) {
    $redirect_url = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : "index.php";
    header("Location: $redirect_url");
    die();
}

// Handle form submission
$errors = [];
$form_data = $_POST ?? [];
$success_message = $_SESSION['registration_success'] ?? '';
unset($_SESSION['registration_success']);

if (!empty($_POST)) {
    $username = trim($_POST["username"]);
    $password = $_POST["password"] ?? '';

    // Basic validation
    if (empty($username)) {
        $errors[] = "Enter Username/Email";
    }
    if (empty($password)) {
        $errors[] = "Enter Password";
    }

    if (count($errors) == 0) {
        if (auth_login_is_rate_limited($mysqli, $username)) {
            $errors[] = "Too many failed login attempts. Please wait 15 minutes and try again.";
            auth_record_login_attempt($mysqli, $username, false, 'rate_limited');
        } else {
            $stmt = $mysqli->prepare("SELECT * FROM users WHERE EMAIL = ? OR USERNAME = ? LIMIT 1");
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();
            $stmt->close();

            $needs_rehash = false;
            $password_valid = $user_data
                ? auth_verify_password($password, $user_data['PASSWORD'] ?? '', $needs_rehash)
                : false;

            if (!$user_data || !$password_valid) {
                $errors[] = "Invalid username or password";
                auth_record_login_attempt($mysqli, $username, false, 'invalid_credentials');
            } elseif (!isset($user_data['ACTIVE']) || $user_data['ACTIVE'] != 'Y') {
                auth_record_login_attempt($mysqli, $username, false, 'inactive');
                header("Location: inactive_account.php");
                exit();
            } else {
                if ($needs_rehash && isset($user_data['USER_ID'])) {
                    auth_upgrade_password_hash($mysqli, (int)$user_data['USER_ID'], $password);
                }

                session_regenerate_id(true);

                $_SESSION["UserActive"] = 'Y';
                $_SESSION["UserName"] = $user_data['EMAIL'] ?: $username;
                $_SESSION["UserEmail"] = $user_data['EMAIL'] ?: $username;
                $_SESSION["UserRole"] = $user_data['USERROLE'] ?? 'user';
                $_SESSION["UserFirstName"] = $user_data['FNAME'] ?? '';
                $_SESSION["UserLastName"] = $user_data['LNAME'] ?? '';
                $_SESSION['LAST_ACTIVITY'] = time();

                setcookie("LoggedInUser", $_SESSION["UserEmail"], time() + 3600, "/", "", !empty($_SERVER['HTTPS']), true);
                auth_record_login_attempt($mysqli, $username, true, 'success');

                $redirect_url = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : "index.php";
                header("Location: $redirect_url");
                exit();
            }
        }
    }
}
ob_end_flush();
?>

<style>
    /* Custom Styles for Login Page - Matching Registration Page */
    .login-container {
        max-width: 500px;
        margin: 80px auto 40px;
        padding: 30px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid #e0e0e0;
    }

    .login-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #2E4053;
    }

    .login-header h1 {
        color: #2E4053;
        font-size: 28px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .login-header p {
        color: #666;
        font-size: 16px;
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
    }

    .toggle-password:hover {
        color: #2E4053;
    }

    .toggle-password:focus {
        outline: none;
    }

    .error-alert {
        background-color: #fff5f5;
        border: 1px solid #fed7d7;
        color: #c53030;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 25px;
        text-align: center;
        font-weight: 500;
    }

    .success-alert {
        background-color: #f0fff4;
        border: 1px solid #c6f6d5;
        color: #276749;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 25px;
        text-align: center;
        font-weight: 500;
    }

    .submit-btn {
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

    .submit-btn:hover {
        background: linear-gradient(135deg, #3a506b 0%, #2E4053 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(46, 64, 83, 0.2);
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .login-links {
        text-align: center;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        color: #666;
    }

    .login-links a {
        color: #2E4053;
        font-weight: 600;
        text-decoration: none;
        display: block;
        margin: 8px 0;
    }

    .login-links a:hover {
        text-decoration: underline;
    }

    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .remember-me {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .remember-me input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #2E4053;
    }

    .forgot-password {
        color: #2E4053;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .login-container {
            margin: 60px 20px 30px;
            padding: 20px;
        }

        .login-header h1 {
            font-size: 24px;
        }

        .form-options {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
    }
</style>

<div class="login-container">
    <div class="login-header">
        <h1>Welcome Back</h1>
        <p>Sign in to your Preferred Equine account</p>
    </div>

    <!-- Show errors if any -->
    <?php if (!empty($errors)): ?>
        <div class="error-alert">
            <?php echo htmlspecialchars($errors[0]); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div class="success-alert">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <form name="login" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?><?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="post">
        <div class="form-group">
            <label for="username">Email or Username *</label>
            <input type="text" class="form-control"
                id="username"
                name="username"
                autocomplete="username"
                placeholder="Enter your email or username"
                value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>"
                required
                autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password *</label>
            <div class="password-wrapper">
                <input type="password" class="form-control"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    placeholder="Enter your password"
                    required>
                <button type="button" class="toggle-password" onclick="togglePassword('password', this)" tabindex="-1">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="form-options">
            <label class="remember-me">
                <input type="checkbox" name="remember" id="remember">
                Remember me
            </label>
            <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
        </div>

        <?php if (isset($_GET['redirect'])): ?>
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect']); ?>">
        <?php endif; ?>

        <button type="submit" class="submit-btn">Sign In</button>

        <div class="login-links">
            <p>Don't have an account? <a href="registration.php">Register here</a></p>
            <p><a href="index.php">← Back to Home</a></p>
        </div>
    </form>
</div>

<script>
    // Focus on username field when page loads
    document.getElementById("username").focus();

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

    // Submit form when Enter is pressed in password field
    document.getElementById("password").addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            document.forms["login"].submit();
        }
    });

    // Add a subtle animation to the login button on hover
    const submitBtn = document.querySelector('.submit-btn');
    if (submitBtn) {
        submitBtn.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });

        submitBtn.addEventListener('mouseleave', function() {
            this.style.transition = 'all 0.3s ease';
        });
    }

    // Optional: Add keyboard shortcut (Ctrl+E or Cmd+E) to toggle password visibility
    document.addEventListener('keydown', function(e) {
        // Check if Ctrl+E (or Cmd+E on Mac) is pressed
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            const passwordField = document.getElementById('password');
            const toggleButton = document.querySelector('.toggle-password');
            if (passwordField && toggleButton) {
                togglePassword('password', toggleButton);
            }
        }
    });
</script>
