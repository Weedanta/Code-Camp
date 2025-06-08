<?php
// views/admin/manage_settings.php - System Settings Page
$pageTitle = 'Pengaturan Sistem';

// Security function
function sanitizeOutput($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Group settings by category
$settingsGroups = [
    'general' => [
        'title' => 'Pengaturan Umum',
        'icon' => 'fas fa-cog',
        'description' => 'Konfigurasi dasar sistem'
    ],
    'email' => [
        'title' => 'Pengaturan Email',
        'icon' => 'fas fa-envelope',
        'description' => 'Konfigurasi SMTP dan template email'
    ],
    'payment' => [
        'title' => 'Pengaturan Pembayaran',
        'icon' => 'fas fa-credit-card',
        'description' => 'Gateway pembayaran dan mata uang'
    ],
    'security' => [
        'title' => 'Keamanan',
        'icon' => 'fas fa-shield-alt',
        'description' => 'Pengaturan keamanan dan akses'
    ],
    'maintenance' => [
        'title' => 'Pemeliharaan',
        'icon' => 'fas fa-tools',
        'description' => 'Backup, optimasi, dan pemeliharaan sistem'
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .settings-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 25px;
            align-items: start;
        }

        .settings-nav {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 100px;
        }

        .nav-title {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .nav-item {
            display: block;
            padding: 12px 15px;
            color: #6c757d;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .nav-item:hover {
            background: #f8f9fa;
            color: #495057;
            text-decoration: none;
        }

        .nav-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .nav-item i {
            width: 20px;
            margin-right: 10px;
        }

        .settings-content {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .content-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
        }

        .content-header h2 {
            margin: 0 0 5px;
            font-size: 24px;
            font-weight: 600;
        }

        .content-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .content-body {
            padding: 30px;
        }

        .settings-section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f3f4;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .setting-item {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .setting-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.1);
        }

        .setting-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .setting-info h4 {
            margin: 0 0 5px;
            font-size: 16px;
            font-weight: 600;
            color: #495057;
        }

        .setting-description {
            font-size: 12px;
            color: #6c757d;
            line-height: 1.4;
        }

        .setting-control {
            margin-top: 15px;
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
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

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #667eea;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            padding: 10px 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .input-addon {
            background: #e9ecef;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 14px;
            color: #495057;
            border: 2px solid #e1e5e9;
        }

        .system-tools {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .tool-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .tool-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.1);
        }

        .tool-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 20px;
            color: white;
        }

        .tool-icon.backup { background: #17a2b8; }
        .tool-icon.optimize { background: #28a745; }
        .tool-icon.clean { background: #ffc107; color: #495057; }
        .tool-icon.export { background: #6f42c1; }
        .tool-icon.health { background: #fd7e14; }

        .tool-title {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .tool-description {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .system-status {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: white;
            border-radius: 6px;
            border-left: 4px solid #28a745;
        }

        .status-item.warning {
            border-left-color: #ffc107;
        }

        .status-item.danger {
            border-left-color: #dc3545;
        }

        .status-label {
            font-size: 13px;
            color: #495057;
            font-weight: 500;
        }

        .status-value {
            font-size: 12px;
            color: #6c757d;
            font-family: monospace;
        }

        .form-actions {
            background: #f8f9fa;
            padding: 20px 30px;
            margin: -30px -30px 0;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .save-indicator {
            display: none;
            align-items: center;
            gap: 8px;
            color: #28a745;
            font-size: 14px;
        }

        .save-indicator.show {
            display: flex;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-content {
            background: white;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            max-width: 300px;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .settings-container {
                grid-template-columns: 1fr;
            }
            
            .settings-nav {
                position: static;
                order: -1;
            }
            
            .nav-item {
                display: inline-block;
                margin-right: 10px;
                margin-bottom: 10px;
            }
            
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .system-tools {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1><?= $pageTitle ?></h1>
                    <div class="breadcrumb">
                        <i class="fas fa-home"></i> Admin / 
                        <i class="fas fa-cog"></i> Pengaturan
                    </div>
                </div>
                <div class="page-actions">
                    <button type="button" class="btn btn-info" id="checkHealthBtn">
                        <i class="fas fa-heartbeat"></i>
                        Cek Kesehatan Sistem
                    </button>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= sanitizeOutput($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= sanitizeOutput($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="settings-container">
                    <!-- Settings Navigation -->
                    <div class="settings-nav">
                        <h3 class="nav-title">Kategori Pengaturan</h3>
                        <?php foreach ($settingsGroups as $key => $group): ?>
                            <a href="#<?= $key ?>" class="nav-item <?= $key === 'general' ? 'active' : '' ?>" data-section="<?= $key ?>">
                                <i class="<?= $group['icon'] ?>"></i>
                                <?= $group['title'] ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Settings Content -->
                    <div class="settings-content">
                        <form method="POST" action="admin.php?action=update_settings" id="settingsForm">
                            <input type="hidden" name="csrf_token" value="<?= sanitizeOutput($_SESSION['csrf_token'] ?? '') ?>">

                            <!-- Content Header -->
                            <div class="content-header" id="contentHeader">
                                <h2 id="contentTitle">Pengaturan Umum</h2>
                                <p id="contentDescription">Konfigurasi dasar sistem</p>
                            </div>

                            <div class="content-body">
                                <!-- System Status -->
                                <div class="system-status">
                                    <h4 style="margin-bottom: 15px;">
                                        <i class="fas fa-server"></i>
                                        Status Sistem
                                    </h4>
                                    <div class="status-grid" id="systemStatus">
                                        <div class="status-item">
                                            <span class="status-label">PHP Version</span>
                                            <span class="status-value"><?= PHP_VERSION ?></span>
                                        </div>
                                        <div class="status-item">
                                            <span class="status-label">Memory Usage</span>
                                            <span class="status-value"><?= round(memory_get_usage(true) / 1024 / 1024, 2) ?> MB</span>
                                        </div>
                                        <div class="status-item">
                                            <span class="status-label">Server Time</span>
                                            <span class="status-value"><?= date('Y-m-d H:i:s') ?></span>
                                        </div>
                                        <div class="status-item">
                                            <span class="status-label">Disk Space</span>
                                            <span class="status-value"><?= round(disk_free_space('.') / 1024 / 1024 / 1024, 2) ?> GB</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- General Settings -->
                                <div class="settings-section" id="general">
                                    <h3 class="section-title">
                                        <i class="fas fa-cog"></i>
                                        Pengaturan Umum
                                    </h3>
                                    <div class="settings-grid">
                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Nama Website</h4>
                                                    <p class="setting-description">Nama yang akan ditampilkan di seluruh website</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <input type="text" name="site_name" class="form-control" value="Code Camp" placeholder="Nama Website">
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Tagline</h4>
                                                    <p class="setting-description">Deskripsi singkat tentang website</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <input type="text" name="site_tagline" class="form-control" value="Platform Bootcamp Terbaik" placeholder="Tagline Website">
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>URL Website</h4>
                                                    <p class="setting-description">URL utama website</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <div class="input-group">
                                                    <span class="input-addon">https://</span>
                                                    <input type="text" name="site_url" class="form-control" value="codecamp.id" placeholder="domain.com">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Mode Maintenance</h4>
                                                    <p class="setting-description">Aktifkan untuk menonaktifkan akses website</p>
                                                </div>
                                                <label class="toggle-switch">
                                                    <input type="checkbox" name="maintenance_mode" value="1">
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Registrasi User</h4>
                                                    <p class="setting-description">Izinkan user baru untuk mendaftar</p>
                                                </div>
                                                <label class="toggle-switch">
                                                    <input type="checkbox" name="allow_registration" value="1" checked>
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Timezone</h4>
                                                    <p class="setting-description">Zona waktu default sistem</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <select name="default_timezone" class="form-control">
                                                    <option value="Asia/Jakarta" selected>Asia/Jakarta (WIB)</option>
                                                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Settings -->
                                <div class="settings-section" id="email" style="display: none;">
                                    <h3 class="section-title">
                                        <i class="fas fa-envelope"></i>
                                        Pengaturan Email
                                    </h3>
                                    <div class="settings-grid">
                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>SMTP Host</h4>
                                                    <p class="setting-description">Server SMTP untuk pengiriman email</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <input type="text" name="smtp_host" class="form-control" value="smtp.gmail.com" placeholder="smtp.gmail.com">
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>SMTP Port</h4>
                                                    <p class="setting-description">Port SMTP (biasanya 587 atau 465)</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <input type="number" name="smtp_port" class="form-control" value="587" placeholder="587">
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>SMTP Username</h4>
                                                    <p class="setting-description">Username untuk autentikasi SMTP</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <input type="email" name="smtp_username" class="form-control" placeholder="email@domain.com">
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>SMTP Password</h4>
                                                    <p class="setting-description">Password untuk autentikasi SMTP</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <input type="password" name="smtp_password" class="form-control" placeholder="••••••••">
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Email Pengirim</h4>
                                                    <p class="setting-description">Email yang akan muncul sebagai pengirim</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <input type="email" name="from_email" class="form-control" value="noreply@codecamp.id" placeholder="noreply@domain.com">
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Nama Pengirim</h4>
                                                    <p class="setting-description">Nama yang akan muncul sebagai pengirim</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <input type="text" name="from_name" class="form-control" value="Code Camp" placeholder="Nama Pengirim">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Settings -->
                                <div class="settings-section" id="payment" style="display: none;">
                                    <h3 class="section-title">
                                        <i class="fas fa-credit-card"></i>
                                        Pengaturan Pembayaran
                                    </h3>
                                    <div class="settings-grid">
                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Mata Uang</h4>
                                                    <p class="setting-description">Mata uang default untuk harga</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <select name="default_currency" class="form-control">
                                                    <option value="IDR" selected>IDR - Rupiah Indonesia</option>
                                                    <option value="USD">USD - US Dollar</option>
                                                    <option value="EUR">EUR - Euro</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Midtrans Server Key</h4>
                                                    <p class="setting-description">Server key dari Midtrans</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <input type="password" name="midtrans_server_key" class="form-control" placeholder="SB-Mid-server-••••••••">
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Midtrans Client Key</h4>
                                                    <p class="setting-description">Client key dari Midtrans</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <input type="text" name="midtrans_client_key" class="form-control" placeholder="SB-Mid-client-••••••••">
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Mode Sandbox</h4>
                                                    <p class="setting-description">Aktifkan untuk testing pembayaran</p>
                                                </div>
                                                <label class="toggle-switch">
                                                    <input type="checkbox" name="payment_sandbox" value="1" checked>
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Security Settings -->
                                <div class="settings-section" id="security" style="display: none;">
                                    <h3 class="section-title">
                                        <i class="fas fa-shield-alt"></i>
                                        Pengaturan Keamanan
                                    </h3>
                                    <div class="settings-grid">
                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Session Timeout</h4>
                                                    <p class="setting-description">Waktu timeout session dalam menit</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <div class="input-group">
                                                    <input type="number" name="session_timeout" class="form-control" value="60" min="15" max="480">
                                                    <span class="input-addon">menit</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Max Login Attempts</h4>
                                                    <p class="setting-description">Maksimal percobaan login yang gagal</p>
                                                </div>
                                            </div>
                                            <div class="setting-control">
                                                <input type="number" name="max_login_attempts" class="form-control" value="5" min="3" max="20">
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Force HTTPS</h4>
                                                    <p class="setting-description">Paksa penggunaan HTTPS</p>
                                                </div>
                                                <label class="toggle-switch">
                                                    <input type="checkbox" name="force_https" value="1">
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="setting-item">
                                            <div class="setting-header">
                                                <div class="setting-info">
                                                    <h4>Two Factor Authentication</h4>
                                                    <p class="setting-description">Aktifkan 2FA untuk admin</p>
                                                </div>
                                                <label class="toggle-switch">
                                                    <input type="checkbox" name="enable_2fa" value="1">
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Maintenance Tools -->
                                <div class="settings-section" id="maintenance" style="display: none;">
                                    <h3 class="section-title">
                                        <i class="fas fa-tools"></i>
                                        Tools Pemeliharaan
                                    </h3>
                                    
                                    <div class="system-tools">
                                        <div class="tool-card">
                                            <div class="tool-icon backup">
                                                <i class="fas fa-database"></i>
                                            </div>
                                            <h4 class="tool-title">Backup Database</h4>
                                            <p class="tool-description">Buat backup database sistem secara manual</p>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="backupDatabase()">
                                                <i class="fas fa-download"></i>
                                                Backup Sekarang
                                            </button>
                                        </div>

                                        <div class="tool-card">
                                            <div class="tool-icon optimize">
                                                <i class="fas fa-rocket"></i>
                                            </div>
                                            <h4 class="tool-title">Optimasi Database</h4>
                                            <p class="tool-description">Optimasi dan perbaiki tabel database</p>
                                            <button type="button" class="btn btn-success btn-sm" onclick="optimizeDatabase()">
                                                <i class="fas fa-wrench"></i>
                                                Optimasi
                                            </button>
                                        </div>

                                        <div class="tool-card">
                                            <div class="tool-icon clean">
                                                <i class="fas fa-broom"></i>
                                            </div>
                                            <h4 class="tool-title">Bersihkan Log</h4>
                                            <p class="tool-description">Hapus log lama untuk menghemat ruang</p>
                                            <button type="button" class="btn btn-warning btn-sm" onclick="cleanLogs()">
                                                <i class="fas fa-trash"></i>
                                                Bersihkan
                                            </button>
                                        </div>

                                        <div class="tool-card">
                                            <div class="tool-icon export">
                                                <i class="fas fa-file-export"></i>
                                            </div>
                                            <h4 class="tool-title">Export Data</h4>
                                            <p class="tool-description">Export data sistem dalam format CSV</p>
                                            <div class="dropdown">
                                                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-download"></i>
                                                    Export
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="admin.php?action=export_data&type=users">Export Users</a></li>
                                                    <li><a class="dropdown-item" href="admin.php?action=export_data&type=bootcamps">Export Bootcamps</a></li>
                                                    <li><a class="dropdown-item" href="admin.php?action=export_data&type=orders">Export Orders</a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="tool-card">
                                            <div class="tool-icon health">
                                                <i class="fas fa-heartbeat"></i>
                                            </div>
                                            <h4 class="tool-title">Cek Kesehatan</h4>
                                            <p class="tool-description">Periksa kesehatan sistem secara menyeluruh</p>
                                            <button type="button" class="btn btn-info btn-sm" onclick="checkSystemHealth()">
                                                <i class="fas fa-stethoscope"></i>
                                                Cek Sekarang
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <div class="save-indicator" id="saveIndicator">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Perubahan disimpan otomatis</span>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary" onclick="resetSettings()">
                                        <i class="fas fa-undo"></i>
                                        Reset ke Default
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Simpan Pengaturan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h4>Memproses...</h4>
            <p id="loadingText">Mohon tunggu sebentar</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navigation functionality
            const navItems = document.querySelectorAll('.nav-item');
            const sections = document.querySelectorAll('.settings-section');
            const contentTitle = document.getElementById('contentTitle');
            const contentDescription = document.getElementById('contentDescription');

            const settingsGroups = <?= json_encode($settingsGroups) ?>;

            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all nav items
                    navItems.forEach(nav => nav.classList.remove('active'));
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                    
                    // Hide all sections
                    sections.forEach(section => section.style.display = 'none');
                    
                    // Show selected section
                    const sectionId = this.dataset.section;
                    const section = document.getElementById(sectionId);
                    if (section) {
                        section.style.display = 'block';
                    }
                    
                    // Update header
                    if (settingsGroups[sectionId]) {
                        contentTitle.textContent = settingsGroups[sectionId].title;
                        contentDescription.textContent = settingsGroups[sectionId].description;
                    }
                });
            });

            // Auto-save functionality
            const form = document.getElementById('settingsForm');
            const saveIndicator = document.getElementById('saveIndicator');
            let autoSaveTimer;

            function scheduleAutoSave() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    autoSave();
                }, 2000);
            }

            function autoSave() {
                const formData = new FormData(form);
                
                fetch('admin.php?action=auto_save_settings', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSaveIndicator();
                    }
                })
                .catch(error => {
                    console.error('Auto-save failed:', error);
                });
            }

            function showSaveIndicator() {
                saveIndicator.classList.add('show');
                setTimeout(() => {
                    saveIndicator.classList.remove('show');
                }, 3000);
            }

            // Add auto-save listeners
            form.addEventListener('input', scheduleAutoSave);
            form.addEventListener('change', scheduleAutoSave);

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                showLoading('Menyimpan pengaturan...');
                
                setTimeout(() => {
                    this.submit();
                }, 1000);
            });

            // System status update
            updateSystemStatus();
            setInterval(updateSystemStatus, 30000); // Update every 30 seconds
        });

        function updateSystemStatus() {
            fetch('admin.php?action=system_status')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const statusGrid = document.getElementById('systemStatus');
                        // Update status items
                        Object.entries(data.status).forEach(([key, value]) => {
                            const item = statusGrid.querySelector(`[data-status="${key}"]`);
                            if (item) {
                                item.querySelector('.status-value').textContent = value;
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Failed to update system status:', error);
                });
        }

        function showLoading(text = 'Memproses...') {
            const overlay = document.getElementById('loadingOverlay');
            const loadingText = document.getElementById('loadingText');
            loadingText.textContent = text;
            overlay.style.display = 'flex';
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'none';
        }

        function backupDatabase() {
            if (confirm('Yakin ingin membuat backup database? Proses ini mungkin memakan waktu beberapa menit.')) {
                showLoading('Membuat backup database...');
                window.location.href = 'admin.php?action=backup_database';
                setTimeout(hideLoading, 5000);
            }
        }

        function optimizeDatabase() {
            if (confirm('Yakin ingin mengoptimasi database? Pastikan tidak ada user yang sedang menggunakan sistem.')) {
                showLoading('Mengoptimasi database...');
                
                fetch('admin.php?action=optimize_database', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        alert('Database berhasil dioptimasi!');
                    } else {
                        alert('Gagal mengoptimasi database: ' + data.message);
                    }
                })
                .catch(error => {
                    hideLoading();
                    alert('Terjadi kesalahan saat mengoptimasi database');
                });
            }
        }

        function cleanLogs() {
            if (confirm('Yakin ingin menghapus log lama? Log yang lebih dari 6 bulan akan dihapus.')) {
                showLoading('Membersihkan log...');
                window.location.href = 'admin.php?action=clean_logs';
                setTimeout(hideLoading, 3000);
            }
        }

        function checkSystemHealth() {
            showLoading('Memeriksa kesehatan sistem...');
            
            fetch('admin.php?action=system_health', {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                
                let message = 'Hasil Pemeriksaan Kesehatan Sistem:\n\n';
                Object.entries(data.checks).forEach(([key, result]) => {
                    const status = result.status ? '✅' : '❌';
                    message += `${status} ${result.name}: ${result.message}\n`;
                });
                
                alert(message);
            })
            .catch(error => {
                hideLoading();
                alert('Gagal memeriksa kesehatan sistem');
            });
        }

        function resetSettings() {
            if (confirm('Yakin ingin mereset semua pengaturan ke default? Tindakan ini tidak dapat dibatalkan!')) {
                showLoading('Mereset pengaturan...');
                
                fetch('admin.php?action=reset_settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        alert('Pengaturan berhasil direset ke default!');
                        location.reload();
                    } else {
                        alert('Gagal mereset pengaturan: ' + data.message);
                    }
                })
                .catch(error => {
                    hideLoading();
                    alert('Terjadi kesalahan saat mereset pengaturan');
                });
            }
        }
    </script>
</body>
</html>