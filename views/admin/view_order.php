<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Order #<?= $order['id'] ?? '' ?> - Code Camp Admin</title>
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
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 ml-12 lg:ml-0">
                        <a 
                            href="admin.php?action=manage_orders" 
                            class="text-gray-500 hover:text-primary transition-colors duration-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Detail Order #<?= $order['id'] ?? '' ?></h1>
                            <p class="text-gray-600 mt-1">Informasi lengkap pesanan</p>
                        </div>
                    </div>
                    
                    <!-- Status Badge -->
                    <div>
                        <?php 
                        $statusClasses = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'cancelled' => 'bg-gray-100 text-gray-800'
                        ];
                        $statusClass = $statusClasses[$order['payment_status'] ?? 'pending'] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full <?= $statusClass ?>">
                            <?= ucfirst($order['payment_status'] ?? 'pending') ?>
                        </span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-4 lg:p-6 max-w-6xl mx-auto space-y-6">
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

                <!-- Order Overview -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Order Info -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Informasi Order</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Order Details -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Order ID</label>
                                        <p class="text-lg font-semibold text-gray-800">#<?= $order['id'] ?></p>
                                    </div>
                                    
                                    <?php if (!empty($order['transaction_id'])): ?>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Transaction ID</label>
                                            <p class="text-sm text-gray-800 font-mono"><?= htmlspecialchars($order['transaction_id']) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Tanggal Order</label>
                                        <p class="text-sm text-gray-800"><?= date('d F Y, H:i', strtotime($order['created_at'] ?? 'now')) ?></p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Metode Pembayaran</label>
                                        <p class="text-sm text-gray-800"><?= htmlspecialchars($order['payment_method'] ?? 'Unknown') ?></p>
                                    </div>
                                </div>
                                
                                <!-- Payment Details -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Total Amount</label>
                                        <p class="text-2xl font-bold text-primary">Rp <?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?></p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Payment Status</label>
                                        <span class="inline-flex px-2 py-1 text-sm font-medium rounded-full <?= $statusClass ?>">
                                            <?= ucfirst($order['payment_status'] ?? 'pending') ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($order['updated_at']) && $order['updated_at'] !== $order['created_at']): ?>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Last Update</label>
                                            <p class="text-sm text-gray-800"><?= date('d F Y, H:i', strtotime($order['updated_at'])) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Informasi Customer</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="h-12 w-12 rounded-full bg-primary flex items-center justify-center">
                                    <span class="text-lg font-semibold text-white">
                                        <?= strtoupper(substr($order['user_name'] ?? 'U', 0, 2)) ?>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($order['user_name'] ?? 'Unknown') ?></h4>
                                    <p class="text-sm text-gray-600">Customer</p>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Email</label>
                                    <p class="text-sm text-gray-800"><?= htmlspecialchars($order['user_email'] ?? 'N/A') ?></p>
                                </div>
                                
                                <?php if (!empty($order['user_phone'])): ?>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Telepon</label>
                                        <p class="text-sm text-gray-800"><?= htmlspecialchars($order['user_phone']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Item yang Dibeli</h3>
                    </div>
                    
                    <div class="p-6">
                        <?php if (empty($orderItems)): ?>
                            <div class="text-center py-8">
                                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500">Tidak ada item dalam order ini</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($orderItems as $item): ?>
                                    <div class="flex items-center p-4 border border-gray-200 rounded-lg">
                                        <!-- Bootcamp Image -->
                                        <div class="h-16 w-24 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                                            <?php if (!empty($item['bootcamp_image'])): ?>
                                                <img 
                                                    src="assets/images/bootcamps/<?= htmlspecialchars($item['bootcamp_image']) ?>" 
                                                    alt="<?= htmlspecialchars($item['bootcamp_title']) ?>"
                                                    class="w-full h-full object-cover"
                                                >
                                            <?php else: ?>
                                                <div class="w-full h-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Item Details -->
                                        <div class="flex-1 ml-4">
                                            <h4 class="font-semibold text-gray-800 mb-1">
                                                <?= htmlspecialchars($item['bootcamp_title'] ?? 'Unknown Bootcamp') ?>
                                            </h4>
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm text-gray-600">
                                                    Quantity: <?= number_format($item['quantity'] ?? 1) ?>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-lg font-semibold text-gray-800">
                                                        Rp <?= number_format($item['price'] ?? 0, 0, ',', '.') ?>
                                                    </div>
                                                    <?php if (($item['price'] ?? 0) != ($item['original_price'] ?? 0)): ?>
                                                        <div class="text-sm text-gray-500 line-through">
                                                            Rp <?= number_format($item['original_price'] ?? 0, 0, ',', '.') ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <!-- Order Summary -->
                                <div class="border-t border-gray-200 pt-4 mt-6">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-gray-800">Total</span>
                                        <span class="text-2xl font-bold text-primary">
                                            Rp <?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions -->
                <?php if ($order['payment_status'] === 'pending'): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Aksi</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <button 
                                    onclick="updateOrderStatus(<?= $order['id'] ?>, 'completed')" 
                                    class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200"
                                >
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Tandai Completed
                                </button>
                                
                                <button 
                                    onclick="updateOrderStatus(<?= $order['id'] ?>, 'failed')" 
                                    class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors duration-200"
                                >
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Tandai Failed
                                </button>
                                
                                <button 
                                    onclick="updateOrderStatus(<?= $order['id'] ?>, 'cancelled')" 
                                    class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors duration-200"
                                >
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                    </svg>
                                    Batalkan Order
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
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
                'completed': 'Tandai order ini sebagai completed? Customer akan mendapatkan akses ke bootcamp.',
                'failed': 'Tandai order ini sebagai failed? Pembayaran akan dianggap gagal.',
                'cancelled': 'Batalkan order ini? Order akan dibatalkan secara permanen.'
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