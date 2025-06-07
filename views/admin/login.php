<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Campus Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-gradient-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .btn-admin:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
        }
        .input-focus:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="bg-gradient-admin min-h-screen flex items-center justify-center p-4">
    <!-- Background Animation -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white opacity-10 rounded-full animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white opacity-5 rounded-full animate-float" style="animation-delay: -2s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-60 h-60 bg-white opacity-5 rounded-full animate-float" style="animation-delay: -4s;"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-lg mb-4">
                <i class="fas fa-shield-alt text-3xl text-indigo-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Admin Panel</h1>
            <p class="text-indigo-100">Campus Hub Management System</p>
        </div>

        <!-- Login Form -->
        <div class="glass-effect rounded-2xl shadow-2xl p-8">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    <span><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="admin.php?action=login" class="space-y-6">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email Admin
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-300"
                           placeholder="admin@campus-hub.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <p class="text-xs text-gray-500 mt-1">Email harus mengandung kata "admin"</p>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-300 pr-12"
                               placeholder="Masukkan password">
                        <button type="button" 
                                onclick="togglePassword()" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <i id="passwordToggleIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Login Button -->
                <button type="submit" 
                        class="w-full btn-admin text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk ke Admin Panel
                </button>

                <!-- Security Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div class="text-sm text-blue-700">
                            <p class="font-medium mb-1">Keamanan Login</p>
                            <ul class="text-xs space-y-1">
                                <li>• Gunakan email yang mengandung kata "admin"</li>
                                <li>• Sesi akan otomatis berakhir setelah 2 jam</li>
                                <li>• Maksimal 5 percobaan login dalam 15 menit</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Test Accounts Info (Remove in production) -->
        <div class="mt-6 glass-effect rounded-lg p-4 text-center">
            <p class="text-white text-sm mb-2">
                <i class="fas fa-key mr-2"></i>Demo Admin Accounts
            </p>
            <div class="text-xs text-indigo-100 space-y-1">
                <p>Super Admin: <code class="bg-black bg-opacity-20 px-2 py-1 rounded">superadmin@campus-hub.com</code></p>
                <p>Admin: <code class="bg-black bg-opacity-20 px-2 py-1 rounded">admin@campus-hub.com</code></p>
                <p>Password: <code class="bg-black bg-opacity-20 px-2 py-1 rounded">secret</code></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-indigo-100 text-sm">
                © 2025 Campus Hub. Sistem Manajemen Admin.
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Focus on email field
            document.getElementById('email').focus();
            
            // Add loading state to form submission
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('button[type="submit"]');
            
            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                submitBtn.disabled = true;
            });
        });

        // Security: Clear form on page unload
        window.addEventListener('beforeunload', function() {
            document.getElementById('password').value = '';
        });
    </script>
</body>
</html>