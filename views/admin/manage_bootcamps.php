<?php
// views/admin/manage_bootcamps.php - Manage Bootcamps Page
$pageTitle = 'Kelola Bootcamps';

// Get current page and filters
$currentPage = $page ?? 1;
$searchTerm = $_GET['search'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
$statusFilter = $_GET['status'] ?? '';

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
        .bootcamp-image {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .bootcamp-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .bootcamp-details h4 {
            margin: 0 0 3px;
            font-size: 14px;
            font-weight: 600;
            color: #495057;
        }

        .bootcamp-details .category {
            font-size: 12px;
            color: #6c757d;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        .bootcamp-details .instructor {
            font-size: 12px;
            color: #667eea;
            margin-top: 2px;
        }

        .price-info {
            text-align: right;
        }

        .price-current {
            font-size: 14px;
            font-weight: 600;
            color: #28a745;
        }

        .price-original {
            font-size: 12px;
            color: #6c757d;
            text-decoration: line-through;
        }

        .discount-badge {
            background: #dc3545;
            color: white;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 3px;
            margin-left: 5px;
        }

        .stats-item {
            text-align: center;
        }

        .stats-value {
            font-size: 16px;
            font-weight: 600;
            color: #667eea;
        }

        .stats-label {
            font-size: 11px;
            color: #6c757d;
        }

        .enrollment-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .enrollment-count {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
        }

        .enrollment-max {
            font-size: 11px;
            color: #6c757d;
        }

        .rating-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .rating-stars {
            color: #ffc107;
            font-size: 12px;
        }

        .rating-count {
            font-size: 11px;
            color: #6c757d;
        }

        .featured-toggle {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
        }

        .featured-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 20px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #667eea;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(20px);
        }

        .quick-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .filter-chip {
            padding: 6px 12px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            font-size: 12px;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .filter-chip.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .filter-chip:hover {
            background: #e9ecef;
            text-decoration: none;
            color: #495057;
        }

        .filter-chip.active:hover {
            background: #5a67d8;
            color: white;
        }

        .bulk-actions-bar {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            justify-content: space-between;
        }

        .bulk-actions-bar.show {
            display: flex;
        }

        .selected-count {
            font-size: 14px;
            color: #495057;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .bootcamp-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .bootcamp-image {
                width: 80px;
                height: 50px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }

            .price-info,
            .stats-item,
            .enrollment-info,
            .rating-info {
                text-align: left;
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
                        <i class="fas fa-graduation-cap"></i> Kelola Bootcamps
                    </div>
                </div>
                <div class="page-actions">
                    <a href="admin.php?action=create_bootcamp" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Tambah Bootcamp
                    </a>
                    <a href="admin.php?action=manage_categories" class="btn btn-secondary">
                        <i class="fas fa-tags"></i>
                        Kelola Kategori
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

                <!-- Quick Filters -->
                <div class="quick-filters">
                    <a href="admin.php?action=manage_bootcamps" 
                       class="filter-chip <?= empty($statusFilter) && empty($categoryFilter) ? 'active' : '' ?>">
                        <i class="fas fa-list"></i> Semua
                    </a>
                    <a href="admin.php?action=manage_bootcamps&status=active" 
                       class="filter-chip <?= $statusFilter === 'active' ? 'active' : '' ?>">
                        <i class="fas fa-play"></i> Aktif
                    </a>
                    <a href="admin.php?action=manage_bootcamps&status=draft" 
                       class="filter-chip <?= $statusFilter === 'draft' ? 'active' : '' ?>">
                        <i class="fas fa-edit"></i> Draft
                    </a>
                    <a href="admin.php?action=manage_bootcamps&featured=1" 
                       class="filter-chip">
                        <i class="fas fa-star"></i> Featured
                    </a>
                </div>

                <!-- Stats Row -->
                <div class="stats-grid mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="stats-item">
                                <div class="stats-value"><?= number_format($totalBootcamps ?? 0) ?></div>
                                <div class="stats-label">Total Bootcamps</div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="stats-item">
                                <div class="stats-value">
                                    <?php
                                    $activeCount = 0;
                                    if (isset($bootcamps)) {
                                        foreach ($bootcamps as $bootcamp) {
                                            if ($bootcamp['status'] === 'active') $activeCount++;
                                        }
                                    }
                                    echo number_format($activeCount);
                                    ?>
                                </div>
                                <div class="stats-label">Bootcamps Aktif</div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="stats-item">
                                <div class="stats-value">
                                    <?php
                                    $featuredCount = 0;
                                    if (isset($bootcamps)) {
                                        foreach ($bootcamps as $bootcamp) {
                                            if ($bootcamp['featured']) $featuredCount++;
                                        }
                                    }
                                    echo number_format($featuredCount);
                                    ?>
                                </div>
                                <div class="stats-label">Featured</div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="stats-item">
                                <div class="stats-value"><?= number_format(count($categories ?? [])) ?></div>
                                <div class="stats-label">Kategori</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="admin.php" class="row g-3">
                            <input type="hidden" name="action" value="manage_bootcamps">
                            
                            <div class="col-md-4">
                                <label for="search" class="form-label">Cari Bootcamp</label>
                                <input type="text" 
                                       id="search" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Judul, deskripsi, atau instruktur..."
                                       value="<?= sanitizeOutput($searchTerm) ?>">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="category" class="form-label">Kategori</label>
                                <select id="category" name="category" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    <?php if (isset($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>" 
                                                    <?= $categoryFilter == $category['id'] ? 'selected' : '' ?>>
                                                <?= sanitizeOutput($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="draft" <?= $statusFilter === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="archived" <?= $statusFilter === 'archived' ? 'selected' : '' ?>>Arsip</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="fas fa-search"></i>
                                    Cari
                                </button>
                                <a href="admin.php?action=manage_bootcamps" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Bulk Actions Bar -->
                <div class="bulk-actions-bar" id="bulkActionsBar">
                    <div class="selected-count">
                        <span id="selectedCount">0</span> bootcamp terpilih
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-warning" id="bulkStatusBtn">
                            <i class="fas fa-edit"></i>
                            Ubah Status
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn">
                            <i class="fas fa-trash"></i>
                            Hapus Terpilih
                        </button>
                    </div>
                </div>

                <!-- Bootcamps Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-graduation-cap"></i>
                            Daftar Bootcamps (<?= number_format($totalBootcamps ?? 0) ?>)
                        </h5>
                    </div>

                    <?php if (!empty($bootcamps)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Bootcamp</th>
                                        <th width="120">Harga</th>
                                        <th width="100">Status</th>
                                        <th width="80">Featured</th>
                                        <th width="100">Peserta</th>
                                        <th width="100">Rating</th>
                                        <th width="120">Tanggal</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bootcamps as $bootcamp): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" 
                                                       name="selected_bootcamps[]" 
                                                       value="<?= $bootcamp['id'] ?>"
                                                       class="form-check-input bootcamp-checkbox">
                                            </td>
                                            <td>
                                                <div class="bootcamp-info">
                                                    <?php if (!empty($bootcamp['image'])): ?>
                                                        <img src="assets/images/bootcamps/<?= sanitizeOutput($bootcamp['image']) ?>" 
                                                             alt="<?= sanitizeOutput($bootcamp['title']) ?>" 
                                                             class="bootcamp-image">
                                                    <?php else: ?>
                                                        <div class="bootcamp-image d-flex align-items-center justify-content-center bg-light">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="bootcamp-details">
                                                        <h4><?= sanitizeOutput($bootcamp['title']) ?></h4>
                                                        <span class="category">
                                                            <i class="fas fa-tag"></i>
                                                            <?= sanitizeOutput($bootcamp['category_name'] ?? 'Uncategorized') ?>
                                                        </span>
                                                        <div class="instructor">
                                                            <i class="fas fa-user"></i>
                                                            <?= sanitizeOutput($bootcamp['instructor_name']) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="price-info">
                                                    <div class="price-current">
                                                        Rp <?= number_format($bootcamp['discount_price'] ?: $bootcamp['price']) ?>
                                                    </div>
                                                    <?php if ($bootcamp['discount_price'] && $bootcamp['discount_price'] < $bootcamp['price']): ?>
                                                        <div class="price-original">
                                                            Rp <?= number_format($bootcamp['price']) ?>
                                                        </div>
                                                        <span class="discount-badge">
                                                            <?= round((($bootcamp['price'] - $bootcamp['discount_price']) / $bootcamp['price']) * 100) ?>%
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $bootcamp['status'] === 'active' ? 'success' : ($bootcamp['status'] === 'draft' ? 'warning' : 'secondary') ?>">
                                                    <?= ucfirst(sanitizeOutput($bootcamp['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <label class="featured-toggle">
                                                    <input type="checkbox" 
                                                           <?= $bootcamp['featured'] ? 'checked' : '' ?>
                                                           data-bootcamp-id="<?= $bootcamp['id'] ?>"
                                                           class="featured-checkbox">
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <div class="enrollment-info">
                                                    <div class="enrollment-count">
                                                        <?= number_format($bootcamp['total_enrollments'] ?? 0) ?>
                                                    </div>
                                                    <?php if ($bootcamp['max_participants']): ?>
                                                        <div class="enrollment-max">
                                                            / <?= number_format($bootcamp['max_participants']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rating-info">
                                                    <?php if ($bootcamp['avg_rating']): ?>
                                                        <div class="rating-stars">
                                                            <?php
                                                            $rating = round($bootcamp['avg_rating']);
                                                            for ($i = 1; $i <= 5; $i++) {
                                                                echo $i <= $rating ? '★' : '☆';
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="rating-count">
                                                            (<?= number_format($bootcamp['review_count'] ?? 0) ?>)
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-size: 12px;">
                                                    <div><?= date('d M Y', strtotime($bootcamp['created_at'])) ?></div>
                                                    <?php if ($bootcamp['start_date']): ?>
                                                        <div class="text-muted">
                                                            Mulai: <?= date('d M Y', strtotime($bootcamp['start_date'])) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-buttons d-flex gap-1">
                                                    <a href="admin.php?action=edit_bootcamp&id=<?= $bootcamp['id'] ?>" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Edit Bootcamp">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <button type="button" 
                                                            class="btn btn-sm btn-info" 
                                                            title="Lihat Detail"
                                                            onclick="viewBootcamp(<?= $bootcamp['id'] ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <a href="admin.php?action=delete_bootcamp&id=<?= $bootcamp['id'] ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       title="Hapus Bootcamp"
                                                       onclick="return confirm('Yakin ingin menghapus bootcamp <?= sanitizeOutput($bootcamp['title']) ?>? Tindakan ini tidak dapat dibatalkan!')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="card-footer">
                                <nav class="d-flex justify-content-center">
                                    <ul class="pagination mb-0">
                                        <?php
                                        $baseUrl = "admin.php?action=manage_bootcamps";
                                        if ($searchTerm) $baseUrl .= "&search=" . urlencode($searchTerm);
                                        if ($categoryFilter) $baseUrl .= "&category=" . urlencode($categoryFilter);
                                        if ($statusFilter) $baseUrl .= "&status=" . urlencode($statusFilter);

                                        // Previous page
                                        if ($currentPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage - 1 ?>">‹</a>
                                            </li>
                                        <?php endif;

                                        // Page numbers
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($totalPages, $currentPage + 2);

                                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor;

                                        // Next page
                                        if ($currentPage < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage + 1 ?>">›</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="card-body text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h5>Tidak ada bootcamp ditemukan</h5>
                            <p class="text-muted">Belum ada bootcamp yang sesuai dengan filter yang dipilih.</p>
                            <a href="admin.php?action=create_bootcamp" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Tambah Bootcamp Pertama
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const bootcampCheckboxes = document.querySelectorAll('.bootcamp-checkbox');
            const bulkActionsBar = document.getElementById('bulkActionsBar');
            const selectedCountSpan = document.getElementById('selectedCount');

            // Handle select all
            selectAllCheckbox?.addEventListener('change', function() {
                bootcampCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });

            // Handle individual checkboxes
            bootcampCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                    updateBulkActions();
                });
            });

            function updateSelectAllState() {
                const checkedCount = document.querySelectorAll('.bootcamp-checkbox:checked').length;
                const totalCount = bootcampCheckboxes.length;
                
                selectAllCheckbox.checked = checkedCount === totalCount;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
            }

            function updateBulkActions() {
                const checkedCount = document.querySelectorAll('.bootcamp-checkbox:checked').length;
                
                if (checkedCount > 0) {
                    bulkActionsBar?.classList.add('show');
                    selectedCountSpan.textContent = checkedCount;
                } else {
                    bulkActionsBar?.classList.remove('show');
                }
            }

            // Featured toggle
            document.querySelectorAll('.featured-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const bootcampId = this.dataset.bootcampId;
                    const featured = this.checked;
                    
                    // Disable checkbox during request
                    this.disabled = true;
                    
                    // Make AJAX request
                    fetch(`admin.php?action=toggle_featured&id=${bootcampId}&featured=${featured}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            // Revert checkbox state on error
                            this.checked = !featured;
                            alert('Gagal mengubah status featured: ' + data.message);
                        }
                    })
                    .catch(error => {
                        // Revert checkbox state on error
                        this.checked = !featured;
                        alert('Terjadi kesalahan saat mengubah status featured');
                    })
                    .finally(() => {
                        this.disabled = false;
                    });
                });
            });

            // Auto-submit search form
            const searchInput = document.getElementById('search');
            let searchTimeout;
            
            searchInput?.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                if (query.length >= 3 || query.length === 0) {
                    searchTimeout = setTimeout(() => {
                        this.closest('form').submit();
                    }, 500);
                }
            });

            // Bulk actions
            document.getElementById('bulkDeleteBtn')?.addEventListener('click', function() {
                const selected = document.querySelectorAll('.bootcamp-checkbox:checked');
                if (selected.length === 0) return;
                
                const bootcampTitles = Array.from(selected).map(cb => {
                    const row = cb.closest('tr');
                    return row.querySelector('.bootcamp-details h4').textContent;
                });
                
                const confirmText = `Yakin ingin menghapus ${selected.length} bootcamp berikut?\n\n${bootcampTitles.join('\n')}\n\nTindakan ini tidak dapat dibatalkan!`;
                
                if (confirm(confirmText)) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'admin.php?action=delete_bootcamps_bulk';
                    
                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = '<?= sanitizeOutput($_SESSION['csrf_token'] ?? '') ?>';
                    form.appendChild(csrfInput);
                    
                    // Add selected IDs
                    selected.forEach(checkbox => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_bootcamps[]';
                        input.value = checkbox.value;
                        form.appendChild(input);
                    });
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // View bootcamp details function
        function viewBootcamp(id) {
            // This could open a modal or redirect to detail page
            window.open(`bootcamp.php?id=${id}`, '_blank');
        }
    </script>
</body>
</html>