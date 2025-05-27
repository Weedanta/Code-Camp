<?php
// Set page variables for header
$current_page = 'my_bootcamps';
$page_title = 'My Bootcamps - Code Camp';

// Include header
include_once 'views/includes/header.php';
?>

<!-- Page Header -->
<div class="bg-blue-900 text-white py-6">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">My Bootcamps</h1>
        <p class="mt-2">Access your enrolled bootcamps and learning materials</p>
    </div>
</div>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <?php if (empty($bootcamps)): ?>
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="text-gray-500 mb-4">
                <i class="fas fa-graduation-cap text-6xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">You haven't enrolled in any bootcamps yet</h2>
            <p class="text-gray-600 mb-6">Browse our catalog and find the perfect bootcamp to advance your skills.</p>
            <a href="index.php?action=bootcamps" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors inline-block">
                Browse Bootcamps
            </a>
        </div>
    <?php else: ?>
        <!-- Search and filter -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-grow">
                    <input type="text" id="searchBootcamps" placeholder="Search your bootcamps..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <select id="sortBootcamps" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="recent">Most Recent</option>
                        <option value="name">Name (A-Z)</option>
                        <option value="date">Start Date</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Bootcamp Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="bootcampCards">
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
                            <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                Enrolled
                            </span>
                        </div>

                        <p class="text-gray-600 mb-4 line-clamp-2 h-12">
                            <?php echo htmlspecialchars(substr($bootcamp['description'], 0, 75)) . '...'; ?>
                        </p>

                        <div class="flex items-center mb-4">
                            <?php if (!empty($bootcamp['instructor_photo'])): ?>
                                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                    <span class="text-gray-600 text-xs font-medium">
                                        <?php echo strtoupper(substr($bootcamp['instructor_name'], 0, 1)); ?>
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

                        <div class="mb-4">
                            <div class="h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-blue-600 rounded-full" style="width: 35%;"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">35% completed</div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                <i class="far fa-calendar-alt mr-1"></i>
                                Started <?php echo date('M d, Y', strtotime($bootcamp['start_date'])); ?>
                            </span>
                            <a href="index.php?action=bootcamp_detail&id=<?php echo $bootcamp['id']; ?>"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Continue
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
                    <a href="index.php?action=my_bootcamps&page=<?php echo $page - 1; ?>"
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
                        <a href="index.php?action=my_bootcamps&page=<?php echo $i; ?>"
                            class="w-10 h-10 mx-1 flex items-center justify-center rounded-full text-gray-700 hover:bg-blue-50">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="index.php?action=my_bootcamps&page=<?php echo $page + 1; ?>"
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
<?php
// Include footer
include_once 'views/includes/footer.php';
?>