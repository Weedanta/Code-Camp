<?php
// Memulai session
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, redirect ke halaman login
    header('Location: ../../../index.php?action=login');
    exit();
}

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
$alamat_email = $_SESSION['alamat_email'];
$no_telepon = isset($_SESSION['no_telepon']) ? $_SESSION['no_telepon'] : '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Code Camp</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/custom.css">
    <link rel="icon" href="../../../assets/images/logo/logo_mobile.png" type="image/x-icon">
    <style>
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

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

        /* Custom styles for mobile menu */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
            opacity: 0;
        }
        
        .mobile-menu-show {
            max-height: 500px;
            opacity: 1;
        }

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
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navbar -->
    <header class="bg-blue-900 shadow-md">
        <div class="container mx-auto px-4 py-2">
            <div class="flex items-center justify-between w-full">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="../../../index.php" class="flex items-center">
                        <img src="../../../assets/images/logo.png" alt="Logo" class="h-16 hidden md:block">
                        <img src="../../../assets/images/logo/logo_mobile.png" alt="Logo" class="md:hidden h-12">
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-8">
                    <a href="../../../index.php" class="text-white hover:text-blue-200 transition-colors duration-300">Home</a>
                    <a href="../../../views/bootcamp/index.php" class="text-white hover:text-blue-200 transition-colors duration-300">Bootcamps</a>
                </nav>

                <!-- User Account -->
                <div class="flex items-center space-x-3">
                    <!-- User Profile Icon -->
                    <div class="relative">
                        <button id="profileButton" class="flex items-center focus:outline-none">
                            <?php if (file_exists("../../../assets/images/users/{$user_id}.jpg")): ?>
                                <img src="../../../assets/images/users/<?php echo $user_id; ?>.jpg" alt="Profile" class="w-10 h-10 rounded-full border-2 border-white">
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white border-2 border-white">
                                    <?php echo substr($name, 0, 1); ?>
                                </div>
                            <?php endif; ?>
                        </button>
                        <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden z-20">
                            <a href="dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            <a href="../../../index.php?action=logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</a>
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

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden mobile-menu w-full mt-2">
                <div class="px-2 pt-4 pb-3 space-y-1 bg-blue-800 rounded-md shadow-lg">
                    <a href="../../../index.php" class="block px-3 py-3 rounded-md text-white hover:bg-blue-700 transition-all duration-200 transform hover:translate-x-1">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Home
                        </span>
                    </a>
                    <a href="../../../views/bootcamp/index.php" class="block px-3 py-3 rounded-md text-white hover:bg-blue-700 transition-all duration-200 transform hover:translate-x-1">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Bootcamps
                        </span>
                    </a>
                    
                    <div class="border-t border-blue-700 my-3 pt-3">
                        <div class="px-3 py-2 text-blue-200 text-sm font-medium">Account</div>
                        <a href="dashboard.php" class="block px-3 py-3 rounded-md text-white hover:bg-blue-700 transition-all duration-200 transform hover:translate-x-1">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                My Profile
                            </span>
                        </a>
                        <a href="change_password.php" class="block px-3 py-3 rounded-md text-white hover:bg-blue-700 transition-all duration-200 transform hover:translate-x-1">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                Change Password
                            </span>
                        </a>
                        <a href="../../../index.php?action=logout" class="block px-3 py-3 rounded-md text-white hover:bg-red-700 transition-all duration-200 transform hover:translate-x-1">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar -->
            <div class="md:w-1/4">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Profile Akun</h2>
                    <div class="space-y-2">
                        <a href="dashboard.php" class="sidebar-item active block text-gray-700 font-medium">Info Personal</a>
                        <a href="change_password.php" class="sidebar-item block text-gray-700 font-medium">Password</a>
                        <a href="delete_account.php" class="sidebar-item block text-gray-700 font-medium text-red-500">Hapus Akun</a>
                    </div>
                </div>
            </div>

            <!-- Profile Info Section -->
            <div class="md:w-3/4">
                <div class="bg-white rounded-lg shadow-md p-6 relative overflow-hidden">
                    <div class="circle-bg"></div>

                    <h2 class="text-xl font-bold text-gray-800 mb-2">Info Personal</h2>
                    <p class="text-gray-600 mb-6">You can update your profile and personal details here.</p>

                    <!-- Alert Messages -->
                    <?php if (isset($_GET['success'])): ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                            <?php
                            $success = $_GET['success'];
                            if ($success == 'profile_updated') {
                                echo "Profil berhasil diperbarui!";
                            } elseif ($success == 'photo_updated') {
                                echo "Foto profil berhasil diperbarui!";
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                            <?php
                            $error = $_GET['error'];
                            if ($error == 'update_failed') {
                                echo "Gagal memperbarui profil. Silakan coba lagi.";
                            } elseif ($error == 'photo_upload_failed') {
                                echo "Gagal mengunggah foto. Periksa format file (JPG/PNG) dan ukuran.";
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Profile Picture -->
                        <div class="text-center">
                            <?php if (file_exists("../../../assets/images/users/{$user_id}.jpg")): ?>
                                <img src="../../../assets/images/users/<?php echo $user_id; ?>.jpg" alt="Profile Picture" class="profile-img mx-auto">
                            <?php else: ?>
                                <div class="profile-img mx-auto bg-blue-500 flex items-center justify-center text-white text-3xl">
                                    <?php echo substr($name, 0, 1); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Profile Info Form -->
                        <div class="flex-grow">
                            <form action="../../../index.php?action=update_profile" method="post">
                                <div class="mb-4">
                                    <label for="name" class="block text-gray-700 font-medium mb-1">Nama</label>
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div class="mb-4">
                                    <label for="alamat_email" class="block text-gray-700 font-medium mb-1">Email Address</label>
                                    <div class="relative">
                                        <input type="email" id="alamat_email" name="alamat_email" value="<?php echo htmlspecialchars($alamat_email); ?>"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="no_telepon" class="block text-gray-700 font-medium mb-1">No. Telp</label>
                                    <input type="tel" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($no_telepon); ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                                        Update
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle with smooth animation
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger');
            
            // Toggle menu visibility with animation
            if (mobileMenu.classList.contains('mobile-menu-show')) {
                // Close menu
                mobileMenu.classList.remove('mobile-menu-show');
                hamburger.classList.remove('active');
            } else {
                // Open menu
                mobileMenu.classList.add('mobile-menu-show');
                hamburger.classList.add('active');
            }
        });

        // Profile dropdown toggle
        document.getElementById('profileButton').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const profileButton = document.getElementById('profileButton');
            const profileDropdown = document.getElementById('profileDropdown');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger');

            // Close profile dropdown if clicked outside
            if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }

            // Close mobile menu if clicked outside
            if (!mobileMenuButton.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.remove('mobile-menu-show');
                hamburger.classList.remove('active');
            }
        });

        // Close mobile menu when window is resized to desktop view
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) { // md breakpoint
                const mobileMenu = document.getElementById('mobile-menu');
                const hamburger = document.getElementById('hamburger');
                mobileMenu.classList.remove('mobile-menu-show');
                hamburger.classList.remove('active');
            }
        });

        // Add smooth scroll effect for mobile menu links
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', function() {
                const mobileMenu = document.getElementById('mobile-menu');
                const hamburger = document.getElementById('hamburger');
                
                // Close menu after clicking a link
                setTimeout(() => {
                    mobileMenu.classList.remove('mobile-menu-show');
                    hamburger.classList.remove('active');
                }, 150);
            });
        });
    </script>
</body>

</html>