<?php
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit();
}

$user_name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful - Code Camp</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="icon" href="assets/images/logo/logo_mobile.png" type="image/x-icon">
</head>

<body class="bg-gray-50 font-sans">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-1">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center">
                        <img src="assets/images/logo.png" alt="Logo" class="h-16 hidden md:block">
                        <img src="assets/images/logo/logo_mobile.png" alt="Logo" class="md:hidden h-12">
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">Home</a>
                    <a href="index.php?action=bootcamps" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">Bootcamps</a>
                   
                    <a href="index.php?action=my_bootcamps" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">My Bootcamps</a>
                    <a href="index.php?action=wishlist" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">Wishlist</a>
                </nav>

                <!-- User Account -->
                <div class="flex items-center space-x-3">
                    <!-- User Profile Icon -->
                    <div class="relative">
                        <button id="profileButton" class="flex items-center focus:outline-none">
                            <?php if (file_exists("assets/images/users/{$user_id}.jpg")): ?>
                                <img src="assets/images/users/<?php echo $user_id; ?>.jpg" alt="Profile" class="w-10 h-10 rounded-full border-2 border-blue-100">
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white border-2 border-blue-100">
                                    <?php echo substr($user_name, 0, 1); ?>
                                </div>
                            <?php endif; ?>
                        </button>
                        <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden z-10">
                            <a href="views/auth/dashboard/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                            <a href="index.php?action=my_orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                            <a href="index.php?action=logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>

                    <!-- Mobile Menu Toggle Button -->
                    <button id="mobile-menu-button" class="md:hidden flex items-center p-2 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden w-full mt-2">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-white rounded-md shadow-md">
                    <a href="index.php" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">Home</a>
                    <a href="index.php?action=bootcamps" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">Bootcamps</a>
                    <a href="views/about/index.php" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">About Us</a>
                    <a href="index.php?action=my_bootcamps" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">My Bootcamps</a>
                    <a href="index.php?action=wishlist" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">Wishlist</a>
                    
                    <div class="border-t border-gray-200 my-2 pt-2">
                        <a href="views/auth/dashboard/dashboard.php" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">My Profile</a>
                        <a href="index.php?action=my_orders" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">My Orders</a>
                        <a href="index.php?action=logout" class="block px-3 py-2 rounded-md text-red-600 hover:bg-red-50">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

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
                
                <div class="flex justify-center space-x-4">
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
                        switch($this->order->payment_method) {
                            case 'credit_card':
                                echo 'Credit / Debit Card';
                                break;
                            case 'bank_transfer':
                                echo 'Bank Transfer';
                                break;
                            default:
                                echo ucfirst(str_replace('_', ' ', $this->order->payment_method));
                        }
                        ?>
                    </div>
                </div>
                
                <div class="border-b pb-4 mb-4">
                    <div class="text-sm text-gray-500 mb-3">Items</div>
                    
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
                </div>
                
                <div class="flex justify-between font-bold text-gray-800">
                    <span>Total</span>
                    <span>Rp <?php echo number_format($this->order->total_amount, 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="assets/images/logo.png" alt="Logo" class="h-16 hidden md:block">
                        <img src="assets/images/logo/logo_mobile.png" alt="Logo" class="md:hidden h-12">
                    </div>
                    <p class="text-blue-200 mb-4">Find the best bootcamps to develop your skills and accelerate your career in the tech world.</p>
                    <p class="text-blue-200">123 Education St, Malang<br>East Java, Indonesia, 65145</p>
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-4">Information</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-blue-200 hover:text-white">Home</a></li>
                        <li><a href="index.php?action=bootcamps" class="text-blue-200 hover:text-white">Bootcamps</a></li>
                        <li><a href="views/about/index.php" class="text-blue-200 hover:text-white">About Us</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">FAQ</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Contact Us</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-4">Account</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php?action=my_bootcamps" class="text-blue-200 hover:text-white">My Bootcamps</a></li>
                        <li><a href="index.php?action=wishlist" class="text-blue-200 hover:text-white">Wishlist</a></li>
                        <li><a href="index.php?action=my_orders" class="text-blue-200 hover:text-white">Order History</a></li>
                        <li><a href="views/auth/dashboard/dashboard.php" class="text-blue-200 hover:text-white">My Profile</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-blue-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-blue-200 text-sm">&copy; 2025 Code Camp. All Rights Reserved.</p>

                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-blue-200 hover:text-white">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-blue-200 hover:text-white">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-blue-200 hover:text-white">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-blue-200 hover:text-white">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Profile dropdown toggle
        document.getElementById('profileButton').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const profileButton = document.getElementById('profileButton');
            const profileDropdown = document.getElementById('profileDropdown');
            
            if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });
    </script>
</body>

</html>