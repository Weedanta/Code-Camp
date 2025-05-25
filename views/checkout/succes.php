<?php
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit();
}

// Set page variables for header
$current_page = ''; // No active menu for success page
$page_title = 'Order Successful - Code Camp';

// Include header
include_once 'views/includes/header.php';
?>

<!-- Page Header -->
<div class="bg-blue-900 text-white py-6">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">Order Successful</h1>
        <p class="mt-2">Your bootcamp enrollment is confirmed</p>
    </div>
</div>

<!-- Checkout Steps -->
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-bold">
                    <i class="fas fa-check"></i>
                </div>
                <span class="text-sm mt-2">Review Order</span>
            </div>
            <div class="h-1 w-12 md:w-24 bg-green-500"></div>
        </div>

        <div class="flex items-center">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-bold">
                    <i class="fas fa-check"></i>
                </div>
                <span class="text-sm mt-2">Payment</span>
            </div>
            <div class="h-1 w-12 md:w-24 bg-green-500"></div>
        </div>

        <div class="flex flex-col items-center">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-bold">
                <i class="fas fa-check"></i>
            </div>
            <span class="text-sm mt-2">Confirmation</span>
        </div>
    </div>

    <!-- Success Message -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8 text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-500 text-5xl"></i>
            </div>

            <h2 class="text-2xl font-bold text-gray-800 mb-2">Payment Successful!</h2>
            <p class="text-gray-600 mb-6">Your enrollment has been confirmed and you can now access your bootcamp.</p>

            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="index.php?action=my_bootcamps" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors">
                    Go to My Bootcamps
                </a>
                <a href="index.php?action=order_detail&id=<?php echo $this->order->id; ?>" class="px-6 py-3 border border-blue-600 text-blue-600 font-medium rounded-md hover:bg-blue-50 transition-colors">
                    View Order Details
                </a>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Order Summary</h2>

            <div class="border-b pb-4 mb-4">
                <div class="text-sm text-gray-500 mb-1">Order ID</div>
                <div class="font-medium">#<?php echo str_pad($this->order->id, 8, '0', STR_PAD_LEFT); ?></div>
            </div>

            <div class="border-b pb-4 mb-4">
                <div class="text-sm text-gray-500 mb-1">Date</div>
                <div class="font-medium"><?php echo date('F d, Y, h:i A', strtotime($this->order->created_at)); ?></div>
            </div>

            <div class="border-b pb-4 mb-4">
                <div class="text-sm text-gray-500 mb-1">Payment Method</div>
                <div class="font-medium">
                    <?php
                    switch ($this->order->payment_method) {
                        case 'credit_card':
                            echo 'Credit / Debit Card';
                            break;
                        case 'bank_transfer':
                            echo 'Bank Transfer';
                            break;
                        case 'e_wallet':
                            echo 'E-Wallet';
                            break;
                        default:
                            echo ucfirst(str_replace('_', ' ', $this->order->payment_method));
                    }
                    ?>
                </div>
            </div>

            <div class="border-b pb-4 mb-4">
                <div class="text-sm text-gray-500 mb-3">Items</div>

                <?php if (isset($items) && !empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <div class="flex items-start mb-3">
                            <?php if (!empty($item['image'])): ?>
                                <img src="assets/images/bootcamps/<?php echo htmlspecialchars($item['image']); ?>"
                                    alt="<?php echo htmlspecialchars($item['title']); ?>"
                                    class="w-16 h-12 object-cover rounded-md mr-3">
                            <?php else: ?>
                                <div class="w-16 h-12 bg-gray-200 rounded-md flex items-center justify-center mr-3">
                                    <span class="text-gray-500 text-xs">No image</span>
                                </div>
                            <?php endif; ?>

                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </h3>
                                <div class="text-sm text-gray-500">
                                    Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-gray-500">No items found.</div>
                <?php endif; ?>
            </div>

            <div class="flex justify-between font-bold text-gray-800">
                <span>Total</span>
                <span>Rp <?php echo number_format($this->order->total_amount, 0, ',', '.'); ?></span>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'views/includes/footer.php';
?>