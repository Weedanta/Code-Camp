<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Bootcamp - Code Camp Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#1e40af',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 overflow-y-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 p-4 lg:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 ml-12 lg:ml-0">
                        <a 
                            href="admin.php?action=manage_bootcamps" 
                            class="text-gray-500 hover:text-primary transition-colors duration-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Buat Bootcamp Baru</h1>
                            <p class="text-gray-600 mt-1">Tambahkan program bootcamp baru ke platform</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-4 lg:p-6 max-w-4xl mx-auto">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success']) ?></span>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error']) ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Form -->
                <form method="POST" action="admin.php?action=create_bootcamp" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <!-- Basic Information -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Informasi Dasar</h3>
                            <p class="text-gray-600 mt-1">Detail utama tentang bootcamp</p>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Judul Bootcamp <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="title" 
                                    name="title" 
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                    placeholder="Contoh: Full Stack Web Development Bootcamp"
                                    oninput="generateSlug()"
                                >
                            </div>

                            <!-- Slug -->
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                    Slug URL <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="slug" 
                                    name="slug" 
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                    placeholder="full-stack-web-development-bootcamp"
                                >
                                <p class="text-sm text-gray-500 mt-1">URL akan menjadi: codecamp.com/bootcamp/<span id="slug-preview">slug-akan-otomatis</span></p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    id="description" 
                                    name="description" 
                                    required 
                                    rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                    placeholder="Jelaskan tentang bootcamp ini, apa yang akan dipelajari, dan manfaatnya..."
                                ></textarea>
                            </div>

                            <!-- Category and Instructor -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Category -->
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Kategori <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        id="category_id" 
                                        name="category_id" 
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                    >
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>">
                                                <?= htmlspecialchars($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Instructor -->
                                <div>
                                    <label for="instructor_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Instruktur <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="instructor_name" 
                                        name="instructor_name" 
                                        required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                        placeholder="Nama instruktur"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Schedule -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Harga & Jadwal</h3>
                            <p class="text-gray-600 mt-1">Informasi harga dan waktu pelaksanaan</p>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Pricing -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Regular Price -->
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                        Harga Regular <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                                        <input 
                                            type="number" 
                                            id="price" 
                                            name="price" 
                                            required 
                                            min="0"
                                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                            placeholder="0"
                                        >
                                    </div>
                                </div>

                                <!-- Discount Price -->
                                <div>
                                    <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-2">
                                        Harga Diskon (Opsional)
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                                        <input 
                                            type="number" 
                                            id="discount_price" 
                                            name="discount_price" 
                                            min="0"
                                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                            placeholder="0"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Start Date -->
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Mulai <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="date" 
                                        id="start_date" 
                                        name="start_date" 
                                        required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                    >
                                </div>

                                <!-- Duration -->
                                <div>
                                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                                        Durasi <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="duration" 
                                        name="duration" 
                                        required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                        placeholder="Contoh: 12 minggu, 3 bulan"
                                    >
                                </div>
                            </div>

                            <!-- Max Participants -->
                            <div>
                                <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maksimal Peserta
                                </label>
                                <input 
                                    type="number" 
                                    id="max_participants" 
                                    name="max_participants" 
                                    min="0"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                    placeholder="0 = tidak terbatas"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Image & Settings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Gambar & Pengaturan</h3>
                            <p class="text-gray-600 mt-1">Upload gambar dan atur status bootcamp</p>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Image Upload -->
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                    Gambar Bootcamp
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary transition-colors duration-200">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-secondary focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                                <span>Upload gambar</span>
                                                <input id="image" name="image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                                            </label>
                                            <p class="pl-1">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 5MB</p>
                                    </div>
                                </div>
                                <!-- Image Preview -->
                                <div id="image-preview" class="mt-4 hidden">
                                    <img id="preview-img" src="" alt="Preview" class="w-full max-w-sm rounded-lg shadow-sm">
                                </div>
                            </div>

                            <!-- Settings -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        id="status" 
                                        name="status" 
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                    >
                                        <option value="draft">Draft - Belum dipublikasikan</option>
                                        <option value="active">Aktif - Dipublikasikan dan dapat dibeli</option>
                                        <option value="archived">Arsip - Tidak aktif</option>
                                    </select>
                                </div>

                                <!-- Featured -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Featured
                                    </label>
                                    <div class="flex items-center space-x-3">
                                        <label class="flex items-center">
                                            <input 
                                                type="radio" 
                                                name="featured" 
                                                value="0" 
                                                checked
                                                class="text-primary focus:ring-primary border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Normal</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input 
                                                type="radio" 
                                                name="featured" 
                                                value="1"
                                                class="text-primary focus:ring-primary border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Featured</span>
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Bootcamp featured akan ditampilkan di halaman utama</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button 
                                type="submit" 
                                class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-secondary transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50"
                            >
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Buat Bootcamp
                            </button>
                            
                            <a 
                                href="admin.php?action=manage_bootcamps" 
                                class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200 text-center"
                            >
                                Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function generateSlug() {
            const title = document.getElementById('title').value;
            const slug = title
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-') // Replace spaces with hyphens
                .replace(/-+/g, '-') // Remove multiple consecutive hyphens
                .trim('-'); // Remove leading/trailing hyphens
            
            document.getElementById('slug').value = slug;
            document.getElementById('slug-preview').textContent = slug || 'slug-akan-otomatis';
        }

        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const slug = document.getElementById('slug').value.trim();
            const description = document.getElementById('description').value.trim();
            const categoryId = document.getElementById('category_id').value;
            const instructor = document.getElementById('instructor_name').value.trim();
            const price = document.getElementById('price').value;
            const startDate = document.getElementById('start_date').value;
            const duration = document.getElementById('duration').value.trim();
            
            if (!title || !slug || !description || !categoryId || !instructor || !price || !startDate || !duration) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi (*)');
                return;
            }
            
            if (parseFloat(price) < 0) {
                e.preventDefault();
                alert('Harga tidak boleh negatif');
                return;
            }
            
            const discountPrice = document.getElementById('discount_price').value;
            if (discountPrice && parseFloat(discountPrice) >= parseFloat(price)) {
                e.preventDefault();
                alert('Harga diskon harus lebih kecil dari harga regular');
                return;
            }
        });

        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>