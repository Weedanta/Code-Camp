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
<div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white py-8">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2">Checkout</h1>
                <p class="text-blue-200">Complete your bootcamp enrollment</p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    <div class="text-sm text-blue-200">Secure Checkout</div>
                    <div class="flex items-center mt-1">
                        <svg class="w-4 h-4 text-green-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-white">SSL Protected</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Checkout Steps -->
<div class="bg-gray-50 py-6">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-center">
            <div class="flex items-center space-x-4 md:space-x-8">
                <!-- Step 1: Review Order -->
                <div class="flex items-center">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shadow-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm mt-2 font-medium text-blue-600">Review Order</span>
                    </div>
                    <div class="h-1 w-12 md:w-16 bg-blue-600 mx-2"></div>
                </div>

                <!-- Step 2: Payment -->
                <div class="flex items-center">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shadow-lg">2</div>
                        <span class="text-sm mt-2 font-medium text-blue-600">Payment</span>
                    </div>
                    <div class="h-1 w-12 md:w-16 bg-gray-300 mx-2"></div>
                </div>

                <!-- Step 3: Confirmation -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-500 font-bold">3</div>
                    <span class="text-sm mt-2 font-medium text-gray-500">Confirmation</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Error Messages -->
<?php if (isset($_GET['error'])): ?>
    <div class="container mx-auto px-4 pt-6">
        <?php if ($_GET['error'] == 'payment_failed'): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-red-800 font-medium">Payment Processing Failed</h3>
                        <p class="text-red-700 text-sm mt-1">Please try again or contact support if the problem persists.</p>
                    </div>
                </div>
            </div>
        <?php elseif ($_GET['error'] == 'invalid_bootcamp'): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-r-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-yellow-800 font-medium">Invalid Bootcamp</h3>
                        <p class="text-yellow-700 text-sm mt-1">The selected bootcamp is not available.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Debug Info (only show if debug parameter is present) -->
<?php if (isset($_GET['debug'])): ?>
    <div class="container mx-auto px-4">
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            <strong class="font-bold">Debug Information:</strong>
            <div class="mt-2 text-sm">
                <p><strong>Bootcamp ID:</strong> <?php echo htmlspecialchars($this->bootcamp->id ?? 'Not set'); ?></p>
                <p><strong>Bootcamp Title:</strong> <?php echo htmlspecialchars($this->bootcamp->title ?? 'Not found'); ?></p>
                <p><strong>Price:</strong> Rp <?php echo isset($this->bootcamp->price) ? number_format($this->bootcamp->price, 0, ',', '.') : '0'; ?></p>
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="lg:flex lg:space-x-8">
            <!-- Left Column: Order Summary -->
            <div class="lg:w-1/2 mb-8 lg:mb-0">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                            Order Summary
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <!-- Bootcamp Details -->
                        <div class="flex items-start space-x-4 pb-6 border-b border-gray-200">
                            <?php if (!empty($this->bootcamp->image)): ?>
                                <img src="assets/images/bootcamps/<?php echo htmlspecialchars($this->bootcamp->image); ?>"
                                     alt="<?php echo htmlspecialchars($this->bootcamp->title); ?>"
                                     class="w-24 h-16 object-cover rounded-lg shadow-sm">
                            <?php else: ?>
                                <div class="w-24 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            <?php endif; ?>

                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 text-lg leading-tight">
                                    <?php echo htmlspecialchars($this->bootcamp->title); ?>
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    by <span class="font-medium"><?php echo htmlspecialchars($this->bootcamp->instructor_name); ?></span>
                                </p>
                                <div class="flex items-center space-x-4 text-sm text-gray-500 mt-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        <?php echo date('d M Y', strtotime($this->bootcamp->start_date)); ?>
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <?php echo htmlspecialchars($this->bootcamp->duration); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="space-y-3 mt-6">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Bootcamp Price</span>
                                <span class="text-gray-900 font-medium">
                                    Rp <?php echo number_format($this->bootcamp->price, 0, ',', '.'); ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($this->bootcamp->discount_price) && $this->bootcamp->discount_price < $this->bootcamp->price): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-green-600">Discount</span>
                                    <span class="text-green-600 font-medium">
                                        -Rp <?php echo number_format($this->bootcamp->price - $this->bootcamp->discount_price, 0, ',', '.'); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Tax (0%)</span>
                                <span class="text-gray-900 font-medium">Rp 0</span>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-blue-600">
                                        <?php 
                                        $final_price = !empty($this->bootcamp->discount_price) && $this->bootcamp->discount_price < $this->bootcamp->price 
                                                     ? $this->bootcamp->discount_price 
                                                     : $this->bootcamp->price;
                                        echo 'Rp ' . number_format($final_price, 0, ',', '.');
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Features/Benefits -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-3">What you'll get:</h4>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Lifetime access to course materials
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Certificate of completion
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Direct access to instructor
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    30-day money-back guarantee
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Payment Information -->
            <div class="lg:w-1/2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            Payment Information
                        </h2>
                    </div>

                    <form method="POST" action="index.php?action=process_order" id="checkoutForm" class="p-6">
                        <!-- Hidden Fields -->
                        <input type="hidden" name="bootcamp_id" value="<?php echo htmlspecialchars($this->bootcamp->id); ?>">
                        
                        <!-- Contact Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="full_name" name="full_name" 
                                           value="<?php echo htmlspecialchars($user_name); ?>" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($email); ?>" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number
                                    </label>
                                    <input type="tel" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($phone); ?>"
                                           placeholder="e.g., +62 812 3456 7890"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h3>
                            <div class="space-y-3">
                                <!-- Credit Card -->
                                <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                                    <input type="radio" name="payment_method" value="credit_card" checked 
                                           class="sr-only peer">
                                    <div class="w-4 h-4 border-2 border-gray-300 rounded-full peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:ring-2 peer-checked:ring-blue-200 transition-all"></div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <span class="font-medium text-gray-900">Credit/Debit Card</span>
                                                <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Recommended</span>
                                            </div>
                                            <div class="flex space-x-2">
                                                <img src="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/flags/4x3/us.svg" alt="Visa" class="h-6 w-8 rounded">
                                                <img src="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/flags/4x3/us.svg" alt="Mastercard" class="h-6 w-8 rounded">
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Secure payment with SSL encryption</p>
                                    </div>
                                </label>

                                <!-- Bank Transfer -->
                                <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                                    <input type="radio" name="payment_method" value="bank_transfer" 
                                           class="sr-only peer">
                                    <div class="w-4 h-4 border-2 border-gray-300 rounded-full peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:ring-2 peer-checked:ring-blue-200 transition-all"></div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-gray-900">Bank Transfer</span>
                                            <div class="flex space-x-2">
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">BCA</span>
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Mandiri</span>
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">BNI</span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Manual transfer via internet banking</p>
                                    </div>
                                </label>

                                <!-- E-Wallet -->
                                <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                                    <input type="radio" name="payment_method" value="e_wallet" 
                                           class="sr-only peer">
                                    <div class="w-4 h-4 border-2 border-gray-300 rounded-full peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:ring-2 peer-checked:ring-blue-200 transition-all"></div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-gray-900">E-Wallet</span>
                                            <div class="flex space-x-2">
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">GoPay</span>
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">OVO</span>
                                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">DANA</span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Quick payment via mobile wallet</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-8">
                            <div class="flex items-start">
                                <input type="checkbox" id="terms" name="terms" required
                                       class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="terms" class="ml-3 text-sm text-gray-600">
                                    I agree to the 
                                    <a href="#" class="text-blue-600 hover:text-blue-800 underline">Terms of Service</a> 
                                    and 
                                    <a href="#" class="text-blue-600 hover:text-blue-800 underline">Privacy Policy</a>
                                    <span class="text-red-500">*</span>
                                </label>
                            </div>
                            <div class="flex items-start mt-3">
                                <input type="checkbox" id="marketing" name="marketing"
                                       class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="marketing" class="ml-3 text-sm text-gray-600">
                                    I want to receive updates about new bootcamps and special offers
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="space-y-4">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold py-4 px-6 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                                    id="submitBtn">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Complete Secure Payment
                                </span>
                            </button>
                            
                            <div class="text-center">
                                <p class="text-sm text-gray-500">
                                    <svg class="w-4 h-4 inline mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Your payment is secured with SSL encryption
                                </p>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Money Back Guarantee -->
                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-green-800">30-Day Money-Back Guarantee</h4>
                            <p class="text-sm text-green-700">Not satisfied? Get a full refund within 30 days.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-auto text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Processing Payment</h3>
        <p class="text-gray-600">Please wait while we process your payment...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    const submitBtn = document.getElementById('submitBtn');
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const terms = document.getElementById('terms');
        const fullName = document.getElementById('full_name');
        const email = document.getElementById('email');
        
        // Check required fields
        if (!terms.checked) {
            e.preventDefault();
            alert('Please accept the Terms of Service and Privacy Policy');
            return false;
        }
        
        if (!fullName.value.trim() || !email.value.trim()) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }
        
        // Show loading overlay
        loadingOverlay.classList.remove('hidden');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            </span>
        `;
    });
    
    // Radio button styling
    const radioButtons = document.querySelectorAll('input[type="radio"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove active styling from all labels
            document.querySelectorAll('label').forEach(label => {
                if (label.querySelector('input[type="radio"]')) {
                    label.classList.remove('border-blue-500', 'bg-blue-50');
                    label.classList.add('border-gray-200');
                }
            });
            
            // Add active styling to selected label
            const selectedLabel = this.closest('label');
            selectedLabel.classList.remove('border-gray-200');
            selectedLabel.classList.add('border-blue-500', 'bg-blue-50');
        });
    });
    
    // Initialize first radio button as selected
    const firstRadio = document.querySelector('input[type="radio"][checked]');
    if (firstRadio) {
        const firstLabel = firstRadio.closest('label');
        firstLabel.classList.remove('border-gray-200');
        firstLabel.classList.add('border-blue-500', 'bg-blue-50');
    }
});
</script>

<?php
// Include footer
include_once 'views/includes/footer.php';
?>