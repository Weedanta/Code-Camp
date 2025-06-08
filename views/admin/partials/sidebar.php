<!-- Mobile menu button -->
<div class="lg:hidden fixed top-4 left-4 z-50">
    <button 
        onclick="toggleSidebar()" 
        class="bg-primary text-white p-2 rounded-lg shadow-lg hover:bg-secondary transition-colors duration-200"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</div>

<!-- Sidebar overlay for mobile -->
<div id="sidebar-overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white shadow-xl transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-50 lg:z-auto">
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="bg-primary rounded-lg p-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Admin Panel</h2>
                    <p class="text-sm text-gray-600">Code Camp</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <!-- Dashboard -->
            <a href="admin.php?action=dashboard" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200 <?= (empty($_GET['action']) || $_GET['action'] == 'dashboard') ? 'bg-primary text-white' : '' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Statistics -->
            <a href="admin.php?action=stats" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200 <?= ($_GET['action'] ?? '') == 'stats' ? 'bg-primary text-white' : '' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>Statistik</span>
            </a>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-4"></div>

            <!-- User Management -->
            <div class="space-y-1">
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Manajemen User</h3>
                <a href="admin.php?action=manage_users" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200 <?= ($_GET['action'] ?? '') == 'manage_users' ? 'bg-primary text-white' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <span>Kelola User</span>
                </a>
            </div>

            <!-- Content Management -->
            <div class="space-y-1">
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Manajemen Konten</h3>
                <a href="admin.php?action=manage_bootcamps" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200 <?= ($_GET['action'] ?? '') == 'manage_bootcamps' ? 'bg-primary text-white' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>Kelola Bootcamp</span>
                </a>
                <a href="admin.php?action=manage_categories" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200 <?= ($_GET['action'] ?? '') == 'manage_categories' ? 'bg-primary text-white' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span>Kelola Kategori</span>
                </a>
            </div>

            <!-- Order Management -->
            <div class="space-y-1">
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Transaksi</h3>
                <a href="admin.php?action=manage_orders" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200 <?= ($_GET['action'] ?? '') == 'manage_orders' ? 'bg-primary text-white' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span>Kelola Order</span>
                </a>
            </div>

            <!-- Community Management -->
            <div class="space-y-1">
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Komunitas</h3>
                <a href="admin.php?action=manage_reviews" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200 <?= ($_GET['action'] ?? '') == 'manage_reviews' ? 'bg-primary text-white' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    <span>Kelola Review</span>
                </a>
                <a href="admin.php?action=manage_forum" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200 <?= ($_GET['action'] ?? '') == 'manage_forum' ? 'bg-primary text-white' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                    </svg>
                    <span>Kelola Forum</span>
                </a>
            </div>

            <!-- System -->
            <div class="space-y-1">
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sistem</h3>
                <a href="admin.php?action=manage_settings" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200 <?= ($_GET['action'] ?? '') == 'manage_settings' ? 'bg-primary text-white' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Pengaturan</span>
                </a>
                <a href="admin.php?action=activity_log" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200 <?= ($_GET['action'] ?? '') == 'activity_log' ? 'bg-primary text-white' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Log Aktivitas</span>
                </a>
            </div>
        </nav>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-200">
            <!-- Admin Profile -->
            <div class="flex items-center space-x-3 mb-4">
                <div class="bg-primary rounded-full w-10 h-10 flex items-center justify-center">
                    <span class="text-white font-semibold text-sm">
                        <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-700 truncate">
                        <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
                    </p>
                    <p class="text-xs text-gray-500 truncate">
                        <?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?>
                    </p>
                </div>
            </div>

            <!-- Profile & Logout buttons -->
            <div class="flex space-x-2">
                <a href="admin.php?action=profile" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-center py-2 px-3 rounded-lg text-sm transition-colors duration-200">
                    Profile
                </a>
                <a href="admin.php?action=logout" class="flex-1 bg-red-100 hover:bg-red-200 text-red-700 text-center py-2 px-3 rounded-lg text-sm transition-colors duration-200" onclick="return confirm('Yakin ingin logout?')">
                    Logout
                </a>
            </div>
        </div>
    </div>
</aside>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
}

// Close sidebar when window is resized to large screen
window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.add('hidden');
    }
});
</script>