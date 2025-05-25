<?php
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit();
}

// Set page variables for header
$current_page = ''; // No active menu for order detail
$page_title = 'Order Detail - Code Camp';

// Include header
include_once 'views/includes/header.php';
?>

<!-- Page Header -->
<div class="bg-blue-900 text-white py-6">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">Order Detail</h1>
        <p class="mt-2">Order #<?php echo str_pad($this->order->id, 8, '0', STR_PAD_LEFT); ?></p>
    </div>
</div>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="text-gray-700 hover:text-blue-600">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="index.php?action=my_orders" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">My Orders</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2 font-medium">
                        Order #<?php echo str_pad($this->order->id, 8, '0', STR_PAD_LEFT); ?>
                    </span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Order Status Banner -->
    <?php
    $status_bg = '';
    $status_text = '';
    $status_icon = '';

    switch ($this->order->payment_status) {
        case 'completed':
            $status_bg = 'bg-green-100';
            $status_text = 'text-green-800';
            $status_icon = '<i class="fas fa-check-circle text-green-500 mr-2"></i>';
            $status_message = 'Payment completed successfully';
            break;
        case 'pending':
            $status_bg = 'bg-yellow-100';
            $status_text = 'text-yellow-800';
            $status_icon = '<i class="fas fa-clock text-yellow-500 mr-2"></i>';
            $status_message = 'Payment is pending';
            break;
        case 'failed':
            $status_bg = 'bg-red-100';
            $status_text = 'text-red-800';
            $status_icon = '<i class="fas fa-times-circle text-red-500 mr-2"></i>';
            $status_message = 'Payment failed';
            break;
        default:
            $status_bg = 'bg-gray-100';
            $status_text = 'text-gray-800';
            $status_icon = '<i class="fas fa-info-circle text-gray-500 mr-2"></i>';
            $status_message = 'Order status: ' . ucfirst($this->order->payment_status);
    }
    ?>
    <div class="<?php echo $status_bg; ?> <?php echo $status_text; ?> p-4 rounded-lg mb-6">
        <div class="flex items-center">
            <?php echo $status_icon; ?>
            <span class="font-medium"><?php echo $status_message; ?></span>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Order Details -->
        <div class="md:w-2/3">
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-800">Order Details</h2>
                </div>

                <div class="p-6">
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Order ID:</span>
                        <span class="text-gray-900 font-medium">#<?php echo str_pad($this->order->id, 8, '0', STR_PAD_LEFT); ?></span>
                    </div>

                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Date:</span>
                        <span class="text-gray-900"><?php echo date('F d, Y, h:i A', strtotime($this->order->created_at)); ?></span>
                    </div>

                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Payment Status:</span>
                        <?php
                        $payment_status_class = '';
                        switch ($this->order->payment_status) {
                            case 'completed':
                                $payment_status_class = 'text-green-600';
                                break;
                            case 'pending':
                                $payment_status_class = 'text-yellow-600';
                                break;
                            case 'failed':
                                $payment_status_class = 'text-red-600';
                                break;
                            default:
                                $payment_status_class = 'text-gray-900';
                        }
                        ?>
                        <span class="<?php echo $payment_status_class; ?> font-medium">
                            <?php echo ucfirst($this->order->payment_status); ?>
                        </span>
                    </div>

                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="text-gray-900">
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
                        </span>
                    </div>

                    <?php if (!empty($this->order->transaction_id)): ?>
                        <div class="flex justify-between mb-4">
                            <span class="text-gray-600">Transaction ID:</span>
                            <span class="text-gray-900"><?php echo $this->order->transaction_id; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-800">Items</h2>
                </div>

                <div class="p-6">
                    <?php foreach ($items as $item): ?>
                        <div class="flex items-start mb-6 last:mb-0">
                            <?php if (!empty($item['image'])): ?>
                                <img src="assets/images/bootcamps/<?php echo htmlspecialchars($item['image']); ?>"
                                    alt="<?php echo htmlspecialchars($item['title']); ?>"
                                    class="w-24 h-16 object-cover rounded-md mr-4">
                            <?php else: ?>
                                <div class="w-24 h-16 bg-gray-200 rounded-md flex items-center justify-center mr-4">
                                    <span class="text-gray-500 text-xs">No image</span>
                                </div>
                            <?php endif; ?>

                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800 mb-1">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </h3>
                                <a href="index.php?action=bootcamp_detail&id=<?php echo $item['bootcamp_id']; ?>" class="text-blue-600 hover:underline text-sm">
                                    View Bootcamp
                                </a>
                            </div>

                            <div class="text-right ml-4">
                                <div class="text-gray-900 font-medium">
                                    Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="md:w-1/3">
            <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-4">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-800">Order Summary</h2>
                </div>

                <div class="p-6">
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">
                                Rp <?php echo number_format($this->order->total_amount, 0, ',', '.'); ?>
                            </span>
                        </div>

                        <!-- You can add more items like tax, discount, etc. here -->

                        <div class="flex justify-between border-t border-gray-200 pt-4 font-bold">
                            <span class="text-gray-800">Total</span>
                            <span class="text-blue-600">
                                Rp <?php echo number_format($this->order->total_amount, 0, ',', '.'); ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($this->order->payment_status == 'completed'): ?>
                        <!-- Actions for completed orders -->
                        <a href="index.php?action=my_bootcamps" class="block w-full px-4 py-2 bg-blue-600 text-white text-center font-medium rounded-md hover:bg-blue-700 transition-colors mb-3">
                            Go to My Bootcamps
                        </a>
                        <button onclick="window.print()" class="block w-full px-4 py-2 border border-gray-300 text-gray-700 text-center font-medium rounded-md hover:bg-gray-50 transition-colors">
                            <i class="fas fa-print mr-2"></i> Print Receipt
                        </button>
                    <?php elseif ($this->order->payment_status == 'pending'): ?>
                        <!-- Actions for pending orders -->
                        <a href="#" class="block w-full px-4 py-2 bg-blue-600 text-white text-center font-medium rounded-md hover:bg-blue-700 transition-colors mb-3">
                            Complete Payment
                        </a>
                        <form action="index.php?action=cancel_order" method="post" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            <input type="hidden" name="order_id" value="<?php echo $this->order->id; ?>">
                            <button type="submit" class="block w-full px-4 py-2 border border-red-600 text-red-600 text-center font-medium rounded-md hover:bg-red-50 transition-colors">
                                Cancel Order
                            </button>
                        </form>
                    <?php elseif ($this->order->payment_status == 'failed'): ?>
                        <!-- Actions for failed orders -->
                        <a href="index.php?action=retry_payment&id=<?php echo $this->order->id; ?>" class="block w-full px-4 py-2 bg-blue-600 text-white text-center font-medium rounded-md hover:bg-blue-700 transition-colors mb-3">
                            Retry Payment
                        </a>
                        <a href="index.php?action=contact_support" class="block w-full px-4 py-2 border border-gray-300 text-gray-700 text-center font-medium rounded-md hover:bg-gray-50 transition-colors">
                            Contact Support
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'views/includes/footer.php';
?>