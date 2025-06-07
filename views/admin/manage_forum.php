<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Forum - Admin Campus Hub</title>
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
        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }
        .status-locked {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .pin-badge {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
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
                        <h1 class="text-2xl font-bold text-gray-900">Kelola Forum</h1>
                        <p class="text-gray-600">Moderasi diskusi dan postingan forum</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="bulkModerate()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-tools mr-2"></i>
                            Bulk Moderate
                        </button>
                        <button onclick="forumSettings()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-cog mr-2"></i>
                            Forum Settings
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

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-comments text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Posts</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo number_format($totalPosts); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Active Posts</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo count(array_filter($posts, function($p) { return $p['status'] === 'active'; })); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-thumbtack text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pinned Posts</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo count(array_filter($posts, function($p) { return $p['is_pinned']; })); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <i class="fas fa-lock text-red-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Locked Posts</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo count(array_filter($posts, function($p) { return $p['is_locked']; })); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="admin.php" class="flex flex-wrap items-center gap-4">
                    <input type="hidden" name="action" value="manage_forum">
                    
                    <!-- Search -->
                    <div class="flex-1 min-w-64">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                   name="search" 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>"
                                   placeholder="Cari judul post, konten, atau user..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <!-- Status Filter -->
                    <div>
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="active" <?php echo ($status ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="pending" <?php echo ($status ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="locked" <?php echo ($status ?? '') === 'locked' ? 'selected' : ''; ?>>Locked</option>
                        </select>
                    </div>
                    
                    <!-- Category Filter -->
                    <div>
                        <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kategori</option>
                            <option value="general" <?php echo ($category ?? '') === 'general' ? 'selected' : ''; ?>>General</option>
                            <option value="help" <?php echo ($category ?? '') === 'help' ? 'selected' : ''; ?>>Help & Support</option>
                            <option value="showcase" <?php echo ($category ?? '') === 'showcase' ? 'selected' : ''; ?>>Project Showcase</option>
                            <option value="bootcamp" <?php echo ($category ?? '') === 'bootcamp' ? 'selected' : ''; ?>>Bootcamp Discussion</option>
                        </select>
                    </div>
                    
                    <!-- Search Button -->
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    
                    <!-- Reset Button -->
                    <a href="admin.php?action=manage_forum" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </a>
                </form>
            </div>

            <!-- Forum Posts Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Forum Posts</h3>
                    <span class="text-sm text-gray-600">Showing <?php echo count($posts); ?> of <?php echo $totalPosts; ?> posts</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Post</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Replies</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($posts)): ?>
                                <?php foreach ($posts as $post): ?>
                                    <tr class="table-hover">
                                        <td class="px-6 py-4">
                                            <div class="max-w-xs">
                                                <div class="flex items-center">
                                                    <h4 class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($post['title']); ?></h4>
                                                    <?php if ($post['is_pinned']): ?>
                                                        <span class="pin-badge ml-2">
                                                            <i class="fas fa-thumbtack text-xs"></i>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="text-sm text-gray-500 mt-1 line-clamp-2"><?php echo htmlspecialchars(substr($post['content'], 0, 100)) . '...'; ?></p>
                                                <div class="text-xs text-gray-400 mt-1">ID: #<?php echo $post['id']; ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-user text-gray-600 text-xs"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($post['user_name']); ?></div>
                                                    <div class="text-xs text-gray-500">User #<?php echo $post['user_id']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                <?php echo ucfirst($post['category'] ?? 'General'); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="flex items-center">
                                                <i class="fas fa-reply text-gray-400 mr-1"></i>
                                                <?php echo number_format($post['reply_count'] ?? 0); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col space-y-1">
                                                <span class="status-badge status-<?php echo $post['status']; ?>">
                                                    <i class="fas fa-circle text-xs mr-1"></i>
                                                    <?php echo ucfirst($post['status']); ?>
                                                </span>
                                                <?php if ($post['is_locked']): ?>
                                                    <span class="text-xs text-red-600">
                                                        <i class="fas fa-lock mr-1"></i>Locked
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div><?php echo date('d M Y', strtotime($post['updated_at'])); ?></div>
                                            <div class="text-xs"><?php echo date('H:i', strtotime($post['updated_at'])); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="forum.php?post=<?php echo $post['id']; ?>" 
                                                   target="_blank"
                                                   class="text-blue-600 hover:text-blue-900 transition-colors" 
                                                   title="View Post">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <button onclick="togglePin(<?php echo $post['id']; ?>, <?php echo $post['is_pinned'] ? 'false' : 'true'; ?>)" 
                                                        class="<?php echo $post['is_pinned'] ? 'text-yellow-600 hover:text-yellow-900' : 'text-gray-400 hover:text-yellow-600'; ?> transition-colors" 
                                                        title="<?php echo $post['is_pinned'] ? 'Unpin Post' : 'Pin Post'; ?>">
                                                    <i class="fas fa-thumbtack"></i>
                                                </button>
                                                
                                                <button onclick="toggleLock(<?php echo $post['id']; ?>, <?php echo $post['is_locked'] ? 'false' : 'true'; ?>)" 
                                                        class="<?php echo $post['is_locked'] ? 'text-red-600 hover:text-red-900' : 'text-gray-400 hover:text-red-600'; ?> transition-colors" 
                                                        title="<?php echo $post['is_locked'] ? 'Unlock Post' : 'Lock Post'; ?>">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                                
                                                <button onclick="deletePost(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars($post['title'], ENT_QUOTES); ?>')" 
                                                        class="text-red-600 hover:text-red-900 transition-colors" 
                                                        title="Delete Post">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <i class="fas fa-comments text-4xl mb-4"></i>
                                            <p class="text-lg font-medium">Tidak ada forum posts ditemukan</p>
                                            <p class="mt-2">Coba ubah filter pencarian Anda</p>
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
                                <a href="?action=manage_forum&page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search ?? ''); ?>&status=<?php echo urlencode($status ?? ''); ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?action=manage_forum&page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search ?? ''); ?>&status=<?php echo urlencode($status ?? ''); ?>" 
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
                                        <a href="?action=manage_forum&page=<?php echo $i; ?>&search=<?php echo urlencode($search ?? ''); ?>&status=<?php echo urlencode($status ?? ''); ?>" 
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
                <h3 class="text-lg font-medium text-gray-900 mt-4">Hapus Forum Post</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus post <span id="deletePostTitle" class="font-medium"></span>? 
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="items-center px-4 py-3 flex space-x-4">
                    <button id="cancelDelete" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400">
                        Batal
                    </button>
                    <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let postToDelete = null;

        function togglePin(postId, pin) {
            const action = pin === 'true' ? 'pin' : 'unpin';
            window.location.href = `admin.php?action=moderate_post&id=${postId}&moderate=${action}`;
        }

        function toggleLock(postId, lock) {
            const action = lock === 'true' ? 'lock' : 'unlock';
            window.location.href = `admin.php?action=moderate_post&id=${postId}&moderate=${action}`;
        }

        function deletePost(id, title) {
            postToDelete = id;
            document.getElementById('deletePostTitle').textContent = title;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function bulkModerate() {
            alert('Fitur bulk moderate akan segera tersedia');
        }

        function forumSettings() {
            alert('Fitur forum settings akan segera tersedia');
        }

        // Modal event handlers
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteModal').classList.add('hidden');
            postToDelete = null;
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (postToDelete) {
                window.location.href = `admin.php?action=delete_forum_post&id=${postToDelete}`;
            }
        });

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                postToDelete = null;
            }
        });
    </script>
</body>
</html>