<?php
// Cek session
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['name'] : '';
$user_id = $is_logged_in ? $_SESSION['user_id'] : '';

// Get sample bootcamps for featured section
require_once 'config/database.php';
require_once 'models/Bootcamp.php';
require_once 'models/Category.php';

$database = new Database();
$db = $database->getConnection();

$bootcamp = new Bootcamp($db);
$category = new Category($db);

// Get featured bootcamps (limit to 3)
$stmt = $bootcamp->readAll(3, 0);
$featured_bootcamps = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories
$categoryStmt = $category->readAll();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Hub - Temukan Bootcamp IT Terbaik</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
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
                    <a href="index.php" class="text-blue-600 font-medium">Home</a>
                    <a href="index.php?action=bootcamps" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">Bootcamps</a>
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

            <!-- Mobile Menu (Single Implementation) -->
            <div id="mobile-menu" class="md:hidden hidden w-full mt-2">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-white rounded-md shadow-md">
                    <a href="index.php" class="block px-3 py-2 rounded-md text-blue-600 font-medium">Home</a>
                    <a href="index.php?action=bootcamps" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">Bootcamps</a>
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
    </header>

    <!-- Hero Section -->
    <section class="bg-blue-900 text-white">
        <div class="container mx-auto px-4 py-16 md:py-20">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 animate__animated animate__fadeInLeft">
                    <h1 class="text-3xl md:text-4xl font-bold mb-4">Wujudkan Potensimu Melalui Pengalaman yang Tak Terbatas!</h1>
                    <p class="mb-6">Kembangkan dirimu sekarang juga melalui program terbaik dari bootcamp terpercaya.</p>
                    <a href="index.php?action=bootcamps" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors duration-300">Mulai Sekarang</a>

                    <div class="flex mt-8 space-x-8">
                        <div class="text-center">
                            <div class="text-2xl font-bold">20+</div>
                            <div class="text-sm text-blue-200">Bootcamp Tersedia</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">10+</div>
                            <div class="text-sm text-blue-200">Kategori</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">5+</div>
                            <div class="text-sm text-blue-200">Partner Bootcamp</div>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 mt-8 md:mt-0 animate__animated animate__fadeInRight">
                    <img src="../assets/images/hero-image.png" alt="Coding Bootcamp" class="rounded-lg shadow-lg md:ml-12 w-auto h-auto">
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-center text-2xl font-bold mb-8">KATEGORI</h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 text-center">
                <?php foreach ($categories as $category): ?>
                    <a href="index.php?action=bootcamp_category&id=<?php echo $category['id']; ?>" class="p-4 hover:shadow-md rounded-lg transition-shadow duration-300 group">
                        <div class="w-16 h-16 mx-auto mb-3 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-300">
                            <?php if (!empty($category['icon'])): ?>
                                <img src="assets/images/icons/<?php echo htmlspecialchars($category['icon']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-8 h-8">
                            <?php else: ?>
                                <i class="fas fa-graduation-cap text-blue-600"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-medium"><?php echo htmlspecialchars($category['name']); ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Bootcamps Section -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-center text-2xl font-bold mb-8">Jelajahi Bootcamp Unggulan</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($featured_bootcamps as $bootcamp): ?>
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
                            <h3 class="text-xl font-bold mb-2">
                                <?php echo htmlspecialchars($bootcamp['title']); ?>
                            </h3>
                            <p class="text-gray-600 mb-4">
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
                                    Instructor: <?php echo htmlspecialchars($bootcamp['instructor_name']); ?>
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
                                <a href="index.php?action=bootcamp_detail&id=<?php echo $bootcamp['id']; ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-8">
                <a href="index.php?action=bootcamps" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors duration-300">
                    Lihat Semua Bootcamp
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-center text-2xl font-bold mb-8">Mengapa Memilih Code Camp?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-award text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Bootcamp Berkualitas</h3>
                    <p class="text-gray-600">Semua bootcamp kami dipilih dengan ketat dan disusun oleh instruktur berpengalaman di bidangnya.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-rocket text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Belajar dengan Kecepatan Anda</h3>
                    <p class="text-gray-600">Akses bootcamp kapan saja dan di mana saja sesuai dengan jadwal dan kecepatan belajar Anda.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-friends text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Komunitas Pendukung</h3>
                    <p class="text-gray-600">Dapatkan dukungan dari komunitas pembelajar dan mentor yang selalu siap membantu Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-12 bg-blue-900 text-white">
        <div class="container mx-auto px-4">
            <h2 class="text-center text-2xl font-bold mb-8">Apa Kata Mereka?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-blue-800 p-6 rounded-lg">
                    <div class="text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="italic mb-4">"Bootcamp UI/UX Design di Code Camp sangat membantu saya memulai karir sebagai UI/UX Designer. Materinya komprehensif dan instrukturnya sangat berpengalaman."</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center mr-3">A</div>
                        <div>
                            <div class="font-medium">Ahmad Rizki</div>
                            <div class="text-sm text-blue-300">UI/UX Designer</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-800 p-6 rounded-lg">
                    <div class="text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="italic mb-4">"Saya telah mengikuti bootcamp Data Analysis dan hasilnya luar biasa. Sekarang saya bisa menganalisis data dengan lebih efektif dan mendapatkan insight yang berharga."</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center mr-3">S</div>
                        <div>
                            <div class="font-medium">Sari Indah</div>
                            <div class="text-sm text-blue-300">Data Analyst</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-800 p-6 rounded-lg">
                    <div class="text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="italic mb-4">"Bootcamp Digital Marketing sangat praktis dan relevan dengan kebutuhan industri saat ini. Sekarang saya bisa menjalankan kampanye marketing yang lebih efektif."</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center mr-3">B</div>
                        <div>
                            <div class="font-medium">Budi Santoso</div>
                            <div class="text-sm text-blue-300">Digital Marketer</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Siap Untuk Memulai Perjalanan Belajar Anda?</h2>
            <p class="text-gray-600 mb-8 max-w-2xl mx-auto">Bergabunglah dengan ribuan pembelajar lainnya dan kembangkan keterampilan Anda melalui bootcamp berkualitas tinggi.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="index.php?action=bootcamps" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors duration-300">
                    Jelajahi Bootcamp
                </a>
                <a href="index.php?action=signup" class="px-6 py-3 border border-blue-600 text-blue-600 font-medium rounded-md hover:bg-blue-50 transition-colors duration-300">
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="assets/images/logo.png" alt="Logo" class="h-16 hidden md:block">
                        <img src="assets/images/logo/logo_mobile.png" alt="Logo" class="md:hidden h-12">
                    </div>
                    <p class="text-blue-200 mb-4">Temukan bootcamp IT terbaik untuk mengembangkan keterampilan dan mempercepat karier Anda dalam dunia teknologi.</p>
                    <p class="text-blue-200">Jl. Pendidikan No. 123, Malang<br>Jawa Timur, Indonesia, 65145</p>
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-4">Kategori</h3>
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
                    <h3 class="text-lg font-bold mb-4">Informasi</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-blue-200 hover:text-white">Home</a></li>
                        <li><a href="index.php?action=bootcamps" class="text-blue-200 hover:text-white">Bootcamp</a></li>
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
        <?php endif; ?>
    </script>
</body>

</html>