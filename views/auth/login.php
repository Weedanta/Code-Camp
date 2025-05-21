<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Campus Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .brand-side {
            background-color: #003366;
            overflow: hidden;
            position: relative;
        }
        
        .brand-side::before, .brand-side::after {
            content: '';
            position: absolute;
            background-color: #0078ff;
            border-radius: 50%;
            z-index: 1;
        }
        
        .brand-side::before {
            width: 500px;
            height: 500px;
            bottom: -250px;
            right: -250px;
            opacity: 0.7;
        }
        
        .brand-side::after {
            width: 300px;
            height: 300px;
            top: -150px;
            left: -150px;
            opacity: 0.5;
        }
        
        .campus-hub-logo {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Form Side -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-6 md:p-10">
            <div class="w-full max-w-md">
                <h1 class="text-2xl font-bold text-gray-800 mb-1">Selamat Datang!</h1>
                <p class="text-gray-600 mb-8">Masuk dengan akun Anda</p>
                
                <!-- Pesan error atau sukses -->
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
                
                <!-- Form login -->
                <form action="index.php?action=process_login" method="post" id="loginForm">
                    <div class="mb-4">
                        <label for="alamat_email" class="block text-gray-700 text-sm font-medium mb-2">Alamat email</label>
                        <input type="email" id="alamat_email" name="alamat_email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="contoh@email.com" required>
                    </div>
                    
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" id="ingat_saya" name="ingat_saya" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="ingat_saya" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
                        </div>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Lupa password?</a>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-300">
                        Masuk
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-700">
                        Belum punya akun? 
                        <a href="index.php?action=signup" class="text-blue-600 hover:text-blue-800 font-medium">Daftar Sekarang!</a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Brand Side -->
        <div class="hidden md:flex md:w-1/2 brand-side items-center justify-center p-10 text-white">
            <div class="text-center campus-hub-logo">
                <img src="../../assets/images/logo.png" alt="Logo" class="w-full mb-4">
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Change icon based on password visibility
            this.innerHTML = type === 'password' 
                ? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>' 
                : '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708"/></svg>';
        });

        // Client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            const email = document.getElementById('alamat_email').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (email === '' || password === '') {
                event.preventDefault();
                alert('Silakan isi semua field');
            }
            
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                event.preventDefault();
                alert('Format email tidak valid');
            }
        });
    </script>
</body>
</html>