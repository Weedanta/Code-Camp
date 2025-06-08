<?php
// views/admin/activity_log.php - Activity Log Page
$pageTitle = 'Log Aktivitas';

// Get filters
$currentPage = $page ?? 1;
$searchTerm = $_GET['search'] ?? '';
$activityType = $_GET['activity_type'] ?? '';
$adminId = $_GET['admin_id'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';

// Security function
function sanitizeOutput($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'baru saja';
    if ($time < 3600) return floor($time/60) . ' menit lalu';
    if ($time < 86400) return floor($time/3600) . ' jam lalu';
    if ($time < 2592000) return floor($time/86400) . ' hari lalu';
    if ($time < 31104000) return floor($time/2592000) . ' bulan lalu';
    return floor($time/31104000) . ' tahun lalu';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .activity-filters {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 100px;
            gap: 15px;
            align-items: end;
        }

        .activity-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 20px;
            color: white;
        }

        .stat-icon.login { background: #28a745; }
        .stat-icon.create { background: #17a2b8; }
        .stat-icon.update { background: #ffc107; color: #495057; }
        .stat-icon.delete { background: #dc3545; }
        .stat-icon.total { background: #667eea; }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }

        .activity-timeline {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .timeline-header {
            background: #f8f9fa;
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .timeline-item {
            display: flex;
            padding: 20px 25px;
            border-bottom: 1px solid #f1f3f4;
            transition: background-color 0.3s ease;
        }

        .timeline-item:hover {
            background: #f8f9fa;
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 16px;
            color: white;
            flex-shrink: 0;
        }

        .activity-icon.login { background: #28a745; }
        .activity-icon.logout { background: #6c757d; }
        .activity-icon.create { background: #17a2b8; }
        .activity-icon.update { background: #ffc107; color: #495057; }
        .activity-icon.delete { background: #dc3545; }
        .activity-icon.approve { background: #28a745; }
        .activity-icon.reject { background: #dc3545; }
        .activity-icon.export { background: #6f42c1; }
        .activity-icon.backup { background: #fd7e14; }
        .activity-icon.security { background: #e83e8c; }

        .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .activity-title {
            font-weight: 600;
            color: #495057;
            margin: 0;
            font-size: 14px;
        }

        .activity-time {
            font-size: 12px;
            color: #6c757d;
            white-space: nowrap;
            margin-left: 15px;
        }

        .activity-description {
            color: #6c757d;
            font-size: 13px;
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .activity-meta {
            display: flex;
            gap: 15px;
            font-size: 11px;
            color: #adb5bd;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .activity-type-badge {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .badge-login { background: #d4edda; color: #155724; }
        .badge-create { background: #d1ecf1; color: #0c5460; }
        .badge-update { background: #fff3cd; color: #856404; }
        .badge-delete { background: #f8d7da; color: #721c24; }
        .badge-security { background: #f3e2f3; color: #6f42c1; }

        .admin-filter {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 12px;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .admin-filter:hover {
            background: #e9ecef;
            text-decoration: none;
            color: #495057;
        }

        .admin-filter.active {
            background: #667eea;
            color: white;
        }

        .admin-avatar {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
            font-weight: 600;
        }

        .export-options {
            display: flex;
            gap: 10px;
        }

        .date-range {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .loading-indicator {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .real-time-indicator {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: #28a745;
            margin-left: 10px;
        }

        .pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #28a745;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.3; }
            100% { opacity: 1; }
        }

        @media (max-width: 768px) {
            .filters-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .activity-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .timeline-item {
                flex-direction: column;
                gap: 15px;
            }

            .activity-icon {
                align-self: flex-start;
            }

            .activity-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .activity-meta {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1><?= $pageTitle ?></h1>
                    <div class="breadcrumb">
                        <i class="fas fa-home"></i> Admin / 
                        <i class="fas fa-history"></i> Log Aktivitas
                        <span class="real-time-indicator">
                            <span class="pulse"></span>
                            Real-time
                        </span>
                    </div>
                </div>
                <div class="page-actions">
                    <div class="export-options">
                        <a href="admin.php?action=export_activity_log&format=csv" class="btn btn-secondary btn-sm">
                            <i class="fas fa-download"></i>
                            Export CSV
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" onclick="refreshLogs()">
                            <i class="fas fa-sync-alt"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= sanitizeOutput($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= sanitizeOutput($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Activity Stats -->
                <div class="activity-stats">
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="stat-number"><?= number_format($totalActivities ?? 0) ?></div>
                        <div class="stat-label">Total Aktivitas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon login">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="stat-number">
                            <?php
                            $loginCount = 0;
                            if (isset($activities)) {
                                foreach ($activities as $activity) {
                                    if ($activity['activity_type'] === 'login') $loginCount++;
                                }
                            }
                            echo number_format($loginCount);
                            ?>
                        </div>
                        <div class="stat-label">Login Hari Ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon create">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="stat-number">
                            <?php
                            $createCount = 0;
                            if (isset($activities)) {
                                foreach ($activities as $activity) {
                                    if (in_array($activity['activity_type'], ['create', 'create_bootcamp', 'create_category'])) $createCount++;
                                }
                            }
                            echo number_format($createCount);
                            ?>
                        </div>
                        <div class="stat-label">Item Dibuat</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon update">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="stat-number">
                            <?php
                            $updateCount = 0;
                            if (isset($activities)) {
                                foreach ($activities as $activity) {
                                    if (in_array($activity['activity_type'], ['update', 'update_user', 'update_bootcamp'])) $updateCount++;
                                }
                            }
                            echo number_format($updateCount);
                            ?>
                        </div>
                        <div class="stat-label">Item Diupdate</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon delete">
                            <i class="fas fa-trash"></i>
                        </div>
                        <div class="stat-number">
                            <?php
                            $deleteCount = 0;
                            if (isset($activities)) {
                                foreach ($activities as $activity) {
                                    if (in_array($activity['activity_type'], ['delete', 'delete_user', 'delete_bootcamp'])) $deleteCount++;
                                }
                            }
                            echo number_format($deleteCount);
                            ?>
                        </div>
                        <div class="stat-label">Item Dihapus</div>
                    </div>
                </div>

                <!-- Activity Filters -->
                <div class="activity-filters">
                    <form method="GET" action="admin.php" class="filters-grid">
                        <input type="hidden" name="action" value="activity_log">
                        
                        <div class="form-group">
                            <label for="search" class="form-label">Cari Aktivitas</label>
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Cari deskripsi aktivitas..."
                                   value="<?= sanitizeOutput($searchTerm) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="activity_type" class="form-label">Jenis Aktivitas</label>
                            <select id="activity_type" name="activity_type" class="form-control">
                                <option value="">Semua Jenis</option>
                                <option value="login" <?= $activityType === 'login' ? 'selected' : '' ?>>Login</option>
                                <option value="logout" <?= $activityType === 'logout' ? 'selected' : '' ?>>Logout</option>
                                <option value="create" <?= $activityType === 'create' ? 'selected' : '' ?>>Create</option>
                                <option value="update" <?= $activityType === 'update' ? 'selected' : '' ?>>Update</option>
                                <option value="delete" <?= $activityType === 'delete' ? 'selected' : '' ?>>Delete</option>
                                <option value="approve" <?= $activityType === 'approve' ? 'selected' : '' ?>>Approve</option>
                                <option value="reject" <?= $activityType === 'reject' ? 'selected' : '' ?>>Reject</option>
                                <option value="export" <?= $activityType === 'export' ? 'selected' : '' ?>>Export</option>
                                <option value="backup" <?= $activityType === 'backup' ? 'selected' : '' ?>>Backup</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" 
                                   id="date_from" 
                                   name="date_from" 
                                   class="form-control"
                                   value="<?= sanitizeOutput($dateFrom) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" 
                                   id="date_to" 
                                   name="date_to" 
                                   class="form-control"
                                   value="<?= sanitizeOutput($dateTo) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_id" class="form-label">Admin</label>
                            <select id="admin_id" name="admin_id" class="form-control">
                                <option value="">Semua Admin</option>
                                <!-- Admin options would be populated from database -->
                            </select>
                        </div>
                        
                        <div class="d-flex flex-column gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="admin.php?action=activity_log" class="btn btn-outline-secondary">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Activity Timeline -->
                <div class="activity-timeline" id="activityTimeline">
                    <div class="timeline-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clock"></i>
                            Timeline Aktivitas
                        </h5>
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted" style="font-size: 12px;">
                                Menampilkan <?= count($activities ?? []) ?> dari <?= number_format($totalActivities ?? 0) ?> aktivitas
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($activities)): ?>
                        <div class="timeline-content">
                            <?php foreach ($activities as $activity): ?>
                                <div class="timeline-item" data-activity-id="<?= $activity['id'] ?>">
                                    <div class="activity-icon <?= sanitizeOutput($activity['activity_type']) ?>">
                                        <?php
                                        $icons = [
                                            'login' => 'fa-sign-in-alt',
                                            'logout' => 'fa-sign-out-alt',
                                            'create' => 'fa-plus',
                                            'update' => 'fa-edit',
                                            'delete' => 'fa-trash',
                                            'approve' => 'fa-check',
                                            'reject' => 'fa-times',
                                            'export' => 'fa-download',
                                            'backup' => 'fa-database',
                                            'security' => 'fa-shield-alt'
                                        ];
                                        $icon = $icons[$activity['activity_type']] ?? 'fa-circle';
                                        ?>
                                        <i class="fas <?= $icon ?>"></i>
                                    </div>
                                    
                                    <div class="activity-content">
                                        <div class="activity-header">
                                            <h6 class="activity-title">
                                                <?= sanitizeOutput($activity['admin_name'] ?? 'System') ?>
                                                <span class="activity-type-badge badge-<?= sanitizeOutput($activity['activity_type']) ?>">
                                                    <?= strtoupper(sanitizeOutput($activity['activity_type'])) ?>
                                                </span>
                                            </h6>
                                            <div class="activity-time" title="<?= date('d M Y H:i:s', strtotime($activity['created_at'])) ?>">
                                                <i class="fas fa-clock"></i>
                                                <?= timeAgo($activity['created_at']) ?>
                                            </div>
                                        </div>
                                        
                                        <div class="activity-description">
                                            <?= sanitizeOutput($activity['description']) ?>
                                        </div>
                                        
                                        <div class="activity-meta">
                                            <?php if (!empty($activity['ip_address'])): ?>
                                                <div class="meta-item">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <span><?= sanitizeOutput($activity['ip_address']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($activity['user_agent'])): ?>
                                                <div class="meta-item">
                                                    <i class="fas fa-desktop"></i>
                                                    <span title="<?= sanitizeOutput($activity['user_agent']) ?>">
                                                        <?php
                                                        // Extract browser info from user agent
                                                        $userAgent = $activity['user_agent'];
                                                        if (strpos($userAgent, 'Chrome') !== false) echo 'Chrome';
                                                        elseif (strpos($userAgent, 'Firefox') !== false) echo 'Firefox';
                                                        elseif (strpos($userAgent, 'Safari') !== false) echo 'Safari';
                                                        elseif (strpos($userAgent, 'Edge') !== false) echo 'Edge';
                                                        else echo 'Unknown';
                                                        ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="meta-item">
                                                <i class="fas fa-fingerprint"></i>
                                                <span>ID: <?= $activity['id'] ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="card-footer">
                                <nav class="d-flex justify-content-center">
                                    <ul class="pagination mb-0">
                                        <?php
                                        $baseUrl = "admin.php?action=activity_log";
                                        if ($searchTerm) $baseUrl .= "&search=" . urlencode($searchTerm);
                                        if ($activityType) $baseUrl .= "&activity_type=" . urlencode($activityType);
                                        if ($adminId) $baseUrl .= "&admin_id=" . urlencode($adminId);
                                        if ($dateFrom) $baseUrl .= "&date_from=" . urlencode($dateFrom);
                                        if ($dateTo) $baseUrl .= "&date_to=" . urlencode($dateTo);

                                        // Previous page
                                        if ($currentPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage - 1 ?>">‹</a>
                                            </li>
                                        <?php endif;

                                        // Page numbers
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($totalPages, $currentPage + 2);

                                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor;

                                        // Next page
                                        if ($currentPage < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage + 1 ?>">›</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h3>Tidak ada aktivitas ditemukan</h3>
                            <p>Belum ada aktivitas yang sesuai dengan filter yang dipilih.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh every 30 seconds
            setInterval(refreshLogs, 30000);
            
            // Real-time updates using polling
            let lastActivityId = <?= $activities[0]['id'] ?? 0 ?>;
            
            function checkForNewActivities() {
                fetch(`admin.php?action=check_new_activities&last_id=${lastActivityId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.hasNew) {
                        showNewActivityNotification(data.count);
                        lastActivityId = data.lastId;
                    }
                })
                .catch(error => {
                    console.error('Error checking for new activities:', error);
                });
            }
            
            // Check for new activities every 10 seconds
            setInterval(checkForNewActivities, 10000);
            
            function showNewActivityNotification(count) {
                const notification = document.createElement('div');
                notification.className = 'alert alert-info';
                notification.style.position = 'fixed';
                notification.style.top = '20px';
                notification.style.right = '20px';
                notification.style.zIndex = '9999';
                notification.innerHTML = `
                    <i class="fas fa-info-circle"></i>
                    ${count} aktivitas baru tersedia. 
                    <a href="javascript:refreshLogs()" style="color: #0c5460; text-decoration: underline;">Refresh untuk melihat</a>
                `;
                
                document.body.appendChild(notification);
                
                // Auto-remove after 5 seconds
                setTimeout(() => {
                    notification.remove();
                }, 5000);
            }
            
            // Filter form enhancements
            const searchInput = document.getElementById('search');
            let searchTimeout;
            
            searchInput?.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.length >= 3 || this.value.length === 0) {
                        this.closest('form').submit();
                    }
                }, 500);
            });
            
            // Quick date filters
            addQuickDateFilters();
            
            function addQuickDateFilters() {
                const dateFromInput = document.getElementById('date_from');
                const dateToInput = document.getElementById('date_to');
                
                // Add quick filter buttons
                const quickFilters = document.createElement('div');
                quickFilters.className = 'd-flex gap-2 mt-2 flex-wrap';
                quickFilters.innerHTML = `
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('today')">Hari Ini</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('yesterday')">Kemarin</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('week')">7 Hari</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('month')">30 Hari</button>
                `;
                
                dateToInput.parentNode.appendChild(quickFilters);
            }
            
            // Export functionality
            const exportBtn = document.querySelector('a[href*="export_activity_log"]');
            exportBtn?.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Build export URL with current filters
                const params = new URLSearchParams(window.location.search);
                params.set('action', 'export_activity_log');
                params.set('format', 'csv');
                
                const exportUrl = 'admin.php?' + params.toString();
                window.open(exportUrl, '_blank');
            });
        });
        
        function refreshLogs() {
            window.location.reload();
        }
        
        function setDateRange(range) {
            const dateFromInput = document.getElementById('date_from');
            const dateToInput = document.getElementById('date_to');
            const today = new Date();
            
            switch (range) {
                case 'today':
                    dateFromInput.value = today.toISOString().split('T')[0];
                    dateToInput.value = today.toISOString().split('T')[0];
                    break;
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    dateFromInput.value = yesterday.toISOString().split('T')[0];
                    dateToInput.value = yesterday.toISOString().split('T')[0];
                    break;
                case 'week':
                    const weekAgo = new Date(today);
                    weekAgo.setDate(weekAgo.getDate() - 7);
                    dateFromInput.value = weekAgo.toISOString().split('T')[0];
                    dateToInput.value = today.toISOString().split('T')[0];
                    break;
                case 'month':
                    const monthAgo = new Date(today);
                    monthAgo.setDate(monthAgo.getDate() - 30);
                    dateFromInput.value = monthAgo.toISOString().split('T')[0];
                    dateToInput.value = today.toISOString().split('T')[0];
                    break;
            }
            
            // Auto-submit form
            dateFromInput.closest('form').submit();
        }
    </script>
</body>
</html>