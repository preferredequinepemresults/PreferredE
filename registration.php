<?php
// Add session status check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("./header.php");

// Get errors and form data from session
$errors = $_SESSION['registration_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];

// Clear session data
unset($_SESSION['registration_errors']);
unset($_SESSION['form_data']);

// Show success message if exists
if (isset($_SESSION['registration_success'])) {
    echo '<div class="alert alert-success" style="max-width: 600px; margin: 20px auto;">' 
         . $_SESSION['registration_success'] . '</div>';
    unset($_SESSION['registration_success']);
}
?>

<style>
/* Custom Styles for Registration Page */
.registration-container {
    max-width: 600px;
    margin: 80px auto 40px;
    padding: 30px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.registration-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #2E4053;
}

.registration-header h1 {
    color: #2E4053;
    font-size: 28px;
    margin-bottom: 10px;
    font-weight: 600;
}

.registration-header p {
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

.form-control-select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 15px;
    background-color: white;
    height: 46px;
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

/* Password match indicator */
.password-match {
    font-size: 13px;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.match-success {
    color: #27ae60;
}

.match-error {
    color: #e74c3c;
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

.error-alert {
    background-color: #fff5f5;
    border: 1px solid #fed7d7;
    color: #c53030;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 25px;
}

.error-alert ul {
    margin: 10px 0 0 0;
    padding-left: 20px;
}

.error-alert li {
    margin-bottom: 5px;
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

.password-strength {
    height: 4px;
    margin-top: 8px;
    border-radius: 2px;
    transition: all 0.3s;
}

/* Responsive */
@media (max-width: 768px) {
    .registration-container {
        margin: 60px 20px 30px;
        padding: 20px;
    }
    
    .registration-header h1 {
        font-size: 24px;
    }
}
</style>

<div class="registration-container">
    <div class="registration-header">
        <h1>Create Your Account</h1>
        <p>Join Preferred Equine to access premium features</p>
    </div>

    <!-- Show errors if any -->
    <?php if (!empty($errors)): ?>
        <div class="error-alert">
            <strong>Please fix the following errors:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="register_action.php" method="POST" id="registrationForm">
        <div class="form-group">
            <label for="user">Email Address *</label>
            <input type="email" class="form-control" placeholder="you@example.com" name="user" id="user" 
                   value="<?php echo htmlspecialchars($form_data['user'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="fname">First Name *</label>
            <input type="text" class="form-control" placeholder="Enter your first name" name="fname" id="fname" 
                   value="<?php echo htmlspecialchars($form_data['fname'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="lname">Last Name *</label>
            <input type="text" class="form-control" placeholder="Enter your last name" name="lname" id="lname" 
                   value="<?php echo htmlspecialchars($form_data['lname'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password *</label>
            <div class="password-wrapper">
                <input type="password" class="form-control" placeholder="Create a strong password" 
                       name="password" id="password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
            <div class="password-strength" id="passwordStrength"></div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password *</label>
            <div class="password-wrapper">
                <input type="password" class="form-control" placeholder="Confirm your password" 
                       name="confirm_password" id="confirm_password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', this)">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
            <div class="password-match" id="passwordMatch">
                <i class="fa fa-info-circle"></i>
                <span>Passwords must match</span>
            </div>
        </div>

        <div class="password-hint">
            <strong>Password Requirements:</strong> Minimum 8 characters with uppercase, lowercase, number, and special character
        </div>

        <div class="form-group">
            <label for="userrole">Account Type *</label>
            <select class="form-control-select" id="userrole" name="userrole" required>
                <option value="N" <?php echo (($form_data['userrole'] ?? '') == 'N') ? 'selected' : ''; ?>>SELECT USER ROLE</option>
                <option value="T" <?php echo (($form_data['userrole'] ?? '') == 'T') ? 'selected' : ''; ?>>THOROUGHBRED Access</option>
                <option value="S" <?php echo (($form_data['userrole'] ?? '') == 'S') ? 'selected' : ''; ?>>STANDARDBRED Access</option>
                <option value="ST" <?php echo (($form_data['userrole'] ?? '') == 'ST') ? 'selected' : ''; ?>>STANDARDBRED & THOROUGHBRED Access</option>
            </select>
        </div>

        <button type="submit" class="submit-btn">Create Account</button>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Sign in here</a></p>
        </div>
    </form>
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

// Password strength indicator and match checker
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('confirm_password');
const strengthBar = document.getElementById('passwordStrength');
const matchIndicator = document.getElementById('passwordMatch');

function checkPasswordStrength() {
    const password = passwordInput.value;
    
    let strength = 0;
    let color = '#e74c3c'; // Red
    
    // Check criteria
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[\W_]/.test(password)) strength++;
    
    // Set color and width based on strength
    if (strength <= 1) {
        color = '#e74c3c'; // Red
        width = '20%';
    } else if (strength <= 3) {
        color = '#f39c12'; // Orange
        width = '60%';
    } else {
        color = '#27ae60'; // Green
        width = '100%';
    }
    
    strengthBar.style.width = width;
    strengthBar.style.backgroundColor = color;
}

function checkPasswordMatch() {
    const password = passwordInput.value;
    const confirm = confirmInput.value;
    
    if (confirm.length === 0) {
        matchIndicator.innerHTML = '<i class="fa fa-info-circle"></i> <span>Confirm your password</span>';
        matchIndicator.className = 'password-match';
    } else if (password === confirm) {
        matchIndicator.innerHTML = '<i class="fa fa-check-circle"></i> <span>Passwords match</span>';
        matchIndicator.className = 'password-match match-success';
    } else {
        matchIndicator.innerHTML = '<i class="fa fa-exclamation-circle"></i> <span>Passwords do not match</span>';
        matchIndicator.className = 'password-match match-error';
    }
}

// Add event listeners
passwordInput.addEventListener('input', function() {
    checkPasswordStrength();
    checkPasswordMatch();
});

confirmInput.addEventListener('input', checkPasswordMatch);

// Form validation
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    const password = passwordInput.value;
    const confirm = confirmInput.value;
    const errors = [];
    
    // Password validation
    if (password.length < 8) {
        errors.push('Password must be at least 8 characters');
    }
    if (!/[A-Z]/.test(password)) {
        errors.push('Password must contain at least one uppercase letter');
    }
    if (!/[a-z]/.test(password)) {
        errors.push('Password must contain at least one lowercase letter');
    }
    if (!/[0-9]/.test(password)) {
        errors.push('Password must contain at least one number');
    }
    if (!/[\W_]/.test(password)) {
        errors.push('Password must contain at least one special character');
    }
    
    // Confirm password validation
    if (password !== confirm) {
        errors.push('Passwords do not match');
    }
    
    // Role validation
    if (document.getElementById('userrole').value === 'N') {
        errors.push('Please select a user role');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        alert('Please fix the following errors:\n\n' + errors.join('\n'));
    }
});

// Initialize match check on page load
checkPasswordMatch();
</script>