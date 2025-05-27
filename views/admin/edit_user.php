<?php
// views/admin/edit_user.php
session_start();

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: /views/auth/login.php');
    exit;
}

// Get user ID
$userId = $_GET['id'] ?? null;
if (!$userId || !is_numeric($userId)) {
    $_SESSION['error'] = 'ID user tidak valid';
    header('Location: /views/admin/manage_users.php');
    exit;
}

// Initialize controller and get user data
require_once '../../config/database.php';
require_once '../../controllers/AdminController.php';

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

$user = $admin->getUserById($userId);
if (!$user) {
    $_SESSION['error'] = 'User tidak ditemukan';
    header('Location: /views/admin/manage_users.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminController = new AdminController($db);
    $adminController->updateUser();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna - Admin Campus Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/custom.css" rel="stylesheet">
</head>
<body>
    <!-- Admin Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/views/admin/dashboard.php">
                <i class="fas fa-shield-alt me-2"></i>Admin Panel - Campus Hub
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i><?= htmlspecialchars($_SESSION['admin_name']) ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/views/admin/dashboard.php?action=logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 bg-light">
                <div class="d-flex flex-column p-3">
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="/views/admin/dashboard.php" class="nav-link">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/views/admin/manage_users.php" class="nav-link active">
                                <i class="fas fa-users me-2"></i>Kelola Akun
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit Pengguna</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/views/admin/manage_users.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Edit Form -->
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-user-edit me-2"></i>Edit Data Pengguna
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="editForm">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="<?= htmlspecialchars($user['name']) ?>" required>
                                            <div class="invalid-feedback">
                                                Nama lengkap harus diisi.
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?= htmlspecialchars($user['alamat_email']) ?>" required>
                                            <div class="invalid-feedback">
                                                Email harus diisi dengan format yang benar.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">No. Telepon</label>
                                            <input type="text" class="form-control" id="phone" name="phone" 
                                                   value="<?= htmlspecialchars($user['no_telepon'] ?? '') ?>"
                                                   placeholder="Contoh: 08123456789">
                                            <div class="form-text">Opsional - kosongkan jika tidak ada</div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tanggal Daftar</label>
                                            <input type="text" class="form-control" 
                                                   value="<?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>" readonly>
                                            <div class="form-text">Tidak dapat diubah</div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="/views/admin/manage_users.php" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Batal
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- User Info Card -->
                        <div class="card shadow mt-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-info">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Pengguna
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>ID Pengguna:</strong> <?= htmlspecialchars($user['id']) ?></p>
                                        <p><strong>Tanggal Daftar:</strong> <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Terakhir Update:</strong> 
                                           <?= $user['updated_at'] ? date('d/m/Y H:i', strtotime($user['updated_at'])) : 'Belum pernah' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation
        (function() {
            'use strict';
            
            const form = document.getElementById('editForm');
            
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                // Additional validation
                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();
                
                if (name.length < 2) {
                    event.preventDefault();
                    document.getElementById('name').setCustomValidity('Nama harus minimal 2 karakter');
                } else {
                    document.getElementById('name').setCustomValidity('');
                }
                
                // Email format validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    event.preventDefault();
                    document.getElementById('email').setCustomValidity('Format email tidak valid');
                } else {
                    document.getElementById('email').setCustomValidity('');
                }
                
                form.classList.add('was-validated');
            }, false);
            
            // Real-time validation
            document.getElementById('name').addEventListener('input', function() {
                const value = this.value.trim();
                if (value.length >= 2) {
                    this.setCustomValidity('');
                } else {
                    this.setCustomValidity('Nama harus minimal 2 karakter');
                }
            });
            
            document.getElementById('email').addEventListener('input', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailRegex.test(this.value)) {
                    this.setCustomValidity('');
                } else {
                    this.setCustomValidity('Format email tidak valid');
                }
            });
        })();
    </script>
</body>
</html>