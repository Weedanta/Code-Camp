<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Review - Code Camp Admin</title>
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
                        <h1 class="text-2xl font-bold text-gray-800">Kelola Review</h1>
                        <p class="text-gray-600 mt-1">Moderasi review dan rating bootcamp</p>
                    </div>
                    <div class="flex items-center space-x-4 mt-4 lg:mt-0">
                        <?php if (($totalReviews ?? 0) > 0): ?>
                            <a 
                                href="admin.php?action=bulk_approve_reviews" 
                                onclick="return confirm('Setujui semua review pending sekaligus?')"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Setujui Semua
                            </a>
                        <?php endif; ?>
                        <span class="bg-primary text-white px-3 py-1 rounded-full text-sm">
                            Total: <?= number_format($totalReviews) ?> review
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
                        <input type="hidden" name="action" value="manage_reviews">
                        
                        <!-- Search -->
                        <div class="flex-1">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Cari review, nama user, atau bootcamp..." 
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
                                <option value="pending" <?= ($status ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="published" <?= ($status ?? '') == 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="rejected" <?= ($status ?? '') == 'rejected' ? 'selected' : '' ?>>Rejected</option>
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

                <!-- Reviews List -->
                <div class="space-y-4">
                    <?php if (empty($reviews)): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                            <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500">Tidak ada review ditemukan</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                                <div class="flex flex-col lg:flex-row gap-6">
                                    <!-- Review Content -->
                                    <div class="flex-1">
                                        <!-- Header -->
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center space-x-3">
                                                <!-- User Avatar -->
                                                <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">
                                                        <?= strtoupper(substr($review['user_name'] ?? 'U', 0, 2)) ?>
                                                    </span>
                                                </div>
                                                
                                                <!-- User Info -->
                                                <div>
                                                    <h4 class="font-semibold text-gray-800">
                                                        <?= htmlspecialchars($review['user_name'] ?? 'Unknown User') ?>
                                                    </h4>
                                                    <p class="text-sm text-gray-600">
                                                        untuk <span class="font-medium"><?= htmlspecialchars($review['bootcamp_title'] ?? 'Unknown Bootcamp') ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <!-- Status Badge -->
                                            <?php 
                                            $statusClasses = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'published' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800'
                                            ];
                                            $statusClass = $statusClasses[$review['status'] ?? 'pending'] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
                                                <?= ucfirst($review['status'] ?? 'pending') ?>
                                            </span>
                                        </div>

                                        <!-- Rating -->
                                        <div class="flex items-center mb-3">
                                            <div class="flex items-center space-x-1">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <svg class="w-5 h-5 <?= $i <= ($review['rating'] ?? 0) ? 'text-yellow-400' : 'text-gray-300' ?>" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="ml-2 text-sm font-medium text-gray-700">
                                                <?= number_format($review['rating'] ?? 0, 1) ?>/5
                                            </span>
                                        </div>

                                        <!-- Review Text -->
                                        <div class="mb-4">
                                            <p class="text-gray-700 leading-relaxed">
                                                <?= nl2br(htmlspecialchars($review['review_text'] ?? '')) ?>
                                            </p>
                                        </div>

                                        <!-- Date -->
                                        <div class="text-sm text-gray-500">
                                            <?= date('d F Y, H:i', strtotime($review['created_at'] ?? 'now')) ?>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex lg:flex-col gap-2 lg:w-32">
                                        <?php if ($review['status'] === 'pending'): ?>
                                            <a 
                                                href="admin.php?action=approve_review&id=<?= $review['id'] ?>" 
                                                class="flex-1 lg:flex-none bg-green-100 text-green-800 px-3 py-2 rounded-lg text-sm font-medium hover:bg-green-200 transition-colors duration-200 text-center"
                                                title="Setujui review"
                                            >
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Setujui
                                            </a>
                                            
                                            <a 
                                                href="admin.php?action=reject_review&id=<?= $review['id'] ?>" 
                                                class="flex-1 lg:flex-none bg-red-100 text-red-800 px-3 py-2 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors duration-200 text-center"
                                                onclick="return confirm('Tolak review ini?')"
                                                title="Tolak review"
                                            >
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Tolak
                                            </a>
                                        <?php elseif ($review['status'] === 'published'): ?>
                                            <div class="flex-1 lg:flex-none bg-green-50 text-green-700 px-3 py-2 rounded-lg text-sm font-medium text-center">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Published
                                            </div>
                                        <?php elseif ($review['status'] === 'rejected'): ?>
                                            <div class="flex-1 lg:flex-none bg-red-50 text-red-700 px-3 py-2 rounded-lg text-sm font-medium text-center">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Rejected
                                            </div>
                                        <?php endif; ?>
                                        
                                        <a 
                                            href="admin.php?action=delete_review&id=<?= $review['id'] ?>" 
                                            class="flex-1 lg:flex-none bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors duration-200 text-center"
                                            onclick="return confirm('Hapus review ini permanen?')"
                                            title="Hapus review"
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
                                Menampilkan <?= (($page - 1) * 20) + 1 ?> sampai <?= min($page * 20, $totalReviews) ?> dari <?= $totalReviews ?> review
                            </div>
                            
                            <div class="flex items-center space-x-1">
                                <!-- Previous Page -->
                                <?php if ($page > 1): ?>
                                    <a 
                                        href="admin.php?action=manage_reviews&page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $status ? '&status=' . urlencode($status) : '' ?>" 
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
                                        href="admin.php?action=manage_reviews&page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $status ? '&status=' . urlencode($status) : '' ?>" 
                                        class="px-3 py-2 text-sm border rounded-md <?= $i == $page ? 'bg-primary text-white border-primary' : 'bg-white border-gray-300 hover:bg-gray-50' ?>"
                                    >
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <!-- Next Page -->
                                <?php if ($page < $totalPages): ?>
                                    <a 
                                        href="admin.php?action=manage_reviews&page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $status ? '&status=' . urlencode($status) : '' ?>" 
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