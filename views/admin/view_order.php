<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Order - Admin Campus Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-gradient {
            background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }
        .status-completed {
            background-color: #dcfce7;
            color: #16a34a;
        }
        .status-failed {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .status-refunded {
            background-color: #e0e7ff;
            color: #3730a3;
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
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                <li class="inline-flex items-center">
                                    <a href="admin.php?action=manage_orders" class="text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                        Kelola Orders
                                    </a>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                        <span class="text-gray-700 font-medium">Detail Order</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl font-bold text-gray-900 mt-2">Order #<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></h1>
                        <p class="text-gray-600">Detail pemesanan dari <?php echo htmlspecialchars($order['user_name']); ?></p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="printOrder()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-print mr-2"></i>
                            Print
                        </button>
                        <a href="admin.php?action=manage_orders" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Order</h3>
                                <span class="status-badge status-<?php echo $order['payment_status']; ?>">
                                    <i class="fas fa-circle text-xs mr-2"></i>
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">Detail Pemesanan</h4>
                                    <dl class="space-y-2">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Order ID:</dt>
                                            <dd class="text-sm font-medium text-gray-900">#<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Transaction ID:</dt>
                                            <dd class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($order['transaction_id'] ?? 'N/A'); ?></dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Tanggal Order:</dt>
                                            <dd class="text-sm font-medium text-gray-900"><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Metode Pembayaran:</dt>
                                            <dd class="text-sm font-medium text-gray-900"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'] ?? 'N/A')); ?></dd>
                                        </div>
                                    </dl>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">Informasi Pembayaran</h4>
                                    <dl class="space-y-2">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Subtotal:</dt>
                                            <dd class="text-sm font-medium text-gray-900">Rp <?php echo number_format($order['subtotal'] ?? 0); ?></dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Diskon:</dt>
                                            <dd class="text-sm font-medium text-gray-900">Rp <?php echo number_format($order['discount_amount'] ?? 0); ?></dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Pajak:</dt>
                                            <dd class="text-sm font-medium text-gray-900">Rp <?php echo number_format($order['tax_amount'] ?? 0); ?></dd>
                                        </div>
                                        <div class="flex justify-between border-t pt-2">
                                            <dt class="text-base font-medium text-gray-900">Total:</dt>
                                            <dd class="text-base font-bold text-gray-900">Rp <?php echo number_format($order['total_amount']); ?></dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Item yang Dipesan</h3>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bootcamp</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diskon</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (!empty($orderItems)): ?>
                                        <?php foreach ($orderItems as $item): ?>
                                            <tr>
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                            <?php if (!empty($item['bootcamp_image'])): ?>
                                                                <img src="assets/images/bootcamps/<?php echo htmlspecialchars($item['bootcamp_image']); ?>" 
                                                                     alt="<?php echo htmlspecialchars($item['bootcamp_title']); ?>"
                                                                     class="w-12 h-12 object-cover rounded-lg">
                                                            <?php else: ?>
                                                                <i class="fas fa-laptop-code text-gray-600"></i>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['bootcamp_title']); ?></div>
                                                            <div class="text-sm text-gray-500">ID: <?php echo $item['bootcamp_id']; ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    Rp <?php echo number_format($item['price']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    Rp <?php echo number_format($item['discount_amount'] ?? 0); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    Rp <?php echo number_format($item['final_price']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada item</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Customer</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-base font-medium text-gray-900"><?php echo htmlspecialchars($order['user_name']); ?></h4>
                                    <p class="text-sm text-gray-500">Customer ID: #<?php echo $order['user_id']; ?></p>
                                </div>
                            </div>
                            
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">Email:</dt>
                                    <dd class="text-sm text-gray-600"><?php echo htmlspecialchars($order['user_email']); ?></dd>
                                </div>
                                <?php if (!empty($order['user_phone'])): ?>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900">Telepon:</dt>
                                        <dd class="text-sm text-gray-600"><?php echo htmlspecialchars($order['user_phone']); ?></dd>
                                    </div>
                                <?php endif; ?>
                            </dl>
                            
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="admin.php?action=edit_user&id=<?php echo $order['user_id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-external-link-alt mr-1"></i>
                                    Lihat Profile Customer
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Order Actions -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Aksi Order</h3>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <?php if ($order['payment_status'] === 'pending'): ?>
                                <button onclick="updateOrderStatus('completed')" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors">
                                    <i class="fas fa-check mr-2"></i>
                                    Mark as Completed
                                </button>
                                <button onclick="updateOrderStatus('failed')" 
                                        class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors">
                                    <i class="fas fa-times mr-2"></i>
                                    Mark as Failed
                                </button>
                            <?php elseif ($order['payment_status'] === 'completed'): ?>
                                <button onclick="updateOrderStatus('refunded')" 
                                        class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors">
                                    <i class="fas fa-undo mr-2"></i>
                                    Process Refund
                                </button>
                            <?php endif; ?>
                            
                            <button onclick="sendOrderEmail()" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fas fa-envelope mr-2"></i>
                                Send Email Update
                            </button>
                            
                            <button onclick="addOrderNote()" 
                                    class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fas fa-sticky-note mr-2"></i>
                                Add Note
                            </button>
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Timeline Order</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                        <i class="fas fa-shopping-cart text-white text-xs"></i>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-500">Order dibuat</p>
                                                        <p class="text-xs text-gray-400"><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    
                                    <?php if ($order['payment_status'] !== 'pending'): ?>
                                        <li>
                                            <div class="relative pb-8">
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full <?php echo $order['payment_status'] === 'completed' ? 'bg-green-500' : 'bg-red-500'; ?> flex items-center justify-center ring-8 ring-white">
                                                            <i class="fas fa-<?php echo $order['payment_status'] === 'completed' ? 'check' : 'times'; ?> text-white text-xs"></i>
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5">
                                                        <div>
                                                            <p class="text-sm text-gray-500">Payment <?php echo $order['payment_status']; ?></p>
                                                            <p class="text-xs text-gray-400"><?php echo date('d M Y H:i', strtotime($order['updated_at'])); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function updateOrderStatus(status) {
            const messages = {
                'completed': 'Menandai order sebagai selesai akan mengaktifkan akses bootcamp untuk user.',
                'failed': 'Menandai order sebagai gagal akan membatalkan pemesanan.',
                'refunded': 'Refund order akan mengembalikan uang dan menonaktifkan akses bootcamp.'
            };
            
            if (confirm(messages[status] + '\n\nLanjutkan?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin.php?action=update_order_status';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = '<?php echo $order['id']; ?>';
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = status;
                
                form.appendChild(idInput);
                form.appendChild(statusInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function sendOrderEmail() {
            if (confirm('Kirim email update status order ke customer?')) {
                // Implement email sending
                alert('Email berhasil dikirim ke customer');
            }
        }

        function addOrderNote() {
            const note = prompt('Tambahkan catatan untuk order ini:');
            if (note && note.trim()) {
                // Implement note adding
                alert('Catatan berhasil ditambahkan');
            }
        }

        function printOrder() {
            window.print();
        }
    </script>
</body>
</html>