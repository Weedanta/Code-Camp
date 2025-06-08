<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas - Code Camp Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="ml-12 lg:ml-0">
                        <h1 class="text-2xl font-bold text-gray-800">Log Aktivitas</h1>
                        <p class="text-gray-600 mt-1">Riwayat aktivitas admin di sistem</p>
                    </div>
                    <div class="flex items-center space-x-4 mt-4 lg:mt-0">
                        <span class="bg-primary text-white px-3 py-1 rounded-full text-sm">
                            Total: <?= number_format($totalActivities) ?> aktivitas
                        </span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-4 lg:p-6 space-y-6">
                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <form method="GET" action="admin.php" class="space-y-4">
                        <input type="hidden" name="action" value="activity_log">
                        
                        <!-- Filter Row 1 -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Aktivitas</label>
                                <input 
                                    type="text" 
                                    id="search"
                                    name="search" 
                                    placeholder="Cari deskripsi atau admin..." 
                                    value="<?= htmlspecialchars($search ?? '') ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary text-sm"
                                >
                            </div>

                            <!-- Activity Type -->
                            <div>
                                <label for="activity_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Aktivitas</label>
                                <select 
                                    id="activity_type"
                                    name="activity_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary text-sm"
                                >
                                    <option value="">Semua Jenis</option>
                                    <option value="login" <?= ($activity_type ?? '') == 'login' ? 'selected' : '' ?>>Login</option>
                                    <option value="logout" <?= ($activity_type ?? '') == 'logout' ? 'selected' : '' ?>>Logout</option>
                                    <option value="create_bootcamp" <?= ($activity_type ?? '') == 'create_bootcamp' ? 'selected' : '' ?>>Buat Bootcamp</option>
                                    <option value="update_bootcamp" <?= ($activity_type ?? '') == 'update_bootcamp' ? 'selected' : '' ?>>Update Bootcamp</option>
                                    <option value="delete_bootcamp" <?= ($activity_type ?? '') == 'delete_bootcamp' ? 'selected' : '' ?>>Hapus Bootcamp</option>
                                    <option value="create_user" <?= ($activity_type ?? '') == 'create_user' ? 'selected' : '' ?>>Buat User</option>
                                    <option value="update_user" <?= ($activity_type ?? '') == 'update_user' ? 'selected' : '' ?>>Update User</option>
                                    <option value="delete_user" <?= ($activity_type ?? '') == 'delete_user' ? 'selected' : '' ?>>Hapus User</option>
                                    <option value="update_settings" <?= ($activity_type ?? '') == 'update_settings' ? 'selected' : '' ?>>Update Settings</option>
                                    <option value="backup_database" <?= ($activity_type ?? '') == 'backup_database' ? 'selected' : '' ?>>Backup Database</option>
                                </select>
                            </div>

                            <!-- Admin Filter -->
                            <div>
                                <label for="admin_id" class="block text-sm font-medium text-gray-700 mb-1">Admin</label>
                                <select 
                                    id="admin_id"
                                    name="admin_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary text-sm"
                                >
                                    <option value="">Semua Admin</option>
                                    <!-- Add admin options here based on your admin list -->
                                    <option value="<?= $_SESSION['admin_id'] ?>" <?= ($admin_id ?? 0) == $_SESSION['admin_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($_SESSION['admin_name']) ?> (Saya)
                                    </option>
                                </select>
                            </div>

                            <!-- Search Button -->
                            <div class="flex items-end">
                                <button 
                                    type="submit" 
                                    class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition-colors duration-200 text-sm"
                                >
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Cari
                                </button>
                            </div>
                        </div>

                        <!-- Filter Row 2 - Date Range -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                            <!-- Date From -->
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input 
                                    type="date" 
                                    id="date_from"
                                    name="date_from" 
                                    value="<?= htmlspecialchars($date_from ?? '') ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary text-sm"
                                >
                            </div>

                            <!-- Date To -->
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                <input 
                                    type="date" 
                                    id="date_to"
                                    name="date_to" 
                                    value="<?= htmlspecialchars($date_to ?? '') ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary text-sm"
                                >
                            </div>

                            <!-- Reset Button -->
                            <div class="flex items-end">
                                <a 
                                    href="admin.php?action=activity_log" 
                                    class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-center text-sm"
                                >
                                    Reset Filter
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Activity Timeline -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Timeline Aktivitas</h3>
                            <span class="text-sm text-gray-500">
                                Halaman <?= $page ?> dari <?= $totalPages ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <?php if (empty($activities)): ?>
                            <div class="text-center py-12">
                                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500">Tidak ada aktivitas ditemukan</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-6">
                                <?php 
                                $currentDate = '';
                                foreach ($activities as $activity): 
                                    $activityDate = date('Y-m-d', strtotime($activity['created_at']));
                                    
                                    // Show date separator
                                    if ($currentDate !== $activityDate):
                                        $currentDate = $activityDate;
                                ?>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 font-medium">
                                        <div class="flex-1 border-t border-gray-200"></div>
                                        <div class="bg-gray-100 px-3 py-1 rounded-full">
                                            <?= date('d F Y', strtotime($activity['created_at'])) ?>
                                        </div>
                                        <div class="flex-1 border-t border-gray-200"></div>
                                    </div>
                                <?php endif; ?>

                                <div class="flex items-start space-x-4">
                                    <!-- Timeline Dot -->
                                    <div class="flex-shrink-0 mt-1">
                                        <?php 
                                        $iconClass = 'w-8 h-8 rounded-full flex items-center justify-center';
                                        $iconBg = 'bg-gray-100';
                                        $iconColor = 'text-gray-600';
                                        
                                        switch($activity['activity_type']) {
                                            case 'login':
                                                $iconBg = 'bg-green-100';
                                                $iconColor = 'text-green-600';
                                                break;
                                            case 'logout':
                                                $iconBg = 'bg-red-100';
                                                $iconColor = 'text-red-600';
                                                break;
                                            case 'create_bootcamp':
                                            case 'create_user':
                                            case 'create_category':
                                                $iconBg = 'bg-blue-100';
                                                $iconColor = 'text-blue-600';
                                                break;
                                            case 'update_bootcamp':
                                            case 'update_user':
                                            case 'update_settings':
                                                $iconBg = 'bg-yellow-100';
                                                $iconColor = 'text-yellow-600';
                                                break;
                                            case 'delete_bootcamp':
                                            case 'delete_user':
                                                $iconBg = 'bg-red-100';
                                                $iconColor = 'text-red-600';
                                                break;
                                            case 'backup_database':
                                            case 'system_health_check':
                                                $iconBg = 'bg-purple-100';
                                                $iconColor = 'text-purple-600';
                                                break;
                                        }
                                        ?>
                                        <div class="<?= $iconClass ?> <?= $iconBg ?>">
                                            <?php if (in_array($activity['activity_type'], ['login', 'logout'])): ?>
                                                <svg class="w-4 h-4 <?= $iconColor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                                </svg>
                                            <?php elseif (strpos($activity['activity_type'], 'create') !== false): ?>
                                                <svg class="w-4 h-4 <?= $iconColor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            <?php elseif (strpos($activity['activity_type'], 'update') !== false): ?>
                                                <svg class="w-4 h-4 <?= $iconColor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            <?php elseif (strpos($activity['activity_type'], 'delete') !== false): ?>
                                                <svg class="w-4 h-4 <?= $iconColor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            <?php else: ?>
                                                <svg class="w-4 h-4 <?= $iconColor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Activity Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <!-- Activity Header -->
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="flex items-center space-x-2">
                                                    <!-- Admin Avatar -->
                                                    <div class="h-6 w-6 rounded-full bg-primary flex items-center justify-center">
                                                        <span class="text-xs font-medium text-white">
                                                            <?= strtoupper(substr($activity['admin_name'] ?? 'A', 0, 1)) ?>
                                                        </span>
                                                    </div>
                                                    <span class="font-medium text-gray-800">
                                                        <?= htmlspecialchars($activity['admin_name'] ?? 'Unknown Admin') ?>
                                                    </span>
                                                    <span class="text-sm text-gray-500">
                                                        <?= date('H:i:s', strtotime($activity['created_at'])) ?>
                                                    </span>
                                                </div>
                                                
                                                <!-- Activity Type Badge -->
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?= $iconBg ?> <?= $iconColor ?>">
                                                    <?= ucwords(str_replace('_', ' ', $activity['activity_type'])) ?>
                                                </span>
                                            </div>

                                            <!-- Activity Description -->
                                            <div class="text-gray-700 mb-3">
                                                <?= htmlspecialchars($activity['description']) ?>
                                            </div>

                                            <!-- Activity Meta -->
                                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                <?php if (!empty($activity['ip_address'])): ?>
                                                    <div class="flex items-center space-x-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                                                        </svg>
                                                        <span>IP: <?= htmlspecialchars($activity['ip_address']) ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($activity['user_agent'])): ?>
                                                    <div class="flex items-center space-x-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <span class="truncate max-w-xs" title="<?= htmlspecialchars($activity['user_agent']) ?>">
                                                            <?= htmlspecialchars(substr($activity['user_agent'], 0, 50)) ?>...
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="px-6 py-4 border-t border-gray-200">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div class="text-sm text-gray-700">
                                    Menampilkan <?= (($page - 1) * 50) + 1 ?> sampai <?= min($page * 50, $totalActivities) ?> dari <?= $totalActivities ?> aktivitas
                                </div>
                                
                                <div class="flex items-center space-x-1">
                                    <!-- Previous Page -->
                                    <?php if ($page > 1): ?>
                                        <a 
                                            href="admin.php?action=activity_log&page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $activity_type ? '&activity_type=' . urlencode($activity_type) : '' ?><?= $admin_id ? '&admin_id=' . $admin_id : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>" 
                                            class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                        >
                                            Sebelumnya
                                        </a>
                                    <?php endif; ?>

                                    <!-- Page Numbers -->
                                    <?php
                                    $startPage = max(1, $page - 2);
                                    $endPage = min($totalPages, $page + 2);
                                    ?>
                                    
                                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                        <a 
                                            href="admin.php?action=activity_log&page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $activity_type ? '&activity_type=' . urlencode($activity_type) : '' ?><?= $admin_id ? '&admin_id=' . $admin_id : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>" 
                                            class="px-3 py-2 text-sm border rounded-md <?= $i == $page ? 'bg-primary text-white border-primary' : 'bg-white border-gray-300 hover:bg-gray-50' ?>"
                                        >
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>

                                    <!-- Next Page -->
                                    <?php if ($page < $totalPages): ?>
                                        <a 
                                            href="admin.php?action=activity_log&page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $activity_type ? '&activity_type=' . urlencode($activity_type) : '' ?><?= $admin_id ? '&admin_id=' . $admin_id : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>" 
                                            class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                        >
                                            Selanjutnya
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Set max date to today for date inputs
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date_from').max = today;
            document.getElementById('date_to').max = today;
            
            // Validate date range
            const dateFrom = document.getElementById('date_from');
            const dateTo = document.getElementById('date_to');
            
            dateFrom.addEventListener('change', function() {
                dateTo.min = this.value;
            });
            
            dateTo.addEventListener('change', function() {
                dateFrom.max = this.value;
            });
        });

        // Auto-refresh every 30 seconds for new activities
        let autoRefresh = setInterval(function() {
            // Only refresh if on first page and no filters
            const urlParams = new URLSearchParams(window.location.search);
            const page = urlParams.get('page') || '1';
            const hasFilters = urlParams.get('search') || urlParams.get('activity_type') || 
                             urlParams.get('admin_id') || urlParams.get('date_from') || urlParams.get('date_to');
            
            if (page === '1' && !hasFilters) {
                // Add a subtle indicator that refresh is happening
                const indicator = document.createElement('div');
                indicator.className = 'fixed top-4 right-4 bg-primary text-white px-3 py-1 rounded-full text-sm z-50';
                indicator.textContent = 'Memperbarui...';
                document.body.appendChild(indicator);
                
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        }, 30000); // 30 seconds

        // Stop auto-refresh when user interacts with filters
        document.querySelector('form').addEventListener('input', function() {
            clearInterval(autoRefresh);
        });
    </script>
</body>
</html>