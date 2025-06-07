<?php
// Set page variables for header
$current_page = 'forum';
$page_title = 'Forum - Code Camp';

// Include header
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
                        <span class="text-gray-500">Buat Post Baru</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-plus-circle text-blue-600"></i> Buat Post Forum Baru
            </h2>
            <p class="mt-2 text-gray-600">Bagikan topik diskusi atau pertanyaan Anda dengan komunitas</p>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p>
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
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Create Post Form -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
                <h5 class="font-medium text-gray-900">
                    <i class="fas fa-edit text-blue-600"></i> Form Post Baru
                </h5>
            </div>
            
            <div class="p-6">
                <form method="POST" action="index.php?action=forum_store">
                    <!-- Title Input -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Judul Post <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               id="title" 
                               name="title" 
                               placeholder="Masukkan judul yang menarik..."
                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
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
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                            id="content" 
                            name="content" 
                            rows="10"
                            placeholder="Tulis konten post Anda di sini..."
                            required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                        <p class="mt-1 text-sm text-gray-500">
                            Jelaskan topik atau pertanyaan Anda secara detail. Gunakan paragraf untuk memudahkan pembacaan.
                        </p>
                    </div>

                    <!-- Tips Section -->
                    <div class="mb-6">
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                            <h6 class="font-medium text-blue-800 mb-2">
                                <i class="fas fa-lightbulb text-blue-600"></i> Tips untuk Post yang Baik:
                            </h6>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Gunakan judul yang jelas dan spesifik</li>
                                <li>• Jelaskan konteks atau latar belakang permasalahan</li>
                                <li>• Gunakan bahasa yang sopan dan mudah dipahami</li>
                                <li>• Berikan detail yang cukup agar orang lain bisa membantu</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between items-center">
                        <a href="index.php?action=forum" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Forum
                        </a>
                        <div class="space-x-2">
                            <button type="reset" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-undo mr-2"></i> Reset
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-paper-plane mr-2"></i> Publikasikan Post
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Section -->
        <div id="preview-section" class="hidden mt-8 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
                <h6 class="font-medium text-gray-900">
                    <i class="fas fa-eye text-blue-600"></i> Preview Post
                </h6>
            </div>
            <div class="p-6">
                <h5 id="preview-title" class="text-xl font-medium text-gray-900 mb-4"></h5>
                <div id="preview-content" class="prose max-w-none text-gray-700"></div>
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
    previewBtn.className = 'inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-2';
    previewBtn.innerHTML = '<i class="fas fa-eye mr-2"></i> Preview';
    previewBtn.onclick = function() {
        if (titleInput.value.trim() || contentInput.value.trim()) {
            previewTitle.textContent = titleInput.value || 'Tanpa Judul';
            previewContent.innerHTML = contentInput.value.replace(/\n/g, '<br>') || 'Tanpa Konten';
            previewSection.classList.remove('hidden');
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
            helpText.className = remaining < 20 ? 'mt-1 text-sm text-red-600' : 'mt-1 text-sm text-yellow-600';
        } else {
            helpText.textContent = 'Maksimal 255 karakter';
            helpText.className = 'mt-1 text-sm text-gray-500';
        }
    });
});
</script>

<?php
// Include footer
require_once __DIR__ . '/../../views/includes/footer.php';
?>