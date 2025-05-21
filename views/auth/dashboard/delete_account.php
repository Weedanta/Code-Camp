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
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account - Code Camp</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/custom.css">
    <link rel="icon" href="../../../assets/images/logo/logo_mobile.png" type="image/x-icon">
    <style>
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
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navbar untuk change_password.php dan delete_account.php -->
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
                        <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden z-10">
                            <a href="dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                            <a href="change_password.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Change Password</a>
                            <a href="../../../index.php?action=logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>

                    <!-- Mobile Menu Toggle Button -->
                    <button id="mobile-menu-button" class="md:hidden flex items-center p-2 rounded-md text-white hover:bg-blue-800 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden w-full mt-2">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-blue-800 rounded-md">
                    <a href="../../../index.php" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">Home</a>
                    <a href="../../../views/bootcamp/index.php" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">Bootcamps</a>
                  

                    <div class="border-t border-blue-700 my-2 pt-2">
                        <a href="dashboard.php" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">My Profile</a>
                        <a href="change_password.php" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">Change Password</a>
                        <a href="delete_account.php" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">Delete Account</a>
                        <a href="../../../index.php?action=logout" class="block px-3 py-2 rounded-md text-white hover:bg-red-800">Logout</a>
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
                        <a href="dashboard.php" class="sidebar-item block text-gray-700 font-medium">Info Personal</a>
                        <a href="change_password.php" class="sidebar-item block text-gray-700 font-medium">Password</a>
                        <a href="delete_account.php" class="sidebar-item active block text-gray-700 font-medium text-red-500">Hapus Akun</a>
                    </div>
                </div>
            </div>

            <!-- Delete Account Section -->
            <div class="md:w-3/4">
                <div class="bg-white rounded-lg shadow-md p-6 relative overflow-hidden">
                    <div class="circle-bg"></div>

                    <h2 class="text-xl font-bold text-gray-800 mb-2">Hapus Akun</h2>
                    <p class="text-gray-600 mb-6">Once you delete your account, there is no going back. Please be certain.</p>

                    <!-- Warning Box -->
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                        <div class="flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="font-bold">Warning: This action cannot be undone.</p>
                                <p>When you delete your account, all of your data will be permanently removed. This includes your profile information, photo, and activity history.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Account Form -->
                    <form action="../../../index.php?action=delete_account" method="post" id="deleteAccountForm" class="space-y-4">
                        <div class="flex items-center mb-4">
                            <input type="checkbox" id="confirm_delete" name="confirm_delete" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded" required>
                            <label for="confirm_delete" class="ml-2 block text-gray-700">I understand that this action is irreversible.</label>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="dashboard.php" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition duration-200">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200">
                                Delete Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle profile dropdown
        document.getElementById('profileButton').addEventListener('click', function() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const profileButton = document.getElementById('profileButton');
            const profileDropdown = document.getElementById('profileDropdown');

            if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.add('hidden');
            }
        });

        // Confirmation before submitting
        document.getElementById('deleteAccountForm').addEventListener('submit', function(event) {
            if (!confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                event.preventDefault();
            }
        });

        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
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

            if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });
    </script>
</body>

</html>