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
    <title>Profile - Campus Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/custom.css">
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
                    <a href="../../../views/about/index.php" class="text-white hover:text-blue-200 transition-colors duration-300">About Us</a>
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
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
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
                    <a href="../../../views/about/index.php" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">About Us</a>

                    <div class="border-t border-blue-700 my-2 pt-2">
                        <a href="dashboard.php" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">My Profile</a>
                        <a href="change_password.php" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">Change Password</a>
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
                                    <label for="alamat_email" class="block text-gray-700 font-medium mb-1">Email Addres</label>
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