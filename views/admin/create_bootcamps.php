<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Bootcamp - Admin Campus Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-gradient {
            background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
        }
        .form-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .image-preview {
            border: 2px dashed #e5e7eb;
            transition: all 0.3s ease;
        }
        .image-preview:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        .image-preview.dragover {
            border-color: #3b82f6;
            background-color: #dbeafe;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 sidebar-gradient shadow-xl">
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-black bg-opacity-20">
                <i class="fas fa-graduation-cap text-2xl text-white mr-3"></i>
                <span class="text-xl font-bold text-white">Campus Hub</span>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="admin.php?action=dashboard" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                <a href="admin.php?action=manage_users" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-users mr-3"></i>
                    Kelola Users
                </a>
                <a href="admin.php?action=manage_bootcamps" class="flex items-center px-4 py-3 text-white bg-indigo-600 rounded-lg">
                    <i class="fas fa-laptop-code mr-3"></i>
                    Kelola Bootcamps
                </a>
                <a href="admin.php?action=manage_categories" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-tags mr-3"></i>
                    Kelola Kategori
                </a>
                <a href="admin.php?action=manage_orders" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-shopping-cart mr-3"></i>
                    Kelola Orders
                </a>
                <a href="admin.php?action=manage_reviews" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-star mr-3"></i>
                    Kelola Reviews
                </a>
                <a href="admin.php?action=manage_forum" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-comments mr-3"></i>
                    Kelola Forum
                </a>
                <a href="admin.php?action=manage_settings" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-cog mr-3"></i>
                    Pengaturan
                </a>
            </nav>
            
            <!-- User Info -->
            <div class="p-4 border-t border-gray-600">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
                        <p class="text-xs text-gray-400"><?php echo htmlspecialchars($_SESSION['admin_role']); ?></p>
                    </div>
                </div>
                <a href="admin.php?action=logout" class="mt-3 w-full flex items-center justify-center px-4 py-2 text-sm text-gray-300 hover:text-white bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                <li class="inline-flex items-center">
                                    <a href="admin.php?action=manage_bootcamps" class="text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-laptop-code mr-2"></i>
                                        Kelola Bootcamps
                                    </a>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                        <span class="text-gray-700 font-medium">Tambah Bootcamp</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl font-bold text-gray-900 mt-2">Tambah Bootcamp Baru</h1>
                        <p class="text-gray-600">Buat program bootcamp atau kursus baru untuk platform</p>
                    </div>
                    <a href="admin.php?action=manage_bootcamps" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6">
            <!-- Alerts -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    <span><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="admin.php?action=create_bootcamp" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::generateCSRFToken(); ?>">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar</h3>
                                <p class="text-sm text-gray-600 mt-1">Detail utama bootcamp yang akan ditampilkan</p>
                            </div>
                            
                            <div class="p-6 space-y-6">
                                <!-- Title -->
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-heading mr-2"></i>Judul Bootcamp *
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           required 
                                           maxlength="255"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                           placeholder="Contoh: Full Stack Web Development Bootcamp"
                                           onblur="generateSlug()">
                                </div>

                                <!-- Slug -->
                                <div>
                                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-link mr-2"></i>URL Slug *
                                    </label>
                                    <input type="text" 
                                           id="slug" 
                                           name="slug" 
                                           required 
                                           maxlength="255"
                                           pattern="[a-z0-9\-]+"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                           placeholder="full-stack-web-development-bootcamp">
                                    <p class="text-xs text-gray-500 mt-1">URL slug akan otomatis dibuat dari judul, atau Anda bisa edit manual</p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-align-left mr-2"></i>Deskripsi *
                                    </label>
                                    <textarea id="description" 
                                              name="description" 
                                              required 
                                              rows="4"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                              placeholder="Jelaskan secara detail tentang bootcamp ini, apa yang akan dipelajari, dan siapa target pesertanya..."></textarea>
                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                        <span>Minimum 50 karakter</span>
                                        <span id="descriptionCount">0 karakter</span>
                                    </div>
                                </div>

                                <!-- Category and Instructor -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-tags mr-2"></i>Kategori *
                                        </label>
                                        <select id="category_id" 
                                                name="category_id" 
                                                required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="">Pilih Kategori</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>">
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="instructor_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-chalkboard-teacher mr-2"></i>Nama Instructor *
                                        </label>
                                        <input type="text" 
                                               id="instructor_name" 
                                               name="instructor_name" 
                                               required 
                                               maxlength="100"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                               placeholder="John Doe">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Schedule -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Harga & Jadwal</h3>
                                <p class="text-sm text-gray-600 mt-1">Pengaturan harga dan waktu pelaksanaan</p>
                            </div>
                            
                            <div class="p-6 space-y-6">
                                <!-- Pricing -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-money-bill mr-2"></i>Harga Normal (Rp) *
                                        </label>
                                        <input type="number" 
                                               id="price" 
                                               name="price" 
                                               required 
                                               min="0"
                                               step="1000"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                               placeholder="1500000">
                                    </div>

                                    <div>
                                        <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-percent mr-2"></i>Harga Diskon (Rp)
                                        </label>
                                        <input type="number" 
                                               id="discount_price" 
                                               name="discount_price" 
                                               min="0"
                                               step="1000"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                               placeholder="1200000">
                                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ada diskon</p>
                                    </div>
                                </div>

                                <!-- Schedule -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-calendar mr-2"></i>Tanggal Mulai
                                        </label>
                                        <input type="date" 
                                               id="start_date" 
                                               name="start_date" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>

                                    <div>
                                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-clock mr-2"></i>Durasi
                                        </label>
                                        <input type="text" 
                                               id="duration" 
                                               name="duration" 
                                               maxlength="50"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                               placeholder="12 Minggu / 3 Bulan">
                                    </div>
                                </div>

                                <!-- Max Participants -->
                                <div>
                                    <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-users mr-2"></i>Maksimal Peserta
                                    </label>
                                    <input type="number" 
                                           id="max_participants" 
                                           name="max_participants" 
                                           min="1"
                                           max="1000"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                           placeholder="25">
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ada batasan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Image Upload -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Gambar Bootcamp</h3>
                                <p class="text-sm text-gray-600 mt-1">Upload gambar cover untuk bootcamp</p>
                            </div>
                            
                            <div class="p-6">
                                <div class="image-preview rounded-lg p-8 text-center" id="imagePreview">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-sm text-gray-600 mb-2">Drag & drop gambar atau klik untuk browse</p>
                                    <p class="text-xs text-gray-500 mb-4">Format: JPG, PNG, GIF (Max: 5MB)</p>
                                    <input type="file" 
                                           id="image" 
                                           name="image" 
                                           accept="image/*"
                                           class="hidden"
                                           onchange="previewImage(this)">
                                    <button type="button" onclick="document.getElementById('image').click()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                        Pilih Gambar
                                    </button>
                                </div>
                                <div id="imagePreviewContainer" class="hidden mt-4">
                                    <img id="previewImg" src="" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                                    <button type="button" onclick="removeImage()" class="mt-2 text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-trash mr-1"></i>Hapus Gambar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Pengaturan</h3>
                                <p class="text-sm text-gray-600 mt-1">Status dan pengaturan lainnya</p>
                            </div>
                            
                            <div class="p-6 space-y-4">
                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-toggle-on mr-2"></i>Status
                                    </label>
                                    <select id="status" 
                                            name="status" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="draft">Draft</option>
                                        <option value="active">Active</option>
                                        <option value="upcoming">Upcoming</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>

                                <!-- Featured -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="featured" class="text-sm font-medium text-gray-700">
                                            <i class="fas fa-star mr-2"></i>Featured
                                        </label>
                                        <p class="text-xs text-gray-500">Tampilkan di halaman utama</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="featured" name="featured" value="1" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <!-- Save as Draft Info -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex">
                                        <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-800 font-medium">Tips:</p>
                                            <p class="text-xs text-blue-700 mt-1">
                                                Simpan sebagai Draft untuk preview terlebih dahulu sebelum mempublikasikan.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="space-y-3">
                                <button type="submit" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                                    <i class="fas fa-save mr-2"></i>
                                    Simpan Bootcamp
                                </button>
                                
                                <button type="button" 
                                        onclick="saveDraft()"
                                        class="w-full bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                                    <i class="fas fa-file-alt mr-2"></i>
                                    Simpan sebagai Draft
                                </button>
                                
                                <a href="admin.php?action=manage_bootcamps" 
                                   class="w-full bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                                    <i class="fas fa-times mr-2"></i>
                                    Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <script>
        // Generate slug from title
        function generateSlug() {
            const title = document.getElementById('title').value;
            const slug = title
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            document.getElementById('slug').value = slug;
        }

        // Character counter for description
        document.getElementById('description').addEventListener('input', function() {
            const count = this.value.length;
            document.getElementById('descriptionCount').textContent = count + ' karakter';
            
            if (count < 50) {
                this.classList.add('border-red-300');
                this.classList.remove('border-gray-300');
            } else {
                this.classList.remove('border-red-300');
                this.classList.add('border-gray-300');
            }
        });

        // Image preview
        function previewImage(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').classList.add('hidden');
                    document.getElementById('imagePreviewContainer').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            document.getElementById('image').value = '';
            document.getElementById('imagePreview').classList.remove('hidden');
            document.getElementById('imagePreviewContainer').classList.add('hidden');
        }

        // Drag and drop
        const imagePreview = document.getElementById('imagePreview');
        
        imagePreview.addEventListener('click', function() {
            document.getElementById('image').click();
        });

        imagePreview.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        imagePreview.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        imagePreview.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('image').files = files;
                previewImage(document.getElementById('image'));
            }
        });

        // Save as draft
        function saveDraft() {
            document.getElementById('status').value = 'draft';
            document.querySelector('form').submit();
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const categoryId = document.getElementById('category_id').value;
            const instructorName = document.getElementById('instructor_name').value.trim();
            const price = document.getElementById('price').value;
            
            let errors = [];
            
            if (title.length < 5) {
                errors.push('Judul bootcamp minimal 5 karakter');
            }
            
            if (description.length < 50) {
                errors.push('Deskripsi minimal 50 karakter');
            }
            
            if (!categoryId) {
                errors.push('Kategori harus dipilih');
            }
            
            if (instructorName.length < 2) {
                errors.push('Nama instructor minimal 2 karakter');
            }
            
            if (!price || price <= 0) {
                errors.push('Harga harus diisi dan lebih dari 0');
            }
            
            if (errors.length > 0) {
                e.preventDefault();
                alert('Mohon perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
            }
        });

        // Auto-save to localStorage (draft)
        function autoSave() {
            const formData = {
                title: document.getElementById('title').value,
                slug: document.getElementById('slug').value,
                description: document.getElementById('description').value,
                category_id: document.getElementById('category_id').value,
                instructor_name: document.getElementById('instructor_name').value,
                price: document.getElementById('price').value,
                discount_price: document.getElementById('discount_price').value,
                start_date: document.getElementById('start_date').value,
                duration: document.getElementById('duration').value,
                max_participants: document.getElementById('max_participants').value,
                status: document.getElementById('status').value,
                featured: document.getElementById('featured').checked
            };
            
            localStorage.setItem('bootcamp_draft', JSON.stringify(formData));
        }

        // Load draft from localStorage
        function loadDraft() {
            const draft = localStorage.getItem('bootcamp_draft');
            if (draft) {
                const data = JSON.parse(draft);
                Object.keys(data).forEach(key => {
                    const element = document.getElementById(key);
                    if (element) {
                        if (element.type === 'checkbox') {
                            element.checked = data[key];
                        } else {
                            element.value = data[key];
                        }
                    }
                });
            }
        }

        // Auto-save every 30 seconds
        setInterval(autoSave, 30000);

        // Load draft on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').min = today;
            
            // Load draft if exists
            if (confirm('Apakah Anda ingin memuat draft yang tersimpan?')) {
                loadDraft();
            }
        });

        // Clear draft on successful submission
        window.addEventListener('beforeunload', function() {
            // Don't clear if form has validation errors
            if (document.querySelector('.border-red-300')) {
                return;
            }
            localStorage.removeItem('bootcamp_draft');
        });
    </script>
</body>
</html>