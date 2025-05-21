<?php
// Memulai session
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, redirect ke halaman login
    header('Location: index.php?action=login');
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
    <title>Change Password - Campus Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .sidebar-item {
            padding: 12px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .sidebar-item:hover, .sidebar-item.active {
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
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="index.php" class="flex items-center">
                    <span class="text-white font-bold text-xl">Campus</span>
                    <span class="bg-white text-blue-600 px-2 py-1 rounded font-bold text-xl">Hub</span>
                </a>
                
                <nav class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-white hover:text-blue-200 transition-colors duration-300">Home</a>
                    <a href="#" class="text-white hover:text-blue-200 transition-colors duration-300">MyEvents</a>
                    <a href="#" class="text-white hover:text-blue-200 transition-colors duration-300">About Us</a>
                </nav>
                
                <!-- User Profile Icon -->
                <div class="relative">
                    <button id="profileButton" class="flex items-center focus:outline-none">
                        <?php if (file_exists("assets/images/users/{$user_id}.jpg")): ?>
                            <img src="assets/images/users/<?php echo $user_id; ?>.jpg" alt="Profile" class="w-10 h-10 rounded-full border-2 border-white">
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white border-2 border-white">
                                <?php echo substr($name, 0, 1); ?>
                            </div>
                        <?php endif; ?>
                    </button>
                    <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden z-10">
                        <a href="dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                        <a href="index.php?action=logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</a>
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
                        <a href="views/auth/change_password.php" class="sidebar-item active block text-gray-700 font-medium">Password</a>
                        <a href="#" class="sidebar-item block text-gray-700 font-medium text-red-500">Hapus Akun</a>
                    </div>
                </div>
            </div>
            
            <!-- Change Password Section -->
            <div class="md:w-3/4">
                <div class="bg-white rounded-lg shadow-md p-6 relative overflow-hidden">
                    <div class="circle-bg"></div>
                    
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Password</h2>
                    <p class="text-gray-600 mb-6">Change your password to keep your account secure.</p>
                    
                    <!-- Alert Messages -->
                    <?php if(isset($_GET['success'])): ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                            <?php 
                                $success = $_GET['success'];
                                if($success == 'password_updated') {
                                    echo "Password berhasil diperbarui!";
                                }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_GET['error'])): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                            <?php 
                                $error = $_GET['error'];
                                if($error == 'password_update_failed') {
                                    echo "Password lama salah atau terjadi kesalahan. Silakan coba lagi.";
                                }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Password Change Form -->
                    <form action="index.php?action=update_password" method="post" id="passwordForm">
                        <div class="mb-4">
                            <label for="current_password" class="block text-gray-700 font-medium mb-1">Password Saat Ini</label>
                            <div class="relative">
                                <input type="password" id="current_password" name="current_password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                                <button type="button" class="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500" data-target="current_password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="new_password" class="block text-gray-700 font-medium mb-1">Password Baru</label>
                            <div class="relative">
                                <input type="password" id="new_password" name="new_password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                                <button type="button" class="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500" data-target="new_password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Password harus memiliki minimal 6 karakter</p>
                        </div>
                        
                        <div class="mb-6">
                            <label for="confirm_password" class="block text-gray-700 font-medium mb-1">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                                <button type="button" class="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500" data-target="confirm_password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                                Update Password
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

        // Toggle password visibility
        document.querySelectorAll('.password-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Change icon
                if (type === 'password') {
                    this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>';
                } else {
                    this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" /><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" /></svg>';
                }
            });
        });

        // Form validation
        document.getElementById('passwordForm').addEventListener('submit', function(event) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                event.preventDefault();
                alert('Password baru dan konfirmasi password tidak cocok!');
            }
            
            if (newPassword.length < 6) {
                event.preventDefault();
                alert('Password minimal 6 karakter!');
            }
        });
    </script>
</body>
</html>