<?php
$current_page = 'forum';
$page_title = 'Edit Post - Code Camp';
require_once __DIR__ . '/../../views/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-3xl mx-auto">
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
                        <a href="index.php?action=forum_detail&id=<?php echo $post['id']; ?>" class="text-gray-700 hover:text-blue-600">Detail Post</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-500">Edit Post</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-edit text-blue-600"></i> Edit Post Forum
            </h2>
            <p class="mt-2 text-gray-600">Perbarui konten post Anda</p>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-700">
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
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Edit Post Form -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h5 class="font-medium text-gray-900">
                    <i class="fas fa-edit text-blue-600"></i> Form Edit Post
                </h5>
                <small class="text-gray-500">
                    <i class="fas fa-clock"></i> Dibuat: <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                </small>
            </div>
            <div class="p-6">
                <form method="POST" action="index.php?action=forum_update">
                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                    
                    <!-- Title Input -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Judul Post <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               id="title" 
                               name="title" 
                               placeholder="Masukkan judul yang menarik..."
                               value="<?php echo htmlspecialchars($post['title']); ?>"
                               required 
                               maxlength="255">
                        <p class="mt-1 text-sm text-gray-500">Maksimal 255 karakter</p>
                    </div>

                    <!-- Content Input -->
                    <div class="mb-6">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                            Konten Post <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            id="content" 
                            name="content" 
                            rows="10"
                            placeholder="Tulis konten post Anda di sini..."
                            required><?php echo htmlspecialchars($post['content']); ?></textarea>
                        <p class="mt-1 text-sm text-gray-500">
                            Jelaskan topik atau pertanyaan Anda secara detail. Gunakan paragraf untuk memudahkan pembacaan.
                        </p>
                    </div>

                    <!-- Info Note -->
                    <div class="mb-6">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-blue-700">
                                        <strong>Catatan:</strong> Setelah diperbarui, post akan menampilkan waktu edit. 
                                        Pastikan perubahan yang Anda lakukan sudah sesuai sebelum menyimpan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between items-center">
                        <a href="index.php?action=forum_detail&id=<?php echo $post['id']; ?>" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Post
                        </a>
                        <div class="space-x-2">
                            <button type="button" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    onclick="showPreview()">
                                <i class="fas fa-eye mr-2"></i> Preview
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Section -->
        <div id="preview-section" class="hidden mt-8 bg-white shadow rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
                <h6 class="font-medium text-gray-900">
                    <i class="fas fa-eye text-blue-600"></i> Preview Post
                </h6>
            </div>
            <div class="p-6">
                <h5 id="preview-title" class="text-xl font-medium text-gray-900 mb-2"></h5>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <span class="flex items-center">
                        <i class="fas fa-user mr-2"></i> <?php echo htmlspecialchars($post['user_name']); ?>
                    </span>
                    <span class="flex items-center ml-4">
                        <i class="fas fa-clock mr-2"></i> <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                    </span>
                    <span class="flex items-center ml-4">
                        <i class="fas fa-edit mr-2"></i> Akan diedit: <?php echo date('d M Y H:i'); ?>
                    </span>
                </div>
                <div id="preview-content" class="prose max-w-none"></div>
            </div>
            <div class="bg-gray-50 px-6 py-3 flex justify-end">
                <button type="button" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        onclick="hidePreview()">
                    <i class="fas fa-times mr-2"></i> Tutup Preview
                </button>
            </div>
        </div>

        <!-- Change History -->
        <?php if ($post['updated_at'] != $post['created_at']): ?>
            <div class="mt-8 bg-white shadow rounded-lg overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h6 class="font-medium text-gray-900">
                        <i class="fas fa-history text-blue-600"></i> Riwayat Perubahan
                    </h6>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-edit mr-2"></i> Terakhir diedit: <?php echo date('d M Y H:i', strtotime($post['updated_at'])); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Keep existing JavaScript but update class names in the functions -->
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

<?php require_once __DIR__ . '/../../views/includes/footer.php'; ?>