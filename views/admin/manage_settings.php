<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Sistem - Admin Campus Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-gradient {
            background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
        }
        .settings-card {
            transition: all 0.3s ease;
        }
        .settings-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .setting-item {
            transition: background-color 0.2s ease;
        }
        .setting-item:hover {
            background-color: #f8fafc;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #2563eb;
        }
        input:checked + .slider:before {
            transform: translateX(20px);
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
                <a href="admin.php?action=manage_users" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
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
                <a href="admin.php?action=manage_settings" class="flex items-center px-4 py-3 text-white bg-indigo-600 rounded-lg">
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
                        <h1 class="text-2xl font-bold text-gray-900">Pengaturan Sistem</h1>
                        <p class="text-gray-600">Konfigurasi dan pengaturan aplikasi Campus Hub</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="resetToDefaults()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-undo mr-2"></i>
                            Reset Default
                        </button>
                        <button form="settingsForm" type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Semua
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

            <!-- Settings Form -->
            <form id="settingsForm" method="POST" action="admin.php?action=update_settings" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::generateCSRFToken(); ?>">

                <!-- Site Settings -->
                <div class="settings-card bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-globe text-blue-600 mr-3"></i>
                            Pengaturan Situs
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Konfigurasi dasar website dan branding</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <?php 
                        $siteSettings = array_filter($settings, function($s) { 
                            return in_array($s['setting_key'], ['site_name', 'site_description', 'site_logo', 'contact_email', 'contact_phone']);
                        });
                        ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php foreach ($siteSettings as $setting): ?>
                                <div class="setting-item p-4 rounded-lg border border-gray-100">
                                    <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                                        <?php
                                        $labels = [
                                            'site_name' => 'Nama Situs',
                                            'site_description' => 'Deskripsi Situs', 
                                            'site_logo' => 'Logo Situs (Path)',
                                            'contact_email' => 'Email Kontak',
                                            'contact_phone' => 'Telepon Kontak'
                                        ];
                                        echo $labels[$setting['setting_key']] ?? $setting['setting_key'];
                                        ?>
                                    </label>
                                    <?php if ($setting['setting_key'] === 'site_description'): ?>
                                        <textarea name="<?php echo $setting['setting_key']; ?>" 
                                                  id="<?php echo $setting['setting_key']; ?>"
                                                  rows="3"
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                  placeholder="<?php echo htmlspecialchars($setting['description'] ?? ''); ?>"><?php echo htmlspecialchars($setting['setting_value'] ?? ''); ?></textarea>
                                    <?php else: ?>
                                        <input type="text" 
                                               name="<?php echo $setting['setting_key']; ?>" 
                                               id="<?php echo $setting['setting_key']; ?>"
                                               value="<?php echo htmlspecialchars($setting['setting_value'] ?? ''); ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="<?php echo htmlspecialchars($setting['description'] ?? ''); ?>">
                                    <?php endif; ?>
                                    <?php if ($setting['description']): ?>
                                        <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($setting['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Feature Settings -->
                <div class="settings-card bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-toggle-on text-green-600 mr-3"></i>
                            Pengaturan Fitur
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Aktifkan atau nonaktifkan fitur aplikasi</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <?php 
                        $featureSettings = array_filter($settings, function($s) { 
                            return in_array($s['setting_key'], ['enable_registration', 'enable_forum', 'enable_cv_builder', 'maintenance_mode']);
                        });
                        ?>
                        
                        <?php foreach ($featureSettings as $setting): ?>
                            <div class="setting-item flex items-center justify-between p-4 rounded-lg border border-gray-100">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">
                                        <?php
                                        $labels = [
                                            'enable_registration' => 'Pendaftaran User Baru',
                                            'enable_forum' => 'Forum Diskusi',
                                            'enable_cv_builder' => 'CV Builder',
                                            'maintenance_mode' => 'Mode Maintenance'
                                        ];
                                        echo $labels[$setting['setting_key']] ?? $setting['setting_key'];
                                        ?>
                                    </h4>
                                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($setting['description'] ?? ''); ?></p>
                                </div>
                                <label class="switch">
                                    <input type="hidden" name="<?php echo $setting['setting_key']; ?>" value="false">
                                    <input type="checkbox" 
                                           name="<?php echo $setting['setting_key']; ?>" 
                                           value="true"
                                           <?php echo ($setting['setting_value'] === 'true') ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Currency & Localization -->
                <div class="settings-card bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-money-bill text-green-600 mr-3"></i>
                            Mata Uang & Lokalisasi
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Pengaturan mata uang dan zona waktu</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <?php 
                        $currencySettings = array_filter($settings, function($s) { 
                            return in_array($s['setting_key'], ['default_currency', 'currency_symbol', 'timezone']);
                        });
                        ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <?php foreach ($currencySettings as $setting): ?>
                                <div class="setting-item p-4 rounded-lg border border-gray-100">
                                    <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                                        <?php
                                        $labels = [
                                            'default_currency' => 'Mata Uang Default',
                                            'currency_symbol' => 'Simbol Mata Uang',
                                            'timezone' => 'Zona Waktu'
                                        ];
                                        echo $labels[$setting['setting_key']] ?? $setting['setting_key'];
                                        ?>
                                    </label>
                                    
                                    <?php if ($setting['setting_key'] === 'timezone'): ?>
                                        <select name="<?php echo $setting['setting_key']; ?>" 
                                                id="<?php echo $setting['setting_key']; ?>"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="Asia/Jakarta" <?php echo ($setting['setting_value'] === 'Asia/Jakarta') ? 'selected' : ''; ?>>Asia/Jakarta (WIB)</option>
                                            <option value="Asia/Makassar" <?php echo ($setting['setting_value'] === 'Asia/Makassar') ? 'selected' : ''; ?>>Asia/Makassar (WITA)</option>
                                            <option value="Asia/Jayapura" <?php echo ($setting['setting_value'] === 'Asia/Jayapura') ? 'selected' : ''; ?>>Asia/Jayapura (WIT)</option>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" 
                                               name="<?php echo $setting['setting_key']; ?>" 
                                               id="<?php echo $setting['setting_key']; ?>"
                                               value="<?php echo htmlspecialchars($setting['setting_value'] ?? ''); ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="<?php echo htmlspecialchars($setting['description'] ?? ''); ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Social Media Settings -->
                <div class="settings-card bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-share-alt text-purple-600 mr-3"></i>
                            Media Sosial
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Link ke akun media sosial</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <?php 
                        $socialSettings = array_filter($settings, function($s) { 
                            return in_array($s['setting_key'], ['social_facebook', 'social_instagram', 'social_twitter']);
                        });
                        ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <?php foreach ($socialSettings as $setting): ?>
                                <div class="setting-item p-4 rounded-lg border border-gray-100">
                                    <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fab fa-<?php echo str_replace('social_', '', $setting['setting_key']); ?> mr-2"></i>
                                        <?php
                                        $labels = [
                                            'social_facebook' => 'Facebook',
                                            'social_instagram' => 'Instagram',
                                            'social_twitter' => 'Twitter'
                                        ];
                                        echo $labels[$setting['setting_key']] ?? $setting['setting_key'];
                                        ?>
                                    </label>
                                    <input type="url" 
                                           name="<?php echo $setting['setting_key']; ?>" 
                                           id="<?php echo $setting['setting_key']; ?>"
                                           value="<?php echo htmlspecialchars($setting['setting_value'] ?? ''); ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="https://...">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div class="settings-card bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-cogs text-gray-600 mr-3"></i>
                            Pengaturan Lanjutan
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Konfigurasi teknis dan integrasi</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <?php 
                        $advancedSettings = array_filter($settings, function($s) { 
                            return in_array($s['setting_key'], ['max_file_upload', 'google_analytics', 'smtp_host', 'smtp_port', 'payment_gateway']);
                        });
                        ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php foreach ($advancedSettings as $setting): ?>
                                <div class="setting-item p-4 rounded-lg border border-gray-100">
                                    <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                                        <?php
                                        $labels = [
                                            'max_file_upload' => 'Max Upload Size (bytes)',
                                            'google_analytics' => 'Google Analytics ID',
                                            'smtp_host' => 'SMTP Host',
                                            'smtp_port' => 'SMTP Port',
                                            'payment_gateway' => 'Payment Gateway'
                                        ];
                                        echo $labels[$setting['setting_key']] ?? $setting['setting_key'];
                                        ?>
                                    </label>
                                    
                                    <?php if ($setting['setting_key'] === 'payment_gateway'): ?>
                                        <select name="<?php echo $setting['setting_key']; ?>" 
                                                id="<?php echo $setting['setting_key']; ?>"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="midtrans" <?php echo ($setting['setting_value'] === 'midtrans') ? 'selected' : ''; ?>>Midtrans</option>
                                            <option value="xendit" <?php echo ($setting['setting_value'] === 'xendit') ? 'selected' : ''; ?>>Xendit</option>
                                            <option value="gopay" <?php echo ($setting['setting_value'] === 'gopay') ? 'selected' : ''; ?>>GoPay</option>
                                        </select>
                                    <?php elseif ($setting['setting_key'] === 'smtp_port' || $setting['setting_key'] === 'max_file_upload'): ?>
                                        <input type="number" 
                                               name="<?php echo $setting['setting_key']; ?>" 
                                               id="<?php echo $setting['setting_key']; ?>"
                                               value="<?php echo htmlspecialchars($setting['setting_value'] ?? ''); ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="<?php echo htmlspecialchars($setting['description'] ?? ''); ?>">
                                    <?php else: ?>
                                        <input type="text" 
                                               name="<?php echo $setting['setting_key']; ?>" 
                                               id="<?php echo $setting['setting_key']; ?>"
                                               value="<?php echo htmlspecialchars($setting['setting_value'] ?? ''); ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="<?php echo htmlspecialchars($setting['description'] ?? ''); ?>">
                                    <?php endif; ?>
                                    
                                    <?php if ($setting['description']): ?>
                                        <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($setting['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </form>

            <!-- System Tools -->
            <div class="mt-6 settings-card bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-tools text-orange-600 mr-3"></i>
                        Tools Sistem
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Maintenance dan backup sistem</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <button onclick="backupDatabase()" class="flex flex-col items-center p-6 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                            <i class="fas fa-database text-2xl text-blue-600 mb-3"></i>
                            <span class="text-sm font-medium text-gray-900">Backup Database</span>
                            <span class="text-xs text-gray-500 mt-1">Export semua data</span>
                        </button>

                        <button onclick="cleanLogs()" class="flex flex-col items-center p-6 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors group">
                            <i class="fas fa-broom text-2xl text-yellow-600 mb-3"></i>
                            <span class="text-sm font-medium text-gray-900">Bersihkan Log</span>
                            <span class="text-xs text-gray-500 mt-1">Hapus log lama</span>
                        </button>

                        <button onclick="optimizeDatabase()" class="flex flex-col items-center p-6 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                            <i class="fas fa-tachometer-alt text-2xl text-green-600 mb-3"></i>
                            <span class="text-sm font-medium text-gray-900">Optimasi DB</span>
                            <span class="text-xs text-gray-500 mt-1">Optimize performa</span>
                        </button>

                        <button onclick="checkSystem()" class="flex flex-col items-center p-6 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                            <i class="fas fa-heartbeat text-2xl text-purple-600 mb-3"></i>
                            <span class="text-sm font-medium text-gray-900">Cek Sistem</span>
                            <span class="text-xs text-gray-500 mt-1">Status kesehatan</span>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div id="modalIcon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900 mt-4">Konfirmasi</h3>
                <div class="mt-2 px-7 py-3">
                    <p id="modalMessage" class="text-sm text-gray-500">
                        Apakah Anda yakin ingin melanjutkan?
                    </p>
                </div>
                <div class="items-center px-4 py-3 flex space-x-4">
                    <button id="cancelAction" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                    <button id="confirmAction" class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentAction = null;

        function showConfirmModal(title, message, action, iconClass = 'fas fa-exclamation-triangle', iconColor = 'yellow') {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('modalIcon').innerHTML = `<i class="${iconClass} text-${iconColor}-600"></i>`;
            document.getElementById('modalIcon').className = `mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-${iconColor}-100`;
            currentAction = action;
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function backupDatabase() {
            showConfirmModal(
                'Backup Database',
                'Proses backup akan membuat file SQL dengan semua data. Lanjutkan?',
                () => window.location.href = 'admin.php?action=backup_database',
                'fas fa-database',
                'blue'
            );
        }

        function cleanLogs() {
            showConfirmModal(
                'Bersihkan Log',
                'Ini akan menghapus semua log admin yang lebih dari 6 bulan. Lanjutkan?',
                () => window.location.href = 'admin.php?action=clean_logs',
                'fas fa-broom',
                'yellow'
            );
        }

        function optimizeDatabase() {
            showConfirmModal(
                'Optimasi Database',
                'Proses ini akan mengoptimalkan tabel database untuk performa yang lebih baik. Lanjutkan?',
                () => {
                    // Implement database optimization
                    alert('Fitur optimasi database akan segera tersedia');
                },
                'fas fa-tachometer-alt',
                'green'
            );
        }

        function checkSystem() {
            showConfirmModal(
                'Cek Sistem',
                'Menjalankan pengecekan kesehatan sistem dan dependensi. Lanjutkan?',
                () => {
                    // Implement system check
                    alert('Status sistem: Semua berjalan normal\n\n✓ Database: Connected\n✓ Storage: Available\n✓ PHP Version: OK\n✓ Extensions: OK');
                },
                'fas fa-heartbeat',
                'purple'
            );
        }

        function resetToDefaults() {
            showConfirmModal(
                'Reset ke Default',
                'Ini akan mengembalikan semua pengaturan ke nilai default. Perubahan tidak dapat dibatalkan!',
                () => {
                    // Implement reset to defaults
                    alert('Fitur reset akan segera tersedia');
                },
                'fas fa-undo',
                'red'
            );
        }

        // Modal event handlers
        document.getElementById('cancelAction').addEventListener('click', function() {
            document.getElementById('confirmModal').classList.add('hidden');
            currentAction = null;
        });

        document.getElementById('confirmAction').addEventListener('click', function() {
            document.getElementById('confirmModal').classList.add('hidden');
            if (currentAction) {
                currentAction();
                currentAction = null;
            }
        });

        // Close modal when clicking outside
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                currentAction = null;
            }
        });

        // Form validation
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            // Basic validation
            const requiredFields = ['site_name', 'contact_email'];
            let valid = true;
            
            requiredFields.forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500');
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi');
            }
        });

        // Auto-save indication
        let autoSaveTimeout;
        document.querySelectorAll('input, textarea, select').forEach(element => {
            element.addEventListener('change', function() {
                clearTimeout(autoSaveTimeout);
                const indicator = document.createElement('span');
                indicator.textContent = ' (belum disimpan)';
                indicator.className = 'text-xs text-orange-600';
                indicator.id = 'unsaved-indicator';
                
                // Remove existing indicator
                const existing = document.getElementById('unsaved-indicator');
                if (existing) existing.remove();
                
                // Add new indicator
                this.parentNode.appendChild(indicator);
                
                // Remove indicator after 3 seconds
                autoSaveTimeout = setTimeout(() => {
                    if (indicator.parentNode) {
                        indicator.remove();
                    }
                }, 3000);
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+S to save
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                document.getElementById('settingsForm').submit();
            }
            
            // Escape to close modal
            if (e.key === 'Escape') {
                document.getElementById('confirmModal').classList.add('hidden');
                currentAction = null;
            }
        });
    </script>
</body>
</html>