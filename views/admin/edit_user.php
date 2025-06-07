<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin Campus Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-gradient {
            background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
        }
        .form-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 sidebar-gradient shadow-xl">
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-black bg-opacity-20">
                <i class="fas fa-graduation-cap text-2xl text-white mr-3"></i>
                <span class="text-xl font-bold text-white">Campus Hub</span>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="admin.php?action=dashboard" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                <a href="admin.php?action=manage_users" class="flex items-center px-4 py-3 text-white bg-indigo-600 rounded-lg">
                    <i class="fas fa-users mr-3"></i>
                    Kelola Users
                </a>
                <a href="admin.php?action=manage_bootcamps" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-laptop-code mr-3"></i>
                    Kelola Bootcamps
                </a>
                <a href="admin.php?action=manage_categories" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-tags mr-3"></i>
                    Kelola Kategori
                </a>
                <a href="admin.php?action=manage_orders" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-shopping-cart mr-3"></i>
                    Kelola Orders
                </a>
                <a href="admin.php?action=manage_reviews" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-star mr-3"></i>
                    Kelola Reviews
                </a>
                <a href="admin.php?action=manage_forum" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-comments mr-3"></i>
                    Kelola Forum
                </a>
                <a href="admin.php?action=manage_settings" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-cog mr-3"></i>
                    Pengaturan
                </a>
            </nav>
            
            <!-- User Info -->
            <div class="p-4 border-t border-gray-600">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
                        <p class="text-xs text-gray-400"><?php echo htmlspecialchars($_SESSION['admin_role']); ?></p>
                    </div>
                </div>
                <a href="admin.php?action=logout" class="mt-3 w-full flex items-center justify-center px-4 py-2 text-sm text-gray-300 hover:text-white bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                <li class="inline-flex items-center">
                                    <a href="admin.php?action=manage_users" class="text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-users mr-2"></i>
                                        Kelola Users
                                    </a>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                        <span class="text-gray-700 font-medium">Edit User</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit User</h1>
                        <p class="text-gray-600">Ubah informasi user: <?php echo htmlspecialchars($user['name']); ?></p>
                    </div>
                    <a href="admin.php?action=manage_users" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6">
            <!-- Alerts -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    <span><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Info Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="form-gradient p-6 text-white text-center">
                            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full mx-auto flex items-center justify-center mb-4">
                                <i class="fas fa-user text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($user['name']); ?></h3>
                            <p class="text-sm opacity-90"><?php echo htmlspecialchars($user['alamat_email']); ?></p>
                            <div class="mt-4 flex justify-center">
                                <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-xs font-medium">
                                    ID: #<?php echo $user['id']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Status</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    <?php echo $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                                        ($user['status'] === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Email Verified</span>
                                <span class="<?php echo $user['email_verified'] ? 'text-green-600' : 'text-red-600'; ?>">
                                    <i class="fas fa-<?php echo $user['email_verified'] ? 'check-circle' : 'times-circle'; ?>"></i>
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Bergabung</span>
                                <span class="text-sm text-gray-900"><?php echo date('d M Y', strtotime($user['created_at'])); ?></span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Terakhir Update</span>
                                <span class="text-sm text-gray-900"><?php echo date('d M Y', strtotime($user['updated_at'])); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- User Statistics -->
                    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Statistik User</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-shopping-cart text-blue-600 w-5"></i>
                                    <span class="text-sm text-gray-600 ml-2">Total Orders</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">0</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-money-bill text-green-600 w-5"></i>
                                    <span class="text-sm text-gray-600 ml-2">Total Spent</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">Rp 0</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-600 w-5"></i>
                                    <span class="text-sm text-gray-600 ml-2">Reviews</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">0</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-comments text-purple-600 w-5"></i>
                                    <span class="text-sm text-gray-600 ml-2">Forum Posts</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Edit Informasi User</h3>
                            <p class="text-sm text-gray-600 mt-1">Perbarui data user dengan informasi yang valid</p>
                        </div>
                        
                        <form method="POST" action="admin.php?action=update_user" class="p-6 space-y-6">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::generateCSRFToken(); ?>">
                            
                            <!-- Personal Information -->
                            <div>
                                <h4 class="text-md font-medium text-gray-900 mb-4">Informasi Pribadi</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-user mr-2"></i>Nama Lengkap
                                        </label>
                                        <input type="text" 
                                               id="name" 
                                               name="name" 
                                               value="<?php echo htmlspecialchars($user['name']); ?>"
                                               required 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                               placeholder="Masukkan nama lengkap">
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-envelope mr-2"></i>Email
                                        </label>
                                        <input type="email" 
                                               id="email" 
                                               name="email" 
                                               value="<?php echo htmlspecialchars($user['alamat_email']); ?>"
                                               required 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                               placeholder="Masukkan email">
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div>
                                <h4 class="text-md font-medium text-gray-900 mb-4">Informasi Kontak</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-phone mr-2"></i>Nomor Telepon
                                        </label>
                                        <input type="tel" 
                                               id="phone" 
                                               name="phone" 
                                               value="<?php echo htmlspecialchars($user['no_telepon'] ?? ''); ?>"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                               placeholder="Masukkan nomor telepon">
                                    </div>
                                    
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-user-check mr-2"></i>Status
                                        </label>
                                        <select id="status" 
                                                name="status" 
                                                required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            <option value="suspended" <?php echo $user['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                                <div class="flex space-x-4">
                                    <button type="submit" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                                        <i class="fas fa-save mr-2"></i>
                                        Simpan Perubahan
                                    </button>
                                    
                                    <a href="admin.php?action=manage_users" 
                                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                                        <i class="fas fa-times mr-2"></i>
                                        Batal
                                    </a>
                                </div>
                                
                                <button type="button" 
                                        onclick="resetPassword(<?php echo $user['id']; ?>)"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg font-medium transition-colors flex items-center">
                                    <i class="fas fa-key mr-2"></i>
                                    Reset Password
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Danger Zone -->
                    <div class="mt-6 bg-white rounded-lg shadow-sm border border-red-200">
                        <div class="px-6 py-4 border-b border-red-200 bg-red-50">
                            <h3 class="text-lg font-semibold text-red-900">Danger Zone</h3>
                            <p class="text-sm text-red-700 mt-1">Tindakan di bawah ini tidak dapat dibatalkan</p>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-md font-medium text-gray-900">Hapus User</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Menghapus user akan menghilangkan semua data termasuk orders, reviews, dan aktivitas forum.
                                    </p>
                                </div>
                                <button type="button" 
                                        onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>')"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                    <i class="fas fa-trash mr-2"></i>
                                    Hapus User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Hapus User</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus user <span id="deleteUserName" class="font-medium"></span>? 
                        Semua data termasuk orders, reviews, dan aktivitas forum akan ikut terhapus.
                    </p>
                </div>
                <div class="items-center px-4 py-3 flex space-x-4">
                    <button id="cancelDelete" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                    <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <i class="fas fa-key text-yellow-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Reset Password</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Password akan direset ke password default: <code class="bg-gray-100 px-2 py-1 rounded">password123</code>
                    </p>
                    <p class="text-sm text-gray-500 mt-2">
                        User akan diminta untuk mengganti password saat login berikutnya.
                    </p>
                </div>
                <div class="items-center px-4 py-3 flex space-x-4">
                    <button id="cancelReset" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                    <button id="confirmReset" class="px-4 py-2 bg-yellow-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let userToDelete = null;
        let userToResetPassword = null;

        function deleteUser(id, name) {
            userToDelete = id;
            document.getElementById('deleteUserName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function resetPassword(id) {
            userToResetPassword = id;
            document.getElementById('resetPasswordModal').classList.remove('hidden');
        }

        // Delete modal handlers
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteModal').classList.add('hidden');
            userToDelete = null;
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (userToDelete) {
                window.location.href = `admin.php?action=delete_user&id=${userToDelete}`;
            }
        });

        // Reset password modal handlers
        document.getElementById('cancelReset').addEventListener('click', function() {
            document.getElementById('resetPasswordModal').classList.add('hidden');
            userToResetPassword = null;
        });

        document.getElementById('confirmReset').addEventListener('click', function() {
            if (userToResetPassword) {
                // You would implement this endpoint
                window.location.href = `admin.php?action=reset_user_password&id=${userToResetPassword}`;
            }
        });

        // Close modals when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                userToDelete = null;
            }
        });

        document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                userToResetPassword = null;
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (name.length < 2) {
                e.preventDefault();
                alert('Nama harus minimal 2 karakter');
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Format email tidak valid');
                return;
            }
        });

        // Auto-focus on name field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('name').focus();
        });
    </script>
</body>
</html>