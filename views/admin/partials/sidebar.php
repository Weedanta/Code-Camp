<?php
// views/admin/partials/sidebar.php
// Template sidebar untuk halaman admin

$current_action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Initialize notification counts (prevent undefined variable errors)
$pending_users = 0;
$pending_orders = 0; 
$pending_reviews = 0;

// Get notification counts if admin is logged in
if (isset($_SESSION['admin_id']) && isset($this) && method_exists($this, 'admin')) {
    try {
        // Get pending counts from model
        $stats = $this->admin->getDashboardStats();
        $pending_reviews = $stats['pending_reviews'] ?? 0;
        
        // You can add more notification counts here
        // $pending_orders = $this->admin->getPendingOrdersCount();
        // $pending_users = $this->admin->getPendingUsersCount();
    } catch (Exception $e) {
        // Silently handle errors
    }
}
?>

<div class="fixed inset-y-0 left-0 z-50 w-64 sidebar-gradient shadow-xl">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 bg-black bg-opacity-20">
            <i class="fas fa-graduation-cap text-2xl text-white mr-3"></i>
            <span class="text-xl font-bold text-white">Campus Hub</span>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <!-- Dashboard -->
            <a href="admin.php?action=dashboard" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo $current_action === 'dashboard' ? 'text-white bg-indigo-600' : 'text-gray-300 hover:text-white hover:bg-gray-700'; ?>">
                <i class="fas fa-tachometer-alt mr-3"></i>
                Dashboard
            </a>
            
            <!-- User Management -->
            <a href="admin.php?action=manage_users" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo in_array($current_action, ['manage_users', 'edit_user']) ? 'text-white bg-indigo-600' : 'text-gray-300 hover:text-white hover:bg-gray-700'; ?>">
                <i class="fas fa-users mr-3"></i>
                Kelola Users
                <?php if ($pending_users > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full"><?php echo $pending_users; ?></span>
                <?php endif; ?>
            </a>
            
            <!-- Bootcamp Management -->
            <a href="admin.php?action=manage_bootcamps" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo in_array($current_action, ['manage_bootcamps', 'create_bootcamp', 'edit_bootcamp']) ? 'text-white bg-indigo-600' : 'text-gray-300 hover:text-white hover:bg-gray-700'; ?>">
                <i class="fas fa-laptop-code mr-3"></i>
                Kelola Bootcamps
            </a>
            
            <!-- Category Management -->
            <a href="admin.php?action=manage_categories" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo in_array($current_action, ['manage_categories', 'create_category', 'edit_category']) ? 'text-white bg-indigo-600' : 'text-gray-300 hover:text-white hover:bg-gray-700'; ?>">
                <i class="fas fa-tags mr-3"></i>
                Kelola Kategori
            </a>
            
            <!-- Order Management -->
            <a href="admin.php?action=manage_orders" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo in_array($current_action, ['manage_orders', 'view_order']) ? 'text-white bg-indigo-600' : 'text-gray-300 hover:text-white hover:bg-gray-700'; ?>">
                <i class="fas fa-shopping-cart mr-3"></i>
                Kelola Orders
                <?php if ($pending_orders > 0): ?>
                    <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full"><?php echo $pending_orders; ?></span>
                <?php endif; ?>
            </a>
            
            <!-- Review Management -->
            <a href="admin.php?action=manage_reviews" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo $current_action === 'manage_reviews' ? 'text-white bg-indigo-600' : 'text-gray-300 hover:text-white hover:bg-gray-700'; ?>">
                <i class="fas fa-star mr-3"></i>
                Kelola Reviews
                <?php if ($pending_reviews > 0): ?>
                    <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full"><?php echo $pending_reviews; ?></span>
                <?php endif; ?>
            </a>
            
            <!-- Forum Management -->
            <a href="admin.php?action=manage_forum" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo $current_action === 'manage_forum' ? 'text-white bg-indigo-600' : 'text-gray-300 hover:text-white hover:bg-gray-700'; ?>">
                <i class="fas fa-comments mr-3"></i>
                Kelola Forum
            </a>
            
            <!-- Analytics -->
            <a href="admin.php?action=stats" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo $current_action === 'stats' ? 'text-white bg-indigo-600' : 'text-gray-300 hover:text-white hover:bg-gray-700'; ?>">
                <i class="fas fa-chart-bar mr-3"></i>
                Analytics
            </a>
            
            <!-- Activity Log -->
            <a href="admin.php?action=activity_log" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo $current_action === 'activity_log' ? 'text-white bg-indigo-600' : 'text-gray-300 hover:text-white hover:bg-gray-700'; ?>">
                <i class="fas fa-history mr-3"></i>
                Log Aktivitas
            </a>
            
            <!-- Settings -->
            <a href="admin.php?action=manage_settings" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo $current_action === 'manage_settings' ? 'text-white bg-indigo-600' : 'text-gray-300 hover:text-white hover:bg-gray-700'; ?>">
                <i class="fas fa-cog mr-3"></i>
                Pengaturan
            </a>
        </nav>
        
        <!-- User Info -->
        <div class="p-4 border-t border-gray-600">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-white truncate">
                        <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
                    </p>
                    <p class="text-xs text-gray-400 capitalize">
                        <?php echo htmlspecialchars($_SESSION['admin_role'] ?? 'Administrator'); ?>
                    </p>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="mt-3 space-y-2">
                <a href="admin.php?action=profile" class="w-full flex items-center justify-center px-3 py-2 text-xs text-gray-300 hover:text-white bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
                    <i class="fas fa-user-edit mr-2"></i>
                    Profile
                </a>
                <a href="admin.php?action=logout" class="w-full flex items-center justify-center px-3 py-2 text-xs text-gray-300 hover:text-white bg-red-700 hover:bg-red-600 rounded-lg transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Overlay for mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-40 lg:hidden hidden"></div>

<style>
.sidebar-gradient {
    background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
}

/* Mobile sidebar toggle */
@media (max-width: 1024px) {
    .sidebar-mobile-hidden {
        transform: translateX(-100%);
    }
    .sidebar-mobile-visible {
        transform: translateX(0);
    }
}
</style>

<script>
// Mobile sidebar toggle functionality
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar-gradient').parentElement;
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar.classList.contains('sidebar-mobile-hidden')) {
        sidebar.classList.remove('sidebar-mobile-hidden');
        sidebar.classList.add('sidebar-mobile-visible');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('sidebar-mobile-hidden');
        sidebar.classList.remove('sidebar-mobile-visible');
        overlay.classList.add('hidden');
    }
}

// Close sidebar when clicking overlay
document.getElementById('sidebarOverlay').addEventListener('click', function() {
    toggleSidebar();
});

// Auto-hide on mobile when clicking menu items
document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', function() {
        if (window.innerWidth < 1024) {
            setTimeout(() => {
                const sidebar = document.querySelector('.sidebar-gradient').parentElement;
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.add('sidebar-mobile-hidden');
                sidebar.classList.remove('sidebar-mobile-visible');
                overlay.classList.add('hidden');
            }, 100);
        }
    });
});

// Initialize mobile sidebar state
if (window.innerWidth < 1024) {
    document.querySelector('.sidebar-gradient').parentElement.classList.add('sidebar-mobile-hidden');
}
</script>