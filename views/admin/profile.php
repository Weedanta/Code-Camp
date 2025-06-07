<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - Campus Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-gradient {
            background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
        }
        .profile-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .form-gradient {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include_once 'views/admin/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Profil Admin</h1>
                        <p class="text-gray-600">Kelola informasi akun dan pengaturan keamanan</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="toggleEditMode()" id="editToggle" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Profile
                        </button>
                    </div>
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
                <!-- Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="profile-gradient p-6 text-white text-center">
                            <div class="w-24 h-24 bg-white bg-opacity-20 rounded-full mx-auto flex items-center justify-center mb-4">
                                <i class="fas fa-user text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></h3>
                            <p class="text-sm opacity-90 capitalize"><?php echo htmlspecialchars($_SESSION['admin_role']); ?></p>
                            <div class="mt-4 flex justify-center">
                                <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-xs font-medium">
                                    ID: #<?php echo $_SESSION['admin_id']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Status</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Last Login</span>
                                <span class="text-sm text-gray-900"><?php echo date('d M Y H:i'); ?></span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Member Since</span>
                                <span class="text-sm text-gray-900"><?php echo date('d M Y', strtotime($admin['created_at'] ?? 'now')); ?></span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Total Sessions</span>
                                <span class="text-sm text-gray-900"><?php echo rand(100, 500); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Admin Activity</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-users text-blue-600 w-5"></i>
                                    <span class="text-sm text-gray-600 ml-2">Users Managed</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900"><?php echo rand(50, 200); ?></span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-laptop-code text-green-600 w-5"></i>
                                    <span class="text-sm text-gray-600 ml-2">Bootcamps Created</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900"><?php echo rand(10, 50); ?></span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-600 w-5"></i>
                                    <span class="text-sm text-gray-600 ml-2">Reviews Moderated</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900"><?php echo rand(100, 300); ?></span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-cog text-purple-600 w-5"></i>
                                    <span class="text-sm text-gray-600 ml-2">Settings Changed</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900"><?php echo rand(20, 80); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Profile</h3>
                            <p class="text-sm text-gray-600 mt-1">Update informasi akun dan preferensi</p>
                        </div>
                        
                        <form id="profileForm" method="POST" action="admin.php?action=update_profile" class="p-6 space-y-6">
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
                                               value="<?php echo htmlspecialchars($_SESSION['admin_name']); ?>"
                                               disabled 
                                               required 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors disabled:bg-gray-50"
                                               placeholder="Masukkan nama lengkap">
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-envelope mr-2"></i>Email
                                        </label>
                                        <input type="email" 
                                               id="email" 
                                               name="email" 
                                               value="<?php echo htmlspecialchars($_SESSION['admin_email']); ?>"
                                               disabled 
                                               required 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors disabled:bg-gray-50"
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
                                               value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>"
                                               disabled 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors disabled:bg-gray-50"
                                               placeholder="Masukkan nomor telepon">
                                    </div>
                                    
                                    <div>
                                        <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-building mr-2"></i>Departemen
                                        </label>
                                        <select id="department" 
                                                name="department" 
                                                disabled
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors disabled:bg-gray-50">
                                            <option value="IT">IT Department</option>
                                            <option value="Education">Education</option>
                                            <option value="Marketing">Marketing</option>
                                            <option value="Finance">Finance</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Preferences -->
                            <div>
                                <h4 class="text-md font-medium text-gray-900 mb-4">Preferensi</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-clock mr-2"></i>Zona Waktu
                                        </label>
                                        <select id="timezone" 
                                                name="timezone" 
                                                disabled
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors disabled:bg-gray-50">
                                            <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                            <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                            <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-language mr-2"></i>Bahasa
                                        </label>
                                        <select id="language" 
                                                name="language" 
                                                disabled
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors disabled:bg-gray-50">
                                            <option value="id">Bahasa Indonesia</option>
                                            <option value="en">English</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Notifications -->
                            <div>
                                <h4 class="text-md font-medium text-gray-900 mb-4">Notifikasi</h4>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label for="email_notifications" class="text-sm font-medium text-gray-700">
                                                Email Notifications
                                            </label>
                                            <p class="text-xs text-gray-500">Terima notifikasi via email</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="email_notifications" name="email_notifications" value="1" 
                                                   checked disabled class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-disabled:opacity-50"></div>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label for="sms_notifications" class="text-sm font-medium text-gray-700">
                                                SMS Notifications
                                            </label>
                                            <p class="text-xs text-gray-500">Terima notifikasi via SMS</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="sms_notifications" name="sms_notifications" value="1" 
                                                   disabled class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-disabled:opacity-50"></div>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label for="system_alerts" class="text-sm font-medium text-gray-700">
                                                System Alerts
                                            </label>
                                            <p class="text-xs text-gray-500">Alert penting sistem</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="system_alerts" name="system_alerts" value="1" 
                                                   checked disabled class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-disabled:opacity-50"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div id="formActions" class="hidden flex items-center justify-between pt-6 border-t border-gray-200">
                                <div class="flex space-x-4">
                                    <button type="submit" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                                        <i class="fas fa-save mr-2"></i>
                                        Simpan Perubahan
                                    </button>
                                    
                                    <button type="button" 
                                            onclick="cancelEdit()"
                                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                                        <i class="fas fa-times mr-2"></i>
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Security Settings -->
                    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Keamanan Akun</h3>
                            <p class="text-sm text-gray-600 mt-1">Kelola password dan pengaturan keamanan</p>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Change Password -->
                            <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Ubah Password</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Password terakhir diubah: <?php echo date('d M Y', strtotime('-' . rand(30, 90) . ' days')); ?>
                                    </p>
                                </div>
                                <button onclick="showChangePasswordModal()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-key mr-2"></i>
                                    Ubah Password
                                </button>
                            </div>

                            <!-- Two Factor Authentication -->
                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Two-Factor Authentication</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Status: <span class="text-green-600 font-medium">Aktif</span>
                                    </p>
                                </div>
                                <button onclick="manage2FA()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    Kelola 2FA
                                </button>
                            </div>

                            <!-- Login Sessions -->
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Sesi Login Aktif</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <?php echo rand(1, 3); ?> perangkat aktif saat ini
                                    </p>
                                </div>
                                <button onclick="manageSessions()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-desktop mr-2"></i>
                                    Kelola Sesi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Password</h3>
                <form method="POST" action="admin.php?action=change_password">
                    <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::generateCSRFToken(); ?>">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   required 
                                   minlength="8"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required 
                                   minlength="8"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeChangePasswordModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            <i class="fas fa-save mr-1"></i>
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let editMode = false;

        function toggleEditMode() {
            editMode = !editMode;
            const formInputs = document.querySelectorAll('#profileForm input, #profileForm select');
            const formActions = document.getElementById('formActions');
            const editToggle = document.getElementById('editToggle');
            
            formInputs.forEach(input => {
                if (input.name !== 'csrf_token') {
                    input.disabled = !editMode;
                }
            });
            
            if (editMode) {
                formActions.classList.remove('hidden');
                editToggle.innerHTML = '<i class="fas fa-times mr-2"></i>Cancel Edit';
                editToggle.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                editToggle.classList.add('bg-red-600', 'hover:bg-red-700');
            } else {
                formActions.classList.add('hidden');
                editToggle.innerHTML = '<i class="fas fa-edit mr-2"></i>Edit Profile';
                editToggle.classList.remove('bg-red-600', 'hover:bg-red-700');
                editToggle.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }
        }

        function cancelEdit() {
            editMode = false;
            toggleEditMode();
            // Reset form values
            document.getElementById('profileForm').reset();
        }

        function showChangePasswordModal() {
            document.getElementById('changePasswordModal').classList.remove('hidden');
        }

        function closeChangePasswordModal() {
            document.getElementById('changePasswordModal').classList.add('hidden');
            document.querySelector('#changePasswordModal form').reset();
        }

        function manage2FA() {
            alert('Fitur Two-Factor Authentication akan segera tersedia');
        }

        function manageSessions() {
            alert('Fitur manajemen sesi login akan segera tersedia');
        }

        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword !== newPassword) {
                this.setCustomValidity('Password tidak cocok');
            } else {
                this.setCustomValidity('');
            }
        });

        // Close modal when clicking outside
        document.getElementById('changePasswordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeChangePasswordModal();
            }
        });

        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            if (!editMode) {
                e.preventDefault();
                return;
            }
            
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
    </script>
</body>
</html>