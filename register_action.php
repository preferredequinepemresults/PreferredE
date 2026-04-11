<?php
// register_action.php
session_start();
require_once("config.php");
require_once("cognito.php");

// Get form data
$username = trim($_POST['user'] ?? '');
$first_name = trim($_POST['fname'] ?? '');
$last_name = trim($_POST['lname'] ?? '');
$password = $_POST['password'] ?? '';
$user_role = $_POST['userrole'] ?? 'N';

// Validation
$errors = [];
if (empty($username)) $errors[] = "Email is required";
if (empty($first_name)) $errors[] = "First name is required";
if (empty($last_name)) $errors[] = "Last name is required";
if (empty($password)) $errors[] = "Password is required";
if ($user_role === 'N') $errors[] = "Please select user role";

// Password validation (Cognito requirements)
if (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters";
}
if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = "Password must contain at least one uppercase letter";
}
if (!preg_match('/[a-z]/', $password)) {
    $errors[] = "Password must contain at least one lowercase letter";
}
if (!preg_match('/[0-9]/', $password)) {
    $errors[] = "Password must contain at least one number";
}
if (!preg_match('/[\W_]/', $password)) { // Special character
    $errors[] = "Password must contain at least one special character";
}

if (empty($errors)) {
    error_log("Attempting to register user: $username");
    
    // Register in Cognito (NO AUTO-CONFIRM)
    $cognito_result = CognitoAuth::register($username, $password, $first_name, $last_name, $user_role);
    
    error_log("Cognito registration result: " . print_r($cognito_result, true));
    
    if ($cognito_result['success']) {
        // Store in YOUR database
        require_once("db-settings.php");
        
        // Insert into users table with 'N' for cognito_verified = 0 (not verified)
        $sql = "INSERT INTO users (USERNAME, FNAME, LNAME, EMAIL, PASSWORD, ACTIVE, USERROLE, cognito_verified) 
                VALUES (?, ?, ?, ?, ?, 'Y', ?, 0)";
        
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $db_password = $password; // Your existing password storage
            
            $stmt->bind_param("ssssss", $username, $first_name, $last_name, $username, $db_password, $user_role);
            
            if ($stmt->execute()) {
                // Store email in session for verification page
                $_SESSION['verify_email'] = $username;
                $_SESSION['verify_name'] = $first_name;
                
                // Redirect to verification page
                header("Location: verify.php");
                exit();
            } else {
                $errors[] = "Database error: " . $stmt->error;
                error_log("Database error: " . $stmt->error);
            }
        } else {
            $errors[] = "Database preparation failed";
            error_log("Database preparation failed: " . $mysqli->error);
        }
    } else {
        $errors[] = $cognito_result['error'];
        error_log("Cognito error: " . $cognito_result['error']);
    }
}
?>

<?php
// If errors, show them in a clean modern page
session_start();
include("./header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Failed | Preferred Equine</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .error-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            margin-top: 2rem;
        }

        .error-card {
            background: white;
            border-radius: 1.5rem;
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .icon-wrapper {
            width: 90px;
            height: 90px;
            background: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .icon-wrapper i {
            font-size: 2.5rem;
            color: #dc2626;
        }

        h1 {
            font-size: 1.8rem;
            color: #1e293b;
            margin-bottom: 0.75rem;
            font-weight: 700;
        }

        .subtitle {
            color: #64748b;
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .error-list {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .error-list h3 {
            color: #991b1b;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .error-list li {
            color: #991b1b;
            font-size: 0.95rem;
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(220, 38, 38, 0.1);
        }

        .error-list li:last-child {
            border-bottom: none;
        }

        .error-list li i {
            color: #dc2626;
            font-size: 0.8rem;
            width: 16px;
        }

        .form-data {
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
            border: 1px solid #e2e8f0;
        }

        .form-data p {
            color: #475569;
            font-size: 0.9rem;
            margin: 0.5rem 0;
            display: flex;
            gap: 1rem;
        }

        .form-data strong {
            min-width: 100px;
            color: #1e293b;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #2E4053;
            color: white;
            text-decoration: none;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .btn-primary:hover {
            background: #1e2b36;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(46, 64, 83, 0.2);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            color: #2E4053;
            text-decoration: none;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #2E4053;
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .error-card {
                padding: 2rem;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-primary, .btn-secondary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="icon-wrapper">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            
            <h1>Registration Failed</h1>
            <p class="subtitle">We couldn't complete your registration</p>
            
            <div class="error-list">
                <h3>
                    <i class="fas fa-exclamation-triangle"></i>
                    Please fix the following errors:
                </h3>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li>
                            <i class="fas fa-times-circle"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <?php if (!empty($_SESSION['form_data'])): ?>
            <div class="form-data">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['form_data']['user'] ?? 'N/A'); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars(($_SESSION['form_data']['fname'] ?? '') . ' ' . ($_SESSION['form_data']['lname'] ?? '')); ?></p>
                <p><strong>Role:</strong> 
                    <?php 
                    $role = $_SESSION['form_data']['userrole'] ?? 'N';
                    $role_names = ['A' => 'Admin', 'T' => 'Thoroughbred', 'S' => 'Standardbred', 'ST' => 'Full Access', 'N' => 'Not Selected'];
                    echo $role_names[$role] ?? $role;
                    ?>
                </p>
            </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <a href="registration.php" class="btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Try Again
                </a>
                <a href="index.php" class="btn-secondary">
                    <i class="fas fa-home"></i>
                    Go Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Store in session for next time
$_SESSION['registration_errors'] = $errors;
$_SESSION['form_data'] = [
    'user' => $username,
    'fname' => $first_name,
    'lname' => $last_name,
    'userrole' => $user_role
];
?>