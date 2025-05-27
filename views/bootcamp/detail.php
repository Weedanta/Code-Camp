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
    transition: all 0.2s ease;
}
.star-rating .fa-star.active {
    color: #FBBF24; /* yellow-400 */
}
.star-rating .fa-star:hover {
    color: #F59E0B; /* yellow-500 */
    transform: scale(1.1);
}
.review-stars .fa-star {
    color: #D1D5DB;
    font-size: 16px;
}
.review-stars .fa-star.active {
    color: #FBBF24;
}

/* Toast notification styles */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    min-width: 300px;
    padding: 16px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    color: white;
    font-weight: 500;
    transform: translateX(400px);
    transition: transform 0.3s ease;
}

.toast.show {
    transform: translateX(0);
}

.toast.success {
    background-color: #10B981;
}

.toast.error {
    background-color: #EF4444;
}

.toast.warning {
    background-color: #F59E0B;
}

.toast .close-btn {
    margin-left: 12px;
    cursor: pointer;
    opacity: 0.8;
}

.toast .close-btn:hover {
    opacity: 1;
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
                    alt="<?php echo htmlspecialchars($this->bootcamp->title); ?>"
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

                <?php if ($is_logged_in): ?>
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
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Share your experience with this bootcamp..."><?php echo $user_review ? $user_review['review_text'] : ''; ?></textarea>
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

<!-- JAVASCRIPT UNTUK REVIEW FUNCTIONALITY -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Review system loaded'); // Debug log

    // Star rating functionality
    const ratingStars = document.querySelectorAll('#ratingStars .fa-star');
    const ratingInput = document.getElementById('rating');
    
    if (ratingStars.length > 0) {
        console.log('Star rating initialized'); // Debug log
        
        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                console.log('Star clicked, rating:', rating); // Debug log
                ratingInput.value = rating;
                
                // Update visual stars
                ratingStars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
            
            // Hover effect
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                ratingStars.forEach((s, index) => {
                    if (index < rating) {
                        s.style.color = '#F59E0B'; // yellow-500
                    } else {
                        s.style.color = '#D1D5DB'; // gray-300
                    }
                });
            });
        });
        
        // Reset on mouse leave
        const ratingContainer = document.getElementById('ratingStars');
        if (ratingContainer) {
            ratingContainer.addEventListener('mouseleave', function() {
                const currentRating = parseInt(ratingInput.value);
                ratingStars.forEach((s, index) => {
                    if (index < currentRating) {
                        s.style.color = '#FBBF24'; // yellow-400
                    } else {
                        s.style.color = '#D1D5DB'; // gray-300
                    }
                });
            });
        }
    }

    // Review form submission
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        console.log('Review form found'); // Debug log
        
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Review form submitted'); // Debug log
            
            const rating = ratingInput.value;
            const reviewText = document.getElementById('reviewText').value.trim();
            const bootcampId = document.getElementById('bootcampId').value;
            
            console.log('Form data:', {rating, reviewText, bootcampId}); // Debug log
            
            // Validation
            if (rating === '0' || rating === '') {
                showToast('Please select a rating', 'error');
                return;
            }
            
            if (reviewText === '') {
                showToast('Please write a review', 'error');
                return;
            }
            
            // Disable submit button
            const submitBtn = document.getElementById('submitReview');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
            
            // Create FormData
            const formData = new FormData();
            formData.append('bootcamp_id', bootcampId);
            formData.append('rating', rating);
            formData.append('review_text', reviewText);
            
            console.log('Sending review to server...'); // Debug log
            
            // Submit review
            fetch('index.php?action=add_review', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response received:', response); // Debug log
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data); // Debug log
                
                if (data.success) {
                    showToast(data.message, 'success');
                    
                    // Reset form if it was a new review
                    if (originalText.includes('Submit')) {
                        reviewForm.reset();
                        ratingInput.value = '0';
                        ratingStars.forEach(s => s.classList.remove('active'));
                    }
                    
                    // Reload page after 2 seconds to show updated review
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error submitting review:', error);
                showToast('Failed to submit review. Please try again.', 'error');
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    }

    // Wishlist functionality
    const wishlistButton = document.getElementById('wishlistButton');
    if (wishlistButton) {
        console.log('Wishlist button found'); // Debug log
        
        wishlistButton.addEventListener('click', function() {
            const bootcampId = this.getAttribute('data-bootcamp-id');
            const heartIcon = this.querySelector('i');
            const isInWishlist = heartIcon.classList.contains('fas');
            
            console.log('Wishlist button clicked, bootcamp:', bootcampId, 'inWishlist:', isInWishlist); // Debug log
            
            // Disable button temporarily
            this.disabled = true;
            
            const formData = new FormData();
            formData.append('bootcamp_id', bootcampId);
            
            const action = isInWishlist ? 'remove_from_wishlist' : 'add_to_wishlist';
            
            fetch(`index.php?action=${action}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Wishlist response:', data); // Debug log
                
                if (data.success) {
                    // Toggle heart icon
                    if (isInWishlist) {
                        heartIcon.classList.remove('fas');
                        heartIcon.classList.add('far');
                    } else {
                        heartIcon.classList.remove('far');
                        heartIcon.classList.add('fas');
                    }
                    
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Wishlist error:', error);
                showToast('Failed to update wishlist. Please try again.', 'error');
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
            });
        });
    }

    // Load more reviews functionality
    const loadMoreBtn = document.getElementById('loadMoreReviews');
    if (loadMoreBtn) {
        let currentPage = 1;
        
        loadMoreBtn.addEventListener('click', function() {
            const bootcampId = document.getElementById('bootcampId').value;
            currentPage++;
            
            console.log('Loading more reviews, page:', currentPage); // Debug log
            
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
            
            fetch(`index.php?action=get_bootcamp_reviews&bootcamp_id=${bootcampId}&page=${currentPage}`)
            .then(response => response.json())
            .then(data => {
                console.log('More reviews loaded:', data); // Debug log
                
                if (data.reviews && data.reviews.length > 0) {
                    const reviewsList = document.getElementById('reviewsList');
                    
                    data.reviews.forEach(review => {
                        const reviewElement = createReviewElement(review);
                        reviewsList.appendChild(reviewElement);
                    });
                    
                    // Hide button if no more reviews
                    if (currentPage >= data.pagination.total_pages) {
                        this.style.display = 'none';
                    }
                } else {
                    this.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error loading more reviews:', error);
                showToast('Failed to load more reviews', 'error');
            })
            .finally(() => {
                // Reset button state
                this.disabled = false;
                this.innerHTML = 'Load More Reviews';
            });
        });
    }
});

// Helper function to create review element
function createReviewElement(review) {
    const reviewDiv = document.createElement('div');
    reviewDiv.className = 'border-b border-gray-200 pb-4 mb-4 last:border-b-0';
    
    const starsHtml = Array.from({length: 5}, (_, i) => {
        const starClass = i < review.rating ? 'active' : '';
        return `<i class="fas fa-star ${starClass}"></i>`;
    }).join('');
    
    reviewDiv.innerHTML = `
        <div class="flex justify-between items-start">
            <div>
                <div class="review-stars">
                    ${starsHtml}
                </div>
                <h4 class="font-medium text-gray-800 mt-1">${escapeHtml(review.user_name)}</h4>
                <p class="text-gray-500 text-sm">${formatDate(review.created_at)}</p>
            </div>
        </div>
        <div class="mt-2 text-gray-700">
            ${escapeHtml(review.review_text).replace(/\n/g, '<br>')}
        </div>
    `;
    
    return reviewDiv;
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

// Toast notification system
function showToast(message, type = 'success') {
    console.log('Showing toast:', message, type); // Debug log
    
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(toast => toast.remove());
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icon = type === 'success' ? 'fas fa-check-circle' : 
                 type === 'error' ? 'fas fa-exclamation-circle' : 
                 'fas fa-info-circle';
    
    toast.innerHTML = `
        <i class="${icon} mr-2"></i>
        <span>${message}</span>
        <button class="close-btn" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    document.body.appendChild(toast);
    
    // Show with animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    }, 5000);
}
</script>

<?php
// Include footer
include_once 'views/includes/footer.php';
?>