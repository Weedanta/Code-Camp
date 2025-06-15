<?php 
$current_page = 'forum';
$page_title = 'Detail Forum - Code Camp';
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
                    <span class="text-gray-500">Detail Post</span>
                </div>
            </li>
        </ol>
    </nav>

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
                            case 'reply_added':
                                echo 'Balasan berhasil ditambahkan!';
                                break;
                            case 'post_created':
                                echo 'Post berhasil dibuat!';
                                break;
                            case 'post_updated':
                                echo 'Post berhasil diperbarui!';
                                break;
                            default:
                                echo 'Operasi berhasil!';
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
                            case 'empty_content':
                                echo 'Konten balasan tidak boleh kosong!';
                                break;
                            case 'reply_failed':
                                echo 'Gagal menambahkan balasan. Silakan coba lagi!';
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

    <!-- Main Post -->
    <div class="bg-white shadow-lg rounded-lg mb-6 overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </h1>
                    <div class="flex items-center text-sm text-gray-500 space-x-4">
                        <span class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <?php echo htmlspecialchars($post['user_name']); ?>
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-clock mr-2 text-gray-400"></i>
                            <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                        </span>
                        <?php if ($post['updated_at'] != $post['created_at']): ?>
                            <span class="flex items-center text-orange-600">
                                <i class="fas fa-edit mr-2"></i>
                                Diedit: <?php echo date('d M Y H:i', strtotime($post['updated_at'])); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                    <div class="flex space-x-2 ml-4">
                        <a href="index.php?action=forum_edit&id=<?php echo $post['id']; ?>" 
                           class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50 transition-colors">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a>
                        <button onclick="confirmDeletePost(<?php echo $post['id']; ?>)" 
                                class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 transition-colors">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="prose max-w-none text-gray-700 leading-relaxed">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>

            <!-- Post Actions -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-comments mr-2"></i>
                            <?php echo count($replies); ?> balasan
                        </span>
                        <button onclick="copyPostLink()" class="flex items-center hover:text-blue-600 transition-colors">
                            <i class="fas fa-link mr-2"></i>
                            Salin Link
                        </button>
                        <button onclick="sharePost()" class="flex items-center hover:text-blue-600 transition-colors">
                            <i class="fas fa-share-alt mr-2"></i>
                            Bagikan
                        </button>
                    </div>
                    <div class="text-sm text-gray-500">
                        ID: #<?php echo $post['id']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Replies Section -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-comments text-blue-600 mr-2"></i> 
                Balasan (<?php echo count($replies); ?>)
            </h3>
        </div>
        
        <!-- Reply Form -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <h4 class="text-md font-medium text-gray-900 mb-4">Tulis Balasan</h4>
                <form method="POST" action="index.php?action=forum_reply">
    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <div class="mb-4">
                        <textarea name="content" id="reply-content" rows="4"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none"
                                  placeholder="Bagikan pemikiran Anda tentang topik ini..."
                                  required></textarea>
                        <p class="mt-1 text-sm text-gray-500">Gunakan bahasa yang sopan dan konstruktif</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-user mr-1"></i> Posting sebagai: <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong>
                        </div>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Anda harus login untuk membalas post ini</p>
                    <a href="index.php?action=login" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Reply List -->
        <div class="divide-y divide-gray-200">
            <?php if (!empty($replies)): ?>
                <?php foreach ($replies as $index => $reply): ?>
                    <div class="p-6" id="reply-<?php echo $reply['id']; ?>">
                        <div class="flex space-x-4">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">
                                        <?php echo strtoupper(substr($reply['user_name'], 0, 1)); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Reply Content -->
                            <div class="flex-1 min-w-0">
                                <!-- Reply Header -->
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <h5 class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($reply['user_name']); ?>
                                        </h5>
                                        <span class="text-xs text-gray-500">
                                            #<?php echo $index + 1; ?>
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            <?php echo date('d M Y H:i', strtotime($reply['created_at'])); ?>
                                        </span>
                                        <?php if ($reply['updated_at'] != $reply['created_at']): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-edit mr-1"></i> Diedit
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Reply Actions -->
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $reply['user_id']): ?>
                                        <div class="flex items-center space-x-2">
                                            <button onclick="editReply(<?php echo $reply['id']; ?>)" 
                                                    class="text-gray-400 hover:text-blue-600 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="confirmDeleteReply(<?php echo $reply['id']; ?>)" 
                                                    class="text-gray-400 hover:text-red-600 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Reply Content Display -->
                                <div id="reply-content-<?php echo $reply['id']; ?>" class="text-gray-700 leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                </div>
                                
                                <!-- Reply Edit Form -->
                                <div id="reply-edit-<?php echo $reply['id']; ?>" class="hidden">
                                    <textarea class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none" 
                                              rows="3"><?php echo htmlspecialchars($reply['content']); ?></textarea>
                                    <div class="mt-3 flex items-center space-x-2">
                                        <button onclick="saveReply(<?php echo $reply['id']; ?>)" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700">
                                            <i class="fas fa-save mr-1"></i> Simpan
                                        </button>
                                        <button onclick="cancelEditReply(<?php echo $reply['id']; ?>)" 
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-times mr-1"></i> Batal
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Reply Footer -->
                                <div class="mt-3 flex items-center space-x-4 text-xs text-gray-500">
                                    <button onclick="replyToReply('<?php echo htmlspecialchars($reply['user_name']); ?>')" 
                                            class="hover:text-blue-600 transition-colors">
                                        <i class="fas fa-reply mr-1"></i> Balas
                                    </button>
                                    <button onclick="copyReplyLink(<?php echo $reply['id']; ?>)" 
                                            class="hover:text-blue-600 transition-colors">
                                        <i class="fas fa-link mr-1"></i> Link
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-8 text-center">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-comments text-gray-400 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Belum ada balasan</h4>
                    <p class="text-gray-600 mb-4">Jadilah yang pertama memberikan balasan untuk post ini!</p>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button onclick="document.getElementById('reply-content').focus()" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-comment mr-2"></i> Tulis Balasan Pertama
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back to Forum Button -->
    <div class="mt-8 text-center">
        <a href="index.php?action=forum" 
           class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Forum
        </a>
    </div>
</div>

<!-- Delete Post Modal -->
<div id="deletePostModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
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
                <button id="confirmDeletePostBtn" 
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-700">
                    Hapus
                </button>
                <button onclick="closeDeletePostModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-24 hover:bg-gray-400">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Reply Modal -->
<div id="deleteReplyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Hapus Balasan</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Yakin ingin menghapus balasan ini? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmDeleteReplyBtn" 
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-700">
                    Hapus
                </button>
                <button onclick="closeDeleteReplyModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-24 hover:bg-gray-400">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg z-50 hidden transition-all duration-300">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="toast-message">Operasi berhasil!</span>
    </div>
</div>

<script>
let deletePostId = null;
let deleteReplyId = null;

// Post deletion functions
function confirmDeletePost(postId) {
    deletePostId = postId;
    document.getElementById('deletePostModal').classList.remove('hidden');
}

function closeDeletePostModal() {
    deletePostId = null;
    document.getElementById('deletePostModal').classList.add('hidden');
}

document.getElementById('confirmDeletePostBtn').addEventListener('click', function() {
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

// Reply functions
function editReply(replyId) {
    document.getElementById('reply-content-' + replyId).style.display = 'none';
    document.getElementById('reply-edit-' + replyId).style.display = 'block';
}

function cancelEditReply(replyId) {
    document.getElementById('reply-content-' + replyId).style.display = 'block';
    document.getElementById('reply-edit-' + replyId).style.display = 'none';
}

function saveReply(replyId) {
    const textarea = document.querySelector('#reply-edit-' + replyId + ' textarea');
    const content = textarea.value.trim();
    
    if (!content) {
        showToast('Konten tidak boleh kosong!', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('id', replyId);
    formData.append('content', content);
    
    fetch('index.php?action=forum_edit_reply', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('reply-content-' + replyId).innerHTML = content.replace(/\n/g, '<br>');
            cancelEditReply(replyId);
            showToast('Balasan berhasil diperbarui!', 'success');
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat menyimpan balasan', 'error');
    });
}

function confirmDeleteReply(replyId) {
    deleteReplyId = replyId;
    document.getElementById('deleteReplyModal').classList.remove('hidden');
}

function closeDeleteReplyModal() {
    deleteReplyId = null;
    document.getElementById('deleteReplyModal').classList.add('hidden');
}

document.getElementById('confirmDeleteReplyBtn').addEventListener('click', function() {
    if (deleteReplyId) {
        const formData = new FormData();
        formData.append('id', deleteReplyId);
        
        fetch('index.php?action=forum_delete_reply', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('reply-' + deleteReplyId).remove();
                closeDeleteReplyModal();
                showToast('Balasan berhasil dihapus!', 'success');
                
                // Update reply count
                updateReplyCount();
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat menghapus balasan', 'error');
        });
    }
});

// Utility functions
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    
    toastMessage.textContent = message;
    
    if (type === 'error') {
        toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-md shadow-lg z-50 transition-all duration-300';
    } else {
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg z-50 transition-all duration-300';
    }
    
    toast.classList.remove('hidden');
    
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}

function copyPostLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        showToast('Link post berhasil disalin!', 'success');
    });
}

function copyReplyLink(replyId) {
    const url = window.location.href + '#reply-' + replyId;
    navigator.clipboard.writeText(url).then(() => {
        showToast('Link balasan berhasil disalin!', 'success');
    });
}

function sharePost() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo addslashes($post['title']); ?>',
            url: window.location.href
        });
    } else {
        copyPostLink();
    }
}

function replyToReply(userName) {
    const textarea = document.getElementById('reply-content');
    const currentContent = textarea.value;
    const mention = '@' + userName + ' ';
    
    if (!currentContent.includes(mention)) {
        textarea.value = mention + currentContent;
    }
    
    textarea.focus();
    textarea.scrollIntoView({ behavior: 'smooth' });
}

function updateReplyCount() {
    const replyElements = document.querySelectorAll('[id^="reply-"]');
    const count = replyElements.length;
    
    // Update reply count in header if exists
    const replyCountElements = document.querySelectorAll('*[data-reply-count]');
    replyCountElements.forEach(element => {
        element.textContent = count + ' balasan';
    });
}

// Close modals when clicking outside
document.getElementById('deletePostModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeletePostModal();
    }
});

document.getElementById('deleteReplyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteReplyModal();
    }
});

// Auto-resize textarea
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
});
</script>

<?php include_once 'views/includes/footer.php'; ?>