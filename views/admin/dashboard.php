<?php
// views/admin/dashboard.php - Admin Dashboard
$pageTitle = 'Dashboard Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Code Camp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: margin-left 0.3s ease;
        }

        .admin-sidebar.collapsed + .main-content {
            margin-left: 70px;
        }

        .content-wrapper {
            padding: 30px;
        }

        .page-header {
            background: white;
            padding: 20px 30px;
            margin: -30px -30px 30px;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .page-header h1 {
            margin: 0;
            color: #495057;
            font-size: 28px;
            font-weight: 600;
        }

        .breadcrumb {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card.users { border-left-color: #28a745; }
        .stat-card.bootcamps { border-left-color: #17a2b8; }
        .stat-card.orders { border-left-color: #ffc107; }
        .stat-card.revenue { border-left-color: #dc3545; }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-title {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
        }

        .stat-icon.users { background: #28a745; }
        .stat-icon.bootcamps { background: #17a2b8; }
        .stat-icon.orders { background: #ffc107; }
        .stat-icon.revenue { background: #dc3545; }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 5px;
        }

        .stat-change {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stat-change.positive {
            color: #28a745;
        }

        .stat-change.negative {
            color: #dc3545;
        }

        .dashboard-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #495057;
            margin: 0;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a67d8;
        }

        .activity-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 14px;
            color: white;
        }

        .activity-icon.login { background: #28a745; }
        .activity-icon.update { background: #17a2b8; }
        .activity-icon.delete { background: #dc3545; }
        .activity-icon.create { background: #ffc107; color: #495057; }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            margin: 0 0 3px;
            font-size: 14px;
            color: #495057;
        }

        .activity-time {
            font-size: 12px;
            color: #6c757d;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .quick-action-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }

        .quick-action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }

        .quick-action-icon {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }

        .quick-action-title {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .dashboard-row {
                grid-template-columns: 1fr;
            }
            
            .content-wrapper {
                padding: 20px;
            }
            
            .page-header {
                padding: 15px 20px;
                margin: -20px -20px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <div class="page-header">
                <h1><?= $pageTitle ?></h1>
                <div class="breadcrumb">
                    <i class="fas fa-home"></i> Admin / Dashboard
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- System Alerts -->
                <?php if (!empty($systemAlerts)): ?>
                    <?php foreach ($systemAlerts as $alert): ?>
                        <div class="alert alert-<?= $alert['type'] ?>">
                            <i class="fas fa-<?= $alert['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle' ?>"></i>
                            <?= htmlspecialchars($alert['message']) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card users">
                        <div class="stat-header">
                            <span class="stat-title">Total Users</span>
                            <div class="stat-icon users">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($stats['total_users'] ?? 0) ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            +<?= $stats['new_users_month'] ?? 0 ?> bulan ini
                        </div>
                    </div>

                    <div class="stat-card bootcamps">
                        <div class="stat-header">
                            <span class="stat-title">Total Bootcamps</span>
                            <div class="stat-icon bootcamps">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($stats['total_bootcamps'] ?? 0) ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-check"></i>
                            <?= $stats['active_bootcamps'] ?? 0 ?> aktif
                        </div>
                    </div>

                    <div class="stat-card orders">
                        <div class="stat-header">
                            <span class="stat-title">Total Orders</span>
                            <div class="stat-icon orders">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($stats['total_orders'] ?? 0) ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            +<?= $stats['orders_month'] ?? 0 ?> bulan ini
                        </div>
                    </div>

                    <div class="stat-card revenue">
                        <div class="stat-header">
                            <span class="stat-title">Total Revenue</span>
                            <div class="stat-icon revenue">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div class="stat-value">Rp <?= number_format($stats['total_revenue'] ?? 0) ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            Rp <?= number_format($stats['revenue_month'] ?? 0) ?> bulan ini
                        </div>
                    </div>
                </div>

                <!-- Dashboard Row -->
                <div class="dashboard-row">
                    <!-- Recent Activities -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3 class="card-title">Aktivitas Terbaru</h3>
                            <a href="admin.php?action=activity_log" class="btn btn-primary">
                                <i class="fas fa-history"></i>
                                Lihat Semua
                            </a>
                        </div>
                        
                        <ul class="activity-list">
                            <?php if (!empty($recentActivities)): ?>
                                <?php foreach (array_slice($recentActivities, 0, 8) as $activity): ?>
                                    <li class="activity-item">
                                        <div class="activity-icon <?= htmlspecialchars($activity['activity_type']) ?>">
                                            <?php
                                            $icons = [
                                                'login' => 'fa-sign-in-alt',
                                                'logout' => 'fa-sign-out-alt',
                                                'create' => 'fa-plus',
                                                'update' => 'fa-edit',
                                                'delete' => 'fa-trash'
                                            ];
                                            $icon = $icons[$activity['activity_type']] ?? 'fa-circle';
                                            ?>
                                            <i class="fas <?= $icon ?>"></i>
                                        </div>
                                        <div class="activity-content">
                                            <p class="activity-text">
                                                <strong><?= htmlspecialchars($activity['admin_name'] ?? 'System') ?></strong>
                                                <?= htmlspecialchars($activity['description']) ?>
                                            </p>
                                            <span class="activity-time">
                                                <i class="fas fa-clock"></i>
                                                <?= date('d M Y H:i', strtotime($activity['created_at'])) ?>
                                            </span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="activity-item">
                                    <div class="activity-content">
                                        <p class="activity-text">Belum ada aktivitas.</p>
                                    </div>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Quick Actions -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3 class="card-title">Aksi Cepat</h3>
                        </div>
                        
                        <div class="quick-actions">
                            <a href="admin.php?action=manage_users" class="quick-action-card">
                                <i class="fas fa-users quick-action-icon"></i>
                                <h4 class="quick-action-title">Kelola Users</h4>
                            </a>
                            
                            <a href="admin.php?action=create_bootcamp" class="quick-action-card">
                                <i class="fas fa-plus quick-action-icon"></i>
                                <h4 class="quick-action-title">Tambah Bootcamp</h4>
                            </a>
                            
                            <a href="admin.php?action=manage_orders" class="quick-action-card">
                                <i class="fas fa-shopping-cart quick-action-icon"></i>
                                <h4 class="quick-action-title">Kelola Orders</h4>
                            </a>
                            
                            <a href="admin.php?action=manage_reviews" class="quick-action-card">
                                <i class="fas fa-star quick-action-icon"></i>
                                <h4 class="quick-action-title">Review
                                    <?php if ($stats['pending_reviews'] > 0): ?>
                                        <span style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 10px; font-size: 10px; margin-left: 5px;">
                                            <?= $stats['pending_reviews'] ?>
                                        </span>
                                    <?php endif; ?>
                                </h4>
                            </a>
                            
                            <a href="admin.php?action=stats" class="quick-action-card">
                                <i class="fas fa-chart-bar quick-action-icon"></i>
                                <h4 class="quick-action-title">Statistik</h4>
                            </a>
                            
                            <a href="admin.php?action=manage_settings" class="quick-action-card">
                                <i class="fas fa-cog quick-action-icon"></i>
                                <h4 class="quick-action-title">Pengaturan</h4>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh stats every 30 seconds
            setInterval(function() {
                fetch('admin.php?action=ajax_dashboard_stats')
                    .then(response => response.json())
                    .then(data => {
                        // Update stats if needed
                        console.log('Stats updated:', data);
                    })
                    .catch(error => {
                        console.log('Failed to update stats:', error);
                    });
            }, 30000);

            // Add click tracking for quick actions
            document.querySelectorAll('.quick-action-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    const title = this.querySelector('.quick-action-title').textContent;
                    console.log('Quick action clicked:', title);
                });
            });
        });
    </script>
</body>
</html>