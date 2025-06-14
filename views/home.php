<?php
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

// Set page variables for header
$current_page = 'home';
$page_title = 'Campus Hub - Temukan Bootcamp IT Terbaik';

// Include header
include_once 'views/includes/header.php';
?>

<!-- Hero Section -->
<section class="bg-blue-900 text-white">
    <div class="container mx-auto px-4 py-16 md:py-20">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 animate__animated animate__fadeInLeft">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">
                    <?php if ($is_logged_in): ?>
                        Halo, <?php echo htmlspecialchars($user_name); ?>! <br> Sudah siap untuk meningkatkan karirmu hari ini?
                    <?php else: ?>
                        Wujudkan Potensimu Melalui Pengalaman yang Tak Terbatas!
                    <?php endif; ?>
                </h1>
                <p class="mb-6">Kembangkan dirimu sekarang juga melalui program terbaik dari bootcamp terpercaya.</p>
                <a href="index.php?action=bootcamps" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors duration-300">Mulai Sekarang</a>

                <div class="flex mt-8 space-x-8">
                    <div class="text-center">
                        <div class="text-2xl font-bold">20+</div>
                        <div class="text-sm text-blue-200">Bootcamp Tersedia</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">4+</div>
                        <div class="text-sm text-blue-200">Kategori</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">5+</div>
                        <div class="text-sm text-blue-200">Partner Bootcamp</div>
                    </div>
                </div>
            </div>
            <div class="md:w-1/2 mt-8 md:mt-0 animate__animated animate__fadeInRight flex justify-end">
                <img src="assets/images/hero-image.png" alt="Coding Bootcamp" class="rounded-lg shadow-lg lg:ml-12 w-full md:w-4/5 h-auto max-w-lg mx-auto md:mx-0">
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
                    <div class="w-16 h-16 mx-auto mb-3 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-300 overflow-hidden p-2">
                        <?php
                        // Generate icon filename based on category name (lowercase, replace spaces with hyphens)
                        $icon_name = strtolower(str_replace([' ', '/', '&'], ['-', '-', 'and'], $category['name']));

                        // Comprehensive icon mapping for common categories
                        $icon_mapping = [
                            'web-development' => 'web.png',
                            'mobile-development' => 'mobile.png',
                            'data-science' => 'data.png',
                            'digital-marketing' => 'marketing.png',
                            'ui-ux-design' => 'design.png',
                            'uiand-ux-design' => 'design.png',
                            'cybersecurity' => 'security.png',
                            'cloud-computing' => 'cloud.png',
                            'artificial-intelligence' => 'ai.png',
                            'machine-learning' => 'ai.png',
                            'game-development' => 'game.png',
                            'blockchain' => 'blockchain.png',
                            'devops' => 'devops.png',
                            'software-engineering' => 'software.png',
                            'database' => 'database.png',
                            'networking' => 'network.png',
                            'programming' => 'code.png',
                            'frontend' => 'frontend.png',
                            'backend' => 'backend.png',
                            'fullstack' => 'fullstack.png'
                        ];

                        // Try to find the appropriate icon
                        $icon_path = null;

                        // First, try exact match with processed name
                        if (file_exists("assets/images/icons/{$icon_name}.png")) {
                            $icon_path = "assets/images/icons/{$icon_name}.png";
                        }
                        // Then try mapping
                        elseif (isset($icon_mapping[$icon_name]) && file_exists("assets/images/icons/" . $icon_mapping[$icon_name])) {
                            $icon_path = "assets/images/icons/" . $icon_mapping[$icon_name];
                        }
                        // Try with original name
                        elseif (file_exists("assets/images/icons/" . strtolower($category['name']) . ".png")) {
                            $icon_path = "assets/images/icons/" . strtolower($category['name']) . ".png";
                        }

                        if ($icon_path): ?>
                            <img src="<?php echo $icon_path; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-10 h-10 object-contain">
                        <?php else: ?>
                            <!-- Fallback to Font Awesome icon with better mapping -->
                            <?php
                            $fa_icons = [
                                'web' => 'fa-globe',
                                'mobile' => 'fa-mobile-alt',
                                'data' => 'fa-chart-bar',
                                'marketing' => 'fa-bullhorn',
                                'design' => 'fa-paint-brush',
                                'security' => 'fa-shield-alt',
                                'cloud' => 'fa-cloud',
                                'ai' => 'fa-robot',
                                'game' => 'fa-gamepad',
                                'blockchain' => 'fa-link'
                            ];

                            $fa_class = 'fa-graduation-cap'; // default
                            foreach ($fa_icons as $key => $icon) {
                                if (strpos($icon_name, $key) !== false) {
                                    $fa_class = $icon;
                                    break;
                                }
                            }
                            ?>
                            <i class="fas <?php echo $fa_class; ?> text-blue-600 text-xl"></i>
                        <?php endif; ?>
                    </div>
                    <h3 class="font-medium text-sm"><?php echo htmlspecialchars($category['name']); ?></h3>
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
                    <!-- Ubah semua gambar bootcamp ke ngoding.jpg -->
                    <img src="assets/images/ngoding.jpg"
                        alt="<?php echo htmlspecialchars($bootcamp['title']); ?>"
                        class="w-full h-48 object-cover">

                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">
                            <?php echo htmlspecialchars($bootcamp['title']); ?>
                        </h3>
                        <p class="text-gray-600 mb-4">
                            <?php echo htmlspecialchars(substr($bootcamp['description'], 0, 100)) . '...'; ?>
                        </p>

                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                <span class="text-gray-600 text-xs font-medium">
                                    <?php echo strtoupper(substr($bootcamp['instructor_name'], 0, 1)); ?>
                                </span>
                            </div>
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
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center mr-3 text-white font-medium">R</div>
                    <div>
                        <div class="font-medium">Roman Sabrina</div>
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
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center mr-3 text-white font-medium">S</div>
                    <div>
                        <div class="font-medium">Selvy</div>
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
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center mr-3 text-white font-medium">S</div>
                    <div>
                        <div class="font-medium">Stefy</div>
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

<?php
// Include footer
include_once 'views/includes/footer.php';
?>