<?php
// Cek session
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['name'] : '';
$user_id = $is_logged_in ? $_SESSION['user_id'] : '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($this->bootcamp->title); ?> - Code Camp</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="icon" href="../../../assets/images/logo/logo_mobile.png" type="image/x-icon">
    <style>
        /* Star rating styles */
        .star-rating {
            display: flex;
            align-items: center;
            font-size: 24px;
        }
        .star-rating .fa-star {
            color: #D1D5DB; /* gray-300 */
            cursor: pointer;
        }
        .star-rating .fa-star.active {
            color: #FBBF24; /* yellow-400 */
        }
        .review-stars .fa-star {
            color: #D1D5DB;
            font-size: 16px;
        }
        .review-stars .fa-star.active {
            color: #FBBF24;
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
                    <a href="index.php?action=bootcamps" class="text-blue-600 font-medium">Bootcamps</a>
                    <a href="views/about/index.php" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">About Us</a>
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
                    <a href="views/about/index.php" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">About Us</a>
                    
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

    <!-- Bootcamp Detail Content -->
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
                        <a href="index.php?action=bootcamps" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">Bootcamps</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="index.php?action=bootcamp_category&id=<?php echo $this->bootcamp->category_id; ?>" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">
                            <?php echo htmlspecialchars($this->bootcamp->category_name); ?>
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2 font-medium truncate">
                            <?php echo htmlspecialchars($this->bootcamp->title); ?>
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        <?php if (isset($_GET['message']) && $_GET['message'] == 'already_enrolled'): ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">You are already enrolled in this bootcamp. Go to <a href="index.php?action=my_bootcamps" class="font-medium underline">My Bootcamps</a> to access it.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Bootcamp Detail Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Bootcamp Header with Image -->
            <div class="relative">
                <?php if (!empty($this->bootcamp->image)): ?>
                    <img src="assets/images/bootcamps/<?php echo htmlspecialchars($this->bootcamp->image); ?>" 
                         alt="<?php echo htmlspecialchars($this->bootcamp->title); ?>" 
                         class="w-full h-64 md:h-80 object-cover">
                <?php else: ?>
                    <div class="w-full h-64 md:h-80 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-500">No image available</span>
                    </div>
                <?php endif; ?>
                
                <!-- Category Label -->
                <div class="absolute top-4 left-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                    <?php echo htmlspecialchars($this->bootcamp->category_name); ?>
                </div>

                <?php if ($is_logged_in): ?>
                    <!-- Wishlist Button -->
                    <button id="wishlistButton" data-bootcamp-id="<?php echo $this->bootcamp->id; ?>" 
                            class="absolute top-4 right-4 bg-white p-2 rounded-full shadow-md hover:bg-gray-100 transition-colors focus:outline-none">
                        <i class="fa<?php echo $in_wishlist ? 's' : 'r'; ?> fa-heart text-red-500 text-xl"></i>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Bootcamp Content -->
            <div class="p-6">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($this->bootcamp->title); ?></h1>
                    
                    <!-- Rating Summary -->
                    <div class="flex items-center mt-2">
                        <div class="review-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo ($i <= $avg_rating) ? 'active' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="ml-2 text-gray-600"><?php echo number_format($avg_rating, 1); ?> (<?php echo $review_count; ?> reviews)</span>
                    </div>
                </div>

                <!-- Price and Enrollment Button -->
                <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
                    <div class="mb-4 md:mb-0">
                        <?php if (!empty($this->bootcamp->discount_price) && $this->bootcamp->discount_price > $this->bootcamp->price): ?>
                            <span class="text-gray-500 line-through text-lg">
                                Rp <?php echo number_format($this->bootcamp->discount_price, 0, ',', '.'); ?>
                            </span><br>
                        <?php endif; ?>
                        <span class="text-blue-600 font-bold text-2xl">
                            Rp <?php echo number_format($this->bootcamp->price, 0, ',', '.'); ?>
                        </span>
                    </div>

                    <?php if ($is_logged_in): ?>
                        <?php if ($user_enrolled): ?>
                            <button disabled class="px-6 py-3 bg-green-500 text-white font-medium rounded-md cursor-default">
                                <i class="fas fa-check-circle mr-2"></i> Enrolled
                            </button>
                        <?php else: ?>
                            <a href="index.php?action=checkout&id=<?php echo $this->bootcamp->id; ?>" 
                               class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors text-center">
                                <i class="fas fa-shopping-cart mr-2"></i> Enroll Now
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="index.php?action=login" 
                           class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors text-center">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login to Enroll
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Bootcamp Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Bootcamp Details</h2>
                        
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="w-8 flex-shrink-0 text-blue-600">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div>
                                    <span class="font-medium">Start Date:</span> 
                                    <?php echo date('d F Y', strtotime($this->bootcamp->start_date)); ?>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="w-8 flex-shrink-0 text-blue-600">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <span class="font-medium">Duration:</span> 
                                    <?php echo htmlspecialchars($this->bootcamp->duration); ?>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="w-8 flex-shrink-0 text-blue-600">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div>
                                    <span class="font-medium">Category:</span> 
                                    <?php echo htmlspecialchars($this->bootcamp->category_name); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Instructor</h2>
                        
                        <div class="flex items-center">
                            <?php if (!empty($this->bootcamp->instructor_photo)): ?>
                                <img src="assets/images/instructors/<?php echo htmlspecialchars($this->bootcamp->instructor_photo); ?>" 
                                     alt="<?php echo htmlspecialchars($this->bootcamp->instructor_name); ?>" 
                                     class="w-16 h-16 rounded-full mr-4 object-cover">
                            <?php else: ?>
                                <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                                    <span class="text-gray-600 text-xl font-medium">
                                        <?php echo substr($this->bootcamp->instructor_name, 0, 1); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-800">
                                    <?php echo htmlspecialchars($this->bootcamp->instructor_name); ?>
                                </h3>
                                <p class="text-gray-600">Instructor</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bootcamp Description -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Description</h2>
                    <div class="text-gray-700 leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($this->bootcamp->description)); ?>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div id="reviews">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Reviews</h2>

                    <?php if ($is_logged_in && $user_enrolled): ?>
                        <!-- Add/Edit Review Form -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h3 class="font-medium text-gray-800 mb-3">
                                <?php echo $user_review ? 'Edit Your Review' : 'Write a Review'; ?>
                            </h3>
                            
                            <form id="reviewForm">
                                <input type="hidden" id="bootcampId" value="<?php echo $this->bootcamp->id; ?>">
                                
                                <div class="mb-3">
                                    <label class="block text-gray-700 mb-1">Rating</label>
                                    <div class="star-rating" id="ratingStars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo ($user_review && $i <= $user_review['rating']) ? 'active' : ''; ?>" data-rating="<?php echo $i; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" id="rating" name="rating" value="<?php echo $user_review ? $user_review['rating'] : '0'; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="reviewText" class="block text-gray-700 mb-1">Review</label>
                                    <textarea id="reviewText" name="review_text" rows="3" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $user_review ? $user_review['review_text'] : ''; ?></textarea>
                                </div>
                                
                                <div class="text-right">
                                    <button type="submit" id="submitReview" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors">
                                        <?php echo $user_review ? 'Update Review' : 'Submit Review'; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Reviews List -->
                    <div id="reviewsList">
                        <?php if (empty($reviews)): ?>
                            <div class="text-center py-6 text-gray-500">
                                <p>No reviews yet. Be the first to review this bootcamp!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="review-stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo ($i <= $review['rating']) ? 'active' : ''; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <h4 class="font-medium text-gray-800 mt-1">
                                                <?php echo htmlspecialchars($review['user_name']); ?>
                                            </h4>
                                            <p class="text-gray-500 text-sm">
                                                <?php echo date('F d, Y', strtotime($review['created_at'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-gray-700">
                                        <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Load More Reviews Button -->
                    <?php if (count($reviews) > 0 && $review_count > count($reviews)): ?>
                        <div class="text-center mt-4">
                            <button id="loadMoreReviews" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                Load More Reviews
                            </button>
                        </div>
                    <?php endif; ?>
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
                        <li><a href="views/about/index.php" class="text-blue-200 hover:text-white">About Us</a></li>
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

        // Wishlist functionality
        const wishlistButton = document.getElementById('wishlistButton');
        if (wishlistButton) {
            wishlistButton.addEventListener('click', function() {
                const bootcampId = this.getAttribute('data-bootcamp-id');
                const isInWishlist = this.querySelector('i').classList.contains('fas');
                
                const action = isInWishlist ? 'remove_from_wishlist' : 'add_to_wishlist';
                const formData = new FormData();
                formData.append('bootcamp_id', bootcampId);
                
                fetch('index.php?action=' + action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const icon = this.querySelector('i');
                        if (isInWishlist) {
                            icon.classList.replace('fas', 'far');
                        } else {
                            icon.classList.replace('far', 'fas');
                        }
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }

        // Star rating functionality
        const ratingStars = document.getElementById('ratingStars');
        if (ratingStars) {
            const stars = ratingStars.querySelectorAll('.fa-star');
            const ratingInput = document.getElementById('rating');
            
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    ratingInput.value = rating;
                    
                    // Update star visuals
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });
            });
        }

        // Review form submission
        const reviewForm = document.getElementById('reviewForm');
        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const bootcampId = document.getElementById('bootcampId').value;
                const rating = document.getElementById('rating').value;
                const reviewText = document.getElementById('reviewText').value;
                
                if (rating == '0') {
                    alert('Please select a rating.');
                    return;
                }
                
                const formData = new FormData();
                formData.append('bootcamp_id', bootcampId);
                formData.append('rating', rating);
                formData.append('review_text', reviewText);
                
                fetch('index.php?action=add_review', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Reload the page to show the updated review
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }

        // Load more reviews
        const loadMoreReviewsBtn = document.getElementById('loadMoreReviews');
        if (loadMoreReviewsBtn) {
            let currentPage = 1;
            
            loadMoreReviewsBtn.addEventListener('click', function() {
                currentPage++;
                
                fetch(`index.php?action=get_bootcamp_reviews&bootcamp_id=<?php echo $this->bootcamp->id; ?>&page=${currentPage}`)
                .then(response => response.json())
                .then(data => {
                    const reviewsList = document.getElementById('reviewsList');
                    
                    // Append new reviews
                    data.reviews.forEach(review => {
                        const reviewElement = document.createElement('div');
                        reviewElement.className = 'border-b border-gray-200 pb-4 mb-4 last:border-b-0';
                        
                        let starsHtml = '';
                        for (let i = 1; i <= 5; i++) {
                            starsHtml += `<i class="fas fa-star ${i <= review.rating ? 'active' : ''}"></i>`;
                        }
                        
                        const reviewHtml = `
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="review-stars">
                                        ${starsHtml}
                                    </div>
                                    <h4 class="font-medium text-gray-800 mt-1">
                                        ${review.user_name}
                                    </h4>
                                    <p class="text-gray-500 text-sm">
                                        ${new Date(review.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 text-gray-700">
                                ${review.review_text.replace(/\n/g, '<br>')}
                            </div>
                        `;
                        
                        reviewElement.innerHTML = reviewHtml;
                        reviewsList.appendChild(reviewElement);
                    });
                    
                    // Hide load more button if all reviews loaded
                    if (currentPage >= data.pagination.total_pages) {
                        loadMoreReviewsBtn.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
        <?php endif; ?>
    </script>
</body>

</html>