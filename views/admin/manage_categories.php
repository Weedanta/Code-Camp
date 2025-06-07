<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Admin Campus Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-gradient {
            background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
        }
        .table-hover:hover {
            background-color: #f8fafc;
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
                        <h1 class="text-2xl font-bold text-gray-900">Kelola Kategori</h1>
                        <p class="text-gray-600">Manajemen kategori bootcamp dan kursus</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Kategori
                        </button>
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

            <!-- Categories Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <?php if (!empty($category['icon'])): ?>
                                        <img src="assets/images/categories/<?php echo htmlspecialchars($category['icon']); ?>" 
                                             alt="<?php echo htmlspecialchars($category['name']); ?>"
                                             class="w-8 h-8 object-contain">
                                    <?php else: ?>
                                        <i class="fas fa-tag text-blue-600"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($category['description'] ?? '', ENT_QUOTES); ?>')" 
                                            class="text-blue-600 hover:text-blue-900 transition-colors" 
                                            title="Edit Kategori">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>')" 
                                            class="text-red-600 hover:text-red-900 transition-colors" 
                                            title="Hapus Kategori">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($category['name']); ?></h3>
                            
                            <?php if (!empty($category['description'])): ?>
                                <p class="text-sm text-gray-600 mb-4"><?php echo htmlspecialchars($category['description']); ?></p>
                            <?php endif; ?>
                            
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>
                                    <?php echo $category['bootcamp_count'] ?? 0; ?> bootcamps
                                </span>
                                <span>
                                    Sort: <?php echo $category['sort_order'] ?? 0; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-tags text-4xl text-gray-400 mb-4"></i>
                        <p class="text-lg font-medium text-gray-900">Belum ada kategori</p>
                        <p class="text-gray-600 mb-4">Mulai dengan menambahkan kategori pertama</p>
                        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Kategori
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Categories Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Kategori</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bootcamps</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <tr class="table-hover">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <?php if (!empty($category['icon'])): ?>
                                                        <img src="assets/images/categories/<?php echo htmlspecialchars($category['icon']); ?>" 
                                                             alt="<?php echo htmlspecialchars($category['name']); ?>"
                                                             class="w-6 h-6 object-contain">
                                                    <?php else: ?>
                                                        <i class="fas fa-tag text-blue-600"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($category['name']); ?></div>
                                                    <div class="text-sm text-gray-500">ID: #<?php echo $category['id']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs truncate">
                                                <?php echo htmlspecialchars($category['description'] ?? '-'); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900"><?php echo $category['bootcamp_count'] ?? 0; ?></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900"><?php echo $category['sort_order'] ?? 0; ?></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('d M Y', strtotime($category['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <button onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($category['description'] ?? '', ENT_QUOTES); ?>')" 
                                                        class="text-blue-600 hover:text-blue-900 transition-colors" 
                                                        title="Edit Kategori">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="deleteCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>')" 
                                                        class="text-red-600 hover:text-red-900 transition-colors" 
                                                        title="Hapus Kategori">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <i class="fas fa-tags text-4xl mb-4"></i>
                                            <p class="text-lg font-medium">Tidak ada kategori ditemukan</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Create/Edit Category Modal -->
    <div id="categoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900 mb-4">Tambah Kategori</h3>
                <form id="categoryForm" method="POST" action="admin.php?action=create_category" enctype="multipart/form-data">
                    <input type="hidden" id="categoryId" name="id" value="">
                    <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::generateCSRFToken(); ?>">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="categoryName" class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori *</label>
                            <input type="text" 
                                   id="categoryName" 
                                   name="name" 
                                   required 
                                   maxlength="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nama kategori">
                        </div>
                        
                        <div>
                            <label for="categoryDescription" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea id="categoryDescription" 
                                      name="description" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Deskripsi kategori (opsional)"></textarea>
                        </div>
                        
                        <div>
                            <label for="categoryIcon" class="block text-sm font-medium text-gray-700 mb-2">Icon Kategori</label>
                            <input type="file" 
                                   id="categoryIcon" 
                                   name="icon" 
                                   accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF (Max: 2MB)</p>
                        </div>
                        
                        <div>
                            <label for="sortOrder" class="block text-sm font-medium text-gray-700 mb-2">Urutan Tampil</label>
                            <input type="number" 
                                   id="sortOrder" 
                                   name="sort_order" 
                                   min="0"
                                   value="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0">
                            <p class="text-xs text-gray-500 mt-1">Semakin kecil angka, semakin awal tampil</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-1"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Hapus Kategori</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus kategori <span id="deleteCategoryName" class="font-medium"></span>? 
                        Semua bootcamp dalam kategori ini akan dipindah ke kategori "Lainnya".
                    </p>
                </div>
                <div class="items-center px-4 py-3 flex space-x-4">
                    <button id="cancelDelete" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                    <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let categoryToDelete = null;

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Kategori';
            document.getElementById('categoryForm').action = 'admin.php?action=create_category';
            document.getElementById('categoryId').value = '';
            document.getElementById('categoryName').value = '';
            document.getElementById('categoryDescription').value = '';
            document.getElementById('categoryIcon').value = '';
            document.getElementById('sortOrder').value = '0';
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function editCategory(id, name, description) {
            document.getElementById('modalTitle').textContent = 'Edit Kategori';
            document.getElementById('categoryForm').action = 'admin.php?action=update_category';
            document.getElementById('categoryId').value = id;
            document.getElementById('categoryName').value = name;
            document.getElementById('categoryDescription').value = description || '';
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }

        function deleteCategory(id, name) {
            categoryToDelete = id;
            document.getElementById('deleteCategoryName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        // Delete modal handlers
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteModal').classList.add('hidden');
            categoryToDelete = null;
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (categoryToDelete) {
                window.location.href = `admin.php?action=delete_category&id=${categoryToDelete}`;
            }
        });

        // Close modals when clicking outside
        document.getElementById('categoryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                categoryToDelete = null;
            }
        });

        // Form validation
        document.getElementById('categoryForm').addEventListener('submit', function(e) {
            const name = document.getElementById('categoryName').value.trim();
            
            if (name.length < 2) {
                e.preventDefault();
                alert('Nama kategori minimal 2 karakter');
                return;
            }
        });
    </script>
</body>
</html>