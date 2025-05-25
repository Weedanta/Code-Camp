<?php
// Get categories for footer if not already loaded
if (!isset($categories)) {
    require_once (isset($base_url) ? $base_url : '') . 'config/database.php';
    require_once (isset($base_url) ? $base_url : '') . 'models/Category.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $category = new Category($db);
    
    // Get all categories
    $categoryStmt = $category->readAll();
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
    <!-- Footer -->
    <footer class="bg-blue-900 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="<?php echo isset($base_url) ? $base_url : ''; ?>assets/images/logo.png" alt="Logo" class="h-16 hidden md:block">
                        <img src="<?php echo isset($base_url) ? $base_url : ''; ?>assets/images/logo/logo_mobile.png" alt="Logo" class="md:hidden h-12">
                    </div>
                    <p class="text-blue-200 mb-4">Temukan bootcamp IT terbaik untuk mengembangkan keterampilan dan mempercepat karier Anda dalam dunia teknologi.</p>
                    <p class="text-blue-200">Jl. Pendidikan No. 123, Malang<br>Jawa Timur, Indonesia, 65145</p>
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-4">Kategori Populer</h3>
                    <ul class="space-y-2">
                        <?php foreach (array_slice($categories, 0, 5) as $category): ?>
                            <li class="flex items-center">
                                <div class="w-6 h-6 mr-2 bg-blue-800 rounded-full flex items-center justify-center">
                                    <?php
                                    $icon_name = strtolower(str_replace([' ', '/', '&'], ['-', '-', 'and'], $category['name']));
                                    $icon_mapping = [
                                        'web-development' => 'web.png',
                                        'mobile-development' => 'mobile.png',
                                        'data-science' => 'data.png',
                                        'digital-marketing' => 'marketing.png',
                                        'ui-ux-design' => 'design.png',
                                        'uiand-ux-design' => 'design.png'
                                    ];

                                    $icon_path = null;
                                    if (file_exists((isset($base_url) ? $base_url : '') . "assets/images/icons/{$icon_name}.png")) {
                                        $icon_path = (isset($base_url) ? $base_url : '') . "assets/images/icons/{$icon_name}.png";
                                    } elseif (isset($icon_mapping[$icon_name]) && file_exists((isset($base_url) ? $base_url : '') . "assets/images/icons/" . $icon_mapping[$icon_name])) {
                                        $icon_path = (isset($base_url) ? $base_url : '') . "assets/images/icons/" . $icon_mapping[$icon_name];
                                    }

                                    if ($icon_path): ?>
                                        <img src="<?php echo $icon_path; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-4 h-4 object-contain filter brightness-0 invert">
                                    <?php else: ?>
                                        <i class="fas fa-chevron-right text-blue-200 text-xs"></i>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=bootcamp_category&id=<?php echo $category['id']; ?>" class="text-blue-200 hover:text-white transition-colors">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-4">Informasi</h3>
                    <ul class="space-y-2">
                        <li><a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php" class="text-blue-200 hover:text-white">Home</a></li>
                        <li><a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php?action=bootcamps" class="text-blue-200 hover:text-white">Bootcamp</a></li>
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

    <!-- JavaScript untuk Mobile Menu dan Profile Dropdown -->
    <script>
        // Mobile menu toggle with hamburger animation
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger');

            // Toggle menu visibility with animation
            if (mobileMenu.classList.contains('mobile-menu-show')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        // Close button for mobile menu
        document.getElementById('close-mobile-menu').addEventListener('click', function() {
            closeMobileMenu();
        });

        // Functions to open/close mobile menu
        function openMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger');
            const body = document.body;

            mobileMenu.classList.add('mobile-menu-show');
            hamburger.classList.add('active');
            body.classList.add('body-scroll-lock');
        }

        function closeMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger');
            const body = document.body;

            mobileMenu.classList.remove('mobile-menu-show');
            hamburger.classList.remove('active');
            body.classList.remove('body-scroll-lock');
        }

        // Profile dropdown toggle (if logged in)
        <?php if (isset($_SESSION['user_id'])): ?>
            const profileButton = document.getElementById('profileButton');
            if (profileButton) {
                profileButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const dropdown = document.getElementById('profileDropdown');
                    dropdown.classList.toggle('hidden');
                });
            }
        <?php endif; ?>

        // Close dropdown and mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuContent = mobileMenu.querySelector('.mobile-menu-content');

            <?php if (isset($_SESSION['user_id'])): ?>
                const profileButton = document.getElementById('profileButton');
                const profileDropdown = document.getElementById('profileDropdown');

                // Close profile dropdown if clicked outside
                if (profileButton && profileDropdown) {
                    if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                }
            <?php endif; ?>

            // Close mobile menu if clicked on backdrop (not on menu content)
            if (mobileMenu.classList.contains('mobile-menu-show') &&
                !mobileMenuButton.contains(e.target) &&
                !mobileMenuContent.contains(e.target)) {
                closeMobileMenu();
            }
        });

        // Close mobile menu when window is resized to desktop view
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) { // md breakpoint
                closeMobileMenu();
            }
        });

        // Close mobile menu when clicking menu links
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', function() {
                setTimeout(() => {
                    closeMobileMenu();
                }, 150);
            });
        });

        // Prevent scrolling when mobile menu is open
        document.getElementById('mobile-menu').addEventListener('touchmove', function(e) {
            if (this.classList.contains('mobile-menu-show')) {
                e.preventDefault();
            }
        }, {
            passive: false
        });

        // Handle escape key to close menu
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenu.classList.contains('mobile-menu-show')) {
                    closeMobileMenu();
                }

                <?php if (isset($_SESSION['user_id'])): ?>
                    const profileDropdown = document.getElementById('profileDropdown');
                    if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                        profileDropdown.classList.add('hidden');
                    }
                <?php endif; ?>
            }
        });
    </script>

    <?php if (isset($additional_js)): ?>
        <?php echo $additional_js; ?>
    <?php endif; ?>
</body>
</html>