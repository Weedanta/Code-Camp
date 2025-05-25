<?php
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit();
}

$user_name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];
$email = $_SESSION['alamat_email'];
$phone = isset($_SESSION['no_telepon']) ? $_SESSION['no_telepon'] : '';

// Set page variables for header
$current_page = ''; // No active menu for checkout
$page_title = 'Checkout - Code Camp';

// Include header
include_once 'views/includes/header.php';
?>

<!-- Page Header -->
<div class="bg-blue-900 text-white py-6">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">Checkout</h1>
        <p class="mt-2">Complete your bootcamp enrollment</p>
    </div>
</div>

<!-- Checkout Steps -->
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">1</div>
                <span class="text-sm mt-2">Review Order</span>
            </div>
            <div class="h-1 w-12 md:w-24 bg-blue-600"></div>
        </div>

        <div class="flex items-center">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">2</div>
                <span class="text-sm mt-2">Payment</span>
            </div>
            <div class="h-1 w-12 md:w-24 bg-blue-600"></div>
        </div>

        <div class="flex flex-col items-center">
            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">3</div>
            <span class="text-sm mt-2">Confirmation</span>
        </div>
    </div>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'payment_failed'): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm">Payment processing failed. Please try again or contact support if the problem persists.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Order Summary -->
        <div class="md:w-1/2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Order Summary</h2>

                <div class="border-b pb-4 mb-4">
                    <div class="flex items-start">
                        <?php if (!empty($this->bootcamp->image)): ?>
                            <img src="assets/images/bootcamps/<?php echo htmlspecialchars($this->bootcamp->image); ?>"
                                alt="<?php echo htmlspecialchars($this->bootcamp->title); ?>"
                                class="w-24 h-16 object-cover rounded-md mr-4">
                        <?php else: ?>
                            <div class="w-24 h-16 bg-gray-200 rounded-md flex items-center justify-center mr-4">
                                <span class="text-gray-500 text-xs">No image</span>
                            </div>
                        <?php endif; ?>

                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800">
                                <?php echo htmlspecialchars($this->bootcamp->title); ?>
                            </h3>
                            <p class="text-sm text-gray-600">
                                by <?php echo htmlspecialchars($this->bootcamp->instructor_name); ?>
                            </p>
                            <div class="flex items-center text-sm text-gray-500 mt-1">
                                <span class="mr-3">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    <?php echo date('d M Y', strtotime($this->bootcamp->start_date)); ?>
                                </span>
                                <span>
                                    <i class="far fa-clock mr-1"></i>
                                    <?php echo htmlspecialchars($this->bootcamp->duration); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bootcamp Price</span>
                        <span class="text-gray-800">
                            Rp <?php echo number_format($this->bootcamp->price, 0, ',', '.'); ?>
                        </span>
                    </div>

                    <?php if (!empty($this->bootcamp->discount_price) && $this->bootcamp->discount_price > $this->bootcamp->price): ?>
                        <div class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span>
                                -Rp <?php echo number_format($this->bootcamp->discount_price - $this->bootcamp->price, 0, ',', '.'); ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-between border-t border-gray-200 pt-2 font-bold">
                        <span class="text-gray-800">Total</span>
                        <span class="text-blue-600">
                            Rp <?php echo number_format($this->bootcamp->price, 0, ',', '.'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="md:w-1/2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Payment Information</h2>

                <form action="index.php?action=process_order" method="post" id="paymentForm">
                    <input type="hidden" name="bootcamp_id" value="<?php echo $this->bootcamp->id; ?>">

                    <!-- Contact Information -->
                    <div class="mb-6">
                        <h3 class="font-medium text-gray-800 mb-3">Contact Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-gray-700 text-sm mb-1">Full Name</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_name); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    readonly>
                            </div>

                            <div>
                                <label for="email" class="block text-gray-700 text-sm mb-1">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    readonly>
                            </div>

                            <div class="md:col-span-2">
                                <label for="phone" class="block text-gray-700 text-sm mb-1">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-6">
                        <h3 class="font-medium text-gray-800 mb-3">Payment Method</h3>

                        <div class="space-y-3">
                            <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-blue-50 transition-colors">
                                <input type="radio" name="payment_method" value="credit_card" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2">Credit / Debit Card</span>
                                <div class="ml-auto flex space-x-2">
                                    <i class="fab fa-cc-visa text-blue-800 text-xl"></i>
                                    <i class="fab fa-cc-mastercard text-red-600 text-xl"></i>
                                </div>
                            </label>

                            <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-blue-50 transition-colors">
                                <input type="radio" name="payment_method" value="bank_transfer" class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2">Bank Transfer</span>
                            </label>

                            <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-blue-50 transition-colors">
                                <input type="radio" name="payment_method" value="e_wallet" class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2">E-Wallet</span>
                            </label>
                        </div>
                    </div>

                    <!-- Card Information (only visible if credit_card selected) -->
                    <div id="cardDetails" class="mb-6">
                        <h3 class="font-medium text-gray-800 mb-3">Card Information</h3>

                        <div class="space-y-4">
                            <div>
                                <label for="card_number" class="block text-gray-700 text-sm mb-1">Card Number</label>
                                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    maxlength="19">
                                <p class="text-xs text-gray-500 mt-1">Use any card number for this demo</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="expiry_date" class="block text-gray-700 text-sm mb-1">Expiration Date</label>
                                    <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        maxlength="5">
                                </div>

                                <div>
                                    <label for="cvv" class="block text-gray-700 text-sm mb-1">CVV</label>
                                    <input type="text" id="cvv" name="cvv" placeholder="123"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        maxlength="3">
                                </div>
                            </div>

                            <div>
                                <label for="card_holder" class="block text-gray-700 text-sm mb-1">Card Holder Name</label>
                                <input type="text" id="card_holder" name="card_holder" placeholder="John Smith"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Note -->
                    <div class="bg-blue-50 p-4 rounded-md mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    This is a demo checkout. No actual payment will be processed.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Submit -->
                    <div>
                        <div class="flex items-start mb-4">
                            <div class="flex items-center h-5">
                                <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="text-gray-700">
                                    I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors">
                            Pay Rp <?php echo number_format($this->bootcamp->price, 0, ',', '.'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'views/includes/footer.php';
?>