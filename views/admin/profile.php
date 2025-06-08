<?php
// views/admin/profile.php - Admin Profile Page
$pageTitle = 'Profile Admin';

// Security function
function sanitizeOutput($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Get current admin info
$currentAdmin = AdminMiddleware::getCurrentAdmin();
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
        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
            animation: float 20s infinite linear;
        }

        @keyframes float {
            0% { transform: rotate(0deg) translate(-50%, -50%); }
            100% { transform: rotate(360deg) translate(-50%, -50%); }
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 25px;
            position: relative;
            z-index: 1;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 600;
            border: 4px solid rgba(255, 255, 255, 0.3);
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-avatar:hover {
            transform: scale(1.05);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .avatar-upload {
            position: absolute;
            bottom: -5px;
            right: -5px;
            width: 30px;
            height: 30px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            border: 3px solid white;
        }

        .profile-details h2 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 600;
        }

        .profile-role {
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 15px;
        }

        .profile-meta {
            display: flex;
            gap: 20px;
            font-size: 14px;
            opacity: 0.9;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .profile-sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .profile-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .section-header {
            background: #f8f9fa;
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #495057;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-content {
            padding: 25px;
        }

        .form-grid {
            display: grid;
            gap: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control:disabled {
            background: #f8f9fa;
            color: #6c757d;
        }

        .input-group {
            position: relative;
        }

        .input-group-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .input-group .form-control {
            padding-left: 45px;
        }

        .password-requirements {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            font-size: 12px;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
        }

        .requirement.valid {
            color: #28a745;
        }

        .requirement.invalid {
            color: #dc3545;
        }

        .activity-summary {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .activity-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
        }

        .stat-number {
            font-size: 20px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 11px;
            color: #6c757d;
            font-weight: 500;
        }

        .recent-activity {
            margin-top: 15px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }

        .activity-text {
            flex: 1;
            font-size: 13px;
            color: #495057;
        }

        .activity-time {
            font-size: 11px;
            color: #6c757d;
        }

        .security-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .security-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .security-item:last-child {
            border-bottom: none;
        }

        .security-label {
            font-size: 13px;
            color: #495057;
        }

        .security-value {
            font-size: 12px;
            color: #6c757d;
            font-family: monospace;
        }

        .session-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            transition: all 0.3s ease;
        }

        .strength-weak { background: #dc3545; width: 25%; }
        .strength-fair { background: #ffc107; width: 50%; }
        .strength-good { background: #28a745; width: 75%; }
        .strength-strong { background: #17a2b8; width: 100%; }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        @media (max-width: 768px) {
            .profile-sections {
                grid-template-columns: 1fr;
            }
            
            .profile-info {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .profile-meta {
                justify-content: center;
            }
            
            .activity-stats {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
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
                        <i class="fas fa-user"></i> Profile
                    </div>
                </div>
                <div class="page-actions">
                    <a href="admin.php?action=activity_log&admin_id=<?= $currentAdmin['id'] ?>" class="btn btn-secondary">
                        <i class="fas fa-history"></i>
                        Aktivitas Saya
                    </a>
                </div>
            </div>

            <div class="content-wrapper">
                <div class="profile-container">
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

                    <!-- Profile Header -->
                    <div class="profile-header">
                        <div class="profile-info">
                            <div class="profile-avatar" onclick="document.getElementById('avatarUpload').click()">
                                <?= strtoupper(substr($currentAdmin['name'] ?? 'A', 0, 2)) ?>
                                <div class="avatar-upload">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <input type="file" id="avatarUpload" accept="image/*" style="display: none;">
                            </div>
                            <div class="profile-details">
                                <h2><?= sanitizeOutput($currentAdmin['name'] ?? 'Admin') ?></h2>
                                <div class="profile-role">
                                    <i class="fas fa-shield-alt"></i>
                                    <?= ucfirst(sanitizeOutput($currentAdmin['role'] ?? 'admin')) ?>
                                </div>
                                <div class="profile-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-envelope"></i>
                                        <span><?= sanitizeOutput($currentAdmin['email'] ?? '') ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Bergabung <?= date('M Y', strtotime($admin['created_at'] ?? '')) ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span>Login terakhir: <?= date('d M Y H:i', strtotime($admin['last_login'] ?? '')) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Sections -->
                    <div class="profile-sections">
                        <!-- Personal Information -->
                        <div class="profile-section">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-user"></i>
                                    Informasi Personal
                                </h3>
                                <button type="button" class="btn btn-sm btn-primary" id="editProfileBtn">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                            </div>
                            <div class="section-content">
                                <form method="POST" action="admin.php?action=update_profile" id="profileForm">
                                    <input type="hidden" name="csrf_token" value="<?= sanitizeOutput($_SESSION['csrf_token'] ?? '') ?>">
                                    
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label for="name">
                                                <i class="fas fa-user"></i>
                                                Nama Lengkap
                                            </label>
                                            <div class="input-group">
                                                <i class="fas fa-user input-group-icon"></i>
                                                <input type="text" 
                                                       id="name" 
                                                       name="name" 
                                                       class="form-control" 
                                                       value="<?= sanitizeOutput($admin['name'] ?? '') ?>"
                                                       disabled
                                                       required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="email">
                                                <i class="fas fa-envelope"></i>
                                                Email
                                            </label>
                                            <div class="input-group">
                                                <i class="fas fa-envelope input-group-icon"></i>
                                                <input type="email" 
                                                       id="email" 
                                                       name="email" 
                                                       class="form-control" 
                                                       value="<?= sanitizeOutput($admin['email'] ?? '') ?>"
                                                       disabled
                                                       required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="phone">
                                                <i class="fas fa-phone"></i>
                                                Nomor Telepon
                                            </label>
                                            <div class="input-group">
                                                <i class="fas fa-phone input-group-icon"></i>
                                                <input type="tel" 
                                                       id="phone" 
                                                       name="phone" 
                                                       class="form-control" 
                                                       value="<?= sanitizeOutput($admin['phone'] ?? '') ?>"
                                                       disabled>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="department">
                                                <i class="fas fa-building"></i>
                                                Departemen
                                            </label>
                                            <div class="input-group">
                                                <i class="fas fa-building input-group-icon"></i>
                                                <input type="text" 
                                                       id="department" 
                                                       name="department" 
                                                       class="form-control" 
                                                       value="<?= sanitizeOutput($admin['department'] ?? '') ?>"
                                                       disabled>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="timezone">
                                                <i class="fas fa-globe"></i>
                                                Timezone
                                            </label>
                                            <select id="timezone" name="timezone" class="form-control" disabled>
                                                <option value="Asia/Jakarta" <?= ($admin['timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' ?>>Asia/Jakarta (WIB)</option>
                                                <option value="Asia/Makassar" <?= ($admin['timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' ?>>Asia/Makassar (WITA)</option>
                                                <option value="Asia/Jayapura" <?= ($admin['timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' ?>>Asia/Jayapura (WIT)</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="language">
                                                <i class="fas fa-language"></i>
                                                Bahasa
                                            </label>
                                            <select id="language" name="language" class="form-control" disabled>
                                                <option value="id" <?= ($admin['language'] ?? '') === 'id' ? 'selected' : '' ?>>Bahasa Indonesia</option>
                                                <option value="en" <?= ($admin['language'] ?? '') === 'en' ? 'selected' : '' ?>>English</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-actions" style="display: none;" id="profileActions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i>
                                            Simpan Perubahan
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="cancelProfileBtn">
                                            <i class="fas fa-times"></i>
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="profile-section">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-shield-alt"></i>
                                    Keamanan
                                </h3>
                            </div>
                            <div class="section-content">
                                <!-- Change Password Form -->
                                <form method="POST" action="admin.php?action=change_password" id="passwordForm">
                                    <input type="hidden" name="csrf_token" value="<?= sanitizeOutput($_SESSION['csrf_token'] ?? '') ?>">
                                    
                                    <div class="form-group">
                                        <label for="current_password">
                                            <i class="fas fa-lock"></i>
                                            Password Saat Ini
                                        </label>
                                        <div class="input-group">
                                            <i class="fas fa-lock input-group-icon"></i>
                                            <input type="password" 
                                                   id="current_password" 
                                                   name="current_password" 
                                                   class="form-control" 
                                                   required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="new_password">
                                            <i class="fas fa-key"></i>
                                            Password Baru
                                        </label>
                                        <div class="input-group">
                                            <i class="fas fa-key input-group-icon"></i>
                                            <input type="password" 
                                                   id="new_password" 
                                                   name="new_password" 
                                                   class="form-control" 
                                                   required>
                                        </div>
                                        <div class="password-strength" id="passwordStrength"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="confirm_password">
                                            <i class="fas fa-check-circle"></i>
                                            Konfirmasi Password Baru
                                        </label>
                                        <div class="input-group">
                                            <i class="fas fa-check-circle input-group-icon"></i>
                                            <input type="password" 
                                                   id="confirm_password" 
                                                   name="confirm_password" 
                                                   class="form-control" 
                                                   required>
                                        </div>
                                    </div>

                                    <div class="password-requirements">
                                        <strong>Syarat Password:</strong>
                                        <div class="requirement" id="req-length">
                                            <i class="fas fa-times"></i>
                                            <span>Minimal 8 karakter</span>
                                        </div>
                                        <div class="requirement" id="req-uppercase">
                                            <i class="fas fa-times"></i>
                                            <span>Minimal 1 huruf besar</span>
                                        </div>
                                        <div class="requirement" id="req-lowercase">
                                            <i class="fas fa-times"></i>
                                            <span>Minimal 1 huruf kecil</span>
                                        </div>
                                        <div class="requirement" id="req-number">
                                            <i class="fas fa-times"></i>
                                            <span>Minimal 1 angka</span>
                                        </div>
                                        <div class="requirement" id="req-special">
                                            <i class="fas fa-times"></i>
                                            <span>Minimal 1 karakter khusus</span>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-key"></i>
                                            Ubah Password
                                        </button>
                                    </div>
                                </form>

                                <!-- Security Information -->
                                <div class="security-info">
                                    <strong>Informasi Keamanan:</strong>
                                    <div class="security-item">
                                        <span class="security-label">IP Address Terakhir:</span>
                                        <span class="security-value"><?= sanitizeOutput($_SERVER['REMOTE_ADDR'] ?? 'Unknown') ?></span>
                                    </div>
                                    <div class="security-item">
                                        <span class="security-label">Browser:</span>
                                        <span class="security-value">
                                            <?php
                                            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                                            if (strpos($userAgent, 'Chrome') !== false) echo 'Chrome';
                                            elseif (strpos($userAgent, 'Firefox') !== false) echo 'Firefox';
                                            elseif (strpos($userAgent, 'Safari') !== false) echo 'Safari';
                                            elseif (strpos($userAgent, 'Edge') !== false) echo 'Edge';
                                            else echo 'Unknown';
                                            ?>
                                        </span>
                                    </div>
                                    <div class="security-item">
                                        <span class="security-label">Session ID:</span>
                                        <span class="security-value"><?= substr(session_id(), 0, 12) ?>...</span>
                                    </div>
                                </div>

                                <!-- Session Info -->
                                <div class="session-info">
                                    <strong>Informasi Sesi:</strong>
                                    <div class="security-item">
                                        <span class="security-label">Login Sejak:</span>
                                        <span class="security-value"><?= date('d M Y H:i:s', $_SESSION['admin_last_activity'] ?? time()) ?></span>
                                    </div>
                                    <div class="security-item">
                                        <span class="security-label">Aktivitas Terakhir:</span>
                                        <span class="security-value" id="lastActivity"><?= date('d M Y H:i:s') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Summary (Full Width) -->
                        <div class="profile-section" style="grid-column: 1 / -1;">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-chart-line"></i>
                                    Ringkasan Aktivitas
                                </h3>
                                <a href="admin.php?action=activity_log&admin_id=<?= $currentAdmin['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-external-link-alt"></i>
                                    Lihat Semua
                                </a>
                            </div>
                            <div class="section-content">
                                <div class="activity-summary">
                                    <div class="activity-stats">
                                        <div class="stat-item">
                                            <div class="stat-number">
                                                <?php
                                                // Calculate today's activities
                                                $todayActivities = 0;
                                                if (isset($recentActivities)) {
                                                    foreach ($recentActivities as $activity) {
                                                        if (date('Y-m-d', strtotime($activity['created_at'])) === date('Y-m-d')) {
                                                            $todayActivities++;
                                                        }
                                                    }
                                                }
                                                echo number_format($todayActivities);
                                                ?>
                                            </div>
                                            <div class="stat-label">Aktivitas Hari Ini</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">
                                                <?php
                                                // Calculate this week's activities
                                                $weekActivities = 0;
                                                if (isset($recentActivities)) {
                                                    $weekStart = date('Y-m-d', strtotime('monday this week'));
                                                    foreach ($recentActivities as $activity) {
                                                        if (date('Y-m-d', strtotime($activity['created_at'])) >= $weekStart) {
                                                            $weekActivities++;
                                                        }
                                                    }
                                                }
                                                echo number_format($weekActivities);
                                                ?>
                                            </div>
                                            <div class="stat-label">Minggu Ini</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number"><?= number_format(count($recentActivities ?? [])) ?></div>
                                            <div class="stat-label">Total Aktivitas</div>
                                        </div>
                                    </div>

                                    <div class="recent-activity">
                                        <strong>Aktivitas Terbaru:</strong>
                                        <?php if (!empty($recentActivities)): ?>
                                            <?php foreach (array_slice($recentActivities, 0, 5) as $activity): ?>
                                                <div class="activity-item">
                                                    <div class="activity-icon">
                                                        <?php
                                                        $icons = [
                                                            'login' => 'fa-sign-in-alt',
                                                            'logout' => 'fa-sign-out-alt',
                                                            'create' => 'fa-plus',
                                                            'update' => 'fa-edit',
                                                            'delete' => 'fa-trash'
                                                        ];
                                                        $icon = $icons[$activity['activity_type']] ?? 'fa-circle';
                                                        ?>
                                                        <i class="fas <?= $icon ?>"></i>
                                                    </div>
                                                    <div class="activity-text">
                                                        <?= sanitizeOutput($activity['description']) ?>
                                                    </div>
                                                    <div class="activity-time">
                                                        <?= date('H:i', strtotime($activity['created_at'])) ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">Belum ada aktivitas terbaru.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Edit Profile functionality
            const editProfileBtn = document.getElementById('editProfileBtn');
            const profileForm = document.getElementById('profileForm');
            const profileActions = document.getElementById('profileActions');
            const cancelProfileBtn = document.getElementById('cancelProfileBtn');
            const profileInputs = profileForm.querySelectorAll('input, select');

            let originalValues = {};

            editProfileBtn.addEventListener('click', function() {
                // Store original values
                profileInputs.forEach(input => {
                    originalValues[input.name] = input.value;
                });

                // Enable inputs
                profileInputs.forEach(input => {
                    input.disabled = false;
                });

                // Show actions
                profileActions.style.display = 'flex';
                editProfileBtn.style.display = 'none';
            });

            cancelProfileBtn.addEventListener('click', function() {
                // Restore original values
                profileInputs.forEach(input => {
                    if (originalValues[input.name] !== undefined) {
                        input.value = originalValues[input.name];
                    }
                });

                // Disable inputs
                profileInputs.forEach(input => {
                    input.disabled = true;
                });

                // Hide actions
                profileActions.style.display = 'none';
                editProfileBtn.style.display = 'inline-flex';
            });

            // Password strength checker
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const passwordStrength = document.getElementById('passwordStrength');

            newPasswordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                checkPasswordRequirements(this.value);
            });

            confirmPasswordInput.addEventListener('input', function() {
                checkPasswordMatch();
            });

            function checkPasswordStrength(password) {
                let strength = 0;
                let className = '';

                if (password.length >= 8) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;

                switch (strength) {
                    case 0:
                    case 1:
                        className = 'strength-weak';
                        break;
                    case 2:
                    case 3:
                        className = 'strength-fair';
                        break;
                    case 4:
                        className = 'strength-good';
                        break;
                    case 5:
                        className = 'strength-strong';
                        break;
                }

                passwordStrength.className = `password-strength ${className}`;
            }

            function checkPasswordRequirements(password) {
                const requirements = {
                    'req-length': password.length >= 8,
                    'req-uppercase': /[A-Z]/.test(password),
                    'req-lowercase': /[a-z]/.test(password),
                    'req-number': /[0-9]/.test(password),
                    'req-special': /[^A-Za-z0-9]/.test(password)
                };

                Object.entries(requirements).forEach(([id, valid]) => {
                    const element = document.getElementById(id);
                    if (valid) {
                        element.classList.add('valid');
                        element.classList.remove('invalid');
                        element.querySelector('i').className = 'fas fa-check';
                    } else {
                        element.classList.add('invalid');
                        element.classList.remove('valid');
                        element.querySelector('i').className = 'fas fa-times';
                    }
                });
            }

            function checkPasswordMatch() {
                const newPassword = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (newPassword && confirmPassword) {
                    if (newPassword === confirmPassword) {
                        confirmPasswordInput.classList.remove('is-invalid');
                        confirmPasswordInput.classList.add('is-valid');
                    } else {
                        confirmPasswordInput.classList.remove('is-valid');
                        confirmPasswordInput.classList.add('is-invalid');
                    }
                }
            }

            // Password form validation
            const passwordForm = document.getElementById('passwordForm');
            passwordForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const currentPassword = document.getElementById('current_password').value;
                const newPassword = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (!currentPassword || !newPassword || !confirmPassword) {
                    alert('Semua field password harus diisi!');
                    return;
                }

                if (newPassword !== confirmPassword) {
                    alert('Konfirmasi password tidak cocok!');
                    return;
                }

                // Check password strength
                if (!SecurityHelper.validatePassword(newPassword)) {
                    alert('Password baru tidak memenuhi syarat keamanan!');
                    return;
                }

                this.submit();
            });

            // Update last activity time
            setInterval(function() {
                document.getElementById('lastActivity').textContent = new Date().toLocaleString('id-ID');
            }, 60000); // Update every minute

            // Avatar upload
            document.getElementById('avatarUpload').addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file maksimal 2MB!');
                        return;
                    }

                    // Here you would upload the avatar
                    console.log('Avatar upload:', file.name);
                    alert('Fitur upload avatar akan segera tersedia!');
                }
            });
        });

        // Helper functions
        const SecurityHelper = {
            validatePassword: function(password) {
                return password.length >= 8 &&
                       /[a-z]/.test(password) &&
                       /[A-Z]/.test(password) &&
                       /[0-9]/.test(password) &&
                       /[^A-Za-z0-9]/.test(password);
            }
        };
    </script>
</body>
</html>