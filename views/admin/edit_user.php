<?php
// views/admin/edit_user.php - Edit User Page
$pageTitle = 'Edit User';

// Security check - prevent XSS
function sanitizeOutput($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: margin-left 0.3s ease;
        }

        .admin-sidebar.collapsed + .main-content {
            margin-left: 70px;
        }

        .content-wrapper {
            padding: 30px;
            max-width: 1200px;
        }

        .page-header {
            background: white;
            padding: 20px 30px;
            margin: -30px -30px 30px;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            margin: 0;
            color: #495057;
            font-size: 28px;
            font-weight: 600;
        }

        .breadcrumb {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }

        .page-actions {
            display: flex;
            gap: 10px;
        }

        .edit-form-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
        }

        .form-header h3 {
            margin: 0 0 5px;
            font-size: 20px;
            font-weight: 600;
        }

        .form-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .form-content {
            padding: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .required {
            color: #dc3545;
        }

        .form-control {
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control:invalid {
            border-color: #dc3545;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .form-control.is-invalid + .invalid-feedback {
            display: block;
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

        .status-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
        }

        .status-option {
            position: relative;
        }

        .status-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .status-option label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .status-option input:checked + label {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .status-indicator.active { background: #28a745; }
        .status-indicator.inactive { background: #dc3545; }
        .status-indicator.pending { background: #ffc107; }

        .user-stats {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            padding-top: 25px;
            border-top: 1px solid #e9ecef;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .security-notice {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 13px;
            color: #856404;
        }

        .help-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        .verification-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 500;
        }

        .verification-status.verified {
            background: #d4edda;
            color: #155724;
        }

        .verification-status.unverified {
            background: #fff3cd;
            color: #856404;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .content-wrapper {
                padding: 20px;
            }
            
            .page-header {
                padding: 15px 20px;
                margin: -20px -20px 20px;
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
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
                        <a href="admin.php?action=manage_users" style="color: #667eea; text-decoration: none;">
                            <i class="fas fa-users"></i> Kelola Users
                        </a> / 
                        Edit User
                    </div>
                </div>
                <div class="page-actions">
                    <a href="admin.php?action=manage_users" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
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

                <?php if (isset($user) && $user): ?>
                    <!-- Security Notice -->
                    <div class="security-notice">
                        <i class="fas fa-shield-alt"></i>
                        <strong>Keamanan:</strong> Pastikan data yang dimasukkan valid dan aman. Perubahan akan tercatat dalam log aktivitas.
                    </div>

                    <!-- User Stats -->
                    <div class="user-stats">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-value"><?= number_format($user['total_orders'] ?? 0) ?></div>
                                <div class="stat-label">Total Orders</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">Rp <?= number_format($user['total_spent'] ?? 0) ?></div>
                                <div class="stat-label">Total Pembelian</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?= date('d M Y', strtotime($user['created_at'])) ?></div>
                                <div class="stat-label">Bergabung</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">
                                    <span class="verification-status <?= $user['email_verified'] ? 'verified' : 'unverified' ?>">
                                        <i class="fas fa-<?= $user['email_verified'] ? 'check-circle' : 'clock' ?>"></i>
                                        <?= $user['email_verified'] ? 'Terverifikasi' : 'Belum Verifikasi' ?>
                                    </span>
                                </div>
                                <div class="stat-label">Status Email</div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <div class="edit-form-container">
                        <div class="form-header">
                            <h3>Edit Data User</h3>
                            <p>ID: <?= sanitizeOutput($user['id']) ?> | Terakhir diupdate: <?= date('d M Y H:i', strtotime($user['updated_at'] ?? $user['created_at'])) ?></p>
                        </div>

                        <div class="form-content">
                            <form method="POST" action="admin.php?action=update_user" id="editUserForm" novalidate>
                                <!-- CSRF Protection -->
                                <input type="hidden" name="csrf_token" value="<?= sanitizeOutput($_SESSION['csrf_token'] ?? '') ?>">
                                <input type="hidden" name="id" value="<?= sanitizeOutput($user['id']) ?>">

                                <div class="form-grid">
                                    <!-- Nama Lengkap -->
                                    <div class="form-group">
                                        <label for="name">
                                            <i class="fas fa-user"></i>
                                            Nama Lengkap <span class="required">*</span>
                                        </label>
                                        <div class="input-group">
                                            <i class="fas fa-user input-group-icon"></i>
                                            <input type="text" 
                                                   id="name" 
                                                   name="name" 
                                                   class="form-control" 
                                                   value="<?= sanitizeOutput($user['name']) ?>"
                                                   required
                                                   minlength="2"
                                                   maxlength="100"
                                                   pattern="[a-zA-Z\s]+">
                                            <div class="invalid-feedback">Nama harus diisi dengan minimal 2 karakter (huruf dan spasi saja).</div>
                                        </div>
                                        <div class="help-text">Masukkan nama lengkap user</div>
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group">
                                        <label for="email">
                                            <i class="fas fa-envelope"></i>
                                            Alamat Email <span class="required">*</span>
                                        </label>
                                        <div class="input-group">
                                            <i class="fas fa-envelope input-group-icon"></i>
                                            <input type="email" 
                                                   id="email" 
                                                   name="email" 
                                                   class="form-control" 
                                                   value="<?= sanitizeOutput($user['alamat_email']) ?>"
                                                   required
                                                   maxlength="255">
                                            <div class="invalid-feedback">Masukkan alamat email yang valid.</div>
                                        </div>
                                        <div class="help-text">Email akan digunakan untuk login dan komunikasi</div>
                                    </div>

                                    <!-- Nomor Telepon -->
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
                                                   value="<?= sanitizeOutput($user['no_telepon']) ?>"
                                                   pattern="[0-9+\-\s()]+"
                                                   maxlength="20">
                                            <div class="invalid-feedback">Masukkan nomor telepon yang valid.</div>
                                        </div>
                                        <div class="help-text">Format: +62 atau 08xx (opsional)</div>
                                    </div>

                                    <!-- Status -->
                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-toggle-on"></i>
                                            Status Akun <span class="required">*</span>
                                        </label>
                                        <div class="status-options">
                                            <div class="status-option">
                                                <input type="radio" 
                                                       id="status_active" 
                                                       name="status" 
                                                       value="active" 
                                                       <?= $user['status'] === 'active' ? 'checked' : '' ?>
                                                       required>
                                                <label for="status_active">
                                                    <span class="status-indicator active"></span>
                                                    Aktif
                                                </label>
                                            </div>
                                            <div class="status-option">
                                                <input type="radio" 
                                                       id="status_inactive" 
                                                       name="status" 
                                                       value="inactive" 
                                                       <?= $user['status'] === 'inactive' ? 'checked' : '' ?>>
                                                <label for="status_inactive">
                                                    <span class="status-indicator inactive"></span>
                                                    Tidak Aktif
                                                </label>
                                            </div>
                                            <div class="status-option">
                                                <input type="radio" 
                                                       id="status_pending" 
                                                       name="status" 
                                                       value="pending" 
                                                       <?= $user['status'] === 'pending' ? 'checked' : '' ?>>
                                                <label for="status_pending">
                                                    <span class="status-indicator pending"></span>
                                                    Pending
                                                </label>
                                            </div>
                                        </div>
                                        <div class="help-text">Status akan mempengaruhi akses user ke sistem</div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save"></i>
                                        Simpan Perubahan
                                    </button>
                                    
                                    <a href="admin.php?action=manage_users" class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                        Batal
                                    </a>
                                    
                                    <a href="admin.php?action=reset_user_password&id=<?= $user['id'] ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('Yakin ingin reset password user ini? Password baru akan dikirim via email.')">
                                        <i class="fas fa-key"></i>
                                        Reset Password
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        User tidak ditemukan atau telah dihapus.
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editUserForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Real-time validation
            const inputs = form.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', validateField);
                input.addEventListener('input', clearError);
            });

            function validateField(e) {
                const field = e.target;
                const isValid = field.checkValidity();
                
                if (!isValid) {
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            }

            function clearError(e) {
                e.target.classList.remove('is-invalid');
            }

            // Email uniqueness check
            const emailInput = document.getElementById('email');
            let emailTimeout;
            
            emailInput.addEventListener('input', function() {
                clearTimeout(emailTimeout);
                const email = this.value.trim();
                const userId = document.querySelector('input[name="id"]').value;
                
                if (email && email !== "<?= sanitizeOutput($user['alamat_email']) ?>") {
                    emailTimeout = setTimeout(() => {
                        checkEmailUniqueness(email, userId);
                    }, 500);
                }
            });

            function checkEmailUniqueness(email, userId) {
                // In a real implementation, this would make an AJAX call
                // to check if email already exists
                console.log('Checking email uniqueness:', email);
            }

            // Phone number formatting
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function() {
                let value = this.value.replace(/[^\d+\-\s()]/g, '');
                this.value = value;
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate all required fields
                let isValid = true;
                inputs.forEach(input => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                        isValid = false;
                    }
                });

                if (!isValid) {
                    alert('Mohon perbaiki error pada form sebelum menyimpan.');
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

                // Submit form
                this.submit();
            });

            // Prevent multiple submissions
            let isSubmitting = false;
            form.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                isSubmitting = true;
            });

            // Auto-save draft (optional enhancement)
            const autoSaveInterval = setInterval(() => {
                const formData = new FormData(form);
                localStorage.setItem(`user_edit_draft_${formData.get('id')}`, JSON.stringify(Object.fromEntries(formData)));
            }, 30000); // Save every 30 seconds

            // Clean up on page unload
            window.addEventListener('beforeunload', () => {
                clearInterval(autoSaveInterval);
            });
        });
    </script>
</body>
</html>