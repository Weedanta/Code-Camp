<?php
// admin.php - Enhanced Admin Panel Router (Bagian yang diperbaiki)
session_start();

// Include required files dengan error handling
$required_files = [
    'config/database.php',
    'controllers/AdminController.php', 
    'helpers/SecurityHelper.php',
    'middleware/AdminMiddleware.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        require_once $file;
    } else {
        die("Required file missing: $file");
    }
}

// Initialize database connection
try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize admin controller
$adminController = new AdminController($db);

// Get action
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Routes that don't require authentication
$publicRoutes = ['login', 'process_login'];

// Check if user is admin (except for login pages)
if (!in_array($action, $publicRoutes)) {
    AdminMiddleware::checkAdminAccess();
}

// Router dengan error handling yang lebih baik
try {
    switch ($action) {
        // ==================== AUTHENTICATION ====================
        case 'login':
        case '':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $adminController->processLogin();
            } else {
                $adminController->showLogin();
            }
            break;
            
        case 'process_login':
            $adminController->processLogin();
            break;
            
        case 'logout':
            $adminController->logout();
            break;
        
        // ==================== DASHBOARD ====================    
        case 'dashboard':
            $adminController->dashboard();
            break;
            
        case 'stats':
            $adminController->detailedStats();
            break;
        
        // ==================== USER MANAGEMENT ====================
        case 'manage_users':
            $adminController->manageUsers();
            break;
            
        case 'edit_user':
            $adminController->editUser();
            break;
            
        case 'update_user':
            $adminController->updateUser();
            break;
            
        case 'delete_user':
            $adminController->deleteUser();
            break;
            
        case 'delete_users_bulk':
            $adminController->deleteUsersBulk();
            break;
            
        case 'reset_user_password':
            $adminController->resetUserPassword();
            break;
        
        // ==================== BOOTCAMP MANAGEMENT ====================
        case 'manage_bootcamps':
            $adminController->manageBootcamps();
            break;
            
        case 'create_bootcamp':
            $adminController->createBootcamp();
            break;
            
        case 'edit_bootcamp':
            $adminController->editBootcamp();
            break;
            
        case 'update_bootcamp':
            $adminController->updateBootcamp();
            break;
            
        case 'delete_bootcamp':
            $adminController->deleteBootcamp();
            break;
            
        case 'toggle_featured_bootcamp':
            $adminController->toggleFeaturedBootcamp();
            break;
        
        // ==================== CATEGORY MANAGEMENT ====================
        case 'manage_categories':
            $adminController->manageCategories();
            break;
            
        case 'create_category':
            $adminController->createCategory();
            break;
            
        case 'update_category':
            $adminController->updateCategory();
            break;
            
        case 'delete_category':
            $adminController->deleteCategory();
            break;
        
        // ==================== ORDER MANAGEMENT ====================
        case 'manage_orders':
            $adminController->manageOrders();
            break;
            
        case 'view_order':
            $adminController->viewOrder();
            break;
            
        case 'update_order_status':
            $adminController->updateOrderStatus();
            break;
        
        // ==================== REVIEW MANAGEMENT ====================
        case 'manage_reviews':
            $adminController->manageReviews();
            break;
            
        case 'approve_review':
            $adminController->approveReview();
            break;
            
        case 'reject_review':
            $adminController->rejectReview();
            break;
            
        case 'delete_review':
            $adminController->deleteReview();
            break;
            
        case 'bulk_approve_reviews':
            $adminController->bulkApproveReviews();
            break;
        
        // ==================== FORUM MANAGEMENT ====================
        case 'manage_forum':
            $adminController->manageForum();
            break;
            
        case 'moderate_post':
            $adminController->moderatePost();
            break;
            
        case 'delete_forum_post':
            $adminController->deleteForumPost();
            break;
        
        // ==================== SETTINGS ====================
        case 'manage_settings':
            $adminController->manageSettings();
            break;
            
        case 'update_settings':
            $adminController->updateSettings();
            break;
        
        // ==================== SYSTEM TOOLS ====================
        case 'backup_database':
            $adminController->backupDatabase();
            break;
            
        case 'clean_logs':
            $adminController->cleanLogs();
            break;
            
        case 'export_data':
            $adminController->exportData();
            break;
            
        case 'optimize_database':
            $adminController->optimizeDatabase();
            break;
            
        case 'check_system':
            $adminController->checkSystemHealth();
            break;
        
        // ==================== ADMIN PROFILE ====================
        case 'profile':
            $adminController->showProfile();
            break;
            
        case 'update_profile':
            $adminController->updateProfile();
            break;
            
        case 'change_password':
            $adminController->changePassword();
            break;
        
        // ==================== ACTIVITY LOG ====================
        case 'activity_log':
            $adminController->activityLog();
            break;
        
        // ==================== AJAX ENDPOINTS ====================
        case 'ajax_get_user':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $adminController->ajaxGetUser();
            }
            break;
            
        case 'ajax_get_bootcamp':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $adminController->ajaxGetBootcamp();
            }
            break;
            
        case 'ajax_dashboard_stats':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $adminController->ajaxDashboardStats();
            }
            break;
            
        default:
            // Log invalid access attempt
            if (isset($_SESSION['admin_id'])) {
                $adminController->logInvalidAccess($action);
            }
            $_SESSION['error'] = 'Halaman tidak ditemukan';
            header('Location: admin.php?action=dashboard');
            exit;
    }
} catch (Exception $e) {
    // Log error dan redirect ke dashboard
    error_log("Admin Router Error: " . $e->getMessage());
    $_SESSION['error'] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
    header('Location: admin.php?action=dashboard');
    exit;
}
?>