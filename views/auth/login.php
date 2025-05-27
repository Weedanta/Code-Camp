<?php
// views/auth/login.php - Updated version with admin detection
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /views/auth/dashboard/dashboard.php');
    exit;
}

if (isset($_SESSION['admin_id'])) {
    header('Location: /views/admin/dashboard.php');
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../config/database.php';
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email dan password harus diisi';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if email contains 'admin' for admin login
        if (strpos(strtolower($email), 'admin') !== false) {
            require_once '../../controllers/AdminController.php';
            $adminController = new AdminController($db);
            $adminController->login();
        } else {
            // Regular user login
            require_once '../../controllers/AuthController.php';
            $authController = new AuthController($db);
            $authController->login();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Campus Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/custom.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .admin-indicator {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
            display: none;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-header login-header text-center py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>Campus Hub
                        </h3>
                        <p class="mb-0 mt-2">Login ke Akun Anda</p>
                    </div>
                    <div class="card-body p-4">
                        <!-- Admin Indicator -->
                        <div id="adminIndicator" class="admin-indicator text-center">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>Mode Admin Terdeteksi</strong>
                            <small class="d-block">Email mengandung kata 'admin'</small>
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

                        <form method="POST" id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Masukkan email Anda" required>
                                <div class="form-text">
                                    <small id="emailHelp">Gunakan email yang mengandung 'admin' untuk akses admin</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Masukkan password Anda" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="mb-0">Belum punya akun?</p>
                                <a href="signup.php" class="text-decoration-none">
                                    <i class="fas fa-user-plus me-1"></i>Daftar Sekarang
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card mt-3 border-0 bg-transparent">
                    <div class="card-body text-center text-white">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Untuk akses admin, gunakan email yang mengandung kata "admin"
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password toggle functionality
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const toggleIcon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        });

        // Admin detection
        document.getElementById('email').addEventListener('input', function() {
            const email = this.value.toLowerCase();
            const adminIndicator = document.getElementById('adminIndicator');
            const emailHelp = document.getElementById('emailHelp');
            
            if (email.includes('admin')) {
                adminIndicator.style.display = 'block';
                emailHelp.innerHTML = '<i class="fas fa-shield-alt me-1"></i><strong>Mode Admin:</strong> Anda akan login sebagai administrator';
                emailHelp.className = 'form-text text-warning fw-bold';
            } else {
                adminIndicator.style.display = 'none';
                emailHelp.innerHTML = 'Gunakan email yang mengandung "admin" untuk akses admin';
                emailHelp.className = 'form-text';
            }
        });

        // Form validation and security
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            // Basic validation
            if (!email || !password) {
                e.preventDefault();
                alert('Email dan password harus diisi');
                return;
            }
            
            // Email format validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Format email tidak valid');
                return;
            }
            
            // Minimum password length
            if (password.length < 6) {
                e.preventDefault();
                alert('Password harus minimal 6 karakter');
                return;
            }
        });

        // XSS Protection - Sanitize inputs
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Remove any script tags or suspicious content
                    this.value = this.value.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
                });
            });
        });
    </script>
</body>
</html>