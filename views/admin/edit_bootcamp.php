<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bootcamp - Code Camp Admin</title>
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
                            <h1 class="text-2xl font-bold text-gray-800">Edit Bootcamp</h1>
                            <p class="text-gray-600 mt-1">Ubah informasi bootcamp</p>
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

                <!-- Bootcamp Overview -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex items-center space-x-4">
                        <!-- Current Image -->
                        <div class="h-20 w-20 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                            <?php if (!empty($bootcamp['image'])): ?>
                                <img 
                                    src="assets/images/bootcamps/<?= htmlspecialchars($bootcamp['image']) ?>" 
                                    alt="<?= htmlspecialchars($bootcamp['title']) ?>"
                                    class="w-full h-full object-cover"
                                >
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Bootcamp Info -->
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($bootcamp['title'] ?? '') ?></h2>
                            <p class="text-gray-600"><?= htmlspecialchars($bootcamp['category_name'] ?? 'Tidak berkategori') ?></p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="text-sm text-gray-500">
                                    ID: <?= $bootcamp['id'] ?>
                                </span>
                                <?php 
                                $statusClass = match($bootcamp['status'] ?? 'draft') {
                                    'active' => 'bg-green-100 text-green-800',
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'archived' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                ?>
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
                                    <?= ucfirst($bootcamp['status'] ?? 'draft') ?>
                                </span>
                                <?php if ($bootcamp['featured'] ?? false): ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        Featured
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Peserta Terdaftar</div>
                            <div class="text-2xl font-bold text-primary"><?= number_format($bootcamp['total_enrollments'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="admin.php?action=update_bootcamp" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="id" value="<?= $bootcamp['id'] ?>">

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
                                    value="<?= htmlspecialchars($bootcamp['title'] ?? '') ?>"
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
                                    value="<?= htmlspecialchars($bootcamp['slug'] ?? '') ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                                    placeholder="full-stack-web-development-bootcamp"
                                >
                                <p class="text-sm text-gray-500 mt-1">URL akan menjadi: codecamp.com/bootcamp/<span id="slug-preview"><?= htmlspecialchars($bootcamp['slug'] ?? '') ?></span></p>
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
                                ><?= htmlspecialchars($bootcamp['description'] ?? '') ?></textarea>
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
                                            <option value="<?= $category['id'] ?>" <?= ($bootcamp['category_id'] ?? 0) == $category['id'] ? 'selected' : '' ?>>
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
                                        value="<?= htmlspecialchars($bootcamp['instructor_name'] ?? '') ?>"
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
                                            value="<?= $bootcamp['price'] ?? 0 ?>"
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
                                            value="<?= $bootcamp['discount_price'] ?? 0 ?>"
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
                                        value="<?= $bootcamp['start_date'] ?? '' ?>"
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
                                        value="<?= htmlspecialchars($bootcamp['duration'] ?? '') ?>"
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
                                    value="<?= $bootcamp['max_participants'] ?? 0 ?>"
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
                            <p class="text-gray-600 mt-1">Upload gambar baru dan atur status bootcamp</p>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Current Image Display -->
                            <?php if (!empty($bootcamp['image'])): ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini</label>
                                    <div class="relative inline-block">
                                        <img 
                                            src="assets/images/bootcamps/<?= htmlspecialchars($bootcamp['image']) ?>" 
                                            alt="Current bootcamp image"
                                            class="w-48 h-32 object-cover rounded-lg shadow-sm"
                                        >
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Image Upload -->
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                    <?= !empty($bootcamp['image']) ? 'Ganti Gambar' : 'Upload Gambar' ?>
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
                                <!-- New Image Preview -->
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
                                        <option value="draft" <?= ($bootcamp['status'] ?? 'draft') == 'draft' ? 'selected' : '' ?>>Draft - Belum dipublikasikan</option>
                                        <option value="active" <?= ($bootcamp['status'] ?? '') == 'active' ? 'selected' : '' ?>>Aktif - Dipublikasikan dan dapat dibeli</option>
                                        <option value="archived" <?= ($bootcamp['status'] ?? '') == 'archived' ? 'selected' : '' ?>>Arsip - Tidak aktif</option>
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
                                                <?= !($bootcamp['featured'] ?? false) ? 'checked' : '' ?>
                                                class="text-primary focus:ring-primary border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Normal</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input 
                                                type="radio" 
                                                name="featured" 
                                                value="1"
                                                <?= ($bootcamp['featured'] ?? false) ? 'checked' : '' ?>
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
                                Simpan Perubahan
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

                <!-- Additional Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Aksi Tambahan</h3>
                        <p class="text-gray-600 mt-1">Opsi lainnya untuk bootcamp ini</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- View Public Page -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-2">Lihat Halaman Publik</h4>
                                <p class="text-sm text-gray-600 mb-4">Lihat bagaimana bootcamp ini tampil untuk user</p>
                                <a 
                                    href="bootcamp/detail.php?slug=<?= htmlspecialchars($bootcamp['slug'] ?? '') ?>" 
                                    target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors duration-200"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M7 7l10 10M17 7v4"></path>
                                    </svg>
                                    Lihat Halaman
                                </a>
                            </div>

                            <!-- Delete Bootcamp -->
                            <div class="border border-red-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-2">Hapus Bootcamp</h4>
                                <p class="text-sm text-gray-600 mb-4">Hapus bootcamp permanen dari sistem</p>
                                <a 
                                    href="admin.php?action=delete_bootcamp&id=<?= $bootcamp['id'] ?>" 
                                    class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors duration-200"
                                    onclick="return confirm('PERINGATAN: Aksi ini akan menghapus bootcamp secara permanen dan tidak dapat dibatalkan. Yakin ingin melanjutkan?')"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus Bootcamp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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