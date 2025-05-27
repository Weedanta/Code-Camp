<?php
// views/admin/manage_users.php
session_start();

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../../index.php?action=login');
    exit;
}

// Initialize controller and get users
require_once '../../config/database.php';
require_once '../../controllers/AdminController.php';
require_once '../../models/Admin.php';

$database = new Database();
$db = $database->getConnection();

// Handle actions via GET
if (isset($_GET['action'])) {
    $adminController = new AdminController($db);
    switch ($_GET['action']) {
        case 'delete':
            $adminController->deleteUser();
            break;
        case 'logout':
            $adminController->logout();
            break;
        case 'edit':
            $adminController->editUser();
            break;
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminController = new AdminController($db);
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                $adminController->updateUser();
                break;
            case 'delete_selected':
                if (isset($_POST['selected_users']) && is_array($_POST['selected_users'])) {
                    foreach ($_POST['selected_users'] as $userId) {
                        $_GET['id'] = $userId; // Set ID for deleteUser method
                        $adminController->deleteUser();
                    }
                }
                break;
        }
    }
}

// Get users data
$admin = new Admin($db);
$users = $admin->getAllUsers();
$userCount = $admin->getUserCount();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if (!empty($search)) {
    // Filter users based on search
    $users = array_filter($users, function ($user) use ($search) {
        return stripos($user['name'], $search) !== false ||
            stripos($user['alamat_email'], $search) !== false ||
            stripos($user['no_telepon'], $search) !== false;
    });
}

// Apply pagination
$totalUsers = count($users);
$totalPages = ceil($totalUsers / $perPage);
$users = array_slice($users, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun - Admin Campus Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/custom.css" rel="stylesheet">
    <style>
        .table-responsive {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .action-buttons .btn {
            margin: 0 0.125rem;
        }
    </style>
</head>

<body>
    <!-- Admin Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/views/admin/dashboard.php">
                <i class="fas fa-shield-alt me-2"></i>Admin Panel - Campus Hub
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i><?= htmlspecialchars($_SESSION['admin_name']) ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?action=logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 bg-light">
                <div class="d-flex flex-column p-3">
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="/views/admin/dashboard.php" class="nav-link">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/views/admin/manage_users.php" class="nav-link active">
                                <i class="fas fa-users me-2"></i>Kelola Akun
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Kelola Akun Pengguna</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="badge bg-primary fs-6">Total: <?= $userCount ?> Pengguna</span>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                                <i class="fas fa-print me-1"></i>Print
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportData()">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" class="d-flex">
                            <input type="hidden" name="page" value="admin_manage_users">
                            <input type="text" class="form-control me-2" name="search"
                                placeholder="Cari nama, email, atau telepon..."
                                value="<?= htmlspecialchars($search) ?>">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="?page=admin_manage_users" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <?php if (!empty($search)): ?>
                            <small class="text-muted">
                                Menampilkan <?= count($users) ?> dari <?= $userCount ?> pengguna
                            </small>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Users Table -->
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Pengguna Terdaftar</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($users)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada pengguna terdaftar</h5>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>No. Telepon</th>
                                            <th>Tanggal Daftar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user['id']) ?></td>
                                                <td><?= htmlspecialchars($user['name']) ?></td>
                                                <td><?= htmlspecialchars($user['alamat_email']) ?></td>
                                                <td><?= htmlspecialchars($user['no_telepon']) ?></td>
                                                <td><?= htmlspecialchars($user['created_at']) ?></td>
                                                <td class="action-buttons">
                                                    <a href="?action=edit&id=<?= urlencode($user['id']) ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="?action=delete&id=<?= urlencode($user['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus pengguna ini?');">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-3">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportData() {
            window.print(); // Placeholder for export functionality
        }
    </script>
</body>

</html>