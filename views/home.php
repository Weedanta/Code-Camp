<?php
// Cek session
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['name'] : '';
$user_id = $is_logged_in ? $_SESSION['user_id'] : '';

// Get sample bootcamps for featured section
require_once 'config/database.php';
require_once 'models/Bootcamp.php';
require_once 'models/Category.php';

$database = new Database();
$db = $database->getConnection();

$bootcamp = new Bootcamp($db);
$category = new Category($db);

// Get featured bootcamps (limit to 3)
$stmt = $bootcamp->readAll(3, 0);
$featured_bootcamps = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories
$categoryStmt = $category->readAll();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Hub - Temukan Bootcamp IT Terbaik</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="icon" href="assets/images/logo/logo_mobile.png" type="image/x-icon">
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
            /* Space for navbar */
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

            .hero h1 {
                font-size: 1.875rem;
                line-height: 2.25rem;
            }
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm relative z-50">
        <div class="container mx-auto px-4 py-1">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center">
                        <img src="assets/images/logo.png" alt="Logo" class="h-16 hidden md:block">
                        <img src="assets/images/logo/logo_mobile.png" alt="Logo" class="md:hidden h-12">
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-blue-600 font-medium">Home</a>
                    <a href="index.php?action=bootcamps" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">Bootcamps</a>

                    <?php if ($is_logged_in): ?>
                        <a href="index.php?action=my_bootcamps" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">My Bootcamps</a>
                        <a href="index.php?action=wishlist" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">Wishlist</a>
                    <?php endif; ?>
                </nav>

                <!-- User Account / Login Buttons -->
                <div class="flex items-center space-x-3">
                    <?php if ($is_logged_in): ?>
                        <!-- User Profile Icon When Logged In -->
                        <div class="relative">
                            <button id="profileButton" class="flex items-center focus:outline-none">
                                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white border-2 border-blue-100">
                                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                                </div>
                            </button>
                            <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden z-20">
                                <a href="views/auth/dashboard/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                                <a href="index.php?action=my_orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                                <a href="index.php?action=logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Login/Signup Buttons When Not Logged In -->
                        <a href="index.php?action=login" class="px-4 py-2 rounded-md border border-blue-600 text-blue-600 hover:bg-blue-50 transition-colors duration-300">Login</a>
                        <a href="index.php?action=signup" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-300">Sign Up</a>
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


                    <a href="index.php?action=bootcamps" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 transform hover:translate-x-1">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>Bootcamps</span>
                    </a>

                    <?php if ($is_logged_in): ?>
                        <a href="index.php?action=my_bootcamps" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>My Bootcamps</span>
                        </a>

                        <a href="index.php?action=wishlist" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span>Wishlist</span>
                        </a>


                        <!-- Account Section -->


                        <a href="views/auth/dashboard/dashboard.php" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>My Profile</span>
                        </a>

                        <a href="index.php?action=my_orders" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2v-6a2 2 0 00-2-2h-2"></path>
                            </svg>
                            <span>My Orders</span>
                        </a>

                        <a href="index.php?action=logout" class="flex items-center px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Logout</span>
                        </a>
                    <?php else: ?>
                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-3"></div>

                        <a href="index.php?action=login" class="flex items-center px-4 py-3 rounded-lg text-blue-600 hover:bg-blue-50 transition-all duration-200 transform hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Login</span>
                        </a>

                        <a href="index.php?action=signup" class="flex items-center px-4 py-3 rounded-lg text-blue-600 hover:bg-blue-50 transition-all duration-200 transform hover:translate-x-1">
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

    <!-- Hero Section -->
    <section class="bg-blue-900 text-white">
        <div class="container mx-auto px-4 py-16 md:py-20">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 animate__animated animate__fadeInLeft">
                    <h1 class="text-3xl md:text-4xl font-bold mb-4">
                        <?php if ($is_logged_in): ?>
                            Halo, <?php echo htmlspecialchars($user_name); ?>! <br> Sudah siap untuk meningkatkan karirmu hari ini?
                        <?php else: ?>
                            Wujudkan Potensimu Melalui Pengalaman yang Tak Terbatas!
                        <?php endif; ?>
                    </h1>
                    <p class="mb-6">Kembangkan dirimu sekarang juga melalui program terbaik dari bootcamp terpercaya.</p>
                    <a href="index.php?action=bootcamps" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors duration-300">Mulai Sekarang</a>

                    <div class="flex mt-8 space-x-8">
                        <div class="text-center">
                            <div class="text-2xl font-bold">20+</div>
                            <div class="text-sm text-blue-200">Bootcamp Tersedia</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">4+</div>
                            <div class="text-sm text-blue-200">Kategori</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">5+</div>
                            <div class="text-sm text-blue-200">Partner Bootcamp</div>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 mt-8 md:mt-0 animate__animated animate__fadeInRight flex justify-end">
                    <img src="assets/images/hero-image.png" alt="Coding Bootcamp" class="rounded-lg shadow-lg lg:ml-12 w-full md:w-4/5 h-auto max-w-lg mx-auto md:mx-0">
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-center text-2xl font-bold mb-8">KATEGORI</h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 text-center">
                <?php foreach ($categories as $category): ?>
                    <a href="index.php?action=bootcamp_category&id=<?php echo $category['id']; ?>" class="p-4 hover:shadow-md rounded-lg transition-shadow duration-300 group">
                        <div class="w-16 h-16 mx-auto mb-3 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-300 overflow-hidden p-2">
                            <?php
                            // Generate icon filename based on category name (lowercase, replace spaces with hyphens)
                            $icon_name = strtolower(str_replace([' ', '/', '&'], ['-', '-', 'and'], $category['name']));

                            // Comprehensive icon mapping for common categories
                            $icon_mapping = [
                                'web-development' => 'web.png',
                                'mobile-development' => 'mobile.png',
                                'data-science' => 'data.png',
                                'digital-marketing' => 'marketing.png',
                                'ui-ux-design' => 'design.png',
                                'uiand-ux-design' => 'design.png',
                                'cybersecurity' => 'security.png',
                                'cloud-computing' => 'cloud.png',
                                'artificial-intelligence' => 'ai.png',
                                'machine-learning' => 'ai.png',
                                'game-development' => 'game.png',
                                'blockchain' => 'blockchain.png',
                                'devops' => 'devops.png',
                                'software-engineering' => 'software.png',
                                'database' => 'database.png',
                                'networking' => 'network.png',
                                'programming' => 'code.png',
                                'frontend' => 'frontend.png',
                                'backend' => 'backend.png',
                                'fullstack' => 'fullstack.png'
                            ];

                            // Try to find the appropriate icon
                            $icon_path = null;

                            // First, try exact match with processed name
                            if (file_exists("assets/images/icons/{$icon_name}.png")) {
                                $icon_path = "assets/images/icons/{$icon_name}.png";
                            }
                            // Then try mapping
                            elseif (isset($icon_mapping[$icon_name]) && file_exists("assets/images/icons/" . $icon_mapping[$icon_name])) {
                                $icon_path = "assets/images/icons/" . $icon_mapping[$icon_name];
                            }
                            // Try with original name
                            elseif (file_exists("assets/images/icons/" . strtolower($category['name']) . ".png")) {
                                $icon_path = "assets/images/icons/" . strtolower($category['name']) . ".png";
                            }

                            if ($icon_path): ?>
                                <img src="<?php echo $icon_path; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-10 h-10 object-contain">
                            <?php else: ?>
                                <!-- Fallback to Font Awesome icon with better mapping -->
                                <?php
                                $fa_icons = [
                                    'web' => 'fa-globe',
                                    'mobile' => 'fa-mobile-alt',
                                    'data' => 'fa-chart-bar',
                                    'marketing' => 'fa-bullhorn',
                                    'design' => 'fa-paint-brush',
                                    'security' => 'fa-shield-alt',
                                    'cloud' => 'fa-cloud',
                                    'ai' => 'fa-robot',
                                    'game' => 'fa-gamepad',
                                    'blockchain' => 'fa-link'
                                ];

                                $fa_class = 'fa-graduation-cap'; // default
                                foreach ($fa_icons as $key => $icon) {
                                    if (strpos($icon_name, $key) !== false) {
                                        $fa_class = $icon;
                                        break;
                                    }
                                }
                                ?>
                                <i class="fas <?php echo $fa_class; ?> text-blue-600 text-xl"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-medium text-sm"><?php echo htmlspecialchars($category['name']); ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Bootcamps Section -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-center text-2xl font-bold mb-8">Jelajahi Bootcamp Unggulan</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($featured_bootcamps as $bootcamp): ?>
                    <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                        <!-- Ubah semua gambar bootcamp ke ngoding.jpg -->
                        <img src="assets/images/ngoding.jpg"
                            alt="<?php echo htmlspecialchars($bootcamp['title']); ?>"
                            class="w-full h-48 object-cover">

                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2">
                                <?php echo htmlspecialchars($bootcamp['title']); ?>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                <?php echo htmlspecialchars(substr($bootcamp['description'], 0, 100)) . '...'; ?>
                            </p>

                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                    <span class="text-gray-600 text-xs font-medium">
                                        <?php echo strtoupper(substr($bootcamp['instructor_name'], 0, 1)); ?>
                                    </span>
                                </div>
                                <span class="text-sm text-gray-700">
                                    Instructor: <?php echo htmlspecialchars($bootcamp['instructor_name']); ?>
                                </span>
                            </div>

                            <div class="flex justify-between text-sm text-gray-500 mb-4">
                                <div>
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <?php echo date('d M Y', strtotime($bootcamp['start_date'])); ?>
                                </div>
                                <div>
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo htmlspecialchars($bootcamp['duration']); ?>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <?php if (!empty($bootcamp['discount_price']) && $bootcamp['discount_price'] > $bootcamp['price']): ?>
                                        <span class="text-gray-500 line-through text-sm">
                                            Rp <?php echo number_format($bootcamp['discount_price'], 0, ',', '.'); ?>
                                        </span><br>
                                    <?php endif; ?>
                                    <span class="text-blue-600 font-bold">
                                        Rp <?php echo number_format($bootcamp['price'], 0, ',', '.'); ?>
                                    </span>
                                </div>
                                <a href="index.php?action=bootcamp_detail&id=<?php echo $bootcamp['id']; ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-8">
                <a href="index.php?action=bootcamps" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors duration-300">
                    Lihat Semua Bootcamp
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-center text-2xl font-bold mb-8">Mengapa Memilih Code Camp?</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-award text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Bootcamp Berkualitas</h3>
                    <p class="text-gray-600">Semua bootcamp kami dipilih dengan ketat dan disusun oleh instruktur berpengalaman di bidangnya.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-rocket text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Belajar dengan Kecepatan Anda</h3>
                    <p class="text-gray-600">Akses bootcamp kapan saja dan di mana saja sesuai dengan jadwal dan kecepatan belajar Anda.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-friends text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Komunitas Pendukung</h3>
                    <p class="text-gray-600">Dapatkan dukungan dari komunitas pembelajar dan mentor yang selalu siap membantu Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-12 bg-blue-900 text-white">
        <div class="container mx-auto px-4">
            <h2 class="text-center text-2xl font-bold mb-8">Apa Kata Mereka?</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-blue-800 p-6 rounded-lg">
                    <div class="text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="italic mb-4">"Bootcamp UI/UX Design di Code Camp sangat membantu saya memulai karir sebagai UI/UX Designer. Materinya komprehensif dan instrukturnya sangat berpengalaman."</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center mr-3 text-white font-medium">A</div>
                        <div>
                            <div class="font-medium">Ahmad Rizki</div>
                            <div class="text-sm text-blue-300">UI/UX Designer</div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-800 p-6 rounded-lg">
                    <div class="text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="italic mb-4">"Saya telah mengikuti bootcamp Data Analysis dan hasilnya luar biasa. Sekarang saya bisa menganalisis data dengan lebih efektif dan mendapatkan insight yang berharga."</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center mr-3 text-white font-medium">S</div>
                        <div>
                            <div class="font-medium">Sari Indah</div>
                            <div class="text-sm text-blue-300">Data Analyst</div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-800 p-6 rounded-lg">
                    <div class="text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="italic mb-4">"Bootcamp Digital Marketing sangat praktis dan relevan dengan kebutuhan industri saat ini. Sekarang saya bisa menjalankan kampanye marketing yang lebih efektif."</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center mr-3 text-white font-medium">B</div>
                        <div>
                            <div class="font-medium">Budi Santoso</div>
                            <div class="text-sm text-blue-300">Digital Marketer</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Siap Untuk Memulai Perjalanan Belajar Anda?</h2>
            <p class="text-gray-600 mb-8 max-w-2xl mx-auto">Bergabunglah dengan ribuan pembelajar lainnya dan kembangkan keterampilan Anda melalui bootcamp berkualitas tinggi.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="index.php?action=bootcamps" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors duration-300">
                    Jelajahi Bootcamp
                </a>
                <a href="index.php?action=signup" class="px-6 py-3 border border-blue-600 text-blue-600 font-medium rounded-md hover:bg-blue-50 transition-colors duration-300">
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="assets/images/logo.png" alt="Logo" class="h-16 hidden md:block">
                        <img src="assets/images/logo/logo_mobile.png" alt="Logo" class="md:hidden h-12">
                    </div>
                    <p class="text-blue-200 mb-4">Temukan bootcamp IT terbaik untuk mengembangkan keterampilan dan mempercepat karier Anda dalam dunia teknologi.</p>
                    <p class="text-blue-200">Jl. Pendidikan No. 123, Malang<br>Jawa Timur, Indonesia, 65145</p>
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-4">Kategori Populer</h3>
                    <ul class="space-y-2">
                        <?php foreach (array_slice($categories, 0, 5) as $category): ?>
                            <li class="flex items-center">
                                <div class="w-6 h-6 mr-2 bg-blue-800 rounded-full flex items-center justify-center">
                                    <?php
                                    $icon_name = strtolower(str_replace([' ', '/', '&'], ['-', '-', 'and'], $category['name']));
                                    $icon_mapping = [
                                        'web-development' => 'web.png',
                                        'mobile-development' => 'mobile.png',
                                        'data-science' => 'data.png',
                                        'digital-marketing' => 'marketing.png',
                                        'ui-ux-design' => 'design.png',
                                        'uiand-ux-design' => 'design.png'
                                    ];

                                    $icon_path = null;
                                    if (file_exists("assets/images/icons/{$icon_name}.png")) {
                                        $icon_path = "assets/images/icons/{$icon_name}.png";
                                    } elseif (isset($icon_mapping[$icon_name]) && file_exists("assets/images/icons/" . $icon_mapping[$icon_name])) {
                                        $icon_path = "assets/images/icons/" . $icon_mapping[$icon_name];
                                    }

                                    if ($icon_path): ?>
                                        <img src="<?php echo $icon_path; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-4 h-4 object-contain filter brightness-0 invert">
                                    <?php else: ?>
                                        <i class="fas fa-chevron-right text-blue-200 text-xs"></i>
                                    <?php endif; ?>
                                </div>
                                <a href="index.php?action=bootcamp_category&id=<?php echo $category['id']; ?>" class="text-blue-200 hover:text-white transition-colors">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-4">Informasi</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-blue-200 hover:text-white">Home</a></li>
                        <li><a href="index.php?action=bootcamps" class="text-blue-200 hover:text-white">Bootcamp</a></li>

                        <li><a href="#" class="text-blue-200 hover:text-white">FAQ</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Contact Us</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-blue-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-blue-200 text-sm">&copy; 2025 Code Camp. All Rights Reserved.</p>

                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-blue-200 hover:text-white">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-blue-200 hover:text-white">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-blue-200 hover:text-white">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-blue-200 hover:text-white">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle with hamburger animation
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger');

            // Toggle menu visibility with animation
            if (mobileMenu.classList.contains('mobile-menu-show')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        // Close button for mobile menu
        document.getElementById('close-mobile-menu').addEventListener('click', function() {
            closeMobileMenu();
        });

        // Functions to open/close mobile menu
        function openMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger');
            const body = document.body;

            mobileMenu.classList.add('mobile-menu-show');
            hamburger.classList.add('active');
            body.classList.add('body-scroll-lock');
        }

        function closeMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger');
            const body = document.body;

            mobileMenu.classList.remove('mobile-menu-show');
            hamburger.classList.remove('active');
            body.classList.remove('body-scroll-lock');
        }

        // Profile dropdown toggle (if logged in)
        <?php if ($is_logged_in): ?>
            document.getElementById('profileButton').addEventListener('click', function(e) {
                e.stopPropagation();
                const dropdown = document.getElementById('profileDropdown');
                dropdown.classList.toggle('hidden');
            });
        <?php endif; ?>

        // Close dropdown and mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuContent = mobileMenu.querySelector('.mobile-menu-content');

            <?php if ($is_logged_in): ?>
                const profileButton = document.getElementById('profileButton');
                const profileDropdown = document.getElementById('profileDropdown');

                // Close profile dropdown if clicked outside
                if (profileButton && profileDropdown) {
                    if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                }
            <?php endif; ?>

            // Close mobile menu if clicked on backdrop (not on menu content)
            if (mobileMenu.classList.contains('mobile-menu-show') &&
                !mobileMenuButton.contains(e.target) &&
                !mobileMenuContent.contains(e.target)) {
                closeMobileMenu();
            }
        });

        // Close mobile menu when window is resized to desktop view
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) { // md breakpoint
                closeMobileMenu();
            }
        });

        // Close mobile menu when clicking menu links
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', function() {
                setTimeout(() => {
                    closeMobileMenu();
                }, 150);
            });
        });

        // Prevent scrolling when mobile menu is open
        document.getElementById('mobile-menu').addEventListener('touchmove', function(e) {
            if (this.classList.contains('mobile-menu-show')) {
                e.preventDefault();
            }
        }, {
            passive: false
        });

        // Handle escape key to close menu
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenu.classList.contains('mobile-menu-show')) {
                    closeMobileMenu();
                }

                <?php if ($is_logged_in): ?>
                    const profileDropdown = document.getElementById('profileDropdown');
                    if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                        profileDropdown.classList.add('hidden');
                    }
                <?php endif; ?>
            }
        });
    </script>
</body>

</html>