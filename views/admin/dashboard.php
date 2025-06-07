<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Campus Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        .sidebar-gradient {
            background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .stat-card {
            background: linear-gradient(135deg, var(--tw-gradient-from), var(--tw-gradient-to));
        }
        .animate-counter {
            animation: countUp 2s ease-out forwards;
        }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 sidebar-gradient shadow-xl">
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-black bg-opacity-20">
                <i class="fas fa-graduation-cap text-2xl text-white mr-3"></i>
                <span class="text-xl font-bold text-white">Campus Hub</span>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="admin.php?action=dashboard" class="flex items-center px-4 py-3 text-white bg-indigo-600 rounded-lg">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                <a href="admin.php?action=manage_users" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-users mr-3"></i>
                    Kelola Users
                </a>
                <a href="admin.php?action=manage_bootcamps" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-laptop-code mr-3"></i>
                    Kelola Bootcamps
                </a>
                <a href="admin.php?action=manage_categories" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-tags mr-3"></i>
                    Kelola Kategori
                </a>
                <a href="admin.php?action=manage_orders" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-shopping-cart mr-3"></i>
                    Kelola Orders
                </a>
                <a href="admin.php?action=manage_reviews" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-star mr-3"></i>
                    Kelola Reviews
                </a>
                <a href="admin.php?action=manage_forum" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-comments mr-3"></i>
                    Kelola Forum
                </a>
                <a href="admin.php?action=manage_settings" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
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
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
                        <p class="text-xs text-gray-400"><?php echo htmlspecialchars($_SESSION['admin_role']); ?></p>
                    </div>
                </div>
                <a href="admin.php?action=logout" class="mt-3 w-full flex items-center justify-center px-4 py-2 text-sm text-gray-300 hover:text-white bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
                        <p class="text-gray-600">Selamat datang di panel admin Campus Hub</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Terakhir login</p>
                            <p class="text-sm font-medium"><?php echo date('d M Y H:i'); ?></p>
                        </div>
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-bell text-indigo-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-6">
            <!-- Alerts -->
            <?php if (!empty($systemAlerts)): ?>
                <div class="mb-6 space-y-3">
                    <?php foreach ($systemAlerts as $alert): ?>
                        <div class="<?php echo $alert['type'] === 'warning' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : 'bg-blue-50 border-blue-200 text-blue-800'; ?> border px-4 py-3 rounded-lg flex items-center">
                            <i class="fas fa-<?php echo $alert['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle'; ?> mr-3"></i>
                            <?php echo htmlspecialchars($alert['message']); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Users -->
                <div class="stat-card from-blue-500 to-blue-600 text-white p-6 rounded-xl card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100">Total Users</p>
                            <p class="text-3xl font-bold animate-counter"><?php echo number_format($stats['total_users']); ?></p>
                            <p class="text-sm text-blue-100 mt-1">
                                +<?php echo $stats['new_users_month']; ?> bulan ini
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Bootcamps -->
                <div class="stat-card from-green-500 to-green-600 text-white p-6 rounded-xl card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100">Total Bootcamps</p>
                            <p class="text-3xl font-bold animate-counter"><?php echo number_format($stats['total_bootcamps']); ?></p>
                            <p class="text-sm text-green-100 mt-1">
                                <?php echo $stats['active_bootcamps']; ?> aktif
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-laptop-code text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="stat-card from-purple-500 to-purple-600 text-white p-6 rounded-xl card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100">Total Orders</p>
                            <p class="text-3xl font-bold animate-counter"><?php echo number_format($stats['total_orders']); ?></p>
                            <p class="text-sm text-purple-100 mt-1">
                                +<?php echo $stats['orders_month']; ?> bulan ini
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="stat-card from-orange-500 to-orange-600 text-white p-6 rounded-xl card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100">Total Revenue</p>
                            <p class="text-3xl font-bold animate-counter">Rp <?php echo number_format($stats['total_revenue']); ?></p>
                            <p class="text-sm text-orange-100 mt-1">
                                +Rp <?php echo number_format($stats['revenue_month']); ?> bulan ini
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Revenue Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trend</h3>
                    <canvas id="revenueChart" height="200"></canvas>
                </div>

                <!-- User Growth Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">User Growth</h3>
                    <canvas id="userChart" height="200"></canvas>
                </div>
            </div>

            <!-- Recent Activities and Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Activities -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($recentActivities)): ?>
                            <div class="space-y-4">
                                <?php foreach (array_slice($recentActivities, 0, 5) as $activity): ?>
                                    <div class="flex items-start space-x-3">
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-gray-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-900"><?php echo htmlspecialchars($activity['description']); ?></p>
                                            <div class="flex items-center mt-1 text-xs text-gray-500">
                                                <span><?php echo htmlspecialchars($activity['admin_name']); ?></span>
                                                <span class="mx-2">•</span>
                                                <span><?php echo date('d M Y H:i', strtotime($activity['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center py-4">Belum ada aktivitas</p>
                        <?php endif; ?>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="admin.php?action=activity_log" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                Lihat semua aktivitas →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <a href="admin.php?action=create_bootcamp" class="flex flex-col items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                                <i class="fas fa-plus text-2xl text-blue-600 mb-2"></i>
                                <span class="text-sm font-medium text-gray-900">Tambah Bootcamp</span>
                            </a>
                            <a href="admin.php?action=manage_users" class="flex flex-col items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                                <i class="fas fa-users text-2xl text-green-600 mb-2"></i>
                                <span class="text-sm font-medium text-gray-900">Kelola Users</span>
                            </a>
                            <a href="admin.php?action=manage_reviews" class="flex flex-col items-center p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors group">
                                <i class="fas fa-star text-2xl text-yellow-600 mb-2"></i>
                                <span class="text-sm font-medium text-gray-900">Review Pending</span>
                                <?php if ($stats['pending_reviews'] > 0): ?>
                                    <span class="mt-1 px-2 py-1 bg-red-500 text-white text-xs rounded-full"><?php echo $stats['pending_reviews']; ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="admin.php?action=backup_database" class="flex flex-col items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                                <i class="fas fa-database text-2xl text-purple-600 mb-2"></i>
                                <span class="text-sm font-medium text-gray-900">Backup DB</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Status</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Server Status</p>
                            <p class="text-xs text-gray-600">Online</p>
                        </div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Database</p>
                            <p class="text-xs text-gray-600">Connected</p>
                        </div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Storage</p>
                            <p class="text-xs text-gray-600">85% Used</p>
                        </div>
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Last Backup</p>
                            <p class="text-xs text-gray-600"><?php echo date('d M Y'); ?></p>
                        </div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Sample data for charts
        const revenueData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue (Juta Rp)',
                data: [45, 52, 48, 61, 55, 67],
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true
            }]
        };

        const userData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'New Users',
                data: [120, 190, 150, 220, 180, 250],
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
            }]
        };

        const chartConfig = {
            type: 'line',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        };

        // Initialize charts
        new Chart(document.getElementById('revenueChart'), {
            ...chartConfig,
            data: revenueData
        });

        new Chart(document.getElementById('userChart'), {
            ...chartConfig,
            data: userData
        });

        // Animate numbers on load
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.animate-counter');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
                const increment = target / 100;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target.toLocaleString();
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current).toLocaleString();
                    }
                }, 20);
            });
        });
    </script>
</body>
</html>