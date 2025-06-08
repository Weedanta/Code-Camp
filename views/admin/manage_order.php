<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Orders - Code Camp Admin</title>
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
                        <h1 class="text-2xl font-bold text-gray-800">Kelola Orders</h1>
                        <p class="text-gray-600 mt-1">Manajemen pesanan dan transaksi</p>
                    </div>
                    <div class="flex items-center space-x-4 mt-4 lg:mt-0">
                        <span class="bg-primary text-white px-3 py-1 rounded-full text-sm">
                            Total: <?= number_format($totalOrders) ?> orders
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
                        <input type="hidden" name="action" value="manage_orders">
                        
                        <!-- Search -->
                        <div class="flex-1">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Cari ID order, nama user, atau email..." 
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
                                <option value="completed" <?= ($status ?? '') == 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="failed" <?= ($status ?? '') == 'failed' ? 'selected' : '' ?>>Failed</option>
                                <option value="cancelled" <?= ($status ?? '') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
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

                <!-- Orders Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Table Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                <h3 class="text-lg font-semibold text-gray-800">Daftar Orders</h3>
                                <span class="text-sm text-gray-500">
                                    Halaman <?= $page ?> dari <?= $totalPages ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                                </svg>
                                            </div>
                                            <p class="text-gray-500">Tidak ada order ditemukan</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        #<?= $order['id'] ?>
                                                    </div>
                                                    <?php if (!empty($order['transaction_id'])): ?>
                                                        <div class="text-sm text-gray-500">
                                                            <?= htmlspecialchars($order['transaction_id']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center">
                                                            <span class="text-sm font-medium text-white">
                                                                <?= strtoupper(substr($order['user_name'] ?? 'U', 0, 2)) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <?= htmlspecialchars($order['user_name'] ?? 'Unknown') ?>
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            <?= htmlspecialchars($order['user_email'] ?? '') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    <?= number_format($order['item_count'] ?? 0) ?> item
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?= htmlspecialchars($order['payment_method'] ?? 'Unknown') ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    Rp <?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php 
                                                $statusClasses = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                    'cancelled' => 'bg-gray-100 text-gray-800'
                                                ];
                                                $statusClass = $statusClasses[$order['payment_status'] ?? 'pending'] ?? 'bg-gray-100 text-gray-800';
                                                ?>
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
                                                    <?= ucfirst($order['payment_status'] ?? 'pending') ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div>
                                                    <?= date('d M Y', strtotime($order['created_at'] ?? 'now')) ?>
                                                </div>
                                                <div class="text-gray-500">
                                                    <?= date('H:i', strtotime($order['created_at'] ?? 'now')) ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a 
                                                        href="admin.php?action=view_order&id=<?= $order['id'] ?>" 
                                                        class="text-primary hover:text-secondary"
                                                        title="Lihat detail order"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                    
                                                    <?php if ($order['payment_status'] === 'pending'): ?>
                                                        <button 
                                                            onclick="updateOrderStatus(<?= $order['id'] ?>, 'completed')" 
                                                            class="text-green-600 hover:text-green-700"
                                                            title="Tandai sebagai completed"
                                                        >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </button>
                                                        
                                                        <button 
                                                            onclick="updateOrderStatus(<?= $order['id'] ?>, 'failed')" 
                                                            class="text-red-600 hover:text-red-700"
                                                            title="Tandai sebagai failed"
                                                        >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="px-6 py-4 border-t border-gray-200">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div class="text-sm text-gray-700">
                                    Menampilkan <?= (($page - 1) * 20) + 1 ?> sampai <?= min($page * 20, $totalOrders) ?> dari <?= $totalOrders ?> orders
                                </div>
                                
                                <div class="flex items-center space-x-1">
                                    <!-- Previous Page -->
                                    <?php if ($page > 1): ?>
                                        <a 
                                            href="admin.php?action=manage_orders&page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $status ? '&status=' . urlencode($status) : '' ?>" 
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
                                            href="admin.php?action=manage_orders&page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $status ? '&status=' . urlencode($status) : '' ?>" 
                                            class="px-3 py-2 text-sm border rounded-md <?= $i == $page ? 'bg-primary text-white border-primary' : 'bg-white border-gray-300 hover:bg-gray-50' ?>"
                                        >
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>

                                    <!-- Next Page -->
                                    <?php if ($page < $totalPages): ?>
                                        <a 
                                            href="admin.php?action=manage_orders&page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $status ? '&status=' . urlencode($status) : '' ?>" 
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
            </div>
        </main>
    </div>

    <!-- Update Status Modal -->
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Update Status Order</h3>
            </div>
            
            <form id="statusForm" method="POST" action="admin.php?action=update_order_status" class="p-6">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" id="status_order_id" name="id" value="">
                <input type="hidden" id="status_new_status" name="status" value="">
                
                <p class="text-gray-600 mb-6" id="statusMessage">
                    Yakin ingin mengubah status order ini?
                </p>

                <div class="flex gap-3">
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-secondary transition-colors duration-200"
                    >
                        Ya, Update
                    </button>
                    <button 
                        type="button" 
                        onclick="closeStatusModal()" 
                        class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200"
                    >
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateOrderStatus(orderId, newStatus) {
            const statusMessages = {
                'completed': 'Tandai order ini sebagai completed?',
                'failed': 'Tandai order ini sebagai failed?',
                'cancelled': 'Batalkan order ini?'
            };
            
            document.getElementById('status_order_id').value = orderId;
            document.getElementById('status_new_status').value = newStatus;
            document.getElementById('statusMessage').textContent = statusMessages[newStatus] || 'Update status order ini?';
            
            document.getElementById('statusModal').classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('statusModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatusModal();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeStatusModal();
            }
        });

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