<?php
// views/admin/manage_user.php - Manage Users Page
$pageTitle = 'Kelola Users';

// Get current page and filters
$currentPage = $page ?? 1;
$searchTerm = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: margin-left 0.3s ease;
        }

        .admin-sidebar.collapsed + .main-content {
            margin-left: 70px;
        }

        .content-wrapper {
            padding: 30px;
        }

        .page-header {
            background: white;
            padding: 20px 30px;
            margin: -30px -30px 30px;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .page-header h1 {
            margin: 0;
            color: #495057;
            font-size: 28px;
            font-weight: 600;
            flex: 1;
        }

        .breadcrumb {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }

        .filters-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filters-form {
            display: grid;
            grid-template-columns: 1fr 200px 120px 120px;
            gap: 15px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 500;
            color: #495057;
            margin-bottom: 5px;
        }

        .form-control {
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.25);
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a67d8;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .users-table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .table-header {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h3 {
            margin: 0;
            font-size: 18px;
            color: #495057;
        }

        .bulk-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .users-table th,
        .users-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .users-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }

        .users-table td {
            font-size: 14px;
            color: #495057;
        }

        .users-table tr:hover {
            background: #f8f9fa;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        .user-details h4 {
            margin: 0 0 3px;
            font-size: 14px;
            font-weight: 500;
        }

        .user-details span {
            font-size: 12px;
            color: #6c757d;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
        }

        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: 20px;
            padding: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            color: #495057;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: #e9ecef;
        }

        .pagination .current {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .no-data {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }

        .no-data i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .checkbox-cell {
            width: 40px;
        }

        .checkbox-cell input[type="checkbox"] {
            transform: scale(1.2);
            accent-color: #667eea;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .content-wrapper {
                padding: 20px;
            }
            
            .page-header {
                padding: 15px 20px;
                margin: -20px -20px 20px;
            }
            
            .filters-form {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .users-table-container {
                overflow-x: auto;
            }
            
            .action-buttons {
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
                        <i class="fas fa-home"></i> Admin / <i class="fas fa-users"></i> Kelola Users
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Stats Row -->
                <div class="stats-row">
                    <div class="stat-item">
                        <div class="stat-number"><?= number_format($totalUsers ?? 0) ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">
                            <?php
                            $activeUsers = 0;
                            if (isset($users)) {
                                foreach ($users as $user) {
                                    if ($user['status'] === 'active') $activeUsers++;
                                }
                            }
                            echo number_format($activeUsers);
                            ?>
                        </div>
                        <div class="stat-label">Users Aktif</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">
                            <?php
                            $newUsers = 0;
                            if (isset($users)) {
                                foreach ($users as $user) {
                                    if (isset($user['created_at']) && strtotime($user['created_at']) > strtotime('-30 days')) {
                                        $newUsers++;
                                    }
                                }
                            }
                            echo number_format($newUsers);
                            ?>
                        </div>
                        <div class="stat-label">Baru (30 Hari)</div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="filters-section">
                    <form method="GET" action="admin.php" class="filters-form">
                        <input type="hidden" name="action" value="manage_users">
                        
                        <div class="form-group">
                            <label for="search">Cari Users</label>
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Nama, email, atau telepon..."
                                   value="<?= htmlspecialchars($searchTerm) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Aktif</option>
                                <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Tidak Aktif</option>
                                <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Cari
                        </button>
                        
                        <a href="admin.php?action=manage_users" class="btn btn-secondary">
                            <i class="fas fa-undo"></i>
                            Reset
                        </a>
                    </form>
                </div>

                <!-- Users Table -->
                <div class="users-table-container">
                    <div class="table-header">
                        <h3>Daftar Users (<?= number_format($totalUsers ?? 0) ?>)</h3>
                        <div class="bulk-actions">
                            <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn" disabled>
                                <i class="fas fa-trash"></i>
                                Hapus Terpilih
                            </button>
                        </div>
                    </div>

                    <?php if (!empty($users)): ?>
                        <form id="bulkForm" method="POST" action="admin.php?action=delete_users_bulk">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th class="checkbox-cell">
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th>User</th>
                                        <th>Kontak</th>
                                        <th>Status</th>
                                        <th>Terdaftar</th>
                                        <th>Orders</th>
                                        <th>Total Belanja</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td class="checkbox-cell">
                                                <input type="checkbox" 
                                                       name="selected_users[]" 
                                                       value="<?= $user['id'] ?>"
                                                       class="user-checkbox">
                                            </td>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar">
                                                        <?= strtoupper(substr($user['name'], 0, 2)) ?>
                                                    </div>
                                                    <div class="user-details">
                                                        <h4><?= htmlspecialchars($user['name']) ?></h4>
                                                        <span>ID: <?= $user['id'] ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-envelope"></i>
                                                    <?= htmlspecialchars($user['alamat_email']) ?>
                                                </div>
                                                <?php if (!empty($user['no_telepon'])): ?>
                                                    <div style="margin-top: 5px;">
                                                        <i class="fas fa-phone"></i>
                                                        <?= htmlspecialchars($user['no_telepon']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge <?= htmlspecialchars($user['status']) ?>">
                                                    <?= ucfirst(htmlspecialchars($user['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= date('d M Y', strtotime($user['created_at'])) ?>
                                            </td>
                                            <td>
                                                <strong><?= number_format($user['total_orders'] ?? 0) ?></strong>
                                            </td>
                                            <td>
                                                <strong>Rp <?= number_format($user['total_spent'] ?? 0) ?></strong>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="admin.php?action=edit_user&id=<?= $user['id'] ?>" 
                                                       class="btn btn-primary btn-sm" 
                                                       title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <a href="admin.php?action=reset_user_password&id=<?= $user['id'] ?>" 
                                                       class="btn btn-warning btn-sm" 
                                                       title="Reset Password"
                                                       onclick="return confirm('Yakin ingin reset password user ini?')">
                                                        <i class="fas fa-key"></i>
                                                    </a>
                                                    
                                                    <a href="admin.php?action=delete_user&id=<?= $user['id'] ?>" 
                                                       class="btn btn-danger btn-sm" 
                                                       title="Hapus User"
                                                       onclick="return confirm('Yakin ingin menghapus user <?= htmlspecialchars($user['name']) ?>? Tindakan ini tidak dapat dibatalkan!')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </form>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="pagination">
                                <?php
                                $baseUrl = "admin.php?action=manage_users";
                                if ($searchTerm) $baseUrl .= "&search=" . urlencode($searchTerm);
                                if ($statusFilter) $baseUrl .= "&status=" . urlencode($statusFilter);

                                // Previous page
                                if ($currentPage > 1): ?>
                                    <a href="<?= $baseUrl ?>&page=<?= $currentPage - 1 ?>">‹ Sebelumnya</a>
                                <?php endif;

                                // Page numbers
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);

                                for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <?php if ($i == $currentPage): ?>
                                        <span class="current"><?= $i ?></span>
                                    <?php else: ?>
                                        <a href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
                                    <?php endif; ?>
                                <?php endfor;

                                // Next page
                                if ($currentPage < $totalPages): ?>
                                    <a href="<?= $baseUrl ?>&page=<?= $currentPage + 1 ?>">Selanjutnya ›</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-users"></i>
                            <h3>Tidak ada users ditemukan</h3>
                            <p>Belum ada data users atau sesuaikan filter pencarian Anda.</p>
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
            const userCheckboxes = document.querySelectorAll('.user-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const bulkForm = document.getElementById('bulkForm');

            // Handle select all
            selectAllCheckbox.addEventListener('change', function() {
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkDeleteButton();
            });

            // Handle individual checkboxes
            userCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                    updateBulkDeleteButton();
                });
            });

            function updateSelectAllState() {
                const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
                const totalCount = userCheckboxes.length;
                
                selectAllCheckbox.checked = checkedCount === totalCount;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
            }

            function updateBulkDeleteButton() {
                const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
                bulkDeleteBtn.disabled = checkedCount === 0;
                bulkDeleteBtn.textContent = checkedCount > 0 ? 
                    `Hapus ${checkedCount} Terpilih` : 'Hapus Terpilih';
            }

            // Handle bulk delete
            bulkDeleteBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    alert('Pilih minimal satu user untuk dihapus.');
                    return;
                }

                const userNames = Array.from(checkedBoxes).map(checkbox => {
                    const row = checkbox.closest('tr');
                    return row.querySelector('.user-details h4').textContent;
                });

                const confirmText = `Yakin ingin menghapus ${checkedBoxes.length} user berikut?\n\n${userNames.join('\n')}\n\nTindakan ini tidak dapat dibatalkan!`;
                
                if (confirm(confirmText)) {
                    bulkForm.submit();
                }
            });

            // Auto-submit search form on enter
            const searchInput = document.getElementById('search');
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    this.closest('form').submit();
                }
            });

            // Real-time search with debounce
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.length >= 3 || this.value.length === 0) {
                        this.closest('form').submit();
                    }
                }, 500);
            });

            // Confirmation for delete actions
            document.querySelectorAll('a[onclick*="confirm"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const confirmText = this.getAttribute('onclick').match(/confirm\('([^']+)'\)/)[1];
                    if (confirm(confirmText)) {
                        window.location.href = this.href;
                    }
                });
            });
        });
    </script>
</body>
</html>