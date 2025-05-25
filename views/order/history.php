<?php
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit();
}

// Set page variables for header
$current_page = ''; // No active menu for order history
$page_title = 'Order History - Code Camp';

// Include header
include_once 'views/includes/header.php';
?>

<!-- Page Header -->
<div class="bg-blue-900 text-white py-6">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">Order History</h1>
        <p class="mt-2">View and manage your orders</p>
    </div>
</div>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <?php if (empty($orders)): ?>
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="text-gray-500 mb-4">
                <i class="fas fa-receipt text-6xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">No orders yet</h2>
            <p class="text-gray-600 mb-6">You haven't made any purchases yet. Start exploring our bootcamps!</p>
            <a href="index.php?action=bootcamps" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors inline-block">
                Browse Bootcamps
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold text-gray-800">Your Orders</h2>
            </div>

            <!-- Order List -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payment Method
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        #<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo date('h:i A', strtotime($order['created_at'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">
                                        Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $status_class = '';
                                    switch ($order['payment_status']) {
                                        case 'completed':
                                            $status_class = 'bg-green-100 text-green-800';
                                            $status_text = 'Completed';
                                            break;
                                        case 'pending':
                                            $status_class = 'bg-yellow-100 text-yellow-800';
                                            $status_text = 'Pending';
                                            break;
                                        case 'failed':
                                            $status_class = 'bg-red-100 text-red-800';
                                            $status_text = 'Failed';
                                            break;
                                        default:
                                            $status_class = 'bg-gray-100 text-gray-800';
                                            $status_text = ucfirst($order['payment_status']);
                                    }
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php
                                    switch ($order['payment_method']) {
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
                                            echo ucfirst(str_replace('_', ' ', $order['payment_method']));
                                    }
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="index.php?action=order_detail&id=<?php echo $order['id']; ?>" class="text-blue-600 hover:text-blue-900">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        <?php if ($page > 1): ?>
                            <a href="index.php?action=my_orders&page=<?php echo $page - 1; ?>"
                                class="w-10 h-10 mx-1 flex items-center justify-center rounded-full border border-blue-600 text-blue-600 hover:bg-blue-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="w-10 h-10 mx-1 flex items-center justify-center rounded-full bg-blue-600 text-white">
                                    <?php echo $i; ?>
                                </span>
                            <?php else: ?>
                                <a href="index.php?action=my_orders&page=<?php echo $i; ?>"
                                    class="w-10 h-10 mx-1 flex items-center justify-center rounded-full text-gray-700 hover:bg-blue-50">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="index.php?action=my_orders&page=<?php echo $page + 1; ?>"
                                class="w-10 h-10 mx-1 flex items-center justify-center rounded-full border border-blue-600 text-blue-600 hover:bg-blue-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Include footer
include_once 'views/includes/footer.php';
?>