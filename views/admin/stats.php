<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Admin Campus Hub</title>
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
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .metric-card {
            background: linear-gradient(135deg, var(--tw-gradient-from), var(--tw-gradient-to));
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include_once 'views/admin/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Analytics & Statistics</h1>
                        <p class="text-gray-600">Analisis mendalam performa platform Campus Hub</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <select id="dateRange" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="7">7 Hari Terakhir</option>
                            <option value="30" selected>30 Hari Terakhir</option>
                            <option value="90">90 Hari Terakhir</option>
                            <option value="365">1 Tahun Terakhir</option>
                        </select>
                        <button onclick="exportReport()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Export Report
                        </button>
                        <button onclick="refreshData()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6">
            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="metric-card from-blue-500 to-blue-600 text-white p-6 rounded-xl card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100">Total Revenue</p>
                            <p class="text-3xl font-bold">Rp <?php echo number_format($detailedStats['total_revenue'] ?? 0); ?></p>
                            <p class="text-sm text-blue-100 mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +12.5% vs bulan lalu
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="metric-card from-green-500 to-green-600 text-white p-6 rounded-xl card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100">New Users</p>
                            <p class="text-3xl font-bold"><?php echo number_format($detailedStats['new_users_month'] ?? 0); ?></p>
                            <p class="text-sm text-green-100 mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +8.2% vs bulan lalu
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="metric-card from-purple-500 to-purple-600 text-white p-6 rounded-xl card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100">Course Enrollments</p>
                            <p class="text-3xl font-bold"><?php echo number_format($detailedStats['total_enrollments'] ?? 0); ?></p>
                            <p class="text-sm text-purple-100 mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +15.3% vs bulan lalu
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="metric-card from-orange-500 to-orange-600 text-white p-6 rounded-xl card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100">Avg. Order Value</p>
                            <p class="text-3xl font-bold">Rp <?php echo number_format($detailedStats['avg_order_value'] ?? 0); ?></p>
                            <p class="text-sm text-orange-100 mt-1">
                                <i class="fas fa-arrow-down mr-1"></i>
                                -2.1% vs bulan lalu
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Revenue Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Revenue Trend</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">Periode:</span>
                            <select class="text-sm border border-gray-300 rounded px-2 py-1">
                                <option>30 Hari</option>
                                <option>90 Hari</option>
                                <option>1 Tahun</option>
                            </select>
                        </div>
                    </div>
                    <canvas id="revenueChart" height="300"></canvas>
                </div>

                <!-- User Growth Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">User Growth</h3>
                        <div class="text-sm text-green-600 font-medium">↗ +156 users this month</div>
                    </div>
                    <canvas id="userGrowthChart" height="300"></canvas>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Bootcamp Performance -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Top Performing Bootcamps</h3>
                    <canvas id="bootcampChart" height="300"></canvas>
                </div>

                <!-- Payment Methods -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Payment Methods Distribution</h3>
                    <canvas id="paymentChart" height="300"></canvas>
                </div>
            </div>

            <!-- Detailed Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Top Bootcamps Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card-hover">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Top Bootcamps by Revenue</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bootcamp</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrollments</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (!empty($detailedStats['top_bootcamps'])): ?>
                                    <?php foreach ($detailedStats['top_bootcamps'] as $index => $bootcamp): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-xs font-medium text-blue-600 mr-3">
                                                        <?php echo $index + 1; ?>
                                                    </div>
                                                    <div class="text-sm font-medium text-gray-900 truncate max-w-xs">
                                                        <?php echo htmlspecialchars($bootcamp['title']); ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo number_format($bootcamp['enrollments']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                Rp <?php echo number_format($bootcamp['revenue'] ?? 0); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">No data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card-hover">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4 max-h-80 overflow-y-auto">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-blue-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900">User mendaftar bootcamp "Full Stack Development"</p>
                                        <p class="text-xs text-gray-500"><?php echo rand(1, 60); ?> menit yang lalu</p>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Conversion Funnel -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Conversion Funnel</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Website Visitors</span>
                            <span class="text-sm font-medium">10,245</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Bootcamp Views</span>
                            <span class="text-sm font-medium">3,456</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 34%"></div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Add to Cart</span>
                            <span class="text-sm font-medium">892</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 9%"></div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Completed Purchase</span>
                            <span class="text-sm font-medium">234</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: 2.3%"></div>
                        </div>
                    </div>
                </div>

                <!-- User Demographics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">User Demographics</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Jakarta</span>
                                <span>45%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 45%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Surabaya</span>
                                <span>20%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 20%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Bandung</span>
                                <span>15%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: 15%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Lainnya</span>
                                <span>20%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: 20%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Platform Health -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Platform Health</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Server Uptime</span>
                            <span class="text-sm font-medium text-green-600">99.9%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Avg. Page Load</span>
                            <span class="text-sm font-medium text-blue-600">1.2s</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Error Rate</span>
                            <span class="text-sm font-medium text-green-600">0.02%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Database Queries</span>
                            <span class="text-sm font-medium text-yellow-600">45ms avg</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Storage Used</span>
                            <span class="text-sm font-medium text-orange-600">2.3GB / 10GB</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue (Juta Rp)',
                    data: [45, 52, 48, 61, 55, 67, 73, 69, 82, 91, 87, 95],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
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
        });

        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        const userGrowthChart = new Chart(userGrowthCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'New Users',
                    data: [120, 190, 150, 220, 180, 250, 300, 280, 350, 420, 380, 450],
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
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
        });

        // Bootcamp Performance Chart
        const bootcampCtx = document.getElementById('bootcampChart').getContext('2d');
        const bootcampChart = new Chart(bootcampCtx, {
            type: 'doughnut',
            data: {
                labels: ['Full Stack Development', 'Data Science', 'Mobile Development', 'UI/UX Design', 'Others'],
                datasets: [{
                    data: [35, 25, 20, 15, 5],
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Payment Methods Chart
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        const paymentChart = new Chart(paymentCtx, {
            type: 'pie',
            data: {
                labels: ['Credit Card', 'Bank Transfer', 'E-Wallet', 'Cash'],
                datasets: [{
                    data: [45, 30, 20, 5],
                    backgroundColor: [
                        '#6366F1',
                        '#EC4899',
                        '#14B8A6',
                        '#F97316'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Functions
        function exportReport() {
            alert('Fitur export report akan segera tersedia');
        }

        function refreshData() {
            location.reload();
        }

        // Date range change handler
        document.getElementById('dateRange').addEventListener('change', function() {
            const range = this.value;
            console.log('Date range changed to:', range);
            // Implement data refresh based on date range
        });
    </script>
</body>
</html>