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
                        <?php echo $_GET['success'] === 'reply_added' ? 'Reply berhasil ditambahkan!' : 'Operasi berhasil!'; ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Post -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </h1>
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <span class="flex items-center">
                            <i class="fas fa-user mr-2"></i>
                            <?php echo htmlspecialchars($post['user_name']); ?>
                        </span>
                        <span class="flex items-center ml-4">
                            <i class="fas fa-clock mr-2"></i>
                            <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                        </span>
                    </div>
                </div>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                    <div class="flex space-x-2">
                        <a href="index.php?action=forum_edit&id=<?php echo $post['id']; ?>" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a>
                        <button onclick="confirmDelete(<?php echo $post['id']; ?>)" 
                                class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="prose max-w-none mt-4">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
        </div>
    </div>

    <!-- Replies Section -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-comments text-blue-600"></i> Balasan (<?php echo count($replies); ?>)
            </h3>
        </div>
        
        <!-- Reply Form -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="p-6 border-b border-gray-200">
                <form method="POST" action="index.php?action=forum_reply&post_id=<?php echo $post['id']; ?>">
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                            Tulis Balasan
                        </label>
                        <textarea name="content" id="content" rows="4"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  required></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Reply List -->
        <div class="divide-y divide-gray-200">
            <?php foreach ($replies as $reply): ?>
                <div class="p-6" id="reply-<?php echo $reply['id']; ?>">
                    <!-- Reply content here -->
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
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
        alert('Konten tidak boleh kosong!');
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
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container').insertBefore(alert, document.querySelector('.container').firstChild);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan reply');
    });
}

function deleteReply(replyId) {
    if (!confirm('Yakin ingin menghapus balasan ini?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('id', replyId);
    
    fetch('index.php?action=forum_delete_reply', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('reply-' + replyId).remove();
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container').insertBefore(alert, document.querySelector('.container').firstChild);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus reply');
    });
}
</script>

<?php include_once 'views/includes/footer.php'; ?>