<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Sistem - Code Camp Admin</title>
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
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="ml-12 lg:ml-0">
                        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h1>
                        <p class="text-gray-600 mt-1">Konfigurasi platform Code Camp</p>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-4 lg:p-6 max-w-6xl mx-auto space-y-6">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success']) ?></span>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error']) ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Settings Form -->
                <form method="POST" action="admin.php?action=update_settings" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <!-- General Settings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Pengaturan Umum</h3>
                            <p class="text-gray-600 mt-1">Konfigurasi dasar platform</p>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Site Name -->
                                <div>
                                    <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Situs
                                    </label>
                                    <input 
                                        type="text" 
                                        id="site_name" 
                                        name="site_name" 
                                        value="<?= htmlspecialchars($settings['site_name']['setting_value'] ?? 'Code Camp') ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                        placeholder="Code Camp"
                                    >
                                </div>

                                <!-- Site Description -->
                                <div>
                                    <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Deskripsi Situs
                                    </label>
                                    <input 
                                        type="text" 
                                        id="site_description" 
                                        name="site_description" 
                                        value="<?= htmlspecialchars($settings['site_description']['setting_value'] ?? 'Platform Bootcamp Programming Terbaik') ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                        placeholder="Platform Bootcamp Programming Terbaik"
                                    >
                                </div>

                                <!-- Contact Email -->
                                <div>
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Kontak
                                    </label>
                                    <input 
                                        type="email" 
                                        id="contact_email" 
                                        name="contact_email" 
                                        value="<?= htmlspecialchars($settings['contact_email']['setting_value'] ?? 'contact@codecamp.com') ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                        placeholder="contact@codecamp.com"
                                    >
                                </div>

                                <!-- Contact Phone -->
                                <div>
                                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Telepon Kontak
                                    </label>
                                    <input 
                                        type="tel" 
                                        id="contact_phone" 
                                        name="contact_phone" 
                                        value="<?= htmlspecialchars($settings['contact_phone']['setting_value'] ?? '+62-xxx-xxxx-xxxx') ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                        placeholder="+62-xxx-xxxx-xxxx"
                                    >
                                </div>
                            </div>

                            <!-- Site Address -->
                            <div>
                                <label for="site_address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat
                                </label>
                                <textarea 
                                    id="site_address" 
                                    name="site_address" 
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                    placeholder="Jl. Contoh No. 123, Jakarta"
                                ><?= htmlspecialchars($settings['site_address']['setting_value'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Settings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Pengaturan Registrasi</h3>
                            <p class="text-gray-600 mt-1">Atur bagaimana user mendaftar</p>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Allow Registration -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Izinkan Registrasi
                                    </label>
                                    <div class="flex items-center space-x-3">
                                        <label class="flex items-center">
                                            <input 
                                                type="radio" 
                                                name="allow_registration" 
                                                value="1" 
                                                <?= ($settings['allow_registration']['setting_value'] ?? '1') == '1' ? 'checked' : '' ?>
                                                class="text-primary focus:ring-primary border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Ya</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input 
                                                type="radio" 
                                                name="allow_registration" 
                                                value="0"
                                                <?= ($settings['allow_registration']['setting_value'] ?? '1') == '0' ? 'checked' : '' ?>
                                                class="text-primary focus:ring-primary border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Tidak</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Email Verification -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Verifikasi Email
                                    </label>
                                    <div class="flex items-center space-x-3">
                                        <label class="flex items-center">
                                            <input 
                                                type="radio" 
                                                name="require_email_verification" 
                                                value="1" 
                                                <?= ($settings['require_email_verification']['setting_value'] ?? '0') == '1' ? 'checked' : '' ?>
                                                class="text-primary focus:ring-primary border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Wajib</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input 
                                                type="radio" 
                                                name="require_email_verification" 
                                                value="0"
                                                <?= ($settings['require_email_verification']['setting_value'] ?? '0') == '0' ? 'checked' : '' ?>
                                                class="text-primary focus:ring-primary border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Tidak Wajib</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Settings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Pengaturan Pembayaran</h3>
                            <p class="text-gray-600 mt-1">Konfigurasi metode pembayaran</p>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Default Currency -->
                                <div>
                                    <label for="default_currency" class="block text-sm font-medium text-gray-700 mb-2">
                                        Mata Uang Default
                                    </label>
                                    <select 
                                        id="default_currency" 
                                        name="default_currency" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                    >
                                        <option value="IDR" <?= ($settings['default_currency']['setting_value'] ?? 'IDR') == 'IDR' ? 'selected' : '' ?>>IDR (Rupiah)</option>
                                        <option value="USD" <?= ($settings['default_currency']['setting_value'] ?? 'IDR') == 'USD' ? 'selected' : '' ?>>USD (Dollar)</option>
                                    </select>
                                </div>

                                <!-- Tax Rate -->
                                <div>
                                    <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                        Rate Pajak (%)
                                    </label>
                                    <input 
                                        type="number" 
                                        id="tax_rate" 
                                        name="tax_rate" 
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value="<?= htmlspecialchars($settings['tax_rate']['setting_value'] ?? '0') ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                        placeholder="0"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature Settings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Pengaturan Fitur</h3>
                            <p class="text-gray-600 mt-1">Aktifkan atau nonaktifkan fitur</p>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Enable Forum -->
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div>
                                        <h4 class="font-medium text-gray-800">Forum Diskusi</h4>
                                        <p class="text-sm text-gray-600">Izinkan user berdiskusi di forum</p>
                                    </div>
                                    <label class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            name="enable_forum" 
                                            value="1"
                                            <?= ($settings['enable_forum']['setting_value'] ?? '1') == '1' ? 'checked' : '' ?>
                                            class="rounded border-gray-300 text-primary focus:ring-primary"
                                        >
                                    </label>
                                </div>

                                <!-- Enable Reviews -->
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div>
                                        <h4 class="font-medium text-gray-800">Review & Rating</h4>
                                        <p class="text-sm text-gray-600">Izinkan user memberikan review</p>
                                    </div>
                                    <label class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            name="enable_reviews" 
                                            value="1"
                                            <?= ($settings['enable_reviews']['setting_value'] ?? '1') == '1' ? 'checked' : '' ?>
                                            class="rounded border-gray-300 text-primary focus:ring-primary"
                                        >
                                    </label>
                                </div>

                                <!-- Enable Wishlist -->
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div>
                                        <h4 class="font-medium text-gray-800">Wishlist</h4>
                                        <p class="text-sm text-gray-600">Fitur simpan bootcamp favorit</p>
                                    </div>
                                    <label class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            name="enable_wishlist" 
                                            value="1"
                                            <?= ($settings['enable_wishlist']['setting_value'] ?? '1') == '1' ? 'checked' : '' ?>
                                            class="rounded border-gray-300 text-primary focus:ring-primary"
                                        >
                                    </label>
                                </div>

                                <!-- Maintenance Mode -->
                                <div class="flex items-center justify-between p-4 border border-red-200 rounded-lg bg-red-50">
                                    <div>
                                        <h4 class="font-medium text-red-800">Mode Maintenance</h4>
                                        <p class="text-sm text-red-600">Tutup situs untuk user biasa</p>
                                    </div>
                                    <label class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            name="maintenance_mode" 
                                            value="1"
                                            <?= ($settings['maintenance_mode']['setting_value'] ?? '0') == '1' ? 'checked' : '' ?>
                                            class="rounded border-red-300 text-red-600 focus:ring-red-500"
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Settings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Media Sosial</h3>
                            <p class="text-gray-600 mt-1">Link ke akun media sosial</p>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Facebook -->
                                <div>
                                    <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">
                                        Facebook URL
                                    </label>
                                    <input 
                                        type="url" 
                                        id="facebook_url" 
                                        name="facebook_url" 
                                        value="<?= htmlspecialchars($settings['facebook_url']['setting_value'] ?? '') ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                        placeholder="https://facebook.com/codecamp"
                                    >
                                </div>

                                <!-- Instagram -->
                                <div>
                                    <label for="instagram_url" class="block text-sm font-medium text-gray-700 mb-2">
                                        Instagram URL
                                    </label>
                                    <input 
                                        type="url" 
                                        id="instagram_url" 
                                        name="instagram_url" 
                                        value="<?= htmlspecialchars($settings['instagram_url']['setting_value'] ?? '') ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                        placeholder="https://instagram.com/codecamp"
                                    >
                                </div>

                                <!-- Twitter -->
                                <div>
                                    <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">
                                        Twitter URL
                                    </label>
                                    <input 
                                        type="url" 
                                        id="twitter_url" 
                                        name="twitter_url" 
                                        value="<?= htmlspecialchars($settings['twitter_url']['setting_value'] ?? '') ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                        placeholder="https://twitter.com/codecamp"
                                    >
                                </div>

                                <!-- LinkedIn -->
                                <div>
                                    <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">
                                        LinkedIn URL
                                    </label>
                                    <input 
                                        type="url" 
                                        id="linkedin_url" 
                                        name="linkedin_url" 
                                        value="<?= htmlspecialchars($settings['linkedin_url']['setting_value'] ?? '') ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                        placeholder="https://linkedin.com/company/codecamp"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button 
                                type="submit" 
                                class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-secondary transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50"
                            >
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Pengaturan
                            </button>
                        </div>
                    </div>
                </form>

                <!-- System Tools -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Tools Sistem</h3>
                        <p class="text-gray-600 mt-1">Utilitas maintenance dan backup</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Database Backup -->
                            <a 
                                href="admin.php?action=backup_database" 
                                onclick="return confirm('Buat backup database sekarang?')"
                                class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-primary hover:bg-primary hover:text-white transition-colors duration-200 group"
                            >
                                <svg class="w-8 h-8 text-blue-600 group-hover:text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                                <span class="text-sm font-medium text-center">Backup Database</span>
                            </a>

                            <!-- Clean Logs -->
                            <a 
                                href="admin.php?action=clean_logs" 
                                onclick="return confirm('Hapus log lama (>6 bulan)?')"
                                class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-primary hover:bg-primary hover:text-white transition-colors duration-200 group"
                            >
                                <svg class="w-8 h-8 text-green-600 group-hover:text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span class="text-sm font-medium text-center">Bersihkan Log</span>
                            </a>

                            <!-- Optimize Database -->
                            <a 
                                href="admin.php?action=optimize_database" 
                                onclick="return confirm('Optimasi database sekarang?')"
                                class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-primary hover:bg-primary hover:text-white transition-colors duration-200 group"
                            >
                                <svg class="w-8 h-8 text-purple-600 group-hover:text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span class="text-sm font-medium text-center">Optimasi DB</span>
                            </a>

                            <!-- System Health -->
                            <button 
                                onclick="checkSystemHealth()"
                                class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-primary hover:bg-primary hover:text-white transition-colors duration-200 group"
                            >
                                <svg class="w-8 h-8 text-orange-600 group-hover:text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-center">Cek Sistem</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- System Health Modal -->
    <div id="healthModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Status Sistem</h3>
                    <button onclick="closeHealthModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div id="healthContent">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
                        <p class="mt-2 text-gray-600">Memeriksa status sistem...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkSystemHealth() {
            document.getElementById('healthModal').classList.remove('hidden');
            
            fetch('admin.php?action=check_system')
                .then(response => response.json())
                .then(data => {
                    let healthContent = '<div class="space-y-3">';
                    
                    // Database Status
                    healthContent += `
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <span class="font-medium">Database</span>
                            <span class="px-2 py-1 text-xs rounded-full ${data.database === 'OK' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${data.database}
                            </span>
                        </div>
                    `;
                    
                    // Disk Space Status
                    healthContent += `
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <span class="font-medium">Disk Space</span>
                            <span class="px-2 py-1 text-xs rounded-full ${data.disk_space === 'OK' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                ${data.disk_space}
                            </span>
                        </div>
                    `;
                    
                    // Memory Status
                    healthContent += `
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <span class="font-medium">Memory</span>
                            <span class="px-2 py-1 text-xs rounded-full ${data.memory === 'OK' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                ${data.memory}
                            </span>
                        </div>
                    `;
                    
                    // Errors
                    if (data.errors && data.errors.length > 0) {
                        healthContent += '<div class="mt-4"><h4 class="font-medium text-red-800 mb-2">Issues:</h4>';
                        data.errors.forEach(error => {
                            healthContent += `<p class="text-sm text-red-600">• ${error}</p>`;
                        });
                        healthContent += '</div>';
                    } else {
                        healthContent += '<p class="text-sm text-green-600 mt-4 text-center">✓ Semua sistem berjalan normal</p>';
                    }
                    
                    healthContent += '</div>';
                    
                    document.getElementById('healthContent').innerHTML = healthContent;
                })
                .catch(error => {
                    document.getElementById('healthContent').innerHTML = `
                        <div class="text-center text-red-600">
                            <p>Error: ${error.message}</p>
                        </div>
                    `;
                });
        }

        function closeHealthModal() {
            document.getElementById('healthModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('healthModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeHealthModal();
            }
        });

        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>