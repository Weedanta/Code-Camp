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
    <title>My Bootcamps - Code Camp</title>
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
                    <a href="index.php?action=bootcamps" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">Bootcamps</a>
                    
                    <a href="index.php?action=my_bootcamps" class="text-blue-600 font-medium">My Bootcamps</a>
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
                    
                    <a href="index.php?action=my_bootcamps" class="block px-3 py-2 rounded-md text-blue-600 bg-blue-50 font-medium">My Bootcamps</a>
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
                            <img src="assets/images/bootcamps/<?php echo htmlspecialchars($bootcamp['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($bootcamp['title']); ?>" 
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
                                <?php echo htmlspecialchars(substr($bootcamp['description'], 0, 100)) . '...'; ?>
                            </p>

                            <div class="flex items-center mb-4">
                                <?php if (!empty($bootcamp['instructor_photo'])): ?>
                                    <img src="assets/images/instructors/<?php echo htmlspecialchars($bootcamp['instructor_photo']); ?>" 
                                         alt="Instructor" class="w-8 h-8 rounded-full mr-2">
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

        // Search functionality
        const searchInput = document.getElementById('searchBootcamps');
        const bootcampCards = document.querySelectorAll('#bootcampCards > div');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                bootcampCards.forEach(card => {
                    const title = card.querySelector('h3').innerText.toLowerCase();
                    const description = card.querySelector('p').innerText.toLowerCase();
                    
                    if (title.includes(searchTerm) || description.includes(searchTerm)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }

        // Sort functionality
        const sortSelect = document.getElementById('sortBootcamps');
        const bootcampContainer = document.getElementById('bootcampCards');
        
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const sortValue = this.value;
                const bootcampCardsArray = Array.from(bootcampCards);
                
                bootcampCardsArray.sort((a, b) => {
                    if (sortValue === 'name') {
                        const titleA = a.querySelector('h3').innerText.toLowerCase();
                        const titleB = b.querySelector('h3').innerText.toLowerCase();
                        return titleA.localeCompare(titleB);
                    } else if (sortValue === 'date') {
                        const dateA = a.querySelector('.text-sm.text-gray-500 i').nextSibling.textContent.trim();
                        const dateB = b.querySelector('.text-sm.text-gray-500 i').nextSibling.textContent.trim();
                        return new Date(dateA) - new Date(dateB);
                    } else {
                        // Default to most recent (as loaded from server)
                        return 0;
                    }
                });
                
                // Reappend sorted cards
                bootcampCardsArray.forEach(card => {
                    bootcampContainer.appendChild(card);
                });
            });
        }
    </script>
</body>

</html>