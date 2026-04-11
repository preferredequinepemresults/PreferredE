<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If not logged in AND not on a public page, redirect
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'registration.php', 'index.php'];

if (!isset($_SESSION['UserName']) && !in_array($current_page, $public_pages)) {
    header("Location: login.php");
    exit();
}

include("./header.php");
include("./session_page.php");
require_once("config.php");
require_once("db-settings.php");
require_once("cognito.php");

// Get user details for display
$username = $_SESSION['UserName'];
$email = $_SESSION['UserEmail'] ?? $username;

// Handle form submissions FIRST (before any output)
$message = '';
$message_type = ''; // success, error, info

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // We need session variables for form processing
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['UserName'])) {
        $username = $_SESSION['UserName'];
        $email = $_SESSION['UserEmail'] ?? $username;

        // Get fresh user data for validation
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE USERNAME = ? OR EMAIL = ? LIMIT 1");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (isset($_POST['update_profile'])) {

            // Update profile info
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $contact = trim($_POST['contact']);

            $update_stmt = $mysqli->prepare("UPDATE users SET FNAME = ?, LNAME = ?, CONTACT = ? WHERE USERNAME = ?");
            $update_stmt->bind_param("ssss", $first_name, $last_name, $contact, $email);

            if ($update_stmt->execute()) {
                // Also update in Cognito if user is verified
                if ($user['cognito_verified'] == 1) {
                    try {
                        require_once 'vendor/autoload.php';

                        $client = new Aws\CognitoIdentityProvider\CognitoIdentityProviderClient([
                            'region' => COGNITO_REGION,
                            'version' => 'latest'
                        ]);

                        // Update user attributes in Cognito
                        $client->adminUpdateUserAttributes([
                            'UserPoolId' => COGNITO_USER_POOL_ID,
                            'Username' => $email,
                            'UserAttributes' => [
                                [
                                    'Name' => 'given_name',
                                    'Value' => $first_name
                                ],
                                [
                                    'Name' => 'family_name',
                                    'Value' => $last_name
                                ]
                            ]
                        ]);

                        error_log("Cognito profile updated for: $email");
                    } catch (Exception $e) {
                        error_log("Cognito attribute update failed: " . $e->getMessage());
                        // Don't fail the profile update if Cognito fails
                        $message = "Profile updated in database but Cognito sync failed. Contact support if needed.";
                        $message_type = "info";
                    }
                }

                // Update session
                $_SESSION['UserFirstName'] = $first_name;
                $_SESSION['UserLastName'] = $last_name;
                $_SESSION['UserName'] = !empty($first_name) ? $first_name : $email;

                if (empty($message)) {
                    $message = "Profile updated successfully!";
                    $message_type = "success";
                }

                // Refresh user data for display
                $user['FNAME'] = $first_name;
                $user['LNAME'] = $last_name;
                $user['CONTACT'] = $contact;
            } else {
                $message = "Error updating profile: " . $update_stmt->error;
                $message_type = "error";
            }
            $update_stmt->close();
        }

        if (isset($_POST['change_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Validate
            if ($new_password !== $confirm_password) {
                $message = "New passwords do not match!";
                $message_type = "error";
            } elseif (strlen($new_password) < 8) {
                $message = "Password must be at least 8 characters";
                $message_type = "error";
            } elseif (!preg_match('/[A-Z]/', $new_password)) {
                $message = "Password must contain at least one uppercase letter";
                $message_type = "error";
            } elseif (!preg_match('/[a-z]/', $new_password)) {
                $message = "Password must contain at least one lowercase letter";
                $message_type = "error";
            } elseif (!preg_match('/[0-9]/', $new_password)) {
                $message = "Password must contain at least one number";
                $message_type = "error";
            } elseif (!preg_match('/[\W_]/', $new_password)) {
                $message = "Password must contain at least one special character";
                $message_type = "error";
            } elseif ($user['PASSWORD'] === $current_password) {

                // Try to update in Cognito first (if user is verified)
                $cognito_success = false;
                $cognito_error = '';

                if ($user['cognito_verified'] == 1) {
                    try {
                        require_once 'vendor/autoload.php';

                        $client = new Aws\CognitoIdentityProvider\CognitoIdentityProviderClient([
                            'region' => COGNITO_REGION,
                            'version' => 'latest'
                        ]);

                        // Method 1: Using admin privileges (IAM role) - doesn't require current password
                        // This is simpler and more reliable
                        try {
                            $client->adminSetUserPassword([
                                'UserPoolId' => COGNITO_USER_POOL_ID,
                                'Username' => $email,
                                'Password' => $new_password,
                                'Permanent' => true
                            ]);

                            $cognito_success = true;
                            error_log("Cognito password updated via admin for: $email");
                        } catch (Exception $adminError) {
                            // If admin method fails, try the user authentication method
                            error_log("Admin password set failed, trying user auth: " . $adminError->getMessage());

                            // First authenticate to get the access token
                            $auth_result = $client->initiateAuth([
                                'AuthFlow' => 'USER_PASSWORD_AUTH',
                                'ClientId' => COGNITO_APP_CLIENT_ID,
                                'AuthParameters' => [
                                    'USERNAME' => $email,
                                    'PASSWORD' => $current_password
                                ]
                            ]);

                            if (isset($auth_result['AuthenticationResult'])) {
                                $access_token = $auth_result['AuthenticationResult']['AccessToken'];

                                // Change password in Cognito
                                $client->changePassword([
                                    'AccessToken' => $access_token,
                                    'PreviousPassword' => $current_password,
                                    'ProposedPassword' => $new_password
                                ]);

                                $cognito_success = true;
                                error_log("Cognito password updated via user auth for: $email");
                            }
                        }
                    } catch (Exception $e) {
                        $cognito_error = $e->getMessage();
                        error_log("Cognito password change failed for {$email}: " . $cognito_error);
                    }
                } else {
                    // User not verified in Cognito, just update database
                    $cognito_success = true; // Treat as success since no Cognito sync needed
                }

                // Update password in database (always do this)
                $update_stmt = $mysqli->prepare("UPDATE users SET PASSWORD = ? WHERE USERNAME = ?");
                $update_stmt->bind_param("ss", $new_password, $email);

                if ($update_stmt->execute()) {
                    if ($cognito_success) {
                        $message = "Password changed successfully in both database and Cognito!";
                        $message_type = "success";
                    } elseif ($user['cognito_verified'] == 1) {
                        $message = "Password updated in database but Cognito sync failed. Error: " . $cognito_error;
                        $message_type = "warning";
                    } else {
                        $message = "Password changed successfully in database. Verify your email to enable Cognito sync.";
                        $message_type = "success";
                    }
                } else {
                    $message = "Error changing password in database: " . $update_stmt->error;
                    $message_type = "error";
                }
                $update_stmt->close();
            } else {
                $message = "Current password is incorrect!";
                $message_type = "error";
            }
        }

        // Handle account deletion
        if (isset($_POST['delete_account'])) {
            $confirm_delete = $_POST['confirm_delete'] ?? '';
            $delete_password = $_POST['delete_password'] ?? '';

            if ($confirm_delete !== 'DELETE') {
                $message = "Please type 'DELETE' to confirm account deletion";
                $message_type = "error";
            } elseif ($delete_password !== $user['PASSWORD']) {
                $message = "Password is incorrect!";
                $message_type = "error";
            } else {
                // Start transaction
                $mysqli->begin_transaction();

                try {
                    // 1. Delete from Cognito if verified
                    if ($user['cognito_verified'] == 1) {
                        try {
                            require_once 'vendor/autoload.php';

                            $client = new Aws\CognitoIdentityProvider\CognitoIdentityProviderClient([
                                'region' => COGNITO_REGION,
                                'version' => 'latest'
                            ]);

                            // Delete user from Cognito
                            $client->adminDeleteUser([
                                'UserPoolId' => COGNITO_USER_POOL_ID,
                                'Username' => $email
                            ]);

                            error_log("Cognito user deleted: $email");
                        } catch (Exception $e) {
                            error_log("Cognito delete failed: " . $e->getMessage());
                            // Continue with database deletion even if Cognito fails
                        }
                    }

                    // 2. Delete from database
                    $delete_stmt = $mysqli->prepare("DELETE FROM users WHERE EMAIL = ?");
                    $delete_stmt->bind_param("s", $email);

                    if (!$delete_stmt->execute()) {
                        throw new Exception("Database delete failed: " . $delete_stmt->error);
                    }

                    // Commit transaction
                    $mysqli->commit();

                    // Clear session and logout
                    $_SESSION = array();
                    session_destroy();
                    setcookie("LoggedInUser", "", time() - 3600, "/");

                    // Redirect with message
                    header("Location: registration.php?deleted=1");
                    exit();
                } catch (Exception $e) {
                    $mysqli->rollback();
                    $message = "Error deleting account: " . $e->getMessage();
                    $message_type = "error";
                    error_log("Account deletion failed: " . $e->getMessage());
                }
            }
        }
    }
}

// Refresh user data after any updates
$stmt = $mysqli->prepare("SELECT * FROM users WHERE USERNAME = ? OR EMAIL = ? LIMIT 1");
$stmt->bind_param("ss", $email, $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Define role names for display
$role_names = [
    'A' => 'Administrator',
    'T' => 'Thoroughbred User',
    'S' => 'Standardbred User',
    'ST' => 'Full Access User',
    'user' => 'Basic User'
];
?>

<style>
    /* Account Page Styles */

    body {
        padding-top: 100px !important;
    }

    .account-container {
        max-width: 1200px;
        margin: 80px auto 40px;
        padding: 0 20px;
    }

    .account-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid #2E4053;
    }

    .account-header h1 {
        color: #2E4053;
        font-size: 32px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .account-header p {
        color: #666;
        font-size: 16px;
    }

    .account-grid {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 30px;
    }

    @media (max-width: 992px) {
        .account-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Sidebar */
    .account-sidebar {
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        padding: 0;
        overflow: hidden;
    }

    .user-profile-card {
        padding: 30px;
        text-align: center;
        background: linear-gradient(135deg, #2E4053 0%, #3a506b 100%);
        color: white;
    }

    .user-avatar {
        width: 100px;
        height: 100px;
        background: white;
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        color: #2E4053;
        font-weight: bold;
    }

    .user-name {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .user-email {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 15px;
    }

    .user-role {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
    }

    .account-menu {
        padding: 20px 0;
    }

    .menu-item {
        display: block;
        padding: 15px 30px;
        color: #555;
        text-decoration: none;
        border-left: 4px solid transparent;
        transition: all 0.3s;
        font-weight: 500;
    }

    .menu-item:hover,
    .menu-item.active {
        background: #f8f9fa;
        color: #2E4053;
        border-left-color: #2E4053;
    }

    .menu-item i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    /* Content Area */
    .account-content {
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        padding: 30px;
    }

    .section-title {
        color: #2E4053;
        font-size: 22px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
        font-weight: 600;
    }

    /* Message Alerts */
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

    .message-warning {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .message-alert i {
        margin-right: 10px;
        font-size: 18px;
    }

    /* Forms */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
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

    .readonly-field {
        background-color: #f8f9fa;
        color: #666;
        cursor: not-allowed;
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

    /* Password strength indicator */
    .password-strength {
        margin-top: 10px;
        height: 6px;
        border-radius: 3px;
        background: #e0e0e0;
        overflow: hidden;
    }

    .strength-bar {
        height: 100%;
        width: 0%;
        transition: all 0.3s ease;
        border-radius: 3px;
    }

    .strength-text {
        font-size: 12px;
        margin-top: 5px;
        text-align: right;
        font-weight: 500;
    }

    .strength-very-weak {
        background-color: #e74c3c;
    }

    .strength-weak {
        background-color: #e67e22;
    }

    .strength-fair {
        background-color: #f1c40f;
    }

    .strength-good {
        background-color: #3498db;
    }

    .strength-strong {
        background-color: #27ae60;
    }

    .strength-requirements {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 10px;
        font-size: 12px;
    }

    .requirement-item {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #7f8c8d;
    }

    .requirement-item i {
        font-size: 12px;
    }

    .requirement-met {
        color: #27ae60;
    }

    .requirement-unmet {
        color: #e74c3c;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2E4053 0%, #3a506b 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #3a506b 0%, #2E4053 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(46, 64, 83, 0.2);
    }

    .btn-primary i {
        margin-right: 8px;
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
    }

    .btn-danger i {
        margin-right: 8px;
    }

    /* Stats Cards - Removed member since */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid #eee;
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        font-size: 30px;
        color: #2E4053;
        margin-bottom: 15px;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #2E4053;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 14px;
        color: #666;
    }

    .verification-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 10px;
    }

    .badge-verified {
        background-color: #d4edda;
        color: #155724;
    }

    .badge-unverified {
        background-color: #fff3cd;
        color: #856404;
    }

    /* Delete Account Section */
    .delete-account-section {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid #dc3545;
    }

    .delete-warning {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        color: #856404;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .delete-warning i {
        color: #dc3545;
        margin-right: 10px;
    }

    .confirm-input {
        border: 2px solid #dc3545 !important;
    }

    .confirm-input:focus {
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.25) !important;
    }
</style>

<div class="account-container">
    <div class="account-header">
        <h1>My Account</h1>
        <p>Manage your profile, settings, and preferences</p>
    </div>

    <?php if ($message): ?>
        <div class="message-alert message-<?php echo $message_type; ?>">
            <i class="fa fa-<?php
                            echo $message_type === 'success' ? 'check-circle' : ($message_type === 'error' ? 'exclamation-circle' : ($message_type === 'warning' ? 'exclamation-triangle' : 'info-circle'));
                            ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="account-grid">
        <!-- Sidebar -->
        <div class="account-sidebar">
            <div class="user-profile-card">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['FNAME'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($user['FNAME'] . ' ' . $user['LNAME']); ?></div>
                <div class="user-email"><?php echo htmlspecialchars($user['EMAIL']); ?></div>
                <div class="user-role">
                    <?php echo $role_names[$user['USERROLE'] ?? 'user']; ?>
                </div>
                <?php if ($user['cognito_verified'] == 1): ?>
                    <span class="verification-badge badge-verified" style="margin-top: 15px;">
                        <i class="fa fa-check-circle"></i> Verified in Cognito
                    </span>
                <?php else: ?>
                    <span class="verification-badge badge-unverified" style="margin-top: 15px;">
                        <i class="fa fa-exclamation-triangle"></i> Email Not Verified
                    </span>
                <?php endif; ?>
            </div>

            <div class="account-menu">
                <a href="#profile" class="menu-item active">
                    <i class="fa fa-user"></i> Profile Information
                </a>
                <a href="#password" class="menu-item">
                    <i class="fa fa-lock"></i> Change Password
                </a>
                <a class="menu-item" href="logout.php">
                    <i class="fa fa-sign-out"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="account-content">
            <!-- Profile Section -->
            <div id="profile">
                <h2 class="section-title">
                    <i class="fa fa-user" style="margin-right: 10px;"></i>Profile Information
                </h2>

                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                value="<?php echo htmlspecialchars($user['FNAME'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                value="<?php echo htmlspecialchars($user['LNAME'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contact">Contact Number</label>
                        <input type="text" class="form-control" id="contact" name="contact"
                            value="<?php echo htmlspecialchars($user['CONTACT'] ?? ''); ?>"
                            placeholder="Enter your contact number">
                    </div>

                    <button type="submit" name="update_profile" class="btn-primary">
                        <i class="fa fa-save"></i> Update Profile
                    </button>
                </form>

                <!-- Stats Cards - Removed member since -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa fa-user-check"></i>
                        </div>
                        <div class="stat-number"><?php echo ($user['ACTIVE'] ?? 'N') === 'Y' ? 'Active' : 'Inactive'; ?></div>
                        <div class="stat-label">Account Status</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa fa-shield-alt"></i>
                        </div>
                        <div class="stat-number"><?php echo $role_names[$user['USERROLE'] ?? 'user']; ?></div>
                        <div class="stat-label">Access Level</div>
                    </div>
                </div>

                <!-- Delete Account Section -->
                <div class="delete-account-section">
                    <h3 style="color: #dc3545; margin-bottom: 20px;">
                        <i class="fa fa-exclamation-triangle"></i> Delete Account
                    </h3>

                    <div class="delete-warning">
                        <i class="fa fa-exclamation-circle"></i>
                        <strong>Warning:</strong> This action is permanent and cannot be undone.
                        All your data will be permanently removed from our system.
                    </div>

                    <form method="POST" action="" onsubmit="return confirmDelete()">
                        <div class="form-group">
                            <label for="delete_password">Enter Your Password to Confirm:</label>
                            <div class="password-wrapper">
                                <input type="password" class="form-control" id="delete_password" name="delete_password" required>
                                <button type="button" class="toggle-password" onclick="togglePassword('delete_password', this)" tabindex="-1">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_delete">Type <strong style="color: #dc3545;">DELETE</strong> to confirm:</label>
                            <input type="text" class="form-control confirm-input" id="confirm_delete" name="confirm_delete"
                                placeholder="DELETE" required pattern="DELETE" style="text-transform: uppercase;">
                        </div>

                        <button type="submit" name="delete_account" class="btn-danger">
                            <i class="fa fa-trash"></i> Permanently Delete My Account
                        </button>
                    </form>
                </div>
            </div>

            <hr style="margin: 40px 0; border-color: #eee;">

            <!-- Change Password Section -->
            <div id="password" style="display: none;">
                <h2 class="section-title">
                    <i class="fa fa-lock" style="margin-right: 10px;"></i>Change Password
                </h2>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('current_password', this)" tabindex="-1">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <div class="password-wrapper">
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <button type="button" class="toggle-password" onclick="togglePassword('new_password', this)" tabindex="-1">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            <!-- Password strength indicator -->
                            <div class="password-strength">
                                <div class="strength-bar" id="strengthBar"></div>
                            </div>
                            <div class="strength-text" id="strengthText"></div>

                            <!-- Password requirements checklist -->
                            <div class="strength-requirements">
                                <div class="requirement-item" id="req-length">
                                    <i class="fa fa-times-circle"></i> <span>8+ characters</span>
                                </div>
                                <div class="requirement-item" id="req-uppercase">
                                    <i class="fa fa-times-circle"></i> <span>Uppercase</span>
                                </div>
                                <div class="requirement-item" id="req-lowercase">
                                    <i class="fa fa-times-circle"></i> <span>Lowercase</span>
                                </div>
                                <div class="requirement-item" id="req-number">
                                    <i class="fa fa-times-circle"></i> <span>Number</span>
                                </div>
                                <div class="requirement-item" id="req-special">
                                    <i class="fa fa-times-circle"></i> <span>Special character</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <div class="password-wrapper">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', this)" tabindex="-1">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="password-hint">
                        <strong>Password Requirements:</strong> Minimum 8 characters with uppercase, lowercase, number, and special character
                    </div>

                    <?php if ($user['cognito_verified'] == 1): ?>
                        <div style="margin-bottom: 15px; font-size: 13px; color: #28a745;">
                            <i class="fa fa-check-circle"></i> Your password will be synchronized with Cognito
                        </div>
                    <?php else: ?>
                        <div style="margin-bottom: 15px; font-size: 13px; color: #856404;">
                            <i class="fa fa-info-circle"></i> Verify your email to enable Cognito password sync
                        </div>
                    <?php endif; ?>

                    <button type="submit" name="change_password" class="btn-primary">
                        <i class="fa fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility function
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
    function checkPasswordStrength(password) {
        let strength = 0;
        let requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[\W_]/.test(password)
        };

        // Update requirement icons
        updateRequirement('req-length', requirements.length);
        updateRequirement('req-uppercase', requirements.uppercase);
        updateRequirement('req-lowercase', requirements.lowercase);
        updateRequirement('req-number', requirements.number);
        updateRequirement('req-special', requirements.special);

        // Calculate strength
        if (requirements.length) strength += 1;
        if (requirements.uppercase) strength += 1;
        if (requirements.lowercase) strength += 1;
        if (requirements.number) strength += 1;
        if (requirements.special) strength += 1;

        return strength;
    }

    function updateRequirement(elementId, isMet) {
        const element = document.getElementById(elementId);
        const icon = element.querySelector('i');

        if (isMet) {
            icon.className = 'fa fa-check-circle';
            element.classList.add('requirement-met');
            element.classList.remove('requirement-unmet');
        } else {
            icon.className = 'fa fa-times-circle';
            element.classList.add('requirement-unmet');
            element.classList.remove('requirement-met');
        }
    }

    function updateStrengthBar(strength) {
        const bar = document.getElementById('strengthBar');
        const text = document.getElementById('strengthText');

        let percentage = (strength / 5) * 100;
        bar.style.width = percentage + '%';

        if (strength <= 1) {
            bar.className = 'strength-bar strength-very-weak';
            text.innerHTML = 'Very Weak';
            text.style.color = '#e74c3c';
        } else if (strength === 2) {
            bar.className = 'strength-bar strength-weak';
            text.innerHTML = 'Weak';
            text.style.color = '#e67e22';
        } else if (strength === 3) {
            bar.className = 'strength-bar strength-fair';
            text.innerHTML = 'Fair';
            text.style.color = '#f1c40f';
        } else if (strength === 4) {
            bar.className = 'strength-bar strength-good';
            text.innerHTML = 'Good';
            text.style.color = '#3498db';
        } else {
            bar.className = 'strength-bar strength-strong';
            text.innerHTML = 'Strong';
            text.style.color = '#27ae60';
        }
    }

    // Simple tab switching
    document.addEventListener('DOMContentLoaded', function() {
        const menuItems = document.querySelectorAll('.menu-item');
        const sections = {
            'profile': document.getElementById('profile'),
            'password': document.getElementById('password')
        };

        // Hide password section initially if we're on profile
        if (window.location.hash === '#password') {
            sections.profile.style.display = 'none';
            sections.password.style.display = 'block';

            menuItems.forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('href') === '#password') {
                    item.classList.add('active');
                }
            });
        } else {
            sections.password.style.display = 'none';
            sections.profile.style.display = 'block';
        }

        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                const href = this.getAttribute('href');

                // Check if this is the logout link (actual page, not anchor)
                if (href === 'logout.php') {
                    // Let the logout happen normally - don't prevent default
                    return true;
                }

                // For anchor links (#profile, #password), handle the tab switching
                e.preventDefault();

                const target = href.substring(1);

                // Hide all sections
                Object.values(sections).forEach(section => {
                    if (section) section.style.display = 'none';
                });

                // Show target section
                if (sections[target]) {
                    sections[target].style.display = 'block';
                }

                // Update active class
                menuItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                // Update URL hash
                window.location.hash = target;
            });
        });
    });

    // Password strength checker event listener
    if (document.getElementById('new_password')) {
        const passwordInput = document.getElementById('new_password');
        const confirmInput = document.getElementById('confirm_password');

        passwordInput.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            updateStrengthBar(strength);

            // Check if passwords match
            if (confirmInput.value) {
                if (this.value === confirmInput.value) {
                    confirmInput.style.borderColor = '#27ae60';
                } else {
                    confirmInput.style.borderColor = '#e74c3c';
                }
            }
        });

        confirmInput.addEventListener('input', function() {
            if (passwordInput.value === this.value) {
                this.style.borderColor = '#27ae60';
            } else {
                this.style.borderColor = '#e74c3c';
            }
        });
    }

    // Form validation
    if (document.querySelector('form')) {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (this.querySelector('[name="change_password"]')) {
                    const newPass = document.getElementById('new_password').value;
                    const confirmPass = document.getElementById('confirm_password').value;
                    const errors = [];

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
                    if (!/[\W_]/.test(newPass)) {
                        errors.push('Password must contain at least one special character');
                    }

                    if (errors.length > 0) {
                        e.preventDefault();
                        alert('Please fix the following errors:\n\n' + errors.join('\n'));
                    }
                }
            });
        });
    }

    // Confirm delete function
    function confirmDelete() {
        return confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.');
    }

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>