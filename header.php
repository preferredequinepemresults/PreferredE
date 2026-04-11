<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("config.php");
?>

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-18763673-4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-18763673-4');
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
    <title>Preferred Equine</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <style>
        /* RESET - Remove ALL extra spacing */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            margin: 0 !important;
            padding: 70px 0 0 0 !important;
        }

        /* Header - Clean and simple */
        .header-area {
            background-color: #2E4053 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 70px !important;
            z-index: 9999 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
        }

        .header-area .main-nav {
            height: 70px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 0 30px !important;
        }

        /* Logo */
        .header-area .logo {
            color: white !important;
            font-size: 1.6rem !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            white-space: nowrap !important;
        }

        .header-area .logo em {
            color: #FFD700 !important;
            font-style: normal !important;
        }

        /* Navigation */
        .header-area .nav {
            display: flex !important;
            align-items: center !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
            gap: 2px !important;
        }

        .header-area .nav>li {
            list-style: none !important;
            position: relative !important;
        }

        .header-area .nav>li>a {
            display: flex !important;
            align-items: center !important;
            padding: 8px 16px !important;
            color: rgba(255, 255, 255, 0.9) !important;
            font-size: 1rem !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
            white-space: nowrap !important;
            line-height: normal !important;
            cursor: pointer !important;
        }

        .header-area .nav>li>a:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: white !important;
        }

        /* Dropdown toggle arrow - using CSS instead of ::after for better control */
        .header-area .nav>li.dropdown>a .arrow {
            display: inline-block !important;
            margin-left: 6px !important;
            font-size: 0.7rem !important;
            color: rgba(255, 255, 255, 0.6) !important;
            transition: transform 0.2s ease !important;
        }

        .header-area .nav>li.dropdown:hover>a .arrow {
            transform: rotate(180deg) !important;
        }

        /* Dropdown menu */
        .header-area .nav>li.dropdown .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            background: white !important;
            min-width: 240px !important;
            border-radius: 8px !important;
            padding: 6px 0 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(-5px) !important;
            transition: all 0.2s ease !important;
            border: none !important;
            z-index: 10000 !important;
            display: block !important;
            /* Override Bootstrap */
        }

        .header-area .nav>li.dropdown:hover .dropdown-menu {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }

        .header-area .dropdown-menu a {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 10px 16px !important;
            color: #2c3e50 !important;
            font-size: 0.95rem !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
            border-left: 3px solid transparent !important;
            white-space: nowrap !important;
            background: transparent !important;
            border-bottom: none !important;
        }

        .header-area .dropdown-menu a i {
            width: 20px !important;
            color: #5a6b7a !important;
            font-size: 1rem !important;
        }

        .header-area .dropdown-menu a:hover {
            background: #f0f4f8 !important;
            color: #2E4053 !important;
            border-left-color: #2E4053 !important;
        }

        .header-area .dropdown-menu a:hover i {
            color: #2E4053 !important;
        }

        /* User menu (last dropdown) */
        .header-area .nav>li:last-child.dropdown {
            margin-left: 10px !important;
        }

        .header-area .nav>li:last-child.dropdown>a {
            background: rgba(255, 255, 255, 0.1) !important;
            border-radius: 30px !important;
            padding: 6px 6px 6px 16px !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .header-area .nav>li:last-child.dropdown .dropdown-menu {
            left: auto !important;
            right: 0 !important;
        }

        /* Auth buttons */
        .header-area .nav>li>a[href="registration.php"] {
            background: #FFD700 !important;
            color: #2E4053 !important;
            font-weight: 600 !important;
            margin-left: 8px !important;
        }

        .header-area .nav>li>a[href="login.php"] {
            border: 2px solid rgba(255, 255, 255, 0.3) !important;
        }

        /* Mobile menu */
        .mobile-toggle {
            display: none !important;
            flex-direction: column !important;
            gap: 5px !important;
            cursor: pointer !important;
            padding: 10px !important;
        }

        .mobile-toggle span {
            width: 25px !important;
            height: 2px !important;
            background: white !important;
            border-radius: 4px !important;
            transition: all 0.3s ease !important;
        }

        @media (max-width: 1024px) {
            body {
                padding-top: 60px !important;
            }

            .header-area {
                height: 60px !important;
            }

            .header-area .main-nav {
                height: 60px !important;
                padding: 0 20px !important;
            }

            .mobile-toggle {
                display: flex !important;
            }

            .header-area .nav {
                position: fixed !important;
                top: 60px !important;
                left: 0 !important;
                right: 0 !important;
                background: #2E4053 !important;
                flex-direction: column !important;
                align-items: stretch !important;
                padding: 15px !important;
                transform: translateY(-150%) !important;
                opacity: 0 !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2) !important;
                max-height: calc(100vh - 60px) !important;
                overflow-y: auto !important;
            }

            .header-area .nav.active {
                transform: translateY(0) !important;
                opacity: 1 !important;
            }

            .header-area .nav>li {
                width: 100% !important;
            }

            .header-area .nav>li>a {
                width: 100% !important;
                justify-content: space-between !important;
            }

            /* Mobile dropdown handling */
            .header-area .nav>li.dropdown .dropdown-menu {
                position: static !important;
                opacity: 1 !important;
                visibility: visible !important;
                transform: none !important;
                box-shadow: none !important;
                background: rgba(255, 255, 255, 0.05) !important;
                margin: 5px 0 0 0 !important;
                display: none !important;
                width: 100% !important;
            }

            .header-area .nav>li.dropdown.active .dropdown-menu {
                display: block !important;
            }

            .header-area .dropdown-menu a {
                color: rgba(255, 255, 255, 0.9) !important;
                padding-left: 30px !important;
            }

            .header-area .dropdown-menu a i {
                color: rgba(255, 255, 255, 0.7) !important;
            }

            .header-area .nav>li:last-child.dropdown {
                margin-left: 0 !important;
            }

            .header-area .nav>li:last-child.dropdown>a {
                background: rgba(255, 255, 255, 0.1) !important;
                margin-top: 5px !important;
            }
        }

        /* Remove ALL possible sources of extra spacing */
        .header-area::before,
        .header-area::after,
        .header-area *::before,
        .header-area *::after,
        br,
        hr,
        .divider {
            display: none !important;
        }
    </style>
</head>

<!-- Header -->
<header class="header-area header-sticky">
    <div class="main-nav">

        <!-- Logo -->
        <a href="index.php" class="logo">
            <img src="assets/images/PEM-ONLINE.png"
                alt="Preferred Equine Online, LLC"
                height="60"
                style="vertical-align: middle; max-height: 60px; width: auto;">
        </a>

        <!-- Mobile Toggle -->
        <div class="mobile-toggle" id="mobileToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <!-- Menu -->
        <ul class="nav" id="mainNav">
            <?php
            $userName = $_SESSION["UserName"] ?? "";
            $userRole = $_SESSION["UserRole"] ?? "";

            if ($userName != "") {
                if ($userRole == "A" || $userRole == "S" || $userRole == "ST") {
            ?>
                    <li class="dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            Standardbred <span class="arrow">▼</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="dam_search.php"><i class="fa fa-search"></i> Horse Search Report</a>
                            <a class="dropdown-item" href="sire_analysis.php"><i class="fa fa-bar-chart"></i> Sire Analysis</a>
                            <a class="dropdown-item" href="sire_analysis_summary.php"><i class="fa fa-pie-chart"></i> Sire Analysis Summary</a>
                            <a class="dropdown-item" href="buyers_report.php"><i class="fa fa-users"></i> Buyer's Report</a>
                            <a class="dropdown-item" href="sales_report.php"><i class="fa fa-line-chart"></i> Sales Report</a>
                            <a class="dropdown-item" href="auction_report.php"><i class="fa fa-gavel"></i> Auction Report</a>
                            <a class="dropdown-item" href="top_buyers.php"><i class="fa fa-trophy"></i> Top Buyers</a>
                            <a class="dropdown-item" href="individual_sales_report.php"><i class="fa fa-file-text"></i> Individual Sales</a>
                            <a class="dropdown-item" href="broodmares_report.php"><i class="fa fa-female"></i> Broodmares Report</a>
                            <a class="dropdown-item" href="cons_analysis.php"><i class="fa fa-building"></i> Consignor Analysis</a>
                            <a class="dropdown-item" href="horse_list.php"><i class="fa fa-list"></i> Horse Inspection</a>
                        </div>
                    </li>
                <?php
                }
                if ($userRole == "A" || $userRole == "T" || $userRole == "ST") {
                ?>
                    <li class="dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            Thoroughbred <span class="arrow">▼</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="horse_search_tb.php"><i class="fa fa-search"></i> Horse Search Report</a>
                            <a class="dropdown-item" href="sire_analysis_tb.php"><i class="fa fa-bar-chart"></i> Sire Analysis</a>
                            <a class="dropdown-item" href="sire_analysis_summary_tb.php"><i class="fa fa-pie-chart"></i> Sire Analysis Summary</a>
                            <a class="dropdown-item" href="buyers_report_tb.php"><i class="fa fa-users"></i> Buyer's Report</a>
                            <a class="dropdown-item" href="sales_report_tb.php"><i class="fa fa-line-chart"></i> Sales Report</a>
                            <a class="dropdown-item" href="auction_report_tb.php"><i class="fa fa-gavel"></i> Auction Report</a>
                            <a class="dropdown-item" href="top_yearling_buyers_tb.php"><i class="fa fa-star"></i> Top Yearling Buyers</a>
                            <a class="dropdown-item" href="top_every_buyers_tb.php"><i class="fa fa-trophy"></i> All Top Buyers</a>
                            <a class="dropdown-item" href="top_mixed_buyers_tb.php"><i class="fa fa-shopping-cart"></i> Top Mixed Buyers</a>
                            <a class="dropdown-item" href="weanling-report.php"><i class="fa fa-child"></i> Weanlings Report</a>
                            <a class="dropdown-item" href="yearling_to_breeze_report.php"><i class="fa fa-forward"></i> Yearling to Breeze</a>
                            <a class="dropdown-item" href="breeze_to_yearling_report.php"><i class="fa fa-backward"></i> Breeze from Yearling</a>
                            <a class="dropdown-item" href="individual_sales_report_tb.php"><i class="fa fa-file-text"></i> Individual Sales</a>
                            <a class="dropdown-item" href="broodmares_report_tb.php"><i class="fa fa-female"></i> Broodmares Report</a>
                            <a class="dropdown-item" href="horse_list_tb.php"><i class="fa fa-list"></i> Horse Inspection</a>
                        </div>
                    </li>
                <?php
                }
                if ($userRole == "A") {
                ?>
                    <li class="dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            File Upload <span class="arrow">▼</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="sales_file_upload.php"><i class="fa fa-upload"></i> Standardbred</a>
                            <a class="dropdown-item" href="sales_file_upload_tb.php"><i class="fa fa-upload"></i> Thoroughbred</a>
                            <a class="dropdown-item" href="https://python.preferredequinesalesresults.com/" target="_blank"><i class="fa fa-code"></i> Python Upload</a>
                            <a class="dropdown-item" href="manage_data.php"><i class="fa fa-database"></i> Manage Data</a>
                            <a class="dropdown-item" href="file_upload_rating_update.php"><i class="fa fa-star"></i> Rating Update</a>
                            <a class="dropdown-item" href="file_upload_et_update.php"><i class="fa fa-clock-o"></i> ET Update</a>
                        </div>
                    </li>

                    <li class="dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            USERS <span class="arrow">▼</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="user_authorization.php"><i class="fa fa-shield"></i> AUTHORIZE USERS</a>
                        </div>
                    </li>
            <?php
                }
            }
            ?>

            <?php
            if ($userName == "") {
                echo '<li><a href="registration.php">REGISTER</a></li>';
                echo '<li><a href="login.php">Login</a></li>';
            } else {
                $displayName = "";
                if (!empty($_SESSION["UserFirstName"]) && !empty($_SESSION["UserLastName"])) {
                    $displayName = $_SESSION["UserFirstName"] . " " . $_SESSION["UserLastName"];
                } else {
                    $displayName = $userName;
                }
                echo '<li class="dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-user-circle"></i> ' . htmlspecialchars($displayName) . ' <span class="arrow">▼</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="myaccount.php"><i class="fa fa-cog"></i> My Account</a>
                            <a class="dropdown-item" href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
                        </div>
                    </li>';
            }
            ?>
        </ul>
    </div>
</header>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Mobile menu toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const mainNav = document.getElementById('mainNav');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            mainNav.classList.toggle('active');

            // Close all dropdowns when closing mobile menu
            if (!mainNav.classList.contains('active')) {
                document.querySelectorAll('.dropdown').forEach(d => {
                    d.classList.remove('active');
                });
            }
        });
    }

    // Handle dropdowns on mobile
    if (window.innerWidth <= 1024) {
        document.querySelectorAll('.dropdown > .dropdown-toggle').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Close other dropdowns
                const currentDropdown = this.parentElement;
                document.querySelectorAll('.dropdown').forEach(d => {
                    if (d !== currentDropdown) {
                        d.classList.remove('active');
                    }
                });

                // Toggle current dropdown
                currentDropdown.classList.toggle('active');
            });
        });
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (mainNav && mobileToggle) {
            if (!mainNav.contains(e.target) && !mobileToggle.contains(e.target)) {
                mainNav.classList.remove('active');
                document.querySelectorAll('.dropdown').forEach(d => {
                    d.classList.remove('active');
                });
            }
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            mainNav.classList.remove('active');
            document.querySelectorAll('.dropdown').forEach(d => {
                d.classList.remove('active');
            });
        }
    });
</script>