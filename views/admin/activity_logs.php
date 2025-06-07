<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log - Admin Campus Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-gradient {
            background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
        }
        .table-hover:hover {
            background-color: #f8fafc;
        }
        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .activity-login { background-color: #dcfce7; color: #16a34a; }
        .activity-logout { background-color: #fee2e2; color: #dc2626; }
        .activity-create { background-color: #dbeafe; color: #2563eb; }
        .activity-update { background-color: #fef3c7; color: #d97706; }
        .activity-delete { background-color: #fecaca; color: #ef4444; }
        .activity-default { background-color: #f3f4f6; color: #6b7280; }
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
                        <h1 class="text-2xl font-bold text-gray-900">Activity Log</h1>
                        <p class="text-gray-600">Riwayat aktivitas admin dan perubahan sistem</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="exportLog()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Export Log
                        </button>
                        <button onclick="clearOldLogs()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Clear Old Logs
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6">
            <!-- Alerts -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-list text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Activities</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo number_format($totalActivities); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-calendar-day text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Today</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo count(array_filter($activities, function($a) { 
                                    return date('Y-m-d', strtotime($a['created_at'])) === date('Y-m-d'); 
                                })); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-users text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Active Admins</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo count(array_unique(array_column($activities, 'admin_id'))); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Critical Actions</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo count(array_filter($activities, function($a) { 
                                    return in_array($a['activity_type'], ['delete_user', 'delete_bootcamp', 'system_change']); 
                                })); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="admin.php" class="flex flex-wrap items-center gap-4">
                    <input type="hidden" name="action" value="activity_log">
                    
                    <!-- Search -->
                    <div class="flex-1 min-w-64">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                   name="search" 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>"
                                   placeholder="Cari aktivitas, admin, atau deskripsi..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <!-- Activity Type Filter -->
                    <div>
                        <select name="activity_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Aktivitas</option>
                            <option value="login" <?php echo ($activity_type ?? '') === 'login' ? 'selected' : ''; ?>>Login</option>
                            <option value="logout" <?php echo ($activity_type ?? '') === 'logout' ? 'selected' : ''; ?>>Logout</option>
                            <option value="create_user" <?php echo ($activity_type ?? '') === 'create_user' ? 'selected' : ''; ?>>Create User</option>
                            <option value="update_user" <?php echo ($activity_type ?? '') === 'update_user' ? 'selected' : ''; ?>>Update User</option>
                            <option value="delete_user" <?php echo ($activity_type ?? '') === 'delete_user' ? 'selected' : ''; ?>>Delete User</option>
                            <option value="create_bootcamp" <?php echo ($activity_type ?? '') === 'create_bootcamp' ? 'selected' : ''; ?>>Create Bootcamp</option>
                            <option value="update_bootcamp" <?php echo ($activity_type ?? '') === 'update_bootcamp' ? 'selected' : ''; ?>>Update Bootcamp</option>
                            <option value="delete_bootcamp" <?php echo ($activity_type ?? '') === 'delete_bootcamp' ? 'selected' : ''; ?>>Delete Bootcamp</option>
                        </select>
                    </div>
                    
                    <!-- Admin Filter -->
                    <div>
                        <select name="admin_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Admin</option>
                            <?php 
                            $uniqueAdmins = array_unique(array_filter(array_map(function($a) {
                                return $a['admin_name'] ? ['id' => $a['admin_id'], 'name' => $a['admin_name']] : null;
                            }, $activities)), function($a) { return $a !== null; });
                            ?>
                            <?php foreach ($uniqueAdmins as $admin): ?>
                                <option value="<?php echo $admin['id']; ?>" <?php echo ($admin_id ?? '') == $admin['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($admin['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Date Range -->
                    <div>
                        <input type="date" 
                               name="date_from" 
                               value="<?php echo htmlspecialchars($date_from ?? ''); ?>"
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <input type="date" 
                               name="date_to" 
                               value="<?php echo htmlspecialchars($date_to ?? ''); ?>"
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- Search Button -->
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    
                    <!-- Reset Button -->
                    <a href="admin.php?action=activity_log" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </a>
                </form>
            </div>

            <!-- Activity Log Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Activity Timeline</h3>
                    <span class="text-sm text-gray-600">Showing <?php echo count($activities); ?> of <?php echo $totalActivities; ?> activities</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($activities)): ?>
                                <?php foreach ($activities as $activity): ?>
                                    <tr class="table-hover">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="activity-icon activity-<?php echo $activity['activity_type']; ?>">
                                                    <i class="fas fa-<?php 
                                                        $icons = [
                                                            'login' => 'sign-in-alt',
                                                            'logout' => 'sign-out-alt',
                                                            'create_user' => 'user-plus',
                                                            'update_user' => 'user-edit',
                                                            'delete_user' => 'user-minus',
                                                            'create_bootcamp' => 'plus-circle',
                                                            'update_bootcamp' => 'edit',
                                                            'delete_bootcamp' => 'trash',
                                                            'backup_database' => 'database',
                                                            'clean_logs' => 'broom'
                                                        ];
                                                        echo $icons[$activity['activity_type']] ?? 'circle';
                                                    ?>"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo ucfirst(str_replace('_', ' ', $activity['activity_type'])); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">ID: #<?php echo $activity['id']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-user text-gray-600 text-xs"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($activity['admin_name'] ?? 'Unknown'); ?>
                                                    </div>
                                                    <div class="text-xs text-gray-500">ID: <?php echo $activity['admin_id']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs">
                                                <?php echo htmlspecialchars($activity['description']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <i class="fas fa-globe text-gray-400 mr-2"></i>
                                                <?php echo htmlspecialchars($activity['ip_address'] ?? 'Unknown'); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div><?php echo date('d M Y', strtotime($activity['created_at'])); ?></div>
                                            <div class="text-xs"><?php echo date('H:i:s', strtotime($activity['created_at'])); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="showActivityDetails(<?php echo $activity['id']; ?>)" 
                                                    class="text-blue-600 hover:text-blue-900 transition-colors" 
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <i class="fas fa-history text-4xl mb-4"></i>
                                            <p class="text-lg font-medium">Tidak ada aktivitas ditemukan</p>
                                            <p class="mt-2">Coba ubah filter pencarian Anda</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="bg-white px-6 py-3 border-t border-gray-200 flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <?php if ($page > 1): ?>
                                <a href="?action=activity_log&page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search ?? ''); ?>&activity_type=<?php echo urlencode($activity_type ?? ''); ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?action=activity_log&page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search ?? ''); ?>&activity_type=<?php echo urlencode($activity_type ?? ''); ?>" 
                                   class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing page <span class="font-medium"><?php echo $page; ?></span> of <span class="font-medium"><?php echo $totalPages; ?></span>
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <a href="?action=activity_log&page=<?php echo $i; ?>&search=<?php echo urlencode($search ?? ''); ?>&activity_type=<?php echo urlencode($activity_type ?? ''); ?>" 
                                           class="<?php echo $i === $page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                </nav>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Activity Details Modal -->
    <div id="activityModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-2/3 max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Activity Details</h3>
                    <button onclick="closeActivityModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="activityContent" class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-900">Activity Type:</label>
                                <p class="text-sm text-gray-600" id="modalActivityType">-</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-900">Admin:</label>
                                <p class="text-sm text-gray-600" id="modalAdminName">-</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-900">IP Address:</label>
                                <p class="text-sm text-gray-600" id="modalIPAddress">-</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-900">Timestamp:</label>
                                <p class="text-sm text-gray-600" id="modalTimestamp">-</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="text-sm font-medium text-gray-900">Description:</label>
                            <p class="text-sm text-gray-600 mt-1" id="modalDescription">-</p>
                        </div>
                        <div class="mt-4">
                            <label class="text-sm font-medium text-gray-900">User Agent:</label>
                            <p class="text-sm text-gray-600 mt-1" id="modalUserAgent">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button onclick="closeActivityModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showActivityDetails(activityId) {
            // In a real implementation, you would fetch details via AJAX
            // For now, we'll show a placeholder
            document.getElementById('modalActivityType').textContent = 'Login';
            document.getElementById('modalAdminName').textContent = 'Admin User';
            document.getElementById('modalIPAddress').textContent = '192.168.1.1';
            document.getElementById('modalTimestamp').textContent = new Date().toLocaleString();
            document.getElementById('modalDescription').textContent = 'Admin successfully logged into the system';
            document.getElementById('modalUserAgent').textContent = navigator.userAgent;
            
            document.getElementById('activityModal').classList.remove('hidden');
        }

        function closeActivityModal() {
            document.getElementById('activityModal').classList.add('hidden');
        }

        function exportLog() {
            const params = new URLSearchParams(window.location.search);
            params.set('action', 'export_activity_log');
            window.open('admin.php?' + params.toString(), '_blank');
        }

        function clearOldLogs() {
            if (confirm('Apakah Anda yakin ingin menghapus log aktivitas yang lebih dari 6 bulan? Tindakan ini tidak dapat dibatalkan.')) {
                window.location.href = 'admin.php?action=clean_logs';
            }
        }

        // Close modal when clicking outside
        document.getElementById('activityModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeActivityModal();
            }
        });

        // Auto-refresh every 30 seconds
        setInterval(function() {
            // Check if there are new activities
            console.log('Checking for new activities...');
        }, 30000);
    </script>
</body>
</html>