<?php
$current_page = 'forum';
$page_title = 'Post Saya - Code Camp';
include_once 'views/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="text-gray-700 hover:text-blue-600">Home</a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="mx-2 text-gray-400">/</span>
                    <a href="index.php?action=forum" class="text-gray-700 hover:text-blue-600">Forum</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="mx-2 text-gray-400">/</span>
                    <span class="text-gray-500">Post Saya</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-user-edit text-blue-600"></i> Post Saya
            </h2>
            <p class="mt-2 text-gray-600">Kelola semua post yang telah Anda buat</p>
        </div>
        <div class="space-x-2">
            <a href="index.php?action=forum" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Forum
            </a>
            <a href="index.php?action=forum_create" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> Buat Post Baru
            </a>
        </div>
    </div>

    <!-- Stats Card -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-alt text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Post</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo count($posts); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-comments text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Balasan</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                <?php 
                                $total_replies = 0;
                                foreach($posts as $post) {
                                    $total_replies += $post['reply_count'];
                                }
                                echo $total_replies;
                                ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-eye text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Post Terpopuler</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                <?php 
                                $max_replies = 0;
                                foreach($posts as $post) {
                                    if($post['reply_count'] > $max_replies) {
                                        $max_replies = $post['reply_count'];
                                    }
                                }
                                echo $max_replies . ' balasan';
                                ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar text-2xl text-orange-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Post Terakhir</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                <?php 
                                if(!empty($posts)) {
                                    echo date('d M', strtotime($posts[0]['created_at']));
                                } else {
                                    echo '-';
                                }
                                ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
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
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filter and Sort Options -->
    <div class="bg-white shadow rounded-lg mb-6 p-4">
        <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-700">Urutkan:</span>
                <select class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                        onchange="sortPosts(this.value)">
                    <option value="newest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="most_replies">Paling Banyak Balasan</option>
                    <option value="least_replies">Paling Sedikit Balasan</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">
                    Menampilkan <?php echo count($posts); ?> post
                </span>
            </div>
        </div>
    </div>

    <!-- My Posts List -->
    <div class="space-y-4" id="posts-container">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="bg-white shadow rounded-lg overflow-hidden post-item" 
                     data-date="<?php echo strtotime($post['created_at']); ?>"
                     data-replies="<?php echo $post['reply_count']; ?>">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h5 class="text-xl font-medium text-gray-900 mr-3">
                                        <a href="index.php?action=forum_detail&id=<?php echo $post['id']; ?>" 
                                           class="hover:text-blue-600">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h5>
                                    <?php if ($post['updated_at'] != $post['created_at']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-edit mr-1"></i> Diedit
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="text-gray-600 mb-4">
                                    <?php echo substr(htmlspecialchars($post['content']), 0, 200); ?>
                                    <?php if (strlen($post['content']) > 200): ?>...<?php endif; ?>
                                </p>
                                
                                <div class="flex items-center text-sm text-gray-500 space-x-4">
                                    <span class="flex items-center">
                                        <i class="fas fa-clock mr-2"></i>
                                        Dibuat: <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                                    </span>
                                    <?php if ($post['updated_at'] != $post['created_at']): ?>
                                        <span class="flex items-center">
                                            <i class="fas fa-edit mr-2"></i>
                                            Diedit: <?php echo date('d M Y H:i', strtotime($post['updated_at'])); ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="flex items-center">
                                        <i class="fas fa-comments mr-2"></i>
                                        <?php echo $post['reply_count']; ?> balasan
                                    </span>
                                </div>
                            </div>
                            
                            <div class="ml-4 flex flex-col space-y-2">
                                <a href="index.php?action=forum_detail&id=<?php echo $post['id']; ?>" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-eye mr-2"></i> Lihat
                                </a>
                                <a href="index.php?action=forum_edit&id=<?php echo $post['id']; ?>" 
                                   class="inline-flex items-center px-3 py-2 border border-blue-300 shadow-sm text-sm font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50">
                                    <i class="fas fa-edit mr-2"></i> Edit
                                </a>
                                <button onclick="confirmDelete(<?php echo $post['id']; ?>)" 
                                        class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                    <i class="fas fa-trash-alt mr-2"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="bg-gray-50 px-6 py-3">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-500">
                                    ID: #<?php echo $post['id']; ?>
                                </span>
                                <?php if ($post['reply_count'] > 0): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-fire mr-1"></i> Aktif
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button onclick="copyPostLink(<?php echo $post['id']; ?>)" 
                                        class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-link"></i>
                                </button>
                                <button onclick="sharePost(<?php echo $post['id']; ?>, '<?php echo addslashes($post['title']); ?>')" 
                                        class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-file-alt text-gray-400 text-3xl"></i>
                </div>
                <h4 class="text-xl font-medium text-gray-900 mb-2">Belum ada post</h4>
                <p class="text-gray-600 mb-6">Anda belum membuat post apapun. Mulai berbagi ide dan diskusi dengan komunitas!</p>
                <a href="index.php?action=forum_create" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i> Buat Post Pertama
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($total_pages) && $total_pages > 1): ?>
        <nav class="mt-8 flex justify-center" aria-label="Pagination">
            <ul class="flex items-center space-x-2">
                <?php if ($page > 1): ?>
                    <li>
                        <a href="index.php?action=forum_my_posts&page=<?php echo ($page - 1); ?>" 
                           class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                            <i class="fas fa-chevron-left mr-2"></i> Previous
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li>
                        <a href="index.php?action=forum_my_posts&page=<?php echo $i; ?>" 
                           class="px-3 py-2 rounded-md text-sm font-medium <?php echo ($i == $page) ? 'bg-blue-600 text-white' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li>
                        <a href="index.php?action=forum_my_posts&page=<?php echo ($page + 1); ?>" 
                           class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                            Next <i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Hapus Post</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Yakin ingin menghapus post ini? Semua balasan juga akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmDeleteBtn" 
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-700">
                    Hapus
                </button>
                <button onclick="closeDeleteModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-24 hover:bg-gray-400">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let deletePostId = null;

function confirmDelete(postId) {
    deletePostId = postId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    deletePostId = null;
    document.getElementById('deleteModal').classList.add('hidden');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deletePostId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?action=forum_delete';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = deletePostId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
});

function sortPosts(sortBy) {
    const container = document.getElementById('posts-container');
    const posts = Array.from(container.getElementsByClassName('post-item'));
    
    posts.sort((a, b) => {
        switch(sortBy) {
            case 'newest':
                return parseInt(b.dataset.date) - parseInt(a.dataset.date);
            case 'oldest':
                return parseInt(a.dataset.date) - parseInt(b.dataset.date);
            case 'most_replies':
                return parseInt(b.dataset.replies) - parseInt(a.dataset.replies);
            case 'least_replies':
                return parseInt(a.dataset.replies) - parseInt(b.dataset.replies);
            default:
                return 0;
        }
    });
    
    posts.forEach(post => container.appendChild(post));
}

function copyPostLink(postId) {
    const url = window.location.origin + '/index.php?action=forum_detail&id=' + postId;
    navigator.clipboard.writeText(url).then(() => {
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
        toast.textContent = 'Link berhasil disalin!';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3000);
    });
}

function sharePost(postId, title) {
    if (navigator.share) {
        navigator.share({
            title: title,
            url: window.location.origin + '/index.php?action=forum_detail&id=' + postId
        });
    } else {
        copyPostLink(postId);
    }
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>

<?php include_once 'views/includes/footer.php'; ?>