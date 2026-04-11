<?php
session_start();
include("./header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Inactive | Preferred Equine</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .inactive-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .inactive-card {
            background: white;
            border-radius: 1.5rem;
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }

        .icon-wrapper {
            width: 100px;
            height: 100px;
            background: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
        }

        .icon-wrapper i {
            font-size: 3rem;
            color: #dc2626;
        }

        h1 {
            font-size: 2rem;
            color: #1e293b;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        p {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 2rem 0;
        }

        .contact-info {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 1rem;
            text-align: left;
        }

        .contact-info h3 {
            font-size: 1.1rem;
            color: #1e293b;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .contact-info p {
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .contact-info i {
            width: 20px;
            color: #667eea;
        }

        .btn-home {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            margin-top: 1.5rem;
            transition: transform 0.2s;
        }

        .btn-home:hover {
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .inactive-card {
                padding: 2rem;
            }
            
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="inactive-container">
        <div class="inactive-card">
            <div class="icon-wrapper">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h1>Account Inactive</h1>
            
            <p>Your account has been deactivated. Please contact the administrator to reactivate your account.</p>
            
            <div class="divider"></div>
            
            <div class="contact-info">
                <h3>Need assistance?</h3>
                <p>
                    <i class="fas fa-clock"></i>
                    <span>Mon-Fri, 9am - 5pm EST</span>
                </p>
            </div>
            
            <a href="index.php" class="btn-home">
                <i class="fas fa-home" style="margin-right: 0.5rem;"></i>
                Return to Homepage
            </a>
        </div>
    </div>
</body>
</html>