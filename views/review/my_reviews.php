<?php
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit();
}

$user_name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];

// Set page variables for header
$current_page = 'my_reviews';
$page_title = 'My Reviews - Campus Hub';

// Additional CSS for star ratings and modals
$additional_css = '
<style>
/* Star rating styles */
.star-rating {
    display: flex;
    align-items: center;
    font-size: 20px;
}
.star-rating .fa-star {
    color: #D1D5DB; /* gray-300 */
    cursor: pointer;
    transition: color 0.2s ease;
}
.star-rating .fa-star.active {
    color: #FBBF24; /* yellow-400 */
}
.star-rating .fa-star:hover {
    color: #F59E0B; /* yellow-500 */
}

.review-stars .fa-star {
    color: #D1D5DB;
    font-size: 16px;
}
.review-stars .fa-star.active {
    color: #FBBF24;
}

/* Modal styles */
.modal {
    transition: opacity 0.25s ease;
}
.modal-body {
    transition: all 0.25s ease;
    transform: scale(0.95);
}
.modal.active .modal-body {
    transform: scale(1);
}

/* Loading state */
.loading {
    opacity: 0.5;
    pointer-events: none;
}

/* Animation classes */
.fade-out {
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.3s ease;
}

.fade-in {
    opacity: 1;
    transform: translateY(0);
    transition: all 0.3s ease;
}
</style>';

// Include header
include_once 'views/includes/header.php';
?>

<!-- Page Header -->
<div class="bg-blue-900 text-white py-6">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">My Reviews</h1>
        <p class="mt-2">Manage your bootcamp reviews and feedback</p>
    </div>
</div>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <?php if (empty($reviews)): ?>
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="text-gray-500 mb-4">
                <i class="fas fa-star text-6xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">No Reviews Yet</h2>
            <p class="text-gray-600 mb-6">You haven't written any reviews. Enroll in bootcamps and share your experience!</p>
            <a href="index.php?action=bootcamps" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors inline-block">
                Browse Bootcamps
            </a>
        </div>
    <?php else: ?>
        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-grow">
                    <input type="text" id="searchReviews" placeholder="Search your reviews..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <select id="sortReviews" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="recent">Most Recent</option>
                        <option value="oldest">Oldest First</option>
                        <option value="rating-high">Highest Rating</option>
                        <option value="rating-low">Lowest Rating</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Reviews Grid -->
        <div class="space-y-6" id="reviewsContainer">
            <?php foreach ($reviews as $review): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 review-card" 
                     data-bootcamp-title="<?php echo htmlspecialchars(strtolower($review['bootcamp_title'])); ?>"
                     data-rating="<?php echo $review['rating']; ?>"
                     data-date="<?php echo $review['created_at']; ?>">
                    <div class="md:flex">
                        <!-- Bootcamp Image -->
                        <div class="md:w-48 md:flex-shrink-0">
                            <img src="assets/images/ngoding.jpg" 
                                 alt="<?php echo htmlspecialchars($review['bootcamp_title']); ?>"
                                 class="h-48 w-full object-cover md:h-full">
                        </div>

                        <!-- Review Content -->
                        <div class="p-6 flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">
                                        <?php echo htmlspecialchars($review['bootcamp_title']); ?>
                                    </h3>
                                    <?php if (!empty($review['category_name'])): ?>
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full mb-2">
                                            <?php echo htmlspecialchars($review['category_name']); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <!-- Rating Display -->
                                    <div class="review-stars mb-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo ($i <= $review['rating']) ? 'active' : ''; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ml-2 text-gray-600 text-sm"><?php echo $review['rating']; ?>/5</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                    <button onclick="editReview(<?php echo $review['id']; ?>)" 
                                            class="px-3 py-1 bg-yellow-500 text-white text-sm rounded-md hover:bg-yellow-600 transition-colors">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    <button onclick="deleteReview(<?php echo $review['id']; ?>)" 
                                            class="px-3 py-1 bg-red-500 text-white text-sm rounded-md hover:bg-red-600 transition-colors">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </div>
                            </div>

                            <!-- Review Text -->
                            <div class="text-gray-700 mb-4">
                                <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                            </div>

                            <!-- Review Meta -->
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <div>
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    Reviewed on <?php echo date('F d, Y', strtotime($review['created_at'])); ?>
                                </div>
                                <?php if ($review['updated_at']): ?>
                                    <div>
                                        <i class="fas fa-edit mr-1"></i>
                                        Updated <?php echo date('M d, Y', strtotime($review['updated_at'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="flex justify-center mt-8">
                <?php if ($page > 1): ?>
                    <a href="index.php?action=my_reviews&page=<?php echo $page - 1; ?>"
                        class="w-10 h-10 mx-1 flex items-center justify-center rounded-full border border-blue-600 text-blue-600 hover:bg-blue-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <a href="index.php?action=my_reviews&page=<?php echo $i; ?>"
                            class="w-10 h-10 mx-1 flex items-center justify-center rounded-full text-gray-700 hover:bg-blue-50">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="index.php?action=my_reviews&page=<?php echo $page + 1; ?>"
                        class="w-10 h-10 mx-1 flex items-center justify-center rounded-full border border-blue-600 text-blue-600 hover:bg-blue-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Edit Review Modal -->
<div id="editReviewModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="modal-body relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Edit Review</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="editReviewForm">
                <input type="hidden" id="editReviewId" name="review_id">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Rating</label>
                    <div class="star-rating" id="editRatingStars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star" data-rating="<?php echo $i; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" id="editRating" name="rating" value="5">
                </div>

                <div class="mb-6">
                    <label for="editReviewText" class="block text-gray-700 text-sm font-medium mb-2">Review</label>
                    <textarea id="editReviewText" name="review_text" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Share your experience with this bootcamp..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="updateReviewBtn"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Update Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="modal-body relative top-20 mx-auto p-5 border w-11/12 md:w-1/3 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Review</h3>
            <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this review? This action cannot be undone.</p>
            
            <div class="flex justify-center space-x-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmDelete()" id="confirmDeleteBtn"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 z-50 hidden">
    <div class="bg-green-500 text-white px-6 py-3 rounded-md shadow-lg flex items-center">
        <span id="toastMessage">Success!</span>
        <button onclick="hideToast()" class="ml-4 text-white hover:text-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<script>
let currentDeleteId = null;

// Search functionality
document.getElementById('searchReviews').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const reviewCards = document.querySelectorAll('.review-card');

    reviewCards.forEach(card => {
        const title = card.getAttribute('data-bootcamp-title');
        if (title.includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

// Sort functionality
document.getElementById('sortReviews').addEventListener('change', function() {
    const sortValue = this.value;
    const container = document.getElementById('reviewsContainer');
    const cards = Array.from(container.querySelectorAll('.review-card'));

    cards.sort((a, b) => {
        switch(sortValue) {
            case 'recent':
                return new Date(b.getAttribute('data-date')) - new Date(a.getAttribute('data-date'));
            case 'oldest':
                return new Date(a.getAttribute('data-date')) - new Date(b.getAttribute('data-date'));
            case 'rating-high':
                return parseInt(b.getAttribute('data-rating')) - parseInt(a.getAttribute('data-rating'));
            case 'rating-low':
                return parseInt(a.getAttribute('data-rating')) - parseInt(b.getAttribute('data-rating'));
            default:
                return 0;
        }
    });

    // Clear container and re-append sorted cards
    container.innerHTML = '';
    cards.forEach(card => container.appendChild(card));
});

// Edit review function
function editReview(reviewId) {
    fetch(`index.php?action=get_review&id=${reviewId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('editReviewId').value = data.data.id;
                document.getElementById('editReviewText').value = data.data.review_text;
                document.getElementById('editRating').value = data.data.rating;
                
                // Update star display
                const stars = document.querySelectorAll('#editRatingStars .fa-star');
                stars.forEach((star, index) => {
                    if (index < data.data.rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });

                // Show modal
                document.getElementById('editReviewModal').classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('editReviewModal').classList.add('active');
                }, 10);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to load review data', 'error');
        });
}

// Star rating for edit modal
document.querySelectorAll('#editRatingStars .fa-star').forEach(star => {
    star.addEventListener('click', function() {
        const rating = parseInt(this.getAttribute('data-rating'));
        document.getElementById('editRating').value = rating;
        
        // Update visual stars
        const stars = document.querySelectorAll('#editRatingStars .fa-star');
        stars.forEach((s, index) => {
            if (index < rating) {
                s.classList.add('active');
            } else {
                s.classList.remove('active');
            }
        });
    });
});

// Edit review form submission
document.getElementById('editReviewForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const button = document.getElementById('updateReviewBtn');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';

    const formData = new FormData(this);
    
    fetch('index.php?action=update_review', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeEditModal();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to update review', 'error');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = 'Update Review';
    });
});

// Delete review function
function deleteReview(reviewId) {
    currentDeleteId = reviewId;
    document.getElementById('deleteConfirmModal').classList.remove('hidden');
    setTimeout(() => {
        document.getElementById('deleteConfirmModal').classList.add('active');
    }, 10);
}

// Confirm delete function
function confirmDelete() {
    if (!currentDeleteId) return;

    const button = document.getElementById('confirmDeleteBtn');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...';

    const formData = new FormData();
    formData.append('review_id', currentDeleteId);

    fetch('index.php?action=delete_review', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeDeleteModal();
            
            // Remove the review card from DOM
            const reviewCard = document.querySelector(`[data-review-id="${currentDeleteId}"]`);
            if (reviewCard) {
                reviewCard.classList.add('fade-out');
                setTimeout(() => {
                    reviewCard.remove();
                    
                    // Check if no reviews left
                    if (document.querySelectorAll('.review-card').length === 0) {
                        location.reload();
                    }
                }, 300);
            } else {
                // If we can't find the specific card, just reload
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to delete review', 'error');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = 'Delete';
    });
}

// Modal functions
function closeEditModal() {
    const modal = document.getElementById('editReviewModal');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 250);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteConfirmModal');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 250);
    currentDeleteId = null;
}

// Toast notification functions
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const toastContainer = toast.querySelector('div');
    
    toastMessage.textContent = message;
    
    // Set color based on type
    toastContainer.className = toastContainer.className.replace(/bg-\w+-500/, `bg-${type === 'error' ? 'red' : 'green'}-500`);
    
    toast.classList.remove('hidden');
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        hideToast();
    }, 5000);
}

function hideToast() {
    document.getElementById('toast').classList.add('hidden');
}

// Close modals when clicking outside
window.addEventListener('click', function(e) {
    const editModal = document.getElementById('editReviewModal');
    const deleteModal = document.getElementById('deleteConfirmModal');
    
    if (e.target === editModal) {
        closeEditModal();
    }
    if (e.target === deleteModal) {
        closeDeleteModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
        closeDeleteModal();
    }
});
</script>

<?php
// Include footer
include_once 'views/includes/footer.php';
?>