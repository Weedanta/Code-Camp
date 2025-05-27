<?php include_once 'views/partials/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php?action=forum">Forum</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($post['title']); ?></li>
                </ol>
            </nav>

            <!-- Alert Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['success']) {
                        case 'post_created':
                            echo 'Post berhasil dibuat!';
                            break;
                        case 'post_updated':
                            echo 'Post berhasil diperbarui!';
                            break;
                        case 'reply_added':
                            echo 'Reply berhasil ditambahkan!';
                            break;
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['error']) {
                        case 'empty_content':
                            echo 'Konten reply tidak boleh kosong!';
                            break;
                        case 'reply_failed':
                            echo 'Gagal menambahkan reply!';
                            break;
                        default:
                            echo 'Terjadi kesalahan!';
                            break;
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Main Post -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><?php echo htmlspecialchars($post['title']); ?></h4>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?action=forum_edit&id=<?php echo $post['id']; ?>">
                                    <i class="fas fa-edit"></i> Edit Post
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="index.php?action=forum_delete" 
                                          onsubmit="return confirm('Yakin ingin menghapus post ini?')" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash"></i> Hapus Post
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($post['user_name']); ?>
                            <i class="fas fa-clock ms-3"></i> <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                            <?php if ($post['updated_at'] != $post['created_at']): ?>
                                <i class="fas fa-edit ms-3"></i> Diedit: <?php echo date('d M Y H:i', strtotime($post['updated_at'])); ?>
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="post-content">
                        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                    </div>
                </div>
            </div>

            <!-- Replies Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-comments"></i> Balasan (<?php echo count($replies); ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Add Reply Form -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" action="index.php?action=forum_add_reply" class="mb-4">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <div class="mb-3">
                                <label for="content" class="form-label">Tambah Balasan</label>
                                <textarea class="form-control" id="content" name="content" rows="4" 
                                          placeholder="Tulis balasan Anda..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-reply"></i> Kirim Balasan
                            </button>
                        </form>
                        <hr>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <a href="index.php?action=login">Login</a> untuk menambahkan balasan.
                        </div>
                    <?php endif; ?>

                    <!-- Replies List -->
                    <?php if (!empty($replies)): ?>
                        <?php foreach ($replies as $reply): ?>
                            <div class="border-bottom pb-3 mb-3" id="reply-<?php echo $reply['id']; ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($reply['user_name']); ?>
                                                <i class="fas fa-clock ms-3"></i> <?php echo date('d M Y H:i', strtotime($reply['created_at'])); ?>
                                                <?php if ($reply['updated_at'] != $reply['created_at']): ?>
                                                    <i class="fas fa-edit ms-3"></i> Diedit
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div class="reply-content" id="reply-content-<?php echo $reply['id']; ?>">
                                            <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                        </div>
                                        <div class="reply-edit-form" id="reply-edit-<?php echo $reply['id']; ?>" style="display: none;">
                                            <textarea class="form-control mb-2" rows="3"><?php echo htmlspecialchars($reply['content']); ?></textarea>
                                            <button type="button" class="btn btn-sm btn-success" onclick="saveReply(<?php echo $reply['id']; ?>)">
                                                <i class="fas fa-save"></i> Simpan
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="cancelEditReply(<?php echo $reply['id']; ?>)">
                                                <i class="fas fa-times"></i> Batal
                                            </button>
                                        </div>
                                    </div>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $reply['user_id']): ?>
                                        <div class="dropdown ms-2">
                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" onclick="editReply(<?php echo $reply['id']; ?>)">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteReply(<?php echo $reply['id']; ?>)">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a></li>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-comment fa-2x text-muted mb-3"></i>
                            <h6 class="text-muted">Belum ada balasan</h6>
                            <p class="text-muted">Jadilah yang pertama memberikan balasan!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Reply Management -->
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

<?php include_once 'views/partials/footer.php'; ?>