<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Code Camp</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" href="../../assets/images/logo/logo_mobile.png" type="image/x-icon">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .signup-container {
            height: 100vh;
            display: flex;
        }
        .form-side {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            overflow-y: auto;
        }
        .form-content {
            width: 70%;
            max-width: 450px;
            padding: 40px 0;
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
            .signup-container {
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
    <div class="signup-container">
        <!-- Form Side -->
        <div class="form-side">
            <div class="form-content">
                <h1 class="text-2xl font-bold text-gray-800 mb-1">Daftar Sekarang!</h1>
                <p class="text-gray-600 mb-6">Buat akun untuk di BREECE</p>
                
                <!-- Alert Messages -->
                <?php if(isset($_GET['error'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                        <?php 
                            $error = $_GET['error'];
                            if($error == 'empty') {
                                echo "Silakan isi semua field";
                            } elseif($error == 'email_exists') {
                                echo "Email sudah digunakan";
                            } elseif($error == 'password_mismatch') {
                                echo "Password tidak cocok";
                            } else {
                                echo "Terjadi kesalahan, silakan coba lagi";
                            }
                        ?>
                    </div>
                <?php endif; ?>
                
                <!-- Sign Up Form -->
                <form action="../../index.php?action=process_signup" method="post" id="signupForm">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 mb-1">Nama</label>
                        <input type="text" name="name" id="name" 
                            class="input-field" 
                            placeholder="Nama lengkap" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="alamat_email" class="block text-gray-700 mb-1">Alamat email</label>
                        <input type="email" name="alamat_email" id="alamat_email" 
                            class="input-field" 
                            placeholder="contoh@email.com" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 mb-1">Password</label>
                        <div class="password-field">
                            <input type="password" name="password" id="password" 
                                class="input-field" 
                                placeholder="Minimal 6 karakter" required>
                            <button type="button" id="togglePassword" class="password-toggle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="block text-gray-700 mb-1">Konfirmasi Password</label>
                        <div class="password-field">
                            <input type="password" name="confirm_password" id="confirm_password" 
                                class="input-field" 
                                placeholder="Konfirmasi password Anda" required>
                            <button type="button" id="toggleConfirmPassword" class="password-toggle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="no_telepon" class="block text-gray-700 mb-1">No Telepon</label>
                        <input type="tel" name="no_telepon" id="no_telepon" 
                            class="input-field" 
                            placeholder="Contoh: 0812xxxxxxxx">
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 rounded transition duration-200">
                        Daftar
                    </button>
                </form>
                
                <div class="text-center mt-6">
                    <p class="text-gray-600 text-sm">
                        Sudah punya akun? 
                        <a href="../../index.php?action=login" class="text-blue-500 hover:underline font-medium">Login disini</a>
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

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('confirm_password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Change icon
            if (type === 'password') {
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>';
            } else {
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" /><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" /></svg>';
            }
        });

        // Validation
        document.getElementById('signupForm').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                event.preventDefault();
                alert('Password dan konfirmasi password tidak cocok!');
            }
            
            if (password.length < 6) {
                event.preventDefault();
                alert('Password minimal 6 karakter!');
            }
        });
    </script>
</body>
</html>