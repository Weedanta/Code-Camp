<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Reviews - Admin Campus Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-gradient {
            background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
        }
        .table-hover:hover {
            background-color: #f8fafc;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }
        .status-published {
            background-color: #dcfce7;
            color: #16a34a;
        }
        .status-rejected {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .rating-stars {
            color: #fbbf24;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include_once 'views/admin/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Kelola Reviews</h1>
                        <p class="text-gray-600">Moderasi dan manajemen ulasan bootcamp</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="bulkApprove()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-check-double mr-2"></i>
                            Approve All Pending
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6">
            <!-- Alerts -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    <span><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-star text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Reviews</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo number_format($totalReviews); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pending</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo count(array_filter($reviews, function($r) { return $r['status'] === 'pending'; })); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Published</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo count(array_filter($reviews, function($r) { return $r['status'] === 'published'; })); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-star text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Avg Rating</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php 
                                $publishedReviews = array_filter($reviews, function($r) { return $r['status'] === 'published'; });
                                $avgRating = !empty($publishedReviews) ? array_sum(array_column($publishedReviews, 'rating')) / count($publishedReviews) : 0;
                                echo number_format($avgRating, 1);
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="admin.php" class="flex flex-wrap items-center gap-4">
                    <input type="hidden" name="action" value="manage_reviews">
                    
                    <!-- Search -->
                    <div class="flex-1 min-w-64">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                   name="search" 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>"
                                   placeholder="Cari bootcamp, user, atau isi review..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <!-- Status Filter -->
                    <div>
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="pending" <?php echo ($status ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="published" <?php echo ($status ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="rejected" <?php echo ($status ?? '') === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    
                    <!-- Rating Filter -->
                    <div>
                        <select name="rating" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Rating</option>
                            <option value="5" <?php echo ($rating ?? '') === '5' ? 'selected' : ''; ?>>5 Bintang</option>
                            <option value="4" <?php echo ($rating ?? '') === '4' ? 'selected' : ''; ?>>4 Bintang</option>
                            <option value="3" <?php echo ($rating ?? '') === '3' ? 'selected' : ''; ?>>3 Bintang</option>
                            <option value="2" <?php echo ($rating ?? '') === '2' ? 'selected' : ''; ?>>2 Bintang</option>
                            <option value="1" <?php echo ($rating ?? '') === '1' ? 'selected' : ''; ?>>1 Bintang</option>
                        </select>
                    </div>
                    
                    <!-- Search Button -->
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    
                    <!-- Reset Button -->
                    <a href="admin.php?action=manage_reviews" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </a>
                </form>
            </div>

            <!-- Reviews Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Reviews</h3>
                    <span class="text-sm text-gray-600">Showing <?php echo count($reviews); ?> of <?php echo $totalReviews; ?> reviews</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Review</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bootcamp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($reviews)): ?>
                                <?php foreach ($reviews as $review): ?>
                                    <tr class="table-hover">
                                        <td class="px-6 py-4">
                                            <div class="max-w-xs">
                                                <p class="text-sm text-gray-900 line-clamp-3"><?php echo htmlspecialchars($review['review_text']); ?></p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                                <?php echo htmlspecialchars($review['bootcamp_title']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-user text-gray-600"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($review['user_name']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?> text-sm"></i>
                                                <?php endfor; ?>
                                                <span class="ml-2 text-sm text-gray-600"><?php echo $review['rating']; ?>/5</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge status-<?php echo $review['status']; ?>">
                                                <i class="fas fa-circle text-xs mr-1"></i>
                                                <?php echo ucfirst($review['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div><?php echo date('d M Y', strtotime($review['created_at'])); ?></div>
                                            <div class="text-xs"><?php echo date('H:i', strtotime($review['created_at'])); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <button onclick="viewReview(<?php echo $review['id']; ?>)" 
                                                        class="text-blue-600 hover:text-blue-900 transition-colors" 
                                                        title="View Full Review">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <?php if ($review['status'] === 'pending'): ?>
                                                    <button onclick="updateReviewStatus(<?php echo $review['id']; ?>, 'published')" 
                                                            class="text-green-600 hover:text-green-900 transition-colors" 
                                                            title="Approve Review">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button onclick="updateReviewStatus(<?php echo $review['id']; ?>, 'rejected')" 
                                                            class="text-red-600 hover:text-red-900 transition-colors" 
                                                            title="Reject Review">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php elseif ($review['status'] === 'published'): ?>
                                                    <button onclick="updateReviewStatus(<?php echo $review['id']; ?>, 'rejected')" 
                                                            class="text-red-600 hover:text-red-900 transition-colors" 
                                                            title="Unpublish Review">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </button>
                                                <?php elseif ($review['status'] === 'rejected'): ?>
                                                    <button onclick="updateReviewStatus(<?php echo $review['id']; ?>, 'published')" 
                                                            class="text-green-600 hover:text-green-900 transition-colors" 
                                                            title="Publish Review">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <button onclick="deleteReview(<?php echo $review['id']; ?>)" 
                                                        class="text-red-600 hover:text-red-900 transition-colors" 
                                                        title="Delete Review">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <i class="fas fa-star text-4xl mb-4"></i>
                                            <p class="text-lg font-medium">Tidak ada reviews ditemukan</p>
                                            <p class="mt-2">Coba ubah filter pencarian Anda</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="bg-white px-6 py-3 border-t border-gray-200 flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <?php if ($page > 1): ?>
                                <a href="?action=manage_reviews&page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search ?? ''); ?>&status=<?php echo urlencode($status ?? ''); ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?action=manage_reviews&page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search ?? ''); ?>&status=<?php echo urlencode($status ?? ''); ?>" 
                                   class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing page <span class="font-medium"><?php echo $page; ?></span> of <span class="font-medium"><?php echo $totalPages; ?></span>
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <a href="?action=manage_reviews&page=<?php echo $i; ?>&search=<?php echo urlencode($search ?? ''); ?>&status=<?php echo urlencode($status ?? ''); ?>" 
                                           class="<?php echo $i === $page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                </nav>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Review Detail Modal -->
    <div id="reviewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-2/3 max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Detail Review</h3>
                    <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="reviewContent" class="space-y-4">
                    <!-- Content will be loaded dynamically -->
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button onclick="closeReviewModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Confirmation Modal -->
    <div id="statusModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Update Status Review</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="statusMessage">
                        Apakah Anda yakin ingin mengubah status review?
                    </p>
                </div>
                <div class="items-center px-4 py-3 flex space-x-4">
                    <button id="cancelStatus" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400">
                        Batal
                    </button>
                    <button id="confirmStatus" class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let reviewToUpdate = null;
        let newStatus = null;

        function viewReview(reviewId) {
            // This would typically fetch review details via AJAX
            // For now, just show a placeholder
            document.getElementById('reviewContent').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                    <p class="mt-2 text-gray-600">Loading review details...</p>
                </div>
            `;
            document.getElementById('reviewModal').classList.remove('hidden');
            
            // Simulate loading (replace with actual AJAX call)
            setTimeout(() => {
                document.getElementById('reviewContent').innerHTML = `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Review untuk bootcamp dengan ID: ${reviewId}</p>
                        <p class="mt-2">Detail lengkap review akan dimuat di sini...</p>
                    </div>
                `;
            }, 1000);
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
        }

        function updateReviewStatus(reviewId, status) {
            reviewToUpdate = reviewId;
            newStatus = status;
            
            const statusMessages = {
                'published': 'Review akan dipublikasikan dan tampil di halaman bootcamp.',
                'rejected': 'Review akan ditolak dan tidak akan tampil di halaman bootcamp.',
                'pending': 'Review akan dikembalikan ke status pending untuk review ulang.'
            };
            
            document.getElementById('statusMessage').textContent = statusMessages[status] || 'Mengubah status review.';
            document.getElementById('statusModal').classList.remove('hidden');
        }

        function deleteReview(reviewId) {
            if (confirm('Apakah Anda yakin ingin menghapus review ini? Tindakan ini tidak dapat dibatalkan.')) {
                window.location.href = `admin.php?action=delete_review&id=${reviewId}`;
            }
        }

        function bulkApprove() {
            if (confirm('Apakah Anda yakin ingin menyetujui semua review yang pending?')) {
                window.location.href = 'admin.php?action=bulk_approve_reviews';
            }
        }

        // Status modal handlers
        document.getElementById('cancelStatus').addEventListener('click', function() {
            document.getElementById('statusModal').classList.add('hidden');
            reviewToUpdate = null;
            newStatus = null;
        });

        document.getElementById('confirmStatus').addEventListener('click', function() {
            if (reviewToUpdate && newStatus) {
                if (newStatus === 'published') {
                    window.location.href = `admin.php?action=approve_review&id=${reviewToUpdate}`;
                } else if (newStatus === 'rejected') {
                    window.location.href = `admin.php?action=reject_review&id=${reviewToUpdate}`;
                }
            }
        });

        // Close modals when clicking outside
        document.getElementById('reviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReviewModal();
            }
        });

        document.getElementById('statusModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                reviewToUpdate = null;
                newStatus = null;
            }
        });
    </script>
</body>
</html>