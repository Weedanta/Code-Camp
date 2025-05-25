<?php
// Get the search keyword from URL
$keyword = isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '';

// Set page variables for header
$current_page = 'bootcamps';
$page_title = 'Search Results: ' . $keyword . ' - Code Camp';

// Include header
include_once 'views/includes/header.php';
?>

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

<?php
// Include footer
include_once 'views/includes/footer.php';
?>