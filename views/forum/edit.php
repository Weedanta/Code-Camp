<?php include_once 'views/partials/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php?action=forum">Forum</a></li>
                    <li class="breadcrumb-item"><a href="index.php?action=forum_detail&id=<?php echo $post['id']; ?>">Detail Post</a></li>
                    <li class="breadcrumb-item active">Edit Post</li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="text-center mb-4">
                <h2><i class="fas fa-edit"></i> Edit Post Forum</h2>
                <p class="text-muted">Perbarui konten post Anda</p>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['error']) {
                        case 'empty_fields':
                            echo 'Judul dan konten tidak boleh kosong!';
                            break;
                        case 'update_failed':
                            echo 'Gagal memperbarui post. Silakan coba lagi!';
                            break;
                        default:
                            echo 'Terjadi kesalahan!';
                            break;
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Edit Post Form -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Form Edit Post</h5>
                    <small class="text-muted">
                        <i class="fas fa-clock"></i> Dibuat: <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                    </small>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?action=forum_update">
                        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Post <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   placeholder="Masukkan judul yang menarik..." 
                                   value="<?php echo htmlspecialchars($post['title']); ?>" 
                                   required maxlength="255">
                            <div class="form-text">Maksimal 255 karakter</div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Konten Post <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="10" 
                                      placeholder="Tulis konten post Anda di sini..." 
                                      required><?php echo htmlspecialchars($post['content']); ?></textarea>
                            <div class="form-text">
                                Jelaskan topik atau pertanyaan Anda secara detail. Gunakan paragraf untuk memudahkan pembacaan.
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Catatan:</strong> Setelah diperbarui, post akan menampilkan waktu edit. 
                                Pastikan perubahan yang Anda lakukan sudah sesuai sebelum menyimpan.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="index.php?action=forum_detail&id=<?php echo $post['id']; ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Post
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-info me-2" onclick="showPreview()">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="card mt-4" id="preview-section" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-eye"></i> Preview Post</h6>
                </div>
                <div class="card-body">
                    <h5 id="preview-title"></h5>
                    <small class="text-muted d-block mb-3">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($post['user_name']); ?>
                        <i class="fas fa-clock ms-3"></i> <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                        <i class="fas fa-edit ms-3"></i> Akan diedit: <?php echo date('d M Y H:i'); ?>
                    </small>
                    <div id="preview-content"></div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hidePreview()">
                        <i class="fas fa-times"></i> Tutup Preview
                    </button>
                </div>
            </div>

            <!-- Change History (if needed) -->
            <?php if ($post['updated_at'] != $post['created_at']): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-history"></i> Riwayat Perubahan</h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <i class="fas fa-edit"></i> Terakhir diedit: <?php echo date('d M Y H:i', strtotime($post['updated_at'])); ?>
                        </small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
function showPreview() {
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    
    if (!title.trim() && !content.trim()) {
        alert('Masukkan judul atau konten untuk melihat preview');
        return;
    }
    
    document.getElementById('preview-title').textContent = title || 'Tanpa Judul';
    document.getElementById('preview-content').innerHTML = content.replace(/\n/g, '<br>') || 'Tanpa Konten';
    document.getElementById('preview-section').style.display = 'block';
    document.getElementById('preview-section').scrollIntoView({ behavior: 'smooth' });
}

function hidePreview() {
    document.getElementById('preview-section').style.display = 'none';
}

// Character counter for title
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    
    titleInput.addEventListener('input', function() {
        const remaining = 255 - this.value.length;
        let helpText = this.nextElementSibling;
        if (remaining < 50) {
            helpText.textContent = `Sisa ${remaining} karakter`;
            helpText.className = remaining < 20 ? 'form-text text-danger' : 'form-text text-warning';
        } else {
            helpText.textContent = 'Maksimal 255 karakter';
            helpText.className = 'form-text';
        }
    });

    // Trigger initial count
    titleInput.dispatchEvent(new Event('input'));
});

// Confirm before leaving if changes made
let initialTitle = document.getElementById('title').value;
let initialContent = document.getElementById('content').value;

window.addEventListener('beforeunload', function(e) {
    const currentTitle = document.getElementById('title').value;
    const currentContent = document.getElementById('content').value;
    
    if (currentTitle !== initialTitle || currentContent !== initialContent) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Remove warning when form is submitted
document.querySelector('form').addEventListener('submit', function() {
    window.removeEventListener('beforeunload', arguments.callee);
});
</script>

<?php include_once 'views/partials/footer.php'; ?>