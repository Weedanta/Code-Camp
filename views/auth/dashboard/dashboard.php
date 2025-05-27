<?php
// Set variabel untuk header dashboard
$current_dashboard_page = 'profile';
$page_title = 'Profile - Code Camp';

// Include header dashboard
include_once __DIR__ . '/../../includes/dashboard_header.php';

// Ambil data user tambahan jika diperlukan
$alamat_email = isset($_SESSION['alamat_email']) ? $_SESSION['alamat_email'] : '';
$no_telepon = isset($_SESSION['no_telepon']) ? $_SESSION['no_telepon'] : '';
?>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar -->
        <div class="md:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Profile Akun</h2>
                <div class="space-y-2">
                    <a href="dashboard.php" class="sidebar-item active block text-gray-700 font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Info Personal
                    </a>
                    <a href="change_password.php" class="sidebar-item block text-gray-700 font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7 6h-2m-6 0H4a3 3 0 01-3-3V9a3 3 0 013-3h2.25M15 7V4.5A2.5 2.5 0 0012.5 2h-1A2.5 2.5 0 009 4.5V7m6 0v3H9V7"></path>
                        </svg>
                        Password
                    </a>
                    <a href="delete_account.php" class="sidebar-item block text-gray-700 font-medium text-red-500">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus Akun
                    </a>
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
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?php
                            $success = $_GET['success'];
                            if ($success == 'profile_updated') {
                                echo "Profil berhasil diperbarui!";
                            } elseif ($success == 'photo_updated') {
                                echo "Foto profil berhasil diperbarui!";
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <?php
                            $error = $_GET['error'];
                            if ($error == 'update_failed') {
                                echo "Gagal memperbarui profil. Silakan coba lagi.";
                            } elseif ($error == 'photo_upload_failed') {
                                echo "Gagal mengunggah foto. Periksa format file (JPG/PNG) dan ukuran.";
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Profile Picture -->
                    <div class="text-center">
                        <?php if (file_exists("../../../assets/images/users/{$user_id}.jpg")): ?>
                            <img src="../../../assets/images/users/<?php echo $user_id; ?>.jpg" alt="Profile Picture" class="profile-img mx-auto">
                        <?php else: ?>
                            <div class="profile-img mx-auto bg-blue-500 flex items-center justify-center text-white text-3xl font-bold">
                                <?php echo strtoupper(substr($name, 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Upload Photo Button -->
                        <form action="../../../index.php?action=upload_photo" method="post" enctype="multipart/form-data" class="mt-4">
                            <label for="photo" class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-5l-2-2H5a2 2 0 00-2 2z"></path>
                                </svg>
                                Change Photo
                            </label>
                            <input type="file" id="photo" name="photo" accept="image/*" class="hidden" onchange="this.form.submit()">
                        </form>
                    </div>

                    <!-- Profile Info Form -->
                    <div class="flex-grow">
                        <form action="../../../index.php?action=update_profile" method="post">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-gray-700 font-medium mb-1">Nama Lengkap</label>
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                                </div>

                                <div>
                                    <label for="alamat_email" class="block text-gray-700 font-medium mb-1">Email Address</label>
                                    <div class="relative">
                                        <input type="email" id="alamat_email" name="alamat_email" value="<?php echo htmlspecialchars($alamat_email); ?>"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="no_telepon" class="block text-gray-700 font-medium mb-1">No. Telepon</label>
                                    <input type="number" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($no_telepon); ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                        placeholder="08xxxxxxxxxx">
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Status</label>
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-between items-center">
                                <p class="text-sm text-gray-500">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Last updated: <?php echo date('F j, Y \a\t g:i A'); ?>
                                </p>
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 transform hover:scale-105">
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>