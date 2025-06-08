<?php
// views/admin/manage_categories.php - Manage Categories Page
$pageTitle = 'Kelola Kategori';

// Security function
function sanitizeOutput($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .categories-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 25px;
            align-items: start;
        }

        .category-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            position: relative;
        }

        .category-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.1);
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .category-info h4 {
            margin: 0 0 5px;
            color: #495057;
            font-size: 18px;
            font-weight: 600;
        }

        .category-slug {
            font-size: 12px;
            color: #667eea;
            font-family: monospace;
            background: rgba(102, 126, 234, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        .category-description {
            color: #6c757d;
            font-size: 14px;
            margin: 10px 0;
            line-height: 1.5;
        }

        .category-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f1f3f4;
        }

        .category-stats {
            display: flex;
            gap: 20px;
            font-size: 12px;
            color: #6c757d;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stat-value {
            font-weight: 600;
            color: #495057;
        }

        .category-actions {
            display: flex;
            gap: 8px;
        }

        .drag-handle {
            position: absolute;
            left: -10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: grab;
            color: #6c757d;
            padding: 10px 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .category-item:hover .drag-handle {
            opacity: 1;
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        .sortable-ghost {
            opacity: 0.5;
        }

        .sortable-chosen {
            transform: scale(1.02);
            box-shadow: 0 8px 15px rgba(102, 126, 234, 0.2);
        }

        .create-category-form {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: sticky;
            top: 100px;
        }

        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            margin: -25px -25px 25px;
            border-radius: 12px 12px 0 0;
        }

        .form-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .edit-mode .form-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .slug-display {
            font-size: 12px;
            color: #667eea;
            margin-top: 5px;
            font-family: monospace;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .category-stats-overview {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 20px;
        }

        .stat-card {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .bulk-actions {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            justify-content: space-between;
        }

        .bulk-actions.show {
            display: flex;
        }

        .sort-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #856404;
        }

        @media (max-width: 768px) {
            .categories-grid {
                grid-template-columns: 1fr;
            }

            .create-category-form {
                position: static;
                order: -1;
            }

            .category-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1><?= $pageTitle ?></h1>
                    <div class="breadcrumb">
                        <i class="fas fa-home"></i> Admin / 
                        <a href="admin.php?action=manage_bootcamps" style="color: #667eea; text-decoration: none;">
                            <i class="fas fa-graduation-cap"></i> Kelola Bootcamps
                        </a> / 
                        <i class="fas fa-tags"></i> Kategori
                    </div>
                </div>
                <div class="page-actions">
                    <a href="admin.php?action=manage_bootcamps" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Bootcamps
                    </a>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= sanitizeOutput($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= sanitizeOutput($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Category Stats Overview -->
                <div class="category-stats-overview">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number"><?= number_format(count($categories ?? [])) ?></div>
                            <div class="stat-label">Total Kategori</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">
                                <?php
                                $activeCount = 0;
                                if (isset($categories)) {
                                    foreach ($categories as $category) {
                                        if (($category['status'] ?? 'active') === 'active') $activeCount++;
                                    }
                                }
                                echo number_format($activeCount);
                                ?>
                            </div>
                            <div class="stat-label">Kategori Aktif</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">
                                <?php
                                $totalBootcamps = 0;
                                if (isset($categories)) {
                                    foreach ($categories as $category) {
                                        $totalBootcamps += $category['bootcamp_count'] ?? 0;
                                    }
                                }
                                echo number_format($totalBootcamps);
                                ?>
                            </div>
                            <div class="stat-label">Total Bootcamps</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">
                                <?php
                                $maxBootcamps = 0;
                                if (isset($categories)) {
                                    foreach ($categories as $category) {
                                        $maxBootcamps = max($maxBootcamps, $category['bootcamp_count'] ?? 0);
                                    }
                                }
                                echo number_format($maxBootcamps);
                                ?>
                            </div>
                            <div class="stat-label">Terpopuler</div>
                        </div>
                    </div>
                </div>

                <!-- Sort Info -->
                <div class="sort-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Info:</strong> Drag & drop kategori untuk mengubah urutan tampilan. Perubahan akan tersimpan otomatis.
                </div>

                <!-- Categories Grid -->
                <div class="categories-grid">
                    <!-- Categories List -->
                    <div class="categories-list">
                        <?php if (!empty($categories)): ?>
                            <div id="sortableCategories">
                                <?php foreach ($categories as $category): ?>
                                    <div class="category-item" data-category-id="<?= $category['id'] ?>">
                                        <div class="drag-handle">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>
                                        
                                        <div class="category-header">
                                            <div class="category-info">
                                                <h4><?= sanitizeOutput($category['name']) ?></h4>
                                                <span class="category-slug">/category/<?= sanitizeOutput($category['slug']) ?></span>
                                            </div>
                                            <div class="category-actions">
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary edit-category-btn" 
                                                        data-category='<?= json_encode($category) ?>'
                                                        title="Edit Kategori">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="admin.php?action=delete_category&id=<?= $category['id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   title="Hapus Kategori"
                                                   onclick="return confirm('Yakin ingin menghapus kategori <?= sanitizeOutput($category['name']) ?>? Bootcamps dalam kategori ini akan menjadi uncategorized.')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <?php if (!empty($category['description'])): ?>
                                            <div class="category-description">
                                                <?= sanitizeOutput($category['description']) ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="category-meta">
                                            <div class="category-stats">
                                                <div class="stat-item">
                                                    <i class="fas fa-graduation-cap"></i>
                                                    <span class="stat-value"><?= number_format($category['bootcamp_count'] ?? 0) ?></span>
                                                    <span>Bootcamps</span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="fas fa-sort-numeric-up"></i>
                                                    <span class="stat-value"><?= $category['sort_order'] ?? 0 ?></span>
                                                    <span>Urutan</span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="fas fa-calendar"></i>
                                                    <span><?= date('d M Y', strtotime($category['created_at'] ?? '')) ?></span>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="badge badge-<?= ($category['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                                    <?= ucfirst($category['status'] ?? 'active') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-tags"></i>
                                <h3>Belum ada kategori</h3>
                                <p>Buat kategori pertama untuk mengorganisir bootcamps Anda.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Create/Edit Category Form -->
                    <div class="create-category-form" id="categoryForm">
                        <div class="form-header">
                            <h3 id="formTitle">
                                <i class="fas fa-plus"></i>
                                Tambah Kategori Baru
                            </h3>
                        </div>

                        <form method="POST" action="admin.php?action=create_category" id="categoryFormElement">
                            <input type="hidden" name="csrf_token" value="<?= sanitizeOutput($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="hidden" name="id" id="categoryId">
                            
                            <div class="form-group">
                                <label for="categoryName">
                                    <i class="fas fa-tag"></i>
                                    Nama Kategori <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" 
                                       id="categoryName" 
                                       name="name" 
                                       class="form-control" 
                                       placeholder="Contoh: Web Development"
                                       required
                                       maxlength="100">
                                <div class="slug-display" id="slugDisplay"></div>
                            </div>

                            <div class="form-group">
                                <label for="categoryDescription">
                                    <i class="fas fa-align-left"></i>
                                    Deskripsi
                                </label>
                                <textarea id="categoryDescription" 
                                          name="description" 
                                          class="form-control" 
                                          rows="4"
                                          placeholder="Deskripsi singkat tentang kategori ini..."
                                          maxlength="500"></textarea>
                                <div style="font-size: 12px; color: #6c757d; text-align: right; margin-top: 5px;">
                                    <span id="descCounter">0</span>/500
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="sortOrder">
                                    <i class="fas fa-sort-numeric-up"></i>
                                    Urutan Tampilan
                                </label>
                                <input type="number" 
                                       id="sortOrder" 
                                       name="sort_order" 
                                       class="form-control" 
                                       placeholder="0"
                                       min="0"
                                       max="999"
                                       value="<?= count($categories ?? []) ?>">
                                <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">
                                    Semakin kecil angka, semakin atas posisinya
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i>
                                    <span id="submitText">Simpan Kategori</span>
                                </button>
                                <button type="button" class="btn btn-secondary" id="cancelBtn" style="display: none;">
                                    <i class="fas fa-times"></i>
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Include SortableJS for drag & drop -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryForm = document.getElementById('categoryFormElement');
            const formTitle = document.getElementById('formTitle');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const cancelBtn = document.getElementById('cancelBtn');
            const categoryId = document.getElementById('categoryId');
            const categoryName = document.getElementById('categoryName');
            const categoryDescription = document.getElementById('categoryDescription');
            const sortOrder = document.getElementById('sortOrder');
            const slugDisplay = document.getElementById('slugDisplay');
            const descCounter = document.getElementById('descCounter');

            let isEditMode = false;

            // Slug generation
            categoryName.addEventListener('input', function() {
                const slug = generateSlug(this.value);
                slugDisplay.textContent = slug ? `URL: /category/${slug}` : '';
            });

            function generateSlug(text) {
                return text
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
            }

            // Character counter for description
            categoryDescription.addEventListener('input', function() {
                descCounter.textContent = this.value.length;
            });

            // Edit category
            document.querySelectorAll('.edit-category-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const categoryData = JSON.parse(this.dataset.category);
                    editCategory(categoryData);
                });
            });

            function editCategory(category) {
                isEditMode = true;
                
                // Update form
                categoryForm.action = 'admin.php?action=update_category';
                categoryId.value = category.id;
                categoryName.value = category.name;
                categoryDescription.value = category.description || '';
                sortOrder.value = category.sort_order || 0;
                
                // Update UI
                formTitle.innerHTML = '<i class="fas fa-edit"></i> Edit Kategori';
                submitText.textContent = 'Update Kategori';
                cancelBtn.style.display = 'inline-flex';
                document.getElementById('categoryForm').classList.add('edit-mode');
                
                // Update slug display and counter
                const slug = generateSlug(category.name);
                slugDisplay.textContent = slug ? `URL: /category/${slug}` : '';
                descCounter.textContent = (category.description || '').length;
                
                // Scroll to form
                document.getElementById('categoryForm').scrollIntoView({ behavior: 'smooth' });
            }

            // Cancel edit
            cancelBtn.addEventListener('click', function() {
                resetForm();
            });

            function resetForm() {
                isEditMode = false;
                
                // Reset form
                categoryForm.action = 'admin.php?action=create_category';
                categoryForm.reset();
                categoryId.value = '';
                
                // Reset UI
                formTitle.innerHTML = '<i class="fas fa-plus"></i> Tambah Kategori Baru';
                submitText.textContent = 'Simpan Kategori';
                cancelBtn.style.display = 'none';
                document.getElementById('categoryForm').classList.remove('edit-mode');
                
                // Reset displays
                slugDisplay.textContent = '';
                descCounter.textContent = '0';
                sortOrder.value = <?= count($categories ?? []) ?>;
            }

            // Form validation
            categoryForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!categoryName.value.trim()) {
                    alert('Nama kategori harus diisi!');
                    categoryName.focus();
                    return;
                }
                
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                
                // Submit form
                this.submit();
            });

            // Sortable categories
            const sortableList = document.getElementById('sortableCategories');
            if (sortableList) {
                new Sortable(sortableList, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function(evt) {
                        // Get new order
                        const categoryIds = Array.from(sortableList.children).map(item => {
                            return item.dataset.categoryId;
                        });
                        
                        // Update sort order
                        updateSortOrder(categoryIds);
                    }
                });
            }

            function updateSortOrder(categoryIds) {
                fetch('admin.php?action=update_category_order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        category_ids: categoryIds,
                        csrf_token: '<?= sanitizeOutput($_SESSION['csrf_token'] ?? '') ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update sort order values in the UI
                        categoryIds.forEach((id, index) => {
                            const categoryItem = document.querySelector(`[data-category-id="${id}"]`);
                            const sortOrderSpan = categoryItem.querySelector('.stat-value');
                            if (sortOrderSpan) {
                                sortOrderSpan.textContent = index + 1;
                            }
                        });
                        
                        console.log('Sort order updated successfully');
                    } else {
                        alert('Gagal mengupdate urutan kategori: ' + data.message);
                        // Reload page to reset order
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error updating sort order:', error);
                    alert('Terjadi kesalahan saat mengupdate urutan kategori');
                    location.reload();
                });
            }

            // Auto-save draft
            let autoSaveTimer;
            function scheduleAutoSave() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    if (!isEditMode && (categoryName.value.trim() || categoryDescription.value.trim())) {
                        const draft = {
                            name: categoryName.value,
                            description: categoryDescription.value,
                            sort_order: sortOrder.value
                        };
                        localStorage.setItem('category_draft', JSON.stringify(draft));
                    }
                }, 2000);
            }

            // Load draft
            const savedDraft = localStorage.getItem('category_draft');
            if (savedDraft && !isEditMode) {
                try {
                    const draft = JSON.parse(savedDraft);
                    if (confirm('Ada draft kategori yang belum tersimpan. Muat draft tersebut?')) {
                        categoryName.value = draft.name || '';
                        categoryDescription.value = draft.description || '';
                        sortOrder.value = draft.sort_order || 0;
                        
                        // Update displays
                        const slug = generateSlug(draft.name || '');
                        slugDisplay.textContent = slug ? `URL: /category/${slug}` : '';
                        descCounter.textContent = (draft.description || '').length;
                    }
                    localStorage.removeItem('category_draft');
                } catch (e) {
                    console.error('Failed to load draft:', e);
                }
            }

            // Auto-save on input
            [categoryName, categoryDescription, sortOrder].forEach(input => {
                input.addEventListener('input', scheduleAutoSave);
            });

            // Clear draft on form submit
            categoryForm.addEventListener('submit', function() {
                localStorage.removeItem('category_draft');
            });
        });
    </script>
</body>
</html>