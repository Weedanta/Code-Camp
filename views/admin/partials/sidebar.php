<?php
// Get unread chat count
try {
    require_once __DIR__ . '/../../../models/Chat.php';
    $chat = new Chat();
    $unread_chat_count = $chat->getTotalUnreadForAdmin();
} catch (Exception $e) {
    $unread_chat_count = 0;
}

// Current action untuk highlight menu aktif
$current_action = $_GET['action'] ?? 'dashboard';
?>

<!-- Mobile menu button -->
<div class="lg:hidden fixed top-4 left-4 z-50">
    <button
        onclick="toggleSidebar()"
        class="bg-primary text-white p-2 rounded-lg shadow-lg hover:bg-secondary transition-colors duration-200">
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

            <!-- User Features Management -->
            <div class="space-y-1">
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">User Features</h3>

                <!-- Main Features Management Menu -->
                <div class="relative">
                    <button
                        onclick="toggleFeaturesSubmenu()"
                        class="w-full flex items-center justify-between space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200 <?= in_array($current_action, ['manage_features', 'export_features', 'view_cv', 'clear_old_wishlists', 'backup_cv_data', 'clear_completed_todos']) ? 'bg-primary text-white' : '' ?>">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 00-2 2v2a2 2 0 002 2m0 0h14m-14 0a2 2 0 002 2v2a2 2 0 01-2 2"></path>
                            </svg>
                            <span>User Tools</span>
                        </div>
                        <svg
                            id="featuresChevron"
                            class="w-4 h-4 transition-transform duration-200 <?= in_array($current_action, ['manage_features', 'export_features', 'view_cv', 'clear_old_wishlists', 'backup_cv_data', 'clear_completed_todos']) ? 'rotate-90' : '' ?>"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <!-- Features Submenu -->
                    <div
                        id="featuresSubmenu"
                        class="ml-6 space-y-1 <?= in_array($current_action, ['manage_features', 'export_features', 'view_cv', 'clear_old_wishlists', 'backup_cv_data', 'clear_completed_todos']) ? '' : 'hidden' ?>">
                        <!-- Main Features Dashboard -->
                        <a href="admin.php?action=manage_features" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm <?= $current_action == 'manage_features' ? 'bg-gray-100 text-gray-800' : '' ?>">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                            </svg>
                            <span>Features Dashboard</span>
                        </a>

                        <!-- Wishlist Management -->
                        <a href="admin.php?action=manage_features#wishlist" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span>Wishlist</span>
                        </a>

                        <!-- CV Maker Management -->
                        <a href="admin.php?action=manage_features#cvmaker" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>CV Maker</span>
                        </a>

                        <!-- Todo List Management -->
                        <a href="admin.php?action=manage_features#todolist" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <span>Todo Lists</span>
                        </a>

                        <!-- Export Options -->
                        <div class="border-t border-gray-200 pt-2 mt-2">
                            <p class="px-2 text-xs text-gray-500 mb-1">Export Data</p>

                            <a href="admin.php?action=export_features&type=wishlist" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                                </svg>
                                <span>Export Wishlist</span>
                            </a>

                            <a href="admin.php?action=export_features&type=cv" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                                </svg>
                                <span>Export CV Data</span>
                            </a>

                            <a href="admin.php?action=export_features&type=todo" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                                </svg>
                                <span>Export Todos</span>
                            </a>
                        </div>

                        <!-- Quick Actions -->
                        <div class="border-t border-gray-200 pt-2 mt-2">
                            <p class="px-2 text-xs text-gray-500 mb-1">Quick Actions</p>

                            <a href="admin.php?action=clear_old_wishlists" onclick="return confirm('Clear old wishlist data? This cannot be undone.')" class="flex items-center space-x-2 p-2 rounded-lg text-yellow-600 hover:bg-yellow-50 hover:text-yellow-800 transition-colors duration-200 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>Clear Old Wishlist</span>
                            </a>

                            <a href="admin.php?action=backup_cv_data" class="flex items-center space-x-2 p-2 rounded-lg text-blue-600 hover:bg-blue-50 hover:text-blue-800 transition-colors duration-200 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                                <span>Backup CV Data</span>
                            </a>

                            <a href="admin.php?action=clear_completed_todos" onclick="return confirm('Clear all completed todos? This cannot be undone.')" class="flex items-center space-x-2 p-2 rounded-lg text-green-600 hover:bg-green-50 hover:text-green-800 transition-colors duration-200 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Clear Completed Todos</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Management -->
            <div class="space-y-1">
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer Support</h3>

                <!-- Main Chat Management Menu -->
                <div class="relative">
                    <button
                        onclick="toggleChatSubmenu()"
                        class="w-full flex items-center justify-between space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200 <?= in_array($current_action, ['manage_chat', 'view_chat_room', 'chat_search', 'chat_stats']) ? 'bg-primary text-white' : '' ?>">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"></path>
                            </svg>
                            <span>Chat Management</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <?php if ($unread_chat_count > 0): ?>
                                <span id="chatBadgeMain" class="bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-5 h-5 flex items-center justify-center animate-pulse">
                                    <?php echo $unread_chat_count; ?>
                                </span>
                            <?php endif; ?>
                            <svg id="chatChevron" class="w-4 h-4 transition-transform duration-200 <?= in_array($current_action, ['manage_chat', 'view_chat_room', 'chat_search', 'chat_stats']) ? 'rotate-90' : '' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </button>

                    <!-- Chat Submenu -->
                    <div id="chatSubmenu" class="ml-8 mt-1 space-y-1 <?= in_array($current_action, ['manage_chat', 'view_chat_room', 'chat_search', 'chat_stats']) ? '' : 'hidden' ?>">
                        <a href="admin.php?action=manage_chat" class="flex items-center justify-between space-x-3 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm <?= $current_action == 'manage_chat' ? 'bg-gray-100 text-gray-800 font-medium' : '' ?>">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>Semua Chat</span>
                            </div>
                            <?php if ($unread_chat_count > 0): ?>
                                <span id="chatBadgeAll" class="bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-4 h-4 flex items-center justify-center">
                                    <?php echo $unread_chat_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>

                        <a href="admin.php?action=manage_chat&status=active" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span>Chat Aktif</span>
                        </a>

                        <a href="admin.php?action=manage_chat&status=closed" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                            <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                            <span>Chat Ditutup</span>
                        </a>

                        <div class="border-t border-gray-200 my-1"></div>

                        <a href="admin.php?action=chat_search" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm <?= $current_action == 'chat_search' ? 'bg-gray-100 text-gray-800 font-medium' : '' ?>">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Cari Pesan</span>
                        </a>

                        <a href="admin.php?action=chat_stats" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm <?= $current_action == 'chat_stats' ? 'bg-gray-100 text-gray-800 font-medium' : '' ?>">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span>Analitik Chat</span>
                        </a>

                        <div class="border-t border-gray-200 my-1"></div>

                        <button onclick="bulkCloseChatRooms()" class="w-full flex items-center space-x-2 p-2 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Tutup Semua</span>
                        </button>

                        <button onclick="cleanOldChatMessages()" class="w-full flex items-center space-x-2 p-2 rounded-lg text-orange-600 hover:bg-orange-50 hover:text-orange-700 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Bersihkan Lama</span>
                        </button>
                    </div>
                </div>
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

                <!-- System Tools Submenu -->
                <div class="relative">
                    <button
                        onclick="toggleSystemSubmenu()"
                        class="w-full flex items-center justify-between space-x-3 p-3 rounded-lg text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-.42-.643l-.142-.242.142-.242a6 6 0 00.42-.643l2.387-.477a2 2 0 001.022-.547M19.428 15.428a2 2 0 010 .544l-2.387.477a6 6 0 01-.42.643l-.142.242.142.242a6 6 0 01.42.643l2.387.477a2 2 0 001.022.547M19.428 15.428L17 13l2.428-2.428m0 0L17 8.142m2.428 2.428a2 2 0 000-.544l-2.387-.477a6 6 0 01-.42-.643L16.479 9l.142-.242a6 6 0 01.42-.643l2.387-.477a2 2 0 001.022-.547"></path>
                            </svg>
                            <span>Tools</span>
                        </div>
                        <svg id="systemChevron" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <div id="systemSubmenu" class="ml-8 mt-1 space-y-1 hidden">
                        <a href="admin.php?action=backup_database" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            <span>Backup Database</span>
                        </a>

                        <a href="admin.php?action=optimize_database" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>Optimasi Database</span>
                        </a>

                        <a href="admin.php?action=clean_logs" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Bersihkan Log</span>
                        </a>

                        <a href="admin.php?action=export_data" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Export Data</span>
                        </a>

                        <a href="admin.php?action=check_system_health" class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Cek Sistem</span>
                        </a>
                    </div>
                </div>
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

<style>
    /* Custom styles for chat notifications */
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .5;
        }
    }

    /* Smooth transitions for submenus */
    .submenu-enter {
        opacity: 0;
        transform: translateY(-10px);
    }

    .submenu-enter-active {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 200ms ease-in-out, transform 200ms ease-in-out;
    }
</style>

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

    // Toggle Chat Submenu
    function toggleChatSubmenu() {
        const submenu = document.getElementById('chatSubmenu');
        const chevron = document.getElementById('chatChevron');

        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            chevron.classList.add('rotate-90');
        } else {
            submenu.classList.add('hidden');
            chevron.classList.remove('rotate-90');
        }
    }

    // Toggle System Submenu
    function toggleSystemSubmenu() {
        const submenu = document.getElementById('systemSubmenu');
        const chevron = document.getElementById('systemChevron');

        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            chevron.classList.add('rotate-90');
        } else {
            submenu.classList.add('hidden');
            chevron.classList.remove('rotate-90');
        }
    }

    // Chat Management Functions
    function bulkCloseChatRooms() {
        if (confirm('Apakah Anda yakin ingin menutup semua chat room yang aktif? Aksi ini tidak dapat dibatalkan.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'admin.php?action=bulk_close_chat_rooms';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = '<?php echo $_SESSION['csrf_token'] ?? ''; ?>';
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        }
    }

    function cleanOldChatMessages() {
        const days = prompt('Masukkan jumlah hari (pesan yang lebih lama dari ini akan dihapus):', '30');
        if (days && !isNaN(days) && parseInt(days) > 0) {
            if (confirm(`Apakah Anda yakin ingin menghapus semua pesan chat yang lebih lama dari ${days} hari? Aksi ini tidak dapat dibatalkan.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin.php?action=clean_old_chat_messages';

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = 'csrf_token';
                csrfInput.value = '<?php echo $_SESSION['csrf_token'] ?? ''; ?>';
                form.appendChild(csrfInput);

                // Add days input
                const daysInput = document.createElement('input');
                daysInput.type = 'hidden';
                daysInput.name = 'days';
                daysInput.value = days;
                form.appendChild(daysInput);

                document.body.appendChild(form);
                form.submit();
            }
        }
    }

    // Update unread chat count periodically
    function updateChatUnreadCount() {
        fetch('admin.php?action=ajax_chat_unread_count')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badges = ['chatBadgeMain', 'chatBadgeAll'];

                    badges.forEach(badgeId => {
                        const badge = document.getElementById(badgeId);
                        if (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count;
                                badge.style.display = 'flex';
                                badge.classList.add('animate-pulse');
                            } else {
                                badge.style.display = 'none';
                                badge.classList.remove('animate-pulse');
                            }
                        }
                    });
                }
            })
            .catch(error => console.error('Error updating chat unread count:', error));
    }

    // Update chat count every 30 seconds
    setInterval(updateChatUnreadCount, 30000);

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-open chat submenu if on chat page
        const currentAction = '<?php echo $current_action; ?>';
        if (['manage_chat', 'view_chat_room', 'chat_search', 'chat_stats'].includes(currentAction)) {
            const submenu = document.getElementById('chatSubmenu');
            const chevron = document.getElementById('chatChevron');
            if (submenu && submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                chevron.classList.add('rotate-90');
            }
        }
    });

    function toggleFeaturesSubmenu() {
    const submenu = document.getElementById('featuresSubmenu');
    const chevron = document.getElementById('featuresChevron');
    
    if (submenu.classList.contains('hidden')) {
        submenu.classList.remove('hidden');
        chevron.classList.add('rotate-90');
    } else {
        submenu.classList.add('hidden');
        chevron.classList.remove('rotate-90');
    }
}
</script>