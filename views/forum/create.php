<?php include_once 'views/partials/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php?action=forum">Forum</a></li>
                    <li class="breadcrumb-item active">Buat Post Baru</li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="text-center mb-4">
                <h2><i class="fas fa-plus-circle"></i> Buat Post Forum Baru</h2>
                <p class="text-muted">Bagikan topik diskusi atau pertanyaan Anda dengan komunitas</p>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['error']) {
                        case 'empty_fields':
                            echo 'Judul dan konten tidak boleh kosong!';
                            break;
                        case 'create_failed':
                            echo 'Gagal membuat post. Silakan coba lagi!';
                            break;
                        default:
                            echo 'Terjadi kesalahan!';
                            break;
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Create Post Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Form Post Baru</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?action=forum_store">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Post <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   placeholder="Masukkan judul yang menarik..." 
                                   value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                                   required maxlength="255">
                            <div class="form-text">Maksimal 255 karakter</div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Konten Post <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="10" 
                                      placeholder="Tulis konten post Anda di sini..." 
                                      required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                            <div class="form-text">
                                Jelaskan topik atau pertanyaan Anda secara detail. Gunakan paragraf untuk memudahkan pembacaan.
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-lightbulb"></i> Tips untuk Post yang Baik:</h6>
                                    <ul class="mb-0 small">
                                        <li>Gunakan judul yang jelas dan spesifik</li>
                                        <li>Jelaskan konteks atau latar belakang permasalahan</li>
                                        <li>Gunakan bahasa yang sopan dan mudah dipahami</li>
                                        <li>Berikan detail yang cukup agar orang lain bisa membantu</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="index.php?action=forum" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Forum
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Publikasikan Post
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Section (Optional) -->
            <div class="card mt-4" id="preview-section" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-eye"></i> Preview Post</h6>
                </div>
                <div class="card-body">
                    <h5 id="preview-title"></h5>
                    <div id="preview-content"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Preview -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const contentInput = document.getElementById('content');
    const previewSection = document.getElementById('preview-section');
    const previewTitle = document.getElementById('preview-title');
    const previewContent = document.getElementById('preview-content');

    // Add preview button
    const previewBtn = document.createElement('button');
    previewBtn.type = 'button';
    previewBtn.className = 'btn btn-outline-info me-2';
    previewBtn.innerHTML = '<i class="fas fa-eye"></i> Preview';
    previewBtn.onclick = function() {
        if (titleInput.value.trim() || contentInput.value.trim()) {
            previewTitle.textContent = titleInput.value || 'Tanpa Judul';
            previewContent.innerHTML = contentInput.value.replace(/\n/g, '<br>') || 'Tanpa Konten';
            previewSection.style.display = 'block';
            previewSection.scrollIntoView({ behavior: 'smooth' });
        } else {
            alert('Masukkan judul atau konten untuk melihat preview');
        }
    };

    // Insert preview button before submit button
    const submitBtn = document.querySelector('button[type="submit"]');
    submitBtn.parentNode.insertBefore(previewBtn, submitBtn);

    // Character counter for title
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
});
</script>

<?php include_once 'views/partials/footer.php'; ?>