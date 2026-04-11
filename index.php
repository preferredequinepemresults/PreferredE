<?php
require_once("config.php");
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-18763673-4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-18763673-4');
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>Preferred Equine</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            overflow-x: hidden;
        }

        /* Hero Section with Visual Effects */
        .hero-section {
            position: relative;
            height: calc(100vh - 70px);
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E4053 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 20px;
            overflow: hidden;
        }

        /* Animated background shapes */
        .bg-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            right: -100px;
            background: radial-gradient(circle, rgba(255,215,0,0.1) 0%, transparent 70%);
            animation-delay: 0s;
        }

        .shape-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            left: -150px;
            background: radial-gradient(circle, rgba(255,215,0,0.08) 0%, transparent 70%);
            animation-delay: -5s;
        }

        .shape-3 {
            width: 200px;
            height: 200px;
            top: 30%;
            left: 20%;
            background: radial-gradient(circle, rgba(255,215,0,0.05) 0%, transparent 70%);
            animation-delay: -10s;
        }

        .shape-4 {
            width: 150px;
            height: 150px;
            bottom: 20%;
            right: 15%;
            background: radial-gradient(circle, rgba(255,215,0,0.05) 0%, transparent 70%);
            animation-delay: -15s;
        }

        /* Floating animation */
        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            25% {
                transform: translate(30px, 30px) scale(1.1);
            }
            50% {
                transform: translate(50px, -20px) scale(0.9);
            }
            75% {
                transform: translate(-20px, 40px) scale(1.05);
            }
        }

        /* Particle effects */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: rgba(255, 215, 0, 0.3);
            border-radius: 50%;
            animation: particleFloat 15s infinite linear;
        }

        @keyframes particleFloat {
            from {
                transform: translateY(100vh) translateX(0);
                opacity: 0;
            }
            to {
                transform: translateY(-100px) translateX(100px);
                opacity: 1;
            }
        }

        /* Glow effect */
        .glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,215,0,0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: pulse 8s infinite ease-in-out;
            z-index: 1;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.3;
                transform: translate(-50%, -50%) scale(1);
            }
            50% {
                opacity: 0.6;
                transform: translate(-50%, -50%) scale(1.2);
            }
        }

        /* Content (above effects) */
        .hero-content {
            position: relative;
            z-index: 10;
            max-width: 800px;
            animation: fadeInUp 1s ease;
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            color: white;
            line-height: 1.2;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .hero-title em {
            color: #FFD700;
            font-style: normal;
            display: block;
            font-size: clamp(1.2rem, 3vw, 2rem);
            margin-top: 10px;
            text-shadow: 0 0 20px rgba(255,215,0,0.5);
        }

        .hero-description {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 30px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            padding: 14px 35px;
            background: #FFD700;
            color: #0f172a;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            border: 2px solid #FFD700;
            box-shadow: 0 5px 20px rgba(255, 215, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            background: transparent;
            color: #FFD700;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.5);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-secondary {
            padding: 14px 35px;
            background: transparent;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
            position: relative;
            overflow: hidden;
        }

        .btn-secondary:hover {
            border-color: #FFD700;
            color: #FFD700;
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Simple Footer */
        .simple-footer {
            background: #0f172a;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
            position: relative;
            z-index: 10;
            border-top: 1px solid rgba(255,215,0,0.1);
        }

        .simple-footer p {
            color: rgba(255, 255, 255, 0.6);
            margin: 0;
        }

        /* Remove old styles */
        .main-banner, #bg-video, .js-preloader {
            display: none;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <!-- Hero Section with Visual Effects -->
    <section class="hero-section">
        <!-- Background Shapes -->
        <div class="bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>

        <!-- Central Glow -->
        <div class="glow"></div>

        <!-- Particle System (generated by JavaScript) -->
        <div class="particles" id="particles"></div>

        <!-- Content -->
        <div class="hero-content">
            <h1 class="hero-title">
                Put the power of the world's #1 sales agency to work for you
                <em>There's a reason we're Preferred!</em>
            </h1>
            <p class="hero-description">
                Access comprehensive sales data, pedigrees, and analysis tools.
            </p>
            <div class="hero-buttons">
                <?php if (isset($_SESSION['UserName']) && $_SESSION['UserName'] != ""): ?>
                    <a href="myaccount.php" class="btn-primary">My Account</a>
                    <a href="dam_search.php" class="btn-secondary">Standardbred Search</a>
                    <a href="horse_search_tb.php" class="btn-secondary">Thoroughbred Search</a>
                <?php else: ?>
                    <a href="registration.php" class="btn-primary">Get Started</a>
                    <a href="login.php" class="btn-secondary">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Simple Footer -->
    <footer class="simple-footer">
        <p>Copyright © <?php echo date('Y'); ?> Preferred Equine. All rights reserved.</p>
    </footer>

    <script src="assets/js/jquery-2.1.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    
    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 50;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random position
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (10 + Math.random() * 20) + 's';
                
                // Random size
                const size = Math.random() * 4 + 1;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                
                // Random opacity
                particle.style.opacity = Math.random() * 0.5;
                
                particlesContainer.appendChild(particle);
            }
        }
        
        // Initialize particles
        createParticles();
        
        // Parallax effect on mouse move
        document.addEventListener('mousemove', function(e) {
            const shapes = document.querySelectorAll('.shape');
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            
            shapes.forEach((shape, index) => {
                const speed = (index + 1) * 20;
                const x = (mouseX - 0.5) * speed;
                const y = (mouseY - 0.5) * speed;
                shape.style.transform = `translate(${x}px, ${y}px)`;
            });
        });
    </script>
</body>
</html>