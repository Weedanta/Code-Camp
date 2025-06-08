<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Code Camp Admin</title>
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
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="ml-12 lg:ml-0">
                        <h1 class="text-2xl font-bold text-gray-800">Kelola Kategori</h1>
                        <p class="text-gray-600 mt-1">Manajemen kategori bootcamp Code Camp</p>
                    </div>
                    <div class="flex items-center space-x-4 mt-4 lg:mt-0">
                        <button 
                            onclick="openCreateModal()" 
                            class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Kategori
                        </button>
                        <span class="bg-primary text-white px-3 py-1 rounded-full text-sm">
                            Total: <?= count($categories) ?>
                        </span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-4 lg:p-6 space-y-6">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success']) ?></span>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error']) ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Categories Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (empty($categories)): ?>
                        <div class="col-span-full">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 mb-4">Belum ada kategori</p>
                                <button 
                                    onclick="openCreateModal()" 
                                    class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition-colors duration-200"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Buat Kategori Pertama
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </h3>
                                        <p class="text-gray-600 text-sm mb-3">
                                            <?= htmlspecialchars($category['description'] ?? 'Tidak ada deskripsi') ?>
                                        </p>
                                        
                                        <!-- Stats -->
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                                <?= number_format($category['bootcamp_count'] ?? 0) ?> bootcamp
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                Urutan: <?= $category['sort_order'] ?? 0 ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Category Slug -->
                                <div class="mb-4">
                                    <div class="bg-gray-50 rounded-lg px-3 py-2">
                                        <p class="text-xs text-gray-500 mb-1">Slug URL:</p>
                                        <p class="text-sm font-mono text-gray-700">/category/<?= htmlspecialchars($category['slug'] ?? '') ?></p>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                    <div class="flex space-x-2">
                                        <button 
                                            onclick="openEditModal(<?= htmlspecialchars(json_encode($category)) ?>)" 
                                            class="text-primary hover:text-secondary"
                                            title="Edit kategori"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        
                                        <?php if (($category['bootcamp_count'] ?? 0) == 0): ?>
                                            <a 
                                                href="admin.php?action=delete_category&id=<?= $category['id'] ?>" 
                                                class="text-red-600 hover:text-red-700"
                                                onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                                                title="Hapus kategori"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-400 cursor-not-allowed" title="Tidak dapat dihapus karena masih memiliki bootcamp">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="text-xs text-gray-500">
                                        <?= date('d M Y', strtotime($category['created_at'] ?? 'now')) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Create Category Modal -->
    <div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Tambah Kategori Baru</h3>
                    <button onclick="closeCreateModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <form method="POST" action="admin.php?action=create_category" class="p-6 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <!-- Name -->
                <div>
                    <label for="create_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="create_name" 
                        name="name" 
                        required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                        placeholder="Contoh: Web Development"
                    >
                </div>

                <!-- Description -->
                <div>
                    <label for="create_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea 
                        id="create_description" 
                        name="description" 
                        rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                        placeholder="Deskripsi singkat tentang kategori ini..."
                    ></textarea>
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="create_sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                        Urutan Tampil
                    </label>
                    <input 
                        type="number" 
                        id="create_sort_order" 
                        name="sort_order" 
                        min="0"
                        value="0"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                        placeholder="0"
                    >
                    <p class="text-xs text-gray-500 mt-1">Urutan tampil kategori (semakin kecil semakin atas)</p>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-4">
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-secondary transition-colors duration-200"
                    >
                        Buat Kategori
                    </button>
                    <button 
                        type="button" 
                        onclick="closeCreateModal()" 
                        class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200"
                    >
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Edit Kategori</h3>
                    <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <form method="POST" action="admin.php?action=update_category" class="p-6 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" id="edit_id" name="id" value="">
                
                <!-- Name -->
                <div>
                    <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="edit_name" 
                        name="name" 
                        required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                        placeholder="Contoh: Web Development"
                    >
                </div>

                <!-- Description -->
                <div>
                    <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea 
                        id="edit_description" 
                        name="description" 
                        rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                        placeholder="Deskripsi singkat tentang kategori ini..."
                    ></textarea>
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="edit_sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                        Urutan Tampil
                    </label>
                    <input 
                        type="number" 
                        id="edit_sort_order" 
                        name="sort_order" 
                        min="0"
                        value="0"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-200"
                        placeholder="0"
                    >
                    <p class="text-xs text-gray-500 mt-1">Urutan tampil kategori (semakin kecil semakin atas)</p>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-4">
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-secondary transition-colors duration-200"
                    >
                        Simpan Perubahan
                    </button>
                    <button 
                        type="button" 
                        onclick="closeEditModal()" 
                        class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200"
                    >
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            // Reset form
            document.getElementById('create_name').value = '';
            document.getElementById('create_description').value = '';
            document.getElementById('create_sort_order').value = '0';
            document.getElementById('create_name').focus();
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function openEditModal(category) {
            document.getElementById('editModal').classList.remove('hidden');
            
            // Populate form
            document.getElementById('edit_id').value = category.id;
            document.getElementById('edit_name').value = category.name;
            document.getElementById('edit_description').value = category.description || '';
            document.getElementById('edit_sort_order').value = category.sort_order || 0;
            
            document.getElementById('edit_name').focus();
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.getElementById('createModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreateModal();
            }
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Close modals on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreateModal();
                closeEditModal();
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