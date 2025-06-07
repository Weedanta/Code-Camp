<?php
// admin.php - Admin Panel Router
session_start();

// Include required files
require_once 'config/database.php';
require_once 'controllers/AdminController.php';
require_once 'helpers/SecurityHelper.php';
require_once 'middleware/AdminMiddleware.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize admin controller
$adminController = new AdminController($db);

// Get action
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Routes that don't require authentication
$publicRoutes = ['login'];

// Check if user is admin (except for login page)
if (!in_array($action, $publicRoutes)) {
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header('Location: admin.php?action=login');
        exit;
    }
    
    // Update last activity
    $_SESSION['admin_last_activity'] = time();
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['admin_session_regenerated'])) {
        session_regenerate_id(true);
        $_SESSION['admin_session_regenerated'] = time();
    } elseif (time() - $_SESSION['admin_session_regenerated'] > 300) { // 5 minutes
        session_regenerate_id(true);
        $_SESSION['admin_session_regenerated'] = time();
    }
}

// Router
switch ($action) {
    // ==================== AUTHENTICATION ====================
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminController->processLogin();
        } else {
            $adminController->showLogin();
        }
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
        $adminController->stats();
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
    
    // ==================== CATEGORY MANAGEMENT ====================
    case 'manage_categories':
        $adminController->manageCategories();
        break;
        
    case 'create_category':
        $adminController->createCategory();
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
    
    // ==================== FORUM MANAGEMENT ====================
    case 'manage_forum':
        $adminController->manageForum();
        break;
        
    case 'moderate_post':
        $adminController->moderatePost();
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
        
    default:
        header('Location: admin.php?action=dashboard');
        break;
}
?>