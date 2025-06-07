<?php
session_start();

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

require_once 'config/database.php';
require_once 'controllers/AdminController.php';
require_once 'middleware/AdminMiddleware.php';
require_once 'helpers/SecurityHelper.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Initialize admin controller
$adminController = new AdminController($db);

// Get action (dengan sanitization)
$action = SecurityHelper::sanitizeInput($_GET['action'] ?? 'dashboard');

// Check admin access for all actions except login
if ($action !== 'login' && $action !== 'process_login') {
    AdminMiddleware::checkAccess();
}

try {
    switch ($action) {
        // Authentication
        case 'login':
            $adminController->showLogin();
            break;
            
        case 'process_login':
            $adminController->processLogin();
            break;
            
        case 'logout':
            $adminController->logout();
            break;
            
        // Dashboard
        case 'dashboard':
            $adminController->dashboard();
            break;
            
        case 'stats':
            $adminController->stats();
            break;
            
        // User Management
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
            
        // Bootcamp Management
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
            
        case 'delete_bootcamps_bulk':
            $adminController->deleteBootcampsBulk();
            break;
            
        // Category Management
        case 'manage_categories':
            $adminController->manageCategories();
            break;
            
        case 'create_category':
            $adminController->createCategory();
            break;
            
        case 'edit_category':
            $adminController->editCategory();
            break;
            
        case 'update_category':
            $adminController->updateCategory();
            break;
            
        case 'delete_category':
            $adminController->deleteCategory();
            break;
            
        // Order Management
        case 'manage_orders':
            $adminController->manageOrders();
            break;
            
        case 'view_order':
            $adminController->viewOrder();
            break;
            
        case 'update_order_status':
            $adminController->updateOrderStatus();
            break;
            
        case 'delete_order':
            $adminController->deleteOrder();
            break;
            
        // Review Management
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
            
        // Forum Management
        case 'manage_forum':
            $adminController->manageForum();
            break;
            
        case 'moderate_post':
            $adminController->moderatePost();
            break;
            
        case 'delete_post':
            $adminController->deleteForumPost();
            break;
            
        case 'pin_post':
            $adminController->pinPost();
            break;
            
        // Settings Management
        case 'manage_settings':
            $adminController->manageSettings();
            break;
            
        case 'update_settings':
            $adminController->updateSettings();
            break;
            
        // System Tools
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
            // Log suspicious activity
            SecurityHelper::logSecurityEvent(
                'invalid_admin_action',
                "Invalid admin action attempted: " . $action,
                'warning'
            );
            
            $_SESSION['error'] = 'Aksi tidak valid';
            header('Location: admin.php?action=dashboard');
            exit;
    }
    
} catch (Exception $e) {
    // Log error
    error_log("Admin Error: " . $e->getMessage());
    
    // Don't show detailed error to user
    $_SESSION['error'] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
    header('Location: admin.php?action=dashboard');
    exit;
}