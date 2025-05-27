<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Code Camp</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" href="../../assets/images/logo/logo_mobile.png" type="image/x-icon">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            height: 100vh;
            display: flex;
        }
        .form-side {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
        }
        .form-content {
            width: 70%;
            max-width: 450px;
        }
        .brand-side {
            width: 50%;
            background-color: #1e3a8a;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .brand-side::before,
        .brand-side::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background-color: rgba(59, 130, 246, 0.3);
        }
        .brand-side::before {
            width: 600px;
            height: 600px;
            top: -300px;
            right: -300px;
        }
        .brand-side::after {
            width: 500px;
            height: 500px;
            bottom: -250px;
            left: -250px;
            background-color: rgba(59, 130, 246, 0.2);
        }
        .input-field {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        .input-field:focus {
            border-color: #3b82f6;
            outline: none;
        }
        .password-field {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
        }
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .form-side {
                width: 100%;
                height: 100%;
            }
            .brand-side {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Form Side -->
        <div class="form-side">
            <div class="form-content">
                <h1 class="text-2xl font-bold text-gray-800 mb-1">Selamat Datang!</h1>
                <p class="text-gray-600 mb-6">Masuk dengan akun Anda</p>
                
                <!-- Alert Messages -->
                <?php if(isset($_GET['error'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                        <?php 
                            $error = $_GET['error'];
                            if($error == 'empty') {
                                echo "Silakan isi semua field";
                            } elseif($error == 'invalid') {
                                echo "Email atau password salah";
                            } else {
                                echo "Terjadi kesalahan, silakan coba lagi";
                            }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['success']) && $_GET['success'] == 'register'): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                        Registrasi berhasil! Silakan login.
                    </div>
                <?php endif; ?>
                
                <!-- Login Form -->
                <form action="../../index.php?action=process_login" method="post" id="loginForm">
                    <div class="mb-4">
                        <label for="alamat_email" class="block text-gray-700 mb-1">Alamat email</label>
                        <input type="email" name="alamat_email" id="alamat_email" 
                            class="input-field" 
                            placeholder="Masukkan email Anda" required>
                    </div>
                    
                    <div class="mb-3">
                        <div class="flex justify-between mb-1">
                            <label for="password" class="block text-gray-700">Password</label>
                            <a href="#" class="text-blue-500 text-sm hover:underline">Lupa password?</a>
                        </div>
                        <div class="password-field">
                            <input type="password" name="password" id="password" 
                                class="input-field" 
                                placeholder="Masukkan password" required>
                            <button type="button" id="togglePassword" class="password-toggle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center mb-6">
                        <input type="checkbox" id="ingat_saya" name="ingat_saya" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="ingat_saya" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 rounded transition duration-200">
                        Masuk
                    </button>
                </form>
                
                <div class="text-center mt-6">
                    <p class="text-gray-600 text-sm">
                        Belum punya akun? 
                        <a href="../../index.php?action=signup" class="text-blue-500 hover:underline font-medium">Daftar Sekarang</a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Brand Side -->
        <div class="brand-side">
            <div class="relative z-10">
                <div class="text-4xl font-bold text-white flex items-center">
                    <img src="../../assets/images/logo.png" alt="logo" class="lg:h-40 md:h-32 h-24 mr-2" draggable="false">
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Change icon
            if (type === 'password') {
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>';
            } else {
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" /><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" /></svg>';
            }
        });
    </script>
</body>
</html>