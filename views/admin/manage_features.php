<?php
// views/admin/manage_features.php - New file to create
$page_title = 'Features Management - Code Camp Admin';

// Get data from controller
$wishlistStats = $wishlistStats ?? [];
$cvStats = $cvStats ?? [];
$todoStats = $todoStats ?? [];
$recentWishlists = $recentWishlists ?? [];
$recentCVs = $recentCVs ?? [];
$recentTodos = $recentTodos ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1E40AF',
                    }
                }
            }
        }
    </script>
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .stat-card {
            transition: transform 0.2s ease-in-out;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="admin.php?action=dashboard" class="flex items-center space-x-2 text-gray-700 hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali ke Dashboard</span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">
                        Admin: <strong><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Unknown'); ?></strong>
                    </span>
                    <a href="admin.php?action=logout" class="text-red-600 hover:text-red-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Features Management</h1>
            <p class="text-gray-600 mt-2">Kelola fitur Wishlist, CV Maker, dan Todo List</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 fade-in" role="alert">
                <span class="block sm:inline"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 fade-in" role="alert">
                <span class="block sm:inline"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>

        <!-- Statistics Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Wishlist Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Wishlist</h3>
                        <p class="text-3xl font-bold text-blue-600 mt-2">
                            <?php echo number_format($wishlistStats['total'] ?? 0); ?>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">Total Items</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-600">↗ <?php echo $wishlistStats['today'] ?? 0; ?></span>
                    <span class="text-gray-600 ml-2">hari ini</span>
                </div>
            </div>

            <!-- CV Maker Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">CV Maker</h3>
                        <p class="text-3xl font-bold text-green-600 mt-2">
                            <?php echo number_format($cvStats['total'] ?? 0); ?>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">CV Dibuat</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-600">↗ <?php echo $cvStats['today'] ?? 0; ?></span>
                    <span class="text-gray-600 ml-2">hari ini</span>
                </div>
            </div>

            <!-- Todo List Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Todo List</h3>
                        <p class="text-3xl font-bold text-purple-600 mt-2">
                            <?php echo number_format($todoStats['total'] ?? 0); ?>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">Total Tasks</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-purple-600"><?php echo $todoStats['completed'] ?? 0; ?></span>
                    <span class="text-gray-600 ml-2">selesai</span>
                    <span class="text-gray-400 mx-2">|</span>
                    <span class="text-orange-600"><?php echo $todoStats['pending'] ?? 0; ?></span>
                    <span class="text-gray-600 ml-1">pending</span>
                </div>
            </div>
        </div>

        <!-- Feature Management Tabs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button onclick="showTab('wishlist')" class="tab-button active py-4 px-1 border-b-2 border-primary text-primary font-medium text-sm" data-tab="wishlist">
                        Wishlist Management
                    </button>
                    <button onclick="showTab('cvmaker')" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-tab="cvmaker">
                        CV Maker Management
                    </button>
                    <button onclick="showTab('todolist')" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-tab="todolist">
                        Todo List Management
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Wishlist Tab -->
                <div id="wishlist-tab" class="tab-content">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Kelola Wishlist</h2>
                        <div class="flex space-x-3">
                            <button onclick="exportData('wishlist')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export Data
                            </button>
                            <button onclick="clearOldWishlists()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Clear Old Data
                            </button>
                        </div>
                    </div>

                    <!-- Recent Wishlists -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Recent Wishlist Activities</h3>
                        <div class="space-y-3">
                            <?php if (empty($recentWishlists)): ?>
                                <p class="text-gray-500 text-center py-4">Tidak ada aktivitas wishlist terbaru</p>
                            <?php else: ?>
                                <?php foreach ($recentWishlists as $wishlist): ?>
                                    <div class="flex items-center justify-between bg-white rounded-lg p-4 shadow-sm">
                                        <div class="flex items-center space-x-4">
                                            <div class="bg-blue-100 rounded-full p-2">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($wishlist['user_name'] ?? 'Unknown'); ?></p>
                                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($wishlist['bootcamp_title'] ?? 'Unknown Bootcamp'); ?></p>
                                                <p class="text-xs text-gray-500"><?php echo date('d M Y H:i', strtotime($wishlist['created_at'] ?? 'now')); ?></p>
                                            </div>
                                        </div>
                                        <button onclick="removeWishlist(<?php echo $wishlist['id'] ?? 0; ?>)" class="text-red-600 hover:text-red-800 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- CV Maker Tab -->
                <div id="cvmaker-tab" class="tab-content hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Kelola CV Maker</h2>
                        <div class="flex space-x-3">
                            <button onclick="exportData('cv')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export CVs
                            </button>
                            <button onclick="backupCVData()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                                Backup Data
                            </button>
                        </div>
                    </div>

                    <!-- Recent CVs -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Recent CV Activities</h3>
                        <div class="space-y-3">
                            <?php if (empty($recentCVs)): ?>
                                <p class="text-gray-500 text-center py-4">Tidak ada aktivitas CV terbaru</p>
                            <?php else: ?>
                                <?php foreach ($recentCVs as $cv): ?>
                                    <div class="flex items-center justify-between bg-white rounded-lg p-4 shadow-sm">
                                        <div class="flex items-center space-x-4">
                                            <div class="bg-green-100 rounded-full p-2">
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($cv['user_name'] ?? 'Unknown'); ?></p>
                                                <p class="text-sm text-gray-600">CV dibuat/diupdate</p>
                                                <p class="text-xs text-gray-500"><?php echo date('d M Y H:i', strtotime($cv['updated_at'] ?? $cv['created_at'] ?? 'now')); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="viewCV(<?php echo $cv['user_id'] ?? 0; ?>)" class="text-blue-600 hover:text-blue-800 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            <button onclick="deleteCV(<?php echo $cv['user_id'] ?? 0; ?>)" class="text-red-600 hover:text-red-800 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Todo List Tab -->
                <div id="todolist-tab" class="tab-content hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Kelola Todo List</h2>
                        <div class="flex space-x-3">
                            <button onclick="exportData('todo')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export Tasks
                            </button>
                            <button onclick="clearCompletedTodos()" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Clear Completed
                            </button>
                        </div>
                    </div>

                    <!-- Recent Todos -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Recent Todo Activities</h3>
                        <div class="space-y-3">
                            <?php if (empty($recentTodos)): ?>
                                <p class="text-gray-500 text-center py-4">Tidak ada aktivitas todo terbaru</p>
                            <?php else: ?>
                                <?php foreach ($recentTodos as $todo): ?>
                                    <div class="flex items-center justify-between bg-white rounded-lg p-4 shadow-sm">
                                        <div class="flex items-center space-x-4">
                                            <div class="bg-purple-100 rounded-full p-2">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($todo['user_name'] ?? 'Unknown'); ?></p>
                                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($todo['title'] ?? 'Unknown Task'); ?></p>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span class="px-2 py-1 text-xs rounded-full <?php 
                                                        echo match($todo['status'] ?? 'pending') {
                                                            'completed' => 'bg-green-100 text-green-800',
                                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                                            default => 'bg-gray-100 text-gray-800'
                                                        };
                                                    ?>">
                                                        <?php echo ucfirst($todo['status'] ?? 'pending'); ?>
                                                    </span>
                                                    <span class="px-2 py-1 text-xs rounded-full <?php 
                                                        echo match($todo['priority'] ?? 'low') {
                                                            'high' => 'bg-red-100 text-red-800',
                                                            'medium' => 'bg-yellow-100 text-yellow-800',
                                                            default => 'bg-gray-100 text-gray-800'
                                                        };
                                                    ?>">
                                                        <?php echo ucfirst($todo['priority'] ?? 'low'); ?>
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500"><?php echo date('d M Y H:i', strtotime($todo['created_at'] ?? 'now')); ?></p>
                                            </div>
                                        </div>
                                        <button onclick="deleteTodo(<?php echo $todo['id'] ?? 0; ?>)" class="text-red-600 hover:text-red-800 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Tab Management
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.add('hidden'));

            // Remove active class from all buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('border-primary', 'text-primary');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.remove('hidden');

            // Add active class to selected button
            const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
            activeButton.classList.remove('border-transparent', 'text-gray-500');
            activeButton.classList.add('border-primary', 'text-primary');
        }

        // Export Data Functions
        function exportData(type) {
            if (confirm(`Export ${type} data to CSV?`)) {
                window.location.href = `admin.php?action=export_features&type=${type}`;
            }
        }

        // Wishlist Functions
        function clearOldWishlists() {
            if (confirm('Hapus semua wishlist yang lebih dari 30 hari? Aksi ini tidak dapat dibatalkan.')) {
                window.location.href = 'admin.php?action=clear_old_wishlists';
            }
        }

        function removeWishlist(id) {
            if (confirm('Hapus item wishlist ini?')) {
                window.location.href = `admin.php?action=remove_wishlist&id=${id}`;
            }
        }

        // CV Functions
        function backupCVData() {
            if (confirm('Backup semua data CV?')) {
                window.location.href = 'admin.php?action=backup_cv_data';
            }
        }

        function viewCV(userId) {
            window.open(`admin.php?action=view_cv&user_id=${userId}`, '_blank');
        }

        function deleteCV(userId) {
            if (confirm('Hapus CV ini? Aksi ini tidak dapat dibatalkan.')) {
                window.location.href = `admin.php?action=delete_cv&user_id=${userId}`;
            }
        }

        // Todo Functions
        function clearCompletedTodos() {
            if (confirm('Hapus semua todo yang sudah selesai?')) {
                window.location.href = 'admin.php?action=clear_completed_todos';
            }
        }

        function deleteTodo(id) {
            if (confirm('Hapus todo ini?')) {
                window.location.href = `admin.php?action=delete_todo&id=${id}`;
            }
        }

        // Auto refresh every 30 seconds
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>