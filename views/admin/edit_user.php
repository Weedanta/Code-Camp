<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Code Camp Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#1e40af',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 overflow-y-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 p-4 lg:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 ml-12 lg:ml-0">
                        <a 
                            href="admin.php?action=manage_users" 
                            class="text-gray-500 hover:text-primary transition-colors duration-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
                            <p class="text-gray-600 mt-1">Ubah data pengguna</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-4 lg:p-6 max-w-4xl mx-auto">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success']) ?></span>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error']) ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- User Profile Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="h-16 w-16 rounded-full bg-primary flex items-center justify-center">
                            <span class="text-xl font-semibold text-white">
                                <?= strtoupper(substr($user['name'] ?? 'U', 0, 2)) ?>
                            </span>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($user['name'] ?? '') ?></h2>
                            <p class="text-gray-600"><?= htmlspecialchars($user['alamat_email'] ?? '') ?></p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="text-sm text-gray-500">
                                    Bergabung: <?= date('d M Y', strtotime($user['created_at'] ?? 'now')) ?>
                                </span>
                                <?php 
                                $statusClass = match($user['status'] ?? 'active') {
                                    'active' => 'bg-green-100 text-green-800',
                                    'suspended' => 'bg-yellow-100 text-yellow-800',
                                    'banned' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                ?>
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
                                    <?= ucfirst($user['status'] ?? 'active') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Informasi User</h3>
                        <p class="text-gray-600 mt-1">Update data pengguna di bawah ini</p>
                    </div>

                    <form method="POST" action="admin.php?action=update_user" class="p-6 space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                required 
                                value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                placeholder="Masukkan nama lengkap"
                            >
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required 
                                value="<?= htmlspecialchars($user['alamat_email'] ?? '') ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                placeholder="user@example.com"
                            >
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon
                            </label>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                value="<?= htmlspecialchars($user['no_telepon'] ?? '') ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                placeholder="08xxxxxxxxxx"
                            >
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="status" 
                                name="status" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                            >
                                <option value="active" <?= ($user['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>
                                    Aktif - User dapat login dan mengakses semua fitur
                                </option>
                                <option value="suspended" <?= ($user['status'] ?? '') == 'suspended' ? 'selected' : '' ?>>
                                    Suspended - User sementara tidak dapat login
                                </option>
                                <option value="banned" <?= ($user['status'] ?? '') == 'banned' ? 'selected' : '' ?>>
                                    Banned - User dilarang mengakses platform
                                </option>
                            </select>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6">
                            <button 
                                type="submit" 
                                class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-secondary transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50"
                            >
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Perubahan
                            </button>
                            
                            <a 
                                href="admin.php?action=manage_users" 
                                class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200 text-center"
                            >
                                Batal
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Additional Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Aksi Tambahan</h3>
                        <p class="text-gray-600 mt-1">Opsi lainnya untuk user ini</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Reset Password -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-2">Reset Password</h4>
                                <p class="text-sm text-gray-600 mb-4">Reset password user ke password default</p>
                                <a 
                                    href="admin.php?action=reset_user_password&id=<?= $user['id'] ?>" 
                                    class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition-colors duration-200"
                                    onclick="return confirm('Reset password user ini ke password default?')"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                    Reset Password
                                </a>
                            </div>

                            <!-- Delete User -->
                            <div class="border border-red-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-2">Hapus User</h4>
                                <p class="text-sm text-gray-600 mb-4">Hapus user permanen dari sistem</p>
                                <a 
                                    href="admin.php?action=delete_user&id=<?= $user['id'] ?>" 
                                    class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors duration-200"
                                    onclick="return confirm('PERINGATAN: Aksi ini akan menghapus user secara permanen dan tidak dapat dibatalkan. Yakin ingin melanjutkan?')"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus User
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!name) {
                e.preventDefault();
                alert('Nama lengkap harus diisi');
                return;
            }
            
            if (!email) {
                e.preventDefault();
                alert('Email harus diisi');
                return;
            }
            
            // Email validation
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