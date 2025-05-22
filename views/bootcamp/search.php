<?php
// Cek session
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['name'] : '';
$user_id = $is_logged_in ? $_SESSION['user_id'] : '';

// Get the search keyword from URL
$keyword = isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results: <?php echo $keyword; ?> - Code Camp</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="icon" href="../../../assets/images/logo/logo_mobile.png" type="image/x-icon">
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
                    <a href="index.php?action=bootcamps" class="text-blue-600 font-medium">Bootcamps</a>

                    <?php if ($is_logged_in): ?>
                        <a href="index.php?action=my_bootcamps" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">My Bootcamps</a>
                        <a href="index.php?action=wishlist" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">Wishlist</a>
                    <?php endif; ?>
                </nav>

                <!-- User Account / Login Buttons -->
                <div class="flex items-center space-x-3">
                    <?php if ($is_logged_in): ?>
                        <!-- User Profile Icon When Logged In -->
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
                    <?php else: ?>
                        <!-- Login/Signup Buttons When Not Logged In -->
                        <a href="index.php?action=login" class="px-4 py-2 rounded-md border border-blue-600 text-blue-600 hover:bg-blue-50 transition-colors duration-300">Login</a>
                        <a href="index.php?action=signup" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-300">Sign Up</a>
                    <?php endif; ?>

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
                    <a href="index.php?action=bootcamps" class="block px-3 py-2 rounded-md text-blue-600 bg-blue-50 font-medium">Bootcamps</a>


                    <?php if ($is_logged_in): ?>
                        <a href="index.php?action=my_bootcamps" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">My Bootcamps</a>
                        <a href="index.php?action=wishlist" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">Wishlist</a>
                        <div class="border-t border-gray-200 my-2 pt-2">
                            <a href="views/auth/dashboard/dashboard.php" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">My Profile</a>
                            <a href="index.php?action=my_orders" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">My Orders</a>
                            <a href="index.php?action=logout" class="block px-3 py-2 rounded-md text-red-600 hover:bg-red-50">Logout</a>
                        </div>
                    <?php else: ?>
                        <div class="border-t border-gray-200 my-2 pt-2">
                            <a href="index.php?action=login" class="block px-3 py-2 rounded-md text-blue-600 hover:bg-blue-50">Login</a>
                            <a href="index.php?action=signup" class="block px-3 py-2 rounded-md text-blue-600 hover:bg-blue-50">Sign Up</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <div class="bg-blue-900 text-white py-6">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold">Search Results: "<?php echo $keyword; ?>"</h1>
            <p class="mt-2">Showing bootcamps matching your search</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white shadow-md py-4">
        <div class="container mx-auto px-4">
            <form action="index.php" method="GET" class="flex flex-col md:flex-row gap-2">
                <input type="hidden" name="action" value="bootcamp_search">
                <input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="Search bootcamps..."
                    class="flex-grow px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar Filters -->
            <div class="md:w-1/4">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Categories</h2>
                    <div class="space-y-2">
                        <a href="index.php?action=bootcamps" class="block text-gray-700 hover:text-blue-600 transition-colors">All Bootcamps</a>
                        <?php foreach ($categories as $category): ?>
                            <a href="index.php?action=bootcamp_category&id=<?php echo $category['id']; ?>"
                                class="block text-gray-700 hover:text-blue-600 transition-colors">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Bootcamp Listings -->
            <div class="md:w-3/4">
                <?php if (empty($bootcamps)): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <p class="text-gray-600">No bootcamps found matching "<?php echo $keyword; ?>". Try different keywords or browse our categories.</p>
                        <a href="index.php?action=bootcamps" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors">
                            Browse All Bootcamps
                        </a>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <p class="text-gray-600">Found <span class="font-medium"><?php echo count($bootcamps); ?></span> bootcamps matching "<?php echo $keyword; ?>"</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($bootcamps as $bootcamp): ?>
                            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                                <?php if (!empty($bootcamp['image'])): ?>
                                    <!-- Ubah gambar bootcamp ke ngoding.jpg -->
                                    <img src="assets/images/ngoding.jpg"
                                        alt="<?php echo htmlspecialchars($item['title']); ?>"
                                        class="w-full h-48 object-cover">
                                <?php else: ?>
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">No image available</span>
                                    </div>
                                <?php endif; ?>

                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-xl font-bold text-gray-800">
                                            <?php echo htmlspecialchars($bootcamp['title']); ?>
                                        </h3>
                                    </div>

                                    <p class="text-gray-600 mb-4 line-clamp-2 h-12">
                                        <?php echo htmlspecialchars(substr($bootcamp['description'], 0, 60)) . '...'; ?>
                                    </p>

                                    <div class="flex items-center mb-4">
                                        <?php if (!empty($bootcamp['instructor_photo'])): ?>
                                            <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                                <span class="text-gray-600 text-xs">
                                                    <?php echo substr($bootcamp['instructor_name'], 0, 1); ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                                <span class="text-gray-600 text-xs">
                                                    <?php echo substr($bootcamp['instructor_name'], 0, 1); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        <span class="text-sm text-gray-700">
                                            <?php echo htmlspecialchars($bootcamp['instructor_name']); ?>
                                        </span>
                                    </div>

                                    <div class="flex justify-between text-sm text-gray-500 mb-4">
                                        <div>
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <?php echo date('d M Y', strtotime($bootcamp['start_date'])); ?>
                                        </div>
                                        <div>
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <?php echo htmlspecialchars($bootcamp['duration']); ?>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <?php if (!empty($bootcamp['discount_price']) && $bootcamp['discount_price'] > $bootcamp['price']): ?>
                                                <span class="text-gray-500 line-through text-sm">
                                                    Rp <?php echo number_format($bootcamp['discount_price'], 0, ',', '.'); ?>
                                                </span><br>
                                            <?php endif; ?>
                                            <span class="text-blue-600 font-bold">
                                                Rp <?php echo number_format($bootcamp['price'], 0, ',', '.'); ?>
                                            </span>
                                        </div>
                                        <a href="index.php?action=bootcamp_detail&id=<?php echo $bootcamp['id']; ?>"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                            Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="flex justify-center mt-8">
                            <?php if ($page > 1): ?>
                                <a href="index.php?action=bootcamp_search&keyword=<?php echo urlencode($keyword); ?>&page=<?php echo $page - 1; ?>"
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
                                    <a href="index.php?action=bootcamp_search&keyword=<?php echo urlencode($keyword); ?>&page=<?php echo $i; ?>"
                                        class="w-10 h-10 mx-1 flex items-center justify-center rounded-full text-gray-700 hover:bg-blue-50">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="index.php?action=bootcamp_search&keyword=<?php echo urlencode($keyword); ?>&page=<?php echo $page + 1; ?>"
                                    class="w-10 h-10 mx-1 flex items-center justify-center rounded-full border border-blue-600 text-blue-600 hover:bg-blue-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
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
                    <h3 class="text-lg font-bold mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <?php foreach (array_slice($categories, 0, 5) as $category): ?>
                            <li>
                                <a href="index.php?action=bootcamp_category&id=<?php echo $category['id']; ?>" class="text-blue-200 hover:text-white">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
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
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Profile dropdown toggle (if logged in)
        <?php if ($is_logged_in): ?>
            document.getElementById('profileButton').addEventListener('click', function(e) {
                e.stopPropagation();
                const dropdown = document.getElementById('profileDropdown');
                dropdown.classList.toggle('hidden');
            });

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
        <?php endif; ?>
    </script>
</body>

</html>