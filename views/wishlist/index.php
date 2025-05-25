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

// Set page variables for header
$current_page = 'wishlist';
$page_title = 'My Wishlist - Code Camp';

// Additional CSS for wishlist specific styles
$additional_css = '
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
</style>';

// Include header
include_once 'views/includes/header.php';
?>

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

<?php
// Include footer
include_once 'views/includes/footer.php';
?>