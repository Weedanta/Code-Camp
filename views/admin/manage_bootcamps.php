<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Bootcamps - Admin Campus Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-gradient {
            background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
        }
        .table-hover:hover {
            background-color: #f8fafc;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-active {
            background-color: #dcfce7;
            color: #16a34a;
        }
        .status-upcoming {
            background-color: #dbeafe;
            color: #2563eb;
        }
        .status-closed {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .status-draft {
            background-color: #f3f4f6;
            color: #6b7280;
        }
        .featured-badge {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
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
                        <h1 class="text-2xl font-bold text-gray-900">Kelola Bootcamps</h1>
                        <p class="text-gray-600">Manajemen program bootcamp dan kursus</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="exportBootcamps()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Export CSV
                        </button>
                        <a href="admin.php?action=create_bootcamp" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Bootcamp
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

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-laptop-code text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Bootcamps</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo number_format($totalBootcamps); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Active</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo count(array_filter($bootcamps, function($b) { return $b['status'] === 'active'; })); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Upcoming</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo count(array_filter($bootcamps, function($b) { return $b['status'] === 'upcoming'; })); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="fas fa-star text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Featured</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo count(array_filter($bootcamps, function($b) { return $b['featured']; })); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="admin.php" class="flex flex-wrap items-center gap-4">
                    <input type="hidden" name="action" value="manage_bootcamps">
                    
                    <!-- Search -->
                    <div class="flex-1 min-w-64">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                   name="search" 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>"
                                   placeholder="Cari judul, deskripsi, atau instructor..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <!-- Category Filter -->
                    <div>
                        <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($category ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Status Filter -->
                    <div>
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="active" <?php echo ($status ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="upcoming" <?php echo ($status ?? '') === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                            <option value="closed" <?php echo ($status ?? '') === 'closed' ? 'selected' : ''; ?>>Closed</option>
                            <option value="draft" <?php echo ($status ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    
                    <!-- Search Button -->
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    
                    <!-- Reset Button -->
                    <a href="admin.php?action=manage_bootcamps" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </a>
                </form>
            </div>

            <!-- Bootcamps Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Bootcamps</h3>
                    <span class="text-sm text-gray-600">Showing <?php echo count($bootcamps); ?> of <?php echo $totalBootcamps; ?> bootcamps</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bootcamp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statistik</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($bootcamps)): ?>
                                <?php foreach ($bootcamps as $bootcamp): ?>
                                    <tr class="table-hover">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <?php if (!empty($bootcamp['image'])): ?>
                                                        <img src="assets/images/bootcamps/<?php echo htmlspecialchars($bootcamp['image']); ?>" 
                                                             alt="<?php echo htmlspecialchars($bootcamp['title']); ?>"
                                                             class="w-12 h-12 object-cover rounded-lg">
                                                    <?php else: ?>
                                                        <i class="fas fa-laptop-code text-gray-600"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                                        <?php echo htmlspecialchars($bootcamp['title']); ?>
                                                        <?php if ($bootcamp['featured']): ?>
                                                            <span class="featured-badge ml-2">
                                                                <i class="fas fa-star text-xs"></i>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">ID: #<?php echo $bootcamp['id']; ?></div>
                                                    <div class="text-xs text-gray-400"><?php echo htmlspecialchars($bootcamp['duration']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                <?php echo htmlspecialchars($bootcamp['category_name']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($bootcamp['instructor_name']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                Rp <?php echo number_format($bootcamp['price']); ?>
                                            </div>
                                            <?php if ($bootcamp['discount_price'] && $bootcamp['discount_price'] > 0): ?>
                                                <div class="text-xs text-green-600">
                                                    Diskon: Rp <?php echo number_format($bootcamp['discount_price']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge status-<?php echo $bootcamp['status']; ?>">
                                                <i class="fas fa-circle text-xs mr-1"></i>
                                                <?php echo ucfirst($bootcamp['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div><?php echo number_format($bootcamp['total_enrollments'] ?? 0); ?> enrolled</div>
                                            <div class="text-gray-500">
                                                <?php if ($bootcamp['avg_rating']): ?>
                                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                                    <?php echo number_format($bootcamp['avg_rating'], 1); ?> (<?php echo $bootcamp['review_count']; ?>)
                                                <?php else: ?>
                                                    <span class="text-gray-400">No reviews</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div><?php echo date('d M Y', strtotime($bootcamp['start_date'])); ?></div>
                                            <div class="text-xs text-gray-400">Created: <?php echo date('d M Y', strtotime($bootcamp['created_at'])); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="index.php?action=bootcamp_detail&id=<?php echo $bootcamp['id']; ?>" 
                                                   target="_blank"
                                                   class="text-green-600 hover:text-green-900 transition-colors" 
                                                   title="View Bootcamp">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="admin.php?action=edit_bootcamp&id=<?php echo $bootcamp['id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-900 transition-colors" 
                                                   title="Edit Bootcamp">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="toggleFeatured(<?php echo $bootcamp['id']; ?>, <?php echo $bootcamp['featured'] ? 'false' : 'true'; ?>)" 
                                                        class="<?php echo $bootcamp['featured'] ? 'text-yellow-600 hover:text-yellow-900' : 'text-gray-400 hover:text-yellow-600'; ?> transition-colors" 
                                                        title="<?php echo $bootcamp['featured'] ? 'Remove from Featured' : 'Make Featured'; ?>">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                                <button onclick="deleteBootcamp(<?php echo $bootcamp['id']; ?>, '<?php echo htmlspecialchars($bootcamp['title'], ENT_QUOTES); ?>')" 
                                                        class="text-red-600 hover:text-red-900 transition-colors" 
                                                        title="Delete Bootcamp">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <i class="fas fa-laptop-code text-4xl mb-4"></i>
                                            <p class="text-lg font-medium">Tidak ada bootcamps ditemukan</p>
                                            <p class="mt-2">Coba ubah filter pencarian Anda atau 
                                                <a href="admin.php?action=create_bootcamp" class="text-blue-600 hover:text-blue-800">tambah bootcamp baru</a>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="bg-white px-6 py-3 border-t border-gray-200 flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <?php if ($page > 1): ?>
                                <a href="?action=manage_bootcamps&page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search ?? ''); ?>&category=<?php echo urlencode($category ?? ''); ?>&status=<?php echo urlencode($status ?? ''); ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?action=manage_bootcamps&page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search ?? ''); ?>&category=<?php echo urlencode($category ?? ''); ?>&status=<?php echo urlencode($status ?? ''); ?>" 
                                   class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing page <span class="font-medium"><?php echo $page; ?></span> of <span class="font-medium"><?php echo $totalPages; ?></span>
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <a href="?action=manage_bootcamps&page=<?php echo $i; ?>&search=<?php echo urlencode($search ?? ''); ?>&category=<?php echo urlencode($category ?? ''); ?>&status=<?php echo urlencode($status ?? ''); ?>" 
                                           class="<?php echo $i === $page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                </nav>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Hapus Bootcamp</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus bootcamp <span id="deleteBootcampName" class="font-medium"></span>? 
                        Tindakan ini tidak dapat dibatalkan.
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
        let bootcampToDelete = null;

        function deleteBootcamp(id, title) {
            bootcampToDelete = id;
            document.getElementById('deleteBootcampName').textContent = title;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function toggleFeatured(id, featured) {
            const url = `admin.php?action=toggle_featured_bootcamp&id=${id}&featured=${featured}`;
            fetch(url, { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
        }

        function exportBootcamps() {
            window.open('admin.php?action=export_data&type=bootcamps', '_blank');
        }

        // Modal event handlers
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteModal').classList.add('hidden');
            bootcampToDelete = null;
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (bootcampToDelete) {
                window.location.href = `admin.php?action=delete_bootcamp&id=${bootcampToDelete}`;
            }
        });

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                bootcampToDelete = null;
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('deleteModal').classList.add('hidden');
                bootcampToDelete = null;
            }
        });

        // Auto-refresh stats every 30 seconds
        setInterval(function() {
            // You can implement real-time stats updates here
        }, 30000);
    </script>
</body>
</html>