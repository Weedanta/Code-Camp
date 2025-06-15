<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Code Camp</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .progress-step.active {
            background-color: #3B82F6;
            color: white;
        }
        .progress-step.completed {
            background-color: #10B981;
            color: white;
        }
        .loading {
            display: none;
        }
        .loading.show {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-blue-600 text-white py-4">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Checkout</h1>
                <div class="flex items-center">
                    <i class="fas fa-shield-alt text-green-400 mr-2"></i>
                    <span class="text-sm">SSL Protected</span>
                </div>
            </div>
            <p class="text-blue-100 mt-2">Complete your bootcamp enrollment</p>
        </div>
    </header>

    <!-- Progress Steps -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-center space-x-8">
                <div class="flex items-center">
                    <div class="progress-step completed w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="ml-2 text-sm font-medium">Review Order</span>
                </div>
                <div class="w-16 h-1 bg-blue-600"></div>
                <div class="flex items-center">
                    <div class="progress-step active w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">
                        2
                    </div>
                    <span class="ml-2 text-sm font-medium">Payment</span>
                </div>
                <div class="w-16 h-1 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="progress-step w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-sm font-bold">
                        3
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Confirmation</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-receipt text-blue-600 mr-2"></i>
                            <h2 class="text-lg font-semibold">Order Summary</h2>
                        </div>
                        
                        <!-- Bootcamp Info -->
                        <div class="border rounded-lg p-4 mb-4">
                            <div class="flex space-x-4">
                                <img src="<?php echo !empty($this->bootcamp->image) ? 'assets/images/bootcamps/' . $this->bootcamp->image : 'assets/images/bootcamps/default.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($this->bootcamp->title); ?>" 
                                     class="w-16 h-16 object-cover rounded">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-sm"><?php echo htmlspecialchars($this->bootcamp->title); ?></h3>
                                    <p class="text-gray-600 text-xs">by <?php echo htmlspecialchars($this->bootcamp->instructor_name); ?></p>
                                    <p class="text-gray-500 text-xs">
                                        <i class="fas fa-calendar mr-1"></i>
                                        <?php echo date('d M Y', strtotime($this->bootcamp->start_date)); ?> • 
                                        <?php echo htmlspecialchars($this->bootcamp->duration); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Bootcamp Price</span>
                                <span>Rp <?php echo number_format($this->bootcamp->price, 0, ',', '.'); ?></span>
                            </div>
                            <?php if (!empty($this->bootcamp->discount_price) && $this->bootcamp->discount_price < $this->bootcamp->price): ?>
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>-Rp <?php echo number_format($this->bootcamp->price - $this->bootcamp->discount_price, 0, ',', '.'); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="flex justify-between">
                                <span>Tax (0%)</span>
                                <span>Rp 0</span>
                            </div>
                            <hr class="my-2">
                            <div class="flex justify-between font-bold text-lg text-blue-600">
                                <span>Total</span>
                                <span>Rp <?php 
                                    $final_price = !empty($this->bootcamp->discount_price) ? $this->bootcamp->discount_price : $this->bootcamp->price;
                                    echo number_format($final_price, 0, ',', '.'); 
                                ?></span>
                            </div>
                        </div>

                        <!-- What you'll get -->
                        <div class="mt-6 pt-6 border-t">
                            <h4 class="font-semibold mb-3">What you'll get:</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Lifetime access to course materials</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Certificate of completion</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Access to community forum</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Direct instructor support</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <!-- Error/Success Messages -->
                        <?php if (isset($_GET['error'])): ?>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                <div>
                                    <h4 class="font-semibold text-red-800">Payment Processing Failed</h4>
                                    <p class="text-red-600 text-sm"><?php echo htmlspecialchars($_GET['error']); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <form id="checkoutForm" action="index.php?action=process_order" method="POST">
                            <input type="hidden" name="bootcamp_id" value="<?php echo $this->bootcamp->id; ?>">
                            
                            <!-- Contact Information -->
                            <div class="mb-6">
                                <div class="flex items-center mb-4">
                                    <i class="fas fa-user text-blue-600 mr-2"></i>
                                    <h3 class="text-lg font-semibold">Contact Information</h3>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Full Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="full_name" 
                                               value="<?php echo htmlspecialchars($_SESSION['name']); ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                               readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Email Address <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" name="email_address" 
                                               value="<?php echo htmlspecialchars($_SESSION['alamat_email']); ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                               readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Phone Number
                                        </label>
                                        <input type="tel" name="phone_number" 
                                               value="<?php echo htmlspecialchars($_SESSION['no_telepon'] ?? ''); ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="mb-6">
                                <div class="flex items-center mb-4">
                                    <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                                    <h3 class="text-lg font-semibold">Payment Method</h3>
                                </div>

                                <div class="space-y-3">
                                    <!-- Credit/Debit Card -->
                                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="radio" name="payment_method" value="credit_card" checked 
                                               class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center">
                                                <span class="font-medium">Credit/Debit Card</span>
                                                <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Recommended</span>
                                                <div class="ml-auto flex space-x-2">
                                                    <i class="fab fa-cc-visa text-blue-600"></i>
                                                    <i class="fab fa-cc-mastercard text-red-500"></i>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-500">Secure payment with SSL encryption</p>
                                        </div>
                                    </label>

                                    <!-- Bank Transfer -->
                                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="radio" name="payment_method" value="bank_transfer" 
                                               class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center">
                                                <span class="font-medium">Bank Transfer</span>
                                                <div class="ml-auto flex space-x-2">
                                                    <span class="text-xs text-blue-600">BCA</span>
                                                    <span class="text-xs text-blue-600">Mandiri</span>
                                                    <span class="text-xs text-blue-600">BNI</span>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-500">Transfer to our bank account</p>
                                        </div>
                                    </label>

                                    <!-- Digital Wallet -->
                                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="radio" name="payment_method" value="digital_wallet" 
                                               class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center">
                                                <span class="font-medium">Digital Wallet</span>
                                                <div class="ml-auto flex space-x-2">
                                                    <span class="text-xs text-green-600">GoPay</span>
                                                    <span class="text-xs text-blue-600">OVO</span>
                                                    <span class="text-xs text-red-600">DANA</span>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-500">Pay with your digital wallet</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="mb-6">
                                <label class="flex items-start">
                                    <input type="checkbox" id="terms" required 
                                           class="w-4 h-4 text-blue-600 focus:ring-blue-500 mt-1">
                                    <span class="ml-2 text-sm text-gray-600">
                                        I agree to the <a href="#" class="text-blue-600 hover:underline">Terms and Conditions</a> 
                                        and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                                    </span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex space-x-4">
                                <button type="button" onclick="history.back()" 
                                        class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-md hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Back
                                </button>
                                <button type="submit" id="submitBtn"
                                        class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors font-medium">
                                    <span id="submitText">
                                        <i class="fas fa-lock mr-2"></i>
                                        Complete Payment
                                    </span>
                                    <span id="loadingText" class="loading">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>
                                        Processing...
                                    </span>
                                </button>
                            </div>

                            <!-- Security Notice -->
                            <div class="mt-4 text-center">
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                                    Your payment information is encrypted and secure
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingText = document.getElementById('loadingText');
            const termsCheckbox = document.getElementById('terms');
            
            // Check if terms are accepted
            if (!termsCheckbox.checked) {
                e.preventDefault();
                alert('Please accept the Terms and Conditions to continue.');
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitText.style.display = 'none';
            loadingText.classList.add('show');
            
            // Optional: Add a small delay to show the loading state
            setTimeout(() => {
                // Form will submit naturally
            }, 100);
        });

        // Payment method selection handling
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // You can add specific handling for different payment methods here
                console.log('Payment method selected:', this.value);
            });
        });
    </script>
</body>
</html>