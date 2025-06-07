<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bootcamp - Admin Campus Hub</title>
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
    <?php include_once 'views/admin/partials/sidebar.php'; ?>

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
                                        <span class="text-gray-700 font-medium">Edit Bootcamp</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Bootcamp</h1>
                        <p class="text-gray-600">Perbarui informasi bootcamp: <?php echo htmlspecialchars($bootcamp['title']); ?></p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="index.php?action=bootcamp_detail&id=<?php echo $bootcamp['id']; ?>" 
                           target="_blank"
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-eye mr-2"></i>
                            Preview
                        </a>
                        <a href="admin.php?action=manage_bootcamps" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </div>
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
            <form method="POST" action="admin.php?action=update_bootcamp" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="id" value="<?php echo $bootcamp['id']; ?>">
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
                                           value="<?php echo htmlspecialchars($bootcamp['title']); ?>"
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
                                           value="<?php echo htmlspecialchars($bootcamp['slug'] ?? ''); ?>"
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
                                              rows="6"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                              placeholder="Jelaskan secara detail tentang bootcamp ini, apa yang akan dipelajari, dan siapa target pesertanya..."><?php echo htmlspecialchars($bootcamp['description']); ?></textarea>
                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                        <span>Minimum 50 karakter</span>
                                        <span id="descriptionCount"><?php echo strlen($bootcamp['description']); ?> karakter</span>
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
                                                <option value="<?php echo $category['id']; ?>" 
                                                        <?php echo ($category['id'] == $bootcamp['category_id']) ? 'selected' : ''; ?>>
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
                                               value="<?php echo htmlspecialchars($bootcamp['instructor_name']); ?>"
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
                                               value="<?php echo $bootcamp['price']; ?>"
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
                                               value="<?php echo $bootcamp['discount_price']; ?>"
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
                                               value="<?php echo $bootcamp['start_date']; ?>"
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
                                               value="<?php echo htmlspecialchars($bootcamp['duration']); ?>"
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
                                           value="<?php echo $bootcamp['max_participants'] ?? ''; ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                           placeholder="25">
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ada batasan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Current Image -->
                        <?php if (!empty($bootcamp['image'])): ?>
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900">Gambar Saat Ini</h3>
                                </div>
                                <div class="p-6">
                                    <img src="assets/images/bootcamps/<?php echo htmlspecialchars($bootcamp['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($bootcamp['title']); ?>"
                                         class="w-full h-48 object-cover rounded-lg">
                                    <button type="button" onclick="removeCurrentImage()" class="mt-2 text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-trash mr-1"></i>Hapus Gambar
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Image Upload -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Update Gambar</h3>
                                <p class="text-sm text-gray-600 mt-1">Upload gambar baru untuk bootcamp</p>
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
                                        <i class="fas fa-trash mr-1"></i>Hapus Gambar Baru
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
                                        <option value="draft" <?php echo ($bootcamp['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                        <option value="active" <?php echo ($bootcamp['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="upcoming" <?php echo ($bootcamp['status'] === 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                                        <option value="closed" <?php echo ($bootcamp['status'] === 'closed') ? 'selected' : ''; ?>>Closed</option>
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
                                        <input type="checkbox" id="featured" name="featured" value="1" 
                                               <?php echo ($bootcamp['featured']) ? 'checked' : ''; ?>
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <!-- Statistics -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">Statistik Bootcamp</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total Enrollments:</span>
                                            <span class="font-medium"><?php echo $bootcamp['total_enrollments'] ?? 0; ?></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Avg Rating:</span>
                                            <span class="font-medium">
                                                <?php if ($bootcamp['avg_rating']): ?>
                                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                                    <?php echo number_format($bootcamp['avg_rating'], 1); ?> (<?php echo $bootcamp['review_count']; ?>)
                                                <?php else: ?>
                                                    <span class="text-gray-400">No reviews</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Created:</span>
                                            <span class="font-medium"><?php echo date('d M Y', strtotime($bootcamp['created_at'])); ?></span>
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
                                    Update Bootcamp
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
            
            // Only update if slug field is empty or matches the previous title
            const currentSlug = document.getElementById('slug').value;
            if (!currentSlug || currentSlug === '') {
                document.getElementById('slug').value = slug;
            }
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

        function removeCurrentImage() {
            if (confirm('Apakah Anda yakin ingin menghapus gambar saat ini? Pastikan untuk mengupload gambar baru.')) {
                // Add hidden input to mark current image for deletion
                const form = document.querySelector('form');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_current_image';
                input.value = '1';
                form.appendChild(input);
                
                // Hide current image display
                const currentImageDiv = input.closest('.bg-white');
                if (currentImageDiv) {
                    currentImageDiv.style.display = 'none';
                }
            }
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

        // Unsaved changes warning
        let hasChanges = false;
        document.querySelectorAll('input, textarea, select').forEach(element => {
            element.addEventListener('change', function() {
                hasChanges = true;
            });
        });

        window.addEventListener('beforeunload', function(e) {
            if (hasChanges) {
                e.returnValue = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
            }
        });

        // Reset changes flag on form submit
        document.querySelector('form').addEventListener('submit', function() {
            hasChanges = false;
        });
    </script>
</body>
</html>