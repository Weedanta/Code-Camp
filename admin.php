<?php
// admin.php - Router utama untuk Admin Panel
session_start();

// Error reporting untuk development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once 'config/database.php';
require_once 'controllers/AdminController.php';
require_once 'middleware/AdminMiddleware.php';
require_once 'helper/SecurityHelper.php';

// Configure secure session
SecurityHelper::configureSecureSession();

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = SecurityHelper::generateCSRFToken();
}

// Initialize database connection
try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize AdminController
$adminController = new AdminController($db);

// Get action from URL
$action = $_GET['action'] ?? 'login';

// Define public actions that don't require authentication
$publicActions = ['login', 'process_login'];

// Check admin access for protected routes
if (!in_array($action, $publicActions)) {
    AdminMiddleware::checkAdminAccess();
}

// Route handling
try {
    switch ($action) {
        // ==================== AUTHENTICATION ====================
        case 'login':
            $adminController->showLogin();
            break;
            
        case 'process_login':
            $adminController->processLogin();
            break;
            
        case 'logout':
            $adminController->logout();
            break;

        // ==================== DASHBOARD ====================
        case 'dashboard':
        case '':
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
            
        case 'reset_user_password':
            $adminController->resetUserPassword();
            break;
            
        case 'delete_users_bulk':
            $adminController->deleteUsersBulk();
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

        // ==================== SETTINGS MANAGEMENT ====================
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
            
        case 'check_system_health':
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
            $adminController->ajaxGetUser();
            break;
            
        case 'ajax_get_bootcamp':
            $adminController->ajaxGetBootcamp();
            break;
            
        case 'ajax_dashboard_stats':
            $adminController->ajaxDashboardStats();
            break;

        // ==================== DEFAULT ====================
        default:
            // Log invalid access attempt
            $adminController->logInvalidAccess($action);
            
            $_SESSION['error'] = 'Halaman tidak ditemukan';
            header('Location: admin.php?action=dashboard');
            exit;
    }
    
} catch (Exception $e) {
    // Log the error
    error_log("Admin panel error: " . $e->getMessage());
    
    // Show user-friendly error message
    $_SESSION['error'] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
    
    // Redirect based on authentication status
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        header('Location: admin.php?action=dashboard');
    } else {
        header('Location: admin.php?action=login');
    }
    exit;
}
?>