<?php
// Cek session jika belum di-start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['name'] : '';
$user_id = $is_logged_in ? $_SESSION['user_id'] : '';

// Parameter untuk menentukan halaman aktif
// $current_page harus di-set sebelum include header ini
if (!isset($current_page)) {
    $current_page = 'home';
}

// Function untuk get profile image
function getMainProfileImage($user_id, $base_path = '')
{
    if (!$user_id) return null;

    $profile_extensions = ['jpg', 'jpeg', 'png', 'gif'];

    foreach ($profile_extensions as $ext) {
        $filename = "user_{$user_id}.{$ext}";
        $image_path = $base_path . "assets/images/users/{$filename}";
        if (file_exists($image_path)) {
            return "assets/images/users/{$filename}";
        }
    }
    return null;
}

$profile_image = $is_logged_in ? getMainProfileImage($user_id, isset($base_url) ? $base_url : '') : null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Campus Hub - Temukan Bootcamp IT Terbaik'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo isset($base_url) ? $base_url : ''; ?>assets/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="icon" href="<?php echo isset($base_url) ? $base_url : ''; ?>assets/images/logo/logo_mobile.png" type="image/x-icon">
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
            background: #374151;
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

        /* Floating mobile menu overlay */
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
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            margin: 0 16px;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 25px 50px -12px rgba(59, 130, 246, 0.15);
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

        /* Active menu styles */
        .nav-active {
            color: #2563eb !important;
            font-weight: bold;
        }

        .nav-active-mobile {
            color: #2563eb !important;
            font-weight: bold;
            background-color: #dbeafe !important;
        }

        /* Navbar profile image styles */
        .navbar-profile-img {
            transition: all 0.2s ease-in-out;
        }

        .navbar-profile-img:hover {
            transform: scale(1.05);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }

        /* Smooth body scroll lock */
        .body-scroll-lock {
            overflow: hidden;
            position: fixed;
            width: 100%;
        }

        /* Touch device improvements */
        @media (hover: none) and (pointer: coarse) {
            .group:hover .group-hover\:bg-blue-200 {
                background-color: #dbeafe;
            }

            .mobile-menu-content a:active {
                background-color: #dbeafe;
                transform: translateX(2px);
            }
        }

        /* Better mobile spacing */
        @media (max-width: 640px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
    <?php if (isset($additional_css)): ?>
        <?php echo $additional_css; ?>
    <?php endif; ?>
</head>

<body class="bg-gray-50 font-sans">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm relative z-50">
        <div class="container mx-auto px-4 py-1">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php" class="flex items-center">
                        <img src="<?php echo isset($base_url) ? $base_url : ''; ?>assets/images/logo.png" alt="Logo" class="h-16 hidden md:block">
                        <img src="<?php echo isset($base_url) ? $base_url : ''; ?>assets/images/logo/logo_mobile.png" alt="Logo" class="md:hidden h-12">
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-8">
                    <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php"
                        class="<?php echo ($current_page == 'home') ? 'nav-active' : 'text-gray-700 hover:text-blue-600'; ?> transition-colors duration-300">
                        Home
                    </a>
                    <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=bootcamps"
                        class="<?php echo ($current_page == 'bootcamps') ? 'nav-active' : 'text-gray-700 hover:text-blue-600'; ?> transition-colors duration-300">
                        Bootcamps
                    </a>

                    <?php if ($is_logged_in): ?>
                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=my_bootcamps"
                            class="<?php echo ($current_page == 'my_bootcamps') ? 'nav-active' : 'text-gray-700 hover:text-blue-600'; ?> transition-colors duration-300">
                            My Bootcamps
                        </a>
                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=wishlist"
                            class="<?php echo ($current_page == 'wishlist') ? 'nav-active' : 'text-gray-700 hover:text-blue-600'; ?> transition-colors duration-300">
                            Wishlist
                        </a>
                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=forum"
                            class="<?php echo ($current_page == 'forum') ? 'nav-active' : 'text-gray-700 hover:text-blue-600'; ?> transition-colors duration-300">
                            Forum
                        </a>
                    <?php endif; ?>
                </nav>

                <!-- User Account / Login Buttons -->
                <div class="flex items-center space-x-3">
                    <?php if ($is_logged_in): ?>
                        <!-- User Menu untuk yang sudah login -->
                        <div class="relative ml-3">
                            <div>
                                <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                                    id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Open user menu</span>

                                    <?php if ($profile_image): ?>
                                        <img class="h-8 w-8 rounded-full object-cover navbar-profile-img"
                                            src="<?php echo $profile_image; ?>?v=<?php echo time(); ?>"
                                            alt="<?php echo htmlspecialchars($user_name); ?>">
                                    <?php else: ?>
                                        <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium navbar-profile-img">
                                            <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </button>
                            </div>

                            <!-- Dropdown menu -->
                            <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                                role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" id="user-menu">
                                <div class="px-4 py-2 border-b bg-gray-50">
                                    <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user_name); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($_SESSION['alamat_email'] ?? ''); ?></p>
                                </div>
                                <a href="views/auth/dashboard/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="index.php?action=logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Sign out
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Login/Register buttons untuk yang belum login -->
                        <div class="space-x-2">
                            <a href="index.php?action=login" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                            <a href="index.php?action=signup" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium">Sign Up</a>
                        </div>
                    <?php endif; ?>

                    <!-- Mobile Menu Toggle Button -->
                    <button id="mobile-menu-button" class="md:hidden flex items-center justify-center p-2 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none">
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
                        <h3 class="text-gray-800 font-semibold text-lg">Menu</h3>
                        <button id="close-mobile-menu" class="text-gray-500 hover:text-gray-700 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Navigation Links -->
                    <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php"
                        class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'home') ? 'nav-active-mobile' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'; ?> transition-all duration-200 transform hover:translate-x-1">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Home</span>
                    </a>

                    <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=bootcamps"
                        class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'bootcamps') ? 'nav-active-mobile' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'; ?> transition-all duration-200 transform hover:translate-x-1">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>Bootcamps</span>
                    </a>

                    <?php if ($is_logged_in): ?>
                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=my_bootcamps"
                            class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'my_bootcamps') ? 'nav-active-mobile' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'; ?> transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>My Bootcamps</span>
                        </a>

                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=wishlist"
                            class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'wishlist') ? 'nav-active-mobile' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'; ?> transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span>Wishlist</span>
                        </a>

                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=forum"
                            class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'forum') ? 'nav-active-mobile' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'; ?> transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <span>Forum</span>
                        </a>

                        <!-- Account Section -->
                        <div class="border-t border-gray-200 my-3"></div>

                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>views/auth/dashboard/dashboard.php" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>My Profile</span>
                        </a>

                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=cv_builder" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>CV Builder</span>
                        </a>

                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=todolist" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span>Todo List</span>
                        </a>

                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=my_reviews" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            <span>My Reviews</span>
                        </a>

                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=my_orders" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2v-6a2 2 0 00-2-2h-2"></path>
                            </svg>
                            <span>My Orders</span>
                        </a>

                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=logout" class="flex items-center px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Logout</span>
                        </a>
                    <?php else: ?>
                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-3"></div>

                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=login" class="flex items-center px-4 py-3 rounded-lg text-blue-600 hover:bg-blue-50 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Login</span>
                        </a>

                        <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=signup" class="flex items-center px-4 py-3 rounded-lg text-blue-600 hover:bg-blue-50 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            <span>Sign Up</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <?php if (isset($additional_js_header)): ?>
        <?php echo $additional_js_header; ?>
    <?php endif; ?>

    <script>
        // Mobile menu and dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu elements
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeMobileMenu = document.getElementById('close-mobile-menu');
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger');
            const body = document.body;

            // User dropdown elements
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');

            // Mobile menu functions
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

            // Mobile menu event listeners
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (mobileMenu.classList.contains('mobile-menu-show')) {
                        closeMobileMenuFunc();
                    } else {
                        openMobileMenu();
                    }
                });
            }

            if (closeMobileMenu) {
                closeMobileMenu.addEventListener('click', closeMobileMenuFunc);
            }

            if (mobileMenu) {
                mobileMenu.addEventListener('click', function(e) {
                    if (e.target === mobileMenu) {
                        closeMobileMenuFunc();
                    }
                });
            }

            // Close mobile menu on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeMobileMenuFunc();
                }
            });

            // User dropdown functionality
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                        userMenu.classList.add('hidden');
                    }
                });

                // Close dropdown when opening mobile menu
                if (mobileMenuButton) {
                    mobileMenuButton.addEventListener('click', function() {
                        userMenu.classList.add('hidden');
                    });
                }
            }
        });
    </script>