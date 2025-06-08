<?php
// Cek session jika belum di-start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, redirect ke halaman login
    header('Location: ../../../index.php?action=login');
    exit();
}

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Parameter untuk menentukan halaman aktif dashboard
// $current_dashboard_page harus di-set sebelum include header ini
if (!isset($current_dashboard_page)) {
    $current_dashboard_page = 'profile';
}

// Base URL untuk dashboard
$base_url = '../../../';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Dashboard - Code Camp'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/custom.css">
    <link rel="icon" href="<?php echo $base_url; ?>assets/images/logo/logo_mobile.png" type="image/x-icon">
    <style>
        /* Hamburger animation */
        .hamburger {
            width: 24px;
            height: 18px;
            position: relative;
            cursor: pointer;
        }

        .hamburger-line {
            display: block;
            position: absolute;
            height: 2px;
            width: 100%;
            background: white;
            border-radius: 1px;
            opacity: 1;
            left: 0;
            transform: rotate(0deg);
            transition: .25s ease-in-out;
        }

        .hamburger-line:nth-child(1) {
            top: 0px;
        }

        .hamburger-line:nth-child(2) {
            top: 8px;
        }

        .hamburger-line:nth-child(3) {
            top: 16px;
        }

        .hamburger.active .hamburger-line:nth-child(1) {
            top: 8px;
            transform: rotate(135deg);
        }

        .hamburger.active .hamburger-line:nth-child(2) {
            opacity: 0;
            left: -60px;
        }

        .hamburger.active .hamburger-line:nth-child(3) {
            top: 8px;
            transform: rotate(-135deg);
        }

        /* Mobile menu overlay */
        .mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 40;
            transform: translateY(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            padding-top: 80px;
        }

        .mobile-menu-show {
            transform: translateY(0);
        }

        .mobile-menu-content {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            margin: 0 16px;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            transform: translateY(-30px);
            opacity: 0;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1) 0.1s, opacity 0.3s ease 0.1s;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-height: calc(100vh - 120px);
            overflow-y: auto;
        }

        .mobile-menu-show .mobile-menu-content {
            transform: translateY(0);
            opacity: 1;
        }

        /* Sidebar styles */
        .sidebar-item {
            padding: 12px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .sidebar-item:hover,
        .sidebar-item.active {
            background-color: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }

        .circle-bg {
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background-color: #0284c7;
            z-index: -1;
        }

        /* Profile image styles */
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Touch device improvements */
        @media (hover: none) and (pointer: coarse) {
            .mobile-menu-content a:active {
                background-color: rgba(255, 255, 255, 0.1);
                transform: translateX(2px);
            }
        }

        /* Body scroll lock */
        .body-scroll-lock {
            overflow: hidden;
            position: fixed;
            width: 100%;
        }
    </style>
    <?php if (isset($additional_css)): ?>
        <?php echo $additional_css; ?>
    <?php endif; ?>
</head>

<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="bg-blue-900 shadow-md relative z-50">
        <div class="container mx-auto px-4 py-2">
            <div class="flex items-center justify-between w-full">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="<?php echo $base_url; ?>index.php" class="flex items-center">
                        <img src="<?php echo $base_url; ?>assets/images/logo.png" alt="Logo" class="h-16 hidden md:block">
                        <img src="<?php echo $base_url; ?>assets/images/logo/logo_mobile.png" alt="Logo" class="md:hidden h-12">
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-8">
                    <a href="<?php echo $base_url; ?>index.php" class="text-white hover:text-blue-200 transition-colors duration-300">Home</a>
                    <a href="<?php echo $base_url; ?>index.php?action=bootcamps" class="text-white hover:text-blue-200 transition-colors duration-300">Bootcamps</a>
                </nav>

                <!-- User Account -->
                <div class="flex items-center space-x-3">
                    <!-- User Profile Icon -->
                    <div class="relative">
                        <button id="profileButton" class="flex items-center focus:outline-none">
                            <?php if (file_exists("{$base_url}assets/images/users/{$user_id}.jpg")): ?>
                                <img src="<?php echo $base_url; ?>assets/images/users/<?php echo $user_id; ?>.jpg" alt="Profile" class="w-10 h-10 rounded-full border-2 border-white">
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white border-2 border-white">
                                    <?php echo strtoupper(substr($name, 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </button>
                        <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden z-20">
                            <a href="dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                My Profile
                            </a>
                            <a href="change_password.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7 6h-2m-6 0H4a3 3 0 01-3-3V9a3 3 0 013-3h2.25M15 7V4.5A2.5 2.5 0 0012.5 2h-1A2.5 2.5 0 009 4.5V7m6 0v3H9V7"></path>
                                </svg>
                                Change Password
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <a href="<?php echo $base_url; ?>index.php?action=logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </a>
                        </div>
                    </div>

                    <!-- Mobile Menu Toggle Button -->
                    <button id="mobile-menu-button" class="md:hidden flex items-center justify-center p-2 rounded-md text-white hover:bg-blue-800 focus:outline-none">
                        <div class="hamburger" id="hamburger">
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Enhanced Mobile Menu - Floating Overlay -->
            <div id="mobile-menu" class="mobile-menu">
                <div class="mobile-menu-content px-4 pt-6 pb-4 space-y-1">
                    <!-- Close button -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-white font-semibold text-lg">Dashboard Menu</h3>
                        <button id="close-mobile-menu" class="text-blue-200 hover:text-white p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Navigation Links -->
                    <a href="<?php echo $base_url; ?>index.php" 
                       class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition-all duration-200 transform hover:translate-x-1">
                        <svg class="w-5 h-5 mr-3 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Home</span>
                    </a>

                    <a href="<?php echo $base_url; ?>index.php?action=bootcamps/index.php" 
                       class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition-all duration-200 transform hover:translate-x-1">
                        <svg class="w-5 h-5 mr-3 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>Bootcamps</span>
                    </a>

                    <!-- Dashboard Section -->
                    <div class="border-t border-blue-700 my-3 pt-3">
                        <p class="text-blue-200 text-sm font-medium px-4 mb-2">Account Settings</p>
                        
                        <a href="dashboard.php" 
                           class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_dashboard_page == 'profile') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white'; ?> transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 <?php echo ($current_dashboard_page == 'profile') ? 'text-white' : 'text-blue-200'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>My Profile</span>
                        </a>

                        <a href="change_password.php" 
                           class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_dashboard_page == 'change_password') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white'; ?> transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 <?php echo ($current_dashboard_page == 'change_password') ? 'text-white' : 'text-blue-200'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7 6h-2m-6 0H4a3 3 0 01-3-3V9a3 3 0 013-3h2.25M15 7V4.5A2.5 2.5 0 0012.5 2h-1A2.5 2.5 0 009 4.5V7m6 0v3H9V7"></path>
                            </svg>
                            <span>Change Password</span>
                        </a>

                        <a href="delete_account.php" 
                           class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_dashboard_page == 'delete_account') ? 'bg-red-600 text-white' : 'text-red-300 hover:bg-red-600 hover:text-white'; ?> transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 <?php echo ($current_dashboard_page == 'delete_account') ? 'text-white' : 'text-red-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Delete Account</span>
                        </a>
                    </div>

                    <!-- Logout -->
                    <div class="border-t border-blue-700 pt-3">
                        <a href="<?php echo $base_url; ?>index.php?action=logout" 
                           class="flex items-center px-4 py-3 rounded-lg text-red-300 hover:bg-red-600 hover:text-white transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <script>
        // Mobile menu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeMobileMenu = document.getElementById('close-mobile-menu');
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger');
            const body = document.body;

            function openMobileMenu() {
                mobileMenu.classList.add('mobile-menu-show');
                hamburger.classList.add('active');
                body.classList.add('body-scroll-lock');
            }

            function closeMobileMenuFunc() {
                mobileMenu.classList.remove('mobile-menu-show');
                hamburger.classList.remove('active');
                body.classList.remove('body-scroll-lock');
            }

            // Toggle mobile menu
            mobileMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                if (mobileMenu.classList.contains('mobile-menu-show')) {
                    closeMobileMenuFunc();
                } else {
                    openMobileMenu();
                }
            });

            // Close mobile menu button
            closeMobileMenu.addEventListener('click', closeMobileMenuFunc);

            // Close mobile menu when clicking outside
            mobileMenu.addEventListener('click', function(e) {
                if (e.target === mobileMenu) {
                    closeMobileMenuFunc();
                }
            });

            // Close mobile menu on window resize to desktop view
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) { // md breakpoint
                    closeMobileMenuFunc();
                }
            });

            // Profile dropdown toggle
            const profileButton = document.getElementById('profileButton');
            const profileDropdown = document.getElementById('profileDropdown');

            profileButton.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });

            // Close profile dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });

            // Close dropdown when clicking mobile menu
            mobileMenuButton.addEventListener('click', function() {
                profileDropdown.classList.add('hidden');
            });
        });
    </script>

    <?php if (isset($additional_js_header)): ?>
        <?php echo $additional_js_header; ?>
    <?php endif; ?>