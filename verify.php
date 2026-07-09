<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['verify_email']);
unset($_SESSION['verify_name']);

include("./header.php");
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
    text-align: center;
}

.verify-container h1 {
    color: #2E4053;
    font-size: 28px;
    margin-bottom: 15px;
    font-weight: 600;
}

.verify-container p {
    color: #666;
    font-size: 16px;
    line-height: 1.5;
    margin-bottom: 25px;
}

.btn-primary {
    display: inline-block;
    padding: 14px 24px;
    background: linear-gradient(135deg, #2E4053 0%, #3a506b 100%);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
}

.btn-primary:hover {
    color: white;
    text-decoration: none;
}
</style>

<div class="verify-container">
    <h1>Email Verification Removed</h1>
    <p>This site now uses local account authentication. Separate email verification is no longer required for login.</p>
    <a href="login.php" class="btn-primary">Back to Login</a>
</div>
