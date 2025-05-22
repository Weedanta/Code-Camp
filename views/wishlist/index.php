<?php
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit();
}

$user_name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];

// Pastikan variabel terdefinisi untuk menghindari error
$wishlist_items = isset($wishlist_items) ? $wishlist_items : [];
$total_pages = isset($total_pages) ? $total_pages : 1;
$page = isset($page) ? $page : 1;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - Code Camp</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome dengan integrity check -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="icon" href="assets/images/logo/logo_mobile.png" type="image/x-icon">
    
    <style>
        .wishlist-button {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 10;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .wishlist-button:hover {
            background: #f3f4f6;
            transform: scale(1.05);
        }
        
        .wishlist-button .heart-icon {
            color: #ef4444;
            font-size: 18px;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
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
                    <a href="index.php?action=wishlist" class="text-blue-600 font-medium">Wishlist</a>
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
                                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
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
                    <a href="index.php?action=my_bootcamps" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">My Bootcamps</a>
                    <a href="index.php?action=wishlist" class="block px-3 py-2 rounded-md text-blue-600 bg-blue-50 font-medium">Wishlist</a>
                    
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
            <h1 class="text-3xl font-bold">My Wishlist</h1>
            <p class="mt-2">Bootcamps you've saved for later</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <?php if (empty($wishlist_items)): ?>
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="text-gray-500 mb-4">
                    <i class="far fa-heart text-6xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Your wishlist is empty</h2>
                <p class="text-gray-600 mb-6">Save bootcamps for later by clicking the heart icon on bootcamp cards.</p>
                <a href="index.php?action=bootcamps" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors inline-block">
                    Browse Bootcamps
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Saved Bootcamps (<?php echo count($wishlist_items); ?>)</h2>
                    <a href="index.php?action=bootcamps" class="text-blue-600 hover:underline font-medium">
                        <i class="fas fa-plus mr-1"></i> Add More
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="wishlistContainer">
                    <?php foreach ($wishlist_items as $item): ?>
                        <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 relative" data-wishlist-id="<?php echo isset($item['id']) ? htmlspecialchars($item['id']) : htmlspecialchars($item['bootcamp_id']); ?>">
                            <!-- Remove from wishlist button -->
                            <button class="wishlist-button remove-wishlist-btn" 
                                    data-bootcamp-id="<?php echo isset($item['bootcamp_id']) ? htmlspecialchars($item['bootcamp_id']) : htmlspecialchars($item['id']); ?>"
                                    title="Remove from wishlist">
                                <i class="fas fa-heart heart-icon"></i>
                            </button>

                            <!-- Bootcamp Image -->
                            <img src="assets/images/ngoding.jpg" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                 class="w-full h-48 object-cover"
                                 onerror="this.src='assets/images/ngoding.jpg'">

                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </h3>

                                <p class="text-gray-600 mb-4 line-clamp-2 h-12">
                                    <?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?>
                                </p>

                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                        <span class="text-gray-600 text-xs font-medium">
                                            <?php echo strtoupper(substr($item['instructor_name'], 0, 1)); ?>
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-700">
                                        <?php echo htmlspecialchars($item['instructor_name']); ?>
                                    </span>
                                </div>

                                <div class="flex justify-between text-sm text-gray-500 mb-4">
                                    <div>
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <?php echo date('d M Y', strtotime($item['start_date'])); ?>
                                    </div>
                                    <div>
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <?php echo htmlspecialchars($item['duration']); ?>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <?php if (isset($item['discount_price']) && !empty($item['discount_price']) && $item['discount_price'] > $item['price']): ?>
                                            <span class="text-gray-500 line-through text-sm">
                                                Rp <?php echo number_format($item['discount_price'], 0, ',', '.'); ?>
                                            </span><br>
                                        <?php endif; ?>
                                        <span class="text-blue-600 font-bold">
                                            Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                        </span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="index.php?action=bootcamp_detail&id=<?php echo isset($item['bootcamp_id']) ? htmlspecialchars($item['bootcamp_id']) : htmlspecialchars($item['id']); ?>" 
                                            class="px-3 py-1 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition-colors">
                                            Detail
                                        </a>
                                        <a href="index.php?action=checkout&id=<?php echo isset($item['bootcamp_id']) ? htmlspecialchars($item['bootcamp_id']) : htmlspecialchars($item['id']); ?>" 
                                            class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                            Enroll
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination if needed -->
                <?php if ($total_pages > 1): ?>
                    <div class="flex justify-center mt-8">
                        <?php if ($page > 1): ?>
                            <a href="index.php?action=wishlist&page=<?php echo $page - 1; ?>" 
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
                                <a href="index.php?action=wishlist&page=<?php echo $i; ?>" 
                                   class="w-10 h-10 mx-1 flex items-center justify-center rounded-full text-gray-700 hover:bg-blue-50">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="index.php?action=wishlist&page=<?php echo $page + 1; ?>" 
                               class="w-10 h-10 mx-1 flex items-center justify-center rounded-full border border-blue-600 text-blue-600 hover:bg-blue-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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
                    <h3 class="text-lg font-bold mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php?action=bootcamp_category&id=1" class="text-blue-200 hover:text-white">Web Dev</a></li>
                        <li><a href="index.php?action=bootcamp_category&id=2" class="text-blue-200 hover:text-white">Data Science</a></li>
                        <li><a href="index.php?action=bootcamp_category&id=3" class="text-blue-200 hover:text-white">UI/UX Design</a></li>
                        <li><a href="index.php?action=bootcamp_category&id=4" class="text-blue-200 hover:text-white">Mobile Dev</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-4">Information</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-blue-200 hover:text-white">Home</a></li>
                        <li><a href="index.php?action=bootcamps" class="text-blue-200 hover:text-white">Bootcamps</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">FAQ</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Contact Us</a></li>
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
        console.log('Wishlist page loaded');

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing wishlist functionality');

            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    const mobileMenu = document.getElementById('mobile-menu');
                    if (mobileMenu) {
                        mobileMenu.classList.toggle('hidden');
                    }
                });
            }

            // Profile dropdown toggle
            const profileButton = document.getElementById('profileButton');
            if (profileButton) {
                profileButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const dropdown = document.getElementById('profileDropdown');
                    if (dropdown) {
                        dropdown.classList.toggle('hidden');
                    }
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                const profileButton = document.getElementById('profileButton');
                const profileDropdown = document.getElementById('profileDropdown');
                
                if (profileButton && profileDropdown) {
                    if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                }
            });

            // Remove from wishlist functionality
            const removeButtons = document.querySelectorAll('.remove-wishlist-btn');
            console.log('Found', removeButtons.length, 'remove buttons');

            removeButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('Remove button clicked');
                    
                    const bootcampId = this.getAttribute('data-bootcamp-id');
                    const wishlistCard = this.closest('[data-wishlist-id]');
                    
                    if (!bootcampId) {
                        console.error('No bootcamp ID found');
                        alert('Error: Bootcamp ID not found');
                        return;
                    }
                    
                    console.log('Removing bootcamp ID:', bootcampId);
                    
                    // Disable button and add loading state
                    this.disabled = true;
                    this.classList.add('loading');
                    
                    // Create form data
                    const formData = new FormData();
                    formData.append('bootcamp_id', bootcampId);
                    
                    // Send AJAX request
                    fetch('index.php?action=remove_from_wishlist', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        return response.text().then(text => {
                            console.log('Raw response:', text);
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('JSON parse error:', e);
                                console.error('Response text:', text);
                                throw new Error('Invalid JSON response');
                            }
                        });
                    })
                    .then(data => {
                        console.log('Parsed data:', data);
                        
                        if (data.success) {
                            console.log('Remove successful');
                            
                            if (wishlistCard) {
                                // Add animation
                                wishlistCard.style.transition = 'all 0.3s ease';
                                wishlistCard.style.opacity = '0';
                                wishlistCard.style.transform = 'scale(0.9)';
                                
                                setTimeout(() => {
                                    wishlistCard.remove();
                                    
                                    // Check if there are no more items
                                    const remainingItems = document.querySelectorAll('.remove-wishlist-btn').length;
                                    console.log('Remaining items:', remainingItems);
                                    
                                    if (remainingItems === 0) {
                                        console.log('No more items, reloading page');
                                        window.location.reload();
                                    }
                                }, 300);
                            }
                        } else {
                            console.error('Remove failed:', data.message);
                            alert(data.message || 'Failed to remove from wishlist');
                            
                            // Re-enable button
                            this.disabled = false;
                            this.classList.remove('loading');
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        alert('An error occurred while removing from wishlist. Please try again.');
                        
                        // Re-enable button
                        this.disabled = false;
                        this.classList.remove('loading');
                    });
                });
            });
        });
    </script>
</body>

</html>