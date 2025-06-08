<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Detail - Code Camp Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#1e40af',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 overflow-y-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 p-4 lg:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 ml-12 lg:ml-0">
                        <a 
                            href="admin.php?action=dashboard" 
                            class="text-gray-500 hover:text-primary transition-colors duration-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Statistik Detail</h1>
                            <p class="text-gray-600 mt-1">Analisis mendalam platform Code Camp</p>
                        </div>
                    </div>
                    
                    <!-- Export Options -->
                    <div class="flex items-center space-x-2">
                        <div class="relative">
                            <button 
                                onclick="toggleExportMenu()" 
                                class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export Data
                            </button>
                            <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                <a href="admin.php?action=export_data&type=users" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export Users</a>
                                <a href="admin.php?action=export_data&type=bootcamps" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export Bootcamps</a>
                                <a href="admin.php?action=export_data&type=orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export Orders</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-4 lg:p-6 space-y-6">
                <!-- Overview Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Users Stats -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Users</p>
                                <p class="text-3xl font-bold text-gray-900"><?= number_format($detailedStats['total_users'] ?? 0) ?></p>
                                <p class="text-sm text-green-600">+<?= number_format($detailedStats['new_users_month'] ?? 0) ?> bulan ini</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Stats -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                                <p class="text-3xl font-bold text-gray-900">Rp <?= number_format($detailedStats['total_revenue'] ?? 0, 0, ',', '.') ?></p>
                                <p class="text-sm text-green-600">+Rp <?= number_format($detailedStats['revenue_month'] ?? 0, 0, ',', '.') ?> bulan ini</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Avg Order Value -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Avg Order Value</p>
                                <p class="text-3xl font-bold text-gray-900">Rp <?= number_format($detailedStats['avg_order_value'] ?? 0, 0, ',', '.') ?></p>
                                <p class="text-sm text-gray-500">per transaksi</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Enrollments -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Enrollments</p>
                                <p class="text-3xl font-bold text-gray-900"><?= number_format($detailedStats['total_enrollments'] ?? 0) ?></p>
                                <p class="text-sm text-gray-500">peserta terdaftar</p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Revenue Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Trend Revenue</h3>
                            <p class="text-gray-600 mt-1">Perkembangan pendapatan dalam 6 bulan terakhir</p>
                        </div>
                        <div class="p-6">
                            <canvas id="revenueChart" height="300"></canvas>
                        </div>
                    </div>

                    <!-- User Growth Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Pertumbuhan User</h3>
                            <p class="text-gray-600 mt-1">Registrasi user baru dalam 6 bulan terakhir</p>
                        </div>
                        <div class="p-6">
                            <canvas id="userChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top Bootcamps -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Top Bootcamps by Revenue</h3>
                        <p class="text-gray-600 mt-1">Bootcamp dengan pendapatan tertinggi</p>
                    </div>
                    <div class="p-6">
                        <?php if (empty($detailedStats['top_bootcamps'])): ?>
                            <div class="text-center py-8">
                                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500">Belum ada data bootcamp</p>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Rank</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Bootcamp</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Enrollments</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Revenue</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Progress</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $maxRevenue = max(array_column($detailedStats['top_bootcamps'], 'revenue'));
                                        foreach ($detailedStats['top_bootcamps'] as $index => $bootcamp): 
                                            $percentage = $maxRevenue > 0 ? ($bootcamp['revenue'] / $maxRevenue) * 100 : 0;
                                        ?>
                                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                                <td class="py-4 px-4">
                                                    <div class="flex items-center justify-center w-8 h-8 rounded-full <?= $index < 3 ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' ?> font-semibold text-sm">
                                                        <?= $index + 1 ?>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <div class="font-medium text-gray-900"><?= htmlspecialchars($bootcamp['title']) ?></div>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <span class="text-gray-700"><?= number_format($bootcamp['enrollments'] ?? 0) ?></span>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <span class="font-semibold text-gray-900">Rp <?= number_format($bootcamp['revenue'] ?? 0, 0, ',', '.') ?></span>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="bg-primary h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                                                    </div>
                                                    <span class="text-xs text-gray-500 mt-1"><?= number_format($percentage, 1) ?>%</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Conversion Rate -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="text-center">
                            <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Conversion Rate</h3>
                            <p class="text-3xl font-bold text-green-600">
                                <?php 
                                $conversionRate = ($detailedStats['total_users'] ?? 0) > 0 ? 
                                    (($detailedStats['total_orders'] ?? 0) / $detailedStats['total_users']) * 100 : 0;
                                echo number_format($conversionRate, 1);
                                ?>%
                            </p>
                            <p class="text-sm text-gray-500 mt-1">user menjadi customer</p>
                        </div>
                    </div>

                    <!-- Active Bootcamps -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="text-center">
                            <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Bootcamp Aktif</h3>
                            <p class="text-3xl font-bold text-blue-600"><?= number_format($detailedStats['active_bootcamps'] ?? 0) ?></p>
                            <p class="text-sm text-gray-500 mt-1">dari <?= number_format($detailedStats['total_bootcamps'] ?? 0) ?> total</p>
                        </div>
                    </div>

                    <!-- Pending Reviews -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="text-center">
                            <div class="bg-yellow-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Review Pending</h3>
                            <p class="text-3xl font-bold text-yellow-600"><?= number_format($detailedStats['pending_reviews'] ?? 0) ?></p>
                            <p class="text-sm text-gray-500 mt-1">menunggu moderasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Toggle export menu
        function toggleExportMenu() {
            const menu = document.getElementById('exportMenu');
            menu.classList.toggle('hidden');
        }

        // Close export menu when clicking outside
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('exportMenu');
            const button = e.target.closest('button');
            if (!button || !button.onclick) {
                menu.classList.add('hidden');
            }
        });

        // Sample data for charts
        const monthlyData = {
            labels: ['Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            revenue: [45000000, 52000000, 48000000, 61000000, 55000000, 67000000],
            users: [120, 145, 132, 178, 156, 189]
        };

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: monthlyData.labels,
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: monthlyData.revenue,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(0) + 'M';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // User Growth Chart
        const userCtx = document.getElementById('userChart').getContext('2d');
        new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.labels,
                datasets: [{
                    label: 'New Users',
                    data: monthlyData.users,
                    backgroundColor: '#10b981',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>