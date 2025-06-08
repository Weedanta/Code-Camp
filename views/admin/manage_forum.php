<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Forum - Code Camp Admin</title>
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
                        <h1 class="text-2xl font-bold text-gray-800">Kelola Forum</h1>
                        <p class="text-gray-600 mt-1">Moderasi diskusi dan post forum</p>
                    </div>
                    <div class="flex items-center space-x-4 mt-4 lg:mt-0">
                        <span class="bg-primary text-white px-3 py-1 rounded-full text-sm">
                            Total: <?= number_format($totalPosts) ?> posts
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

                <!-- Filters and Search -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <form method="GET" action="admin.php" class="flex flex-col lg:flex-row gap-4">
                        <input type="hidden" name="action" value="manage_forum">
                        
                        <!-- Search -->
                        <div class="flex-1">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Cari judul post, konten, atau nama user..." 
                                value="<?= htmlspecialchars($search ?? '') ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                            >
                        </div>

                        <!-- Status Filter -->
                        <div class="w-full lg:w-48">
                            <select 
                                name="status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                            >
                                <option value="">Semua Status</option>
                                <option value="published" <?= ($status ?? '') == 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="hidden" <?= ($status ?? '') == 'hidden' ? 'selected' : '' ?>>Hidden</option>
                                <option value="locked" <?= ($status ?? '') == 'locked' ? 'selected' : '' ?>>Locked</option>
                            </select>
                        </div>

                        <!-- Search Button -->
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition-colors duration-200"
                        >
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                    </form>
                </div>

                <!-- Forum Posts -->
                <div class="space-y-4">
                    <?php if (empty($posts)): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                            <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500">Tidak ada post forum ditemukan</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                                <div class="flex flex-col lg:flex-row gap-6">
                                    <!-- Post Content -->
                                    <div class="flex-1">
                                        <!-- Header -->
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center space-x-3">
                                                <!-- Pinned Badge -->
                                                <?php if ($post['is_pinned'] ?? false): ?>
                                                    <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">
                                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 6.707 6.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Pinned
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Locked Badge -->
                                                <?php if ($post['is_locked'] ?? false): ?>
                                                    <div class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Locked
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Status Badge -->
                                            <?php 
                                            $statusClasses = [
                                                'published' => 'bg-green-100 text-green-800',
                                                'hidden' => 'bg-gray-100 text-gray-800',
                                                'locked' => 'bg-red-100 text-red-800'
                                            ];
                                            $statusClass = $statusClasses[$post['status'] ?? 'published'] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
                                                <?= ucfirst($post['status'] ?? 'published') ?>
                                            </span>
                                        </div>

                                        <!-- Title -->
                                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                            <?= htmlspecialchars($post['title'] ?? '') ?>
                                        </h3>

                                        <!-- Content Preview -->
                                        <div class="mb-4">
                                            <p class="text-gray-700 leading-relaxed">
                                                <?= nl2br(htmlspecialchars(substr($post['content'] ?? '', 0, 200))) ?>
                                                <?php if (strlen($post['content'] ?? '') > 200): ?>
                                                    <span class="text-gray-500">...</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>

                                        <!-- Meta Info -->
                                        <div class="flex items-center space-x-6 text-sm text-gray-500">
                                            <!-- Author -->
                                            <div class="flex items-center space-x-2">
                                                <div class="h-6 w-6 rounded-full bg-primary flex items-center justify-center">
                                                    <span class="text-xs font-medium text-white">
                                                        <?= strtoupper(substr($post['user_name'] ?? 'U', 0, 1)) ?>
                                                    </span>
                                                </div>
                                                <span><?= htmlspecialchars($post['user_name'] ?? 'Unknown User') ?></span>
                                            </div>
                                            
                                            <!-- Replies Count -->
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                <span><?= number_format($post['reply_count'] ?? 0) ?> balasan</span>
                                            </div>
                                            
                                            <!-- Date -->
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span><?= date('d M Y, H:i', strtotime($post['created_at'] ?? 'now')) ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex lg:flex-col gap-2 lg:w-40">
                                        <!-- Pin/Unpin -->
                                        <a 
                                            href="admin.php?action=moderate_post&id=<?= $post['id'] ?>&moderate=<?= ($post['is_pinned'] ?? false) ? 'unpin' : 'pin' ?>" 
                                            class="flex-1 lg:flex-none <?= ($post['is_pinned'] ?? false) ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-blue-100 text-blue-800 hover:bg-blue-200' ?> px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-center"
                                            title="<?= ($post['is_pinned'] ?? false) ? 'Unpin post' : 'Pin post' ?>"
                                        >
                                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 6.707 6.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <?= ($post['is_pinned'] ?? false) ? 'Unpin' : 'Pin' ?>
                                        </a>
                                        
                                        <!-- Lock/Unlock -->
                                        <a 
                                            href="admin.php?action=moderate_post&id=<?= $post['id'] ?>&moderate=<?= ($post['is_locked'] ?? false) ? 'unlock' : 'lock' ?>" 
                                            class="flex-1 lg:flex-none <?= ($post['is_locked'] ?? false) ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-orange-100 text-orange-800 hover:bg-orange-200' ?> px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-center"
                                            title="<?= ($post['is_locked'] ?? false) ? 'Unlock post' : 'Lock post' ?>"
                                        >
                                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <?php if ($post['is_locked'] ?? false): ?>
                                                    <path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z"></path>
                                                <?php else: ?>
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                                <?php endif; ?>
                                            </svg>
                                            <?= ($post['is_locked'] ?? false) ? 'Unlock' : 'Lock' ?>
                                        </a>
                                        
                                        <!-- View/Edit -->
                                        <a 
                                            href="forum/detail.php?id=<?= $post['id'] ?>" 
                                            target="_blank"
                                            class="flex-1 lg:flex-none bg-purple-100 text-purple-800 px-3 py-2 rounded-lg text-sm font-medium hover:bg-purple-200 transition-colors duration-200 text-center"
                                            title="Lihat post"
                                        >
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Lihat
                                        </a>
                                        
                                        <!-- Delete -->
                                        <a 
                                            href="admin.php?action=delete_forum_post&id=<?= $post['id'] ?>" 
                                            class="flex-1 lg:flex-none bg-red-100 text-red-800 px-3 py-2 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors duration-200 text-center"
                                            onclick="return confirm('Hapus post ini beserta semua balasannya?')"
                                            title="Hapus post"
                                        >
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-4">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="text-sm text-gray-700">
                                Menampilkan <?= (($page - 1) * 20) + 1 ?> sampai <?= min($page * 20, $totalPosts) ?> dari <?= $totalPosts ?> posts
                            </div>
                            
                            <div class="flex items-center space-x-1">
                                <!-- Previous Page -->
                                <?php if ($page > 1): ?>
                                    <a 
                                        href="admin.php?action=manage_forum&page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $status ? '&status=' . urlencode($status) : '' ?>" 
                                        class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                    >
                                        Sebelumnya
                                    </a>
                                <?php endif; ?>

                                <!-- Page Numbers -->
                                <?php
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                ?>
                                
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <a 
                                        href="admin.php?action=manage_forum&page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $status ? '&status=' . urlencode($status) : '' ?>" 
                                        class="px-3 py-2 text-sm border rounded-md <?= $i == $page ? 'bg-primary text-white border-primary' : 'bg-white border-gray-300 hover:bg-gray-50' ?>"
                                    >
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <!-- Next Page -->
                                <?php if ($page < $totalPages): ?>
                                    <a 
                                        href="admin.php?action=manage_forum&page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $status ? '&status=' . urlencode($status) : '' ?>" 
                                        class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                    >
                                        Selanjutnya
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
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