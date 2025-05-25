<?php
// Set page variables for header
$current_page = 'bootcamps';
$page_title = htmlspecialchars($this->bootcamp->title) . ' - Code Camp';

// Additional CSS for star ratings
$additional_css = '
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
</style>';

// Include header
include_once 'views/includes/header.php';
?>

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
                <!-- Ubah gambar bootcamp ke ngoding.jpg -->
                <img src="assets/images/ngoding.jpg"
                    alt="<?php echo htmlspecialchars($item['title']); ?>"
                    class="w-full h-48 object-cover">
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

<?php
// Include footer
include_once 'views/includes/footer.php';
?>