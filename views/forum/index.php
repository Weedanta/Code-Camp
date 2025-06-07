<?php include_once 'views/includes/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900">
            <i class="fas fa-comments text-blue-600"></i> Forum Diskusi
        </h2>
        <div class="space-x-2">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?action=forum_create" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i> Buat Post Baru
                </a>
                <a href="index.php?action=forum_my_posts" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-user mr-2"></i> Post Saya
                </a>
            <?php else: ?>
                <a href="index.php?action=login" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login untuk Posting
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search Form -->
    <div class="bg-white shadow rounded-lg mb-6 p-4">
        <form method="GET" action="index.php" class="flex gap-4">
            <input type="hidden" name="action" value="forum_search">
            <div class="flex-1">
                <input type="text" name="q" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Cari topik diskusi..."
                       value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
            </div>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-search mr-2"></i> Cari
            </button>
        </form>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-green-700">
                        <?php
                        switch ($_GET['success']) {
                            case 'post_created':
                                echo 'Post berhasil dibuat!';
                                break;
                            case 'post_updated':
                                echo 'Post berhasil diperbarui!';
                                break;
                            case 'post_deleted':
                                echo 'Post berhasil dihapus!';
                                break;
                            case 'reply_added':
                                echo 'Reply berhasil ditambahkan!';
                                break;
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-red-700">
                        <?php
                        switch ($_GET['error']) {
                            case 'post_not_found':
                                echo 'Post tidak ditemukan!';
                                break;
                            case 'unauthorized':
                                echo 'Anda tidak memiliki akses untuk melakukan aksi ini!';
                                break;
                            default:
                                echo 'Terjadi kesalahan!';
                                break;
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Forum Posts -->
    <div class="space-y-4">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between">
                            <div class="flex-1">
                                <h5 class="text-xl font-medium text-gray-900 mb-2">
                                    <a href="index.php?action=forum_detail&id=<?php echo $post['id']; ?>" 
                                       class="hover:text-blue-600">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </h5>
                                <p class="text-gray-600 mb-4">
                                    <?php echo substr(htmlspecialchars($post['content']), 0, 150); ?>
                                    <?php if (strlen($post['content']) > 150): ?>...<?php endif; ?>
                                </p>
                                <div class="flex items-center text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <i class="fas fa-user mr-2"></i>
                                        <?php echo htmlspecialchars($post['user_name']); ?>
                                    </span>
                                    <span class="flex items-center ml-4">
                                        <i class="fas fa-clock mr-2"></i>
                                        <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                                    </span>
                                    <span class="flex items-center ml-4">
                                        <i class="fas fa-comments mr-2"></i>
                                        <?php echo $post['reply_count']; ?> balasan
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="index.php?action=forum_detail&id=<?php echo $post['id']; ?>" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-eye mr-2"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fas fa-comments text-gray-400 text-5xl mb-4"></i>
                <h4 class="text-xl font-medium text-gray-900 mb-2">Belum ada post forum</h4>
                <p class="text-gray-600 mb-4">Jadilah yang pertama membuat diskusi!</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="index.php?action=forum_create" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i> Buat Post Pertama
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($total_pages) && $total_pages > 1): ?>
        <nav class="mt-8 flex justify-center" aria-label="Pagination">
            <ul class="flex items-center space-x-2">
                <?php if ($page > 1): ?>
                    <li>
                        <a href="index.php?action=forum&page=<?php echo ($page - 1); ?>" 
                           class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                            <i class="fas fa-chevron-left mr-2"></i> Previous
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li>
                        <a href="index.php?action=forum&page=<?php echo $i; ?>" 
                           class="px-3 py-2 rounded-md text-sm font-medium <?php echo ($i == $page) ? 'bg-blue-600 text-white' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li>
                        <a href="index.php?action=forum&page=<?php echo ($page + 1); ?>" 
                           class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                            Next <i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include_once 'views/includes/footer.php'; ?>