<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Code Camp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            margin: 20px;
            position: relative;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px 30px;
            text-align: center;
            position: relative;
        }

        .login-header::before {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 20px solid #764ba2;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
        }

        .login-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .login-form {
            padding: 40px 30px 30px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 16px;
        }

        .form-group.has-label .input-icon {
            top: calc(50% + 10px);
        }

        .password-toggle {
            cursor: pointer;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: #667eea;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #667eea;
        }

        .remember-me label {
            font-size: 14px;
            color: #6c757d;
            margin: 0;
        }

        .login-footer {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .login-footer p {
            font-size: 12px;
            color: #6c757d;
            margin: 0;
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

        .security-notice i {
            color: #ffc107;
            margin-right: 8px;
        }

        .loading-spinner {
            display: none;
            margin-right: 10px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .btn-login.loading .loading-spinner {
            display: inline-block;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
            }
            
            .login-header {
                padding: 30px 20px 25px;
            }
            
            .login-form {
                padding: 30px 20px 25px;
            }
            
            .login-footer {
                padding: 15px 20px;
            }
        }

        /* Floating particles animation */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }

        @keyframes float {
            0% {
                opacity: 0;
                transform: translateY(100vh) rotate(0deg);
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                transform: translateY(-100px) rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <div class="particles" id="particles"></div>

    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1>Admin Panel</h1>
            <p>Masuk untuk mengakses dashboard admin</p>
        </div>

        <form class="login-form" method="POST" action="admin.php?action=process_login" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <div class="security-notice">
                <i class="fas fa-shield-alt"></i>
                <strong>Peringatan Keamanan:</strong> Hanya administrator yang memiliki email dengan kata 'admin' yang dapat mengakses panel ini.
            </div>

            <div class="form-group has-label">
                <label for="email">Email Admin</label>
                <input type="email" 
                       class="form-control" 
                       id="email" 
                       name="email" 
                       placeholder="Masukkan email admin Anda"
                       required 
                       autocomplete="username">
                <i class="fas fa-envelope input-icon"></i>
            </div>

            <div class="form-group has-label">
                <label for="password">Password</label>
                <input type="password" 
                       class="form-control" 
                       id="password" 
                       name="password" 
                       placeholder="Masukkan password Anda"
                       required 
                       autocomplete="current-password">
                <i class="fas fa-eye password-toggle input-icon" id="passwordToggle"></i>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <i class="fas fa-spinner loading-spinner"></i>
                <span class="btn-text">Masuk ke Admin Panel</span>
            </button>
        </form>

        <div class="login-footer">
            <p>&copy; <?= date('Y') ?> Code Camp. Sistem Admin Panel.</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Create floating particles
            function createParticles() {
                const particles = document.getElementById('particles');
                for (let i = 0; i < 50; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.width = Math.random() * 4 + 1 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 15 + 's';
                    particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                    particles.appendChild(particle);
                }
            }
            createParticles();

            // Password toggle
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');

            passwordToggle.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.className = 'fas fa-eye-slash password-toggle input-icon';
                } else {
                    passwordInput.type = 'password';
                    this.className = 'fas fa-eye password-toggle input-icon';
                }
            });

            // Form submission with loading state
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const btnText = loginBtn.querySelector('.btn-text');

            loginForm.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                
                // Check if email contains 'admin'
                if (!email.toLowerCase().includes('admin')) {
                    e.preventDefault();
                    alert('Email harus mengandung kata "admin" untuk mengakses panel admin.');
                    return;
                }

                // Show loading state
                loginBtn.disabled = true;
                loginBtn.classList.add('loading');
                btnText.textContent = 'Memverifikasi...';

                // If validation passes, allow form submission
                // The loading state will be maintained until page reload
            });

            // Input validation
            const emailInput = document.getElementById('email');
            emailInput.addEventListener('input', function() {
                const email = this.value.toLowerCase();
                if (email && !email.includes('admin')) {
                    this.style.borderColor = '#dc3545';
                    this.style.background = '#fff5f5';
                } else {
                    this.style.borderColor = '#e1e5e9';
                    this.style.background = '#f8f9fa';
                }
            });

            // Auto-focus on email field
            emailInput.focus();

            // Prevent multiple rapid submissions
            let isSubmitting = false;
            loginForm.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                isSubmitting = true;
            });

            // Security warning for non-HTTPS
            if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
                console.warn('⚠️ Koneksi tidak aman! Gunakan HTTPS untuk keamanan login admin.');
            }
        });

        // Prevent back button after logout
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    </script>
</body>
</html>