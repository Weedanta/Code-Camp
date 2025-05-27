<?php
// Memanggil AuthController dan other controllers
require_once 'controllers/AuthController.php';
require_once 'controllers/BootcampController.php';
require_once 'controllers/WishlistController.php';
require_once 'controllers/OrderController.php';
require_once 'controllers/ReviewController.php';
require_once 'controllers/CVController.php';
require_once 'controllers/TodoListController.php';

// Inisialisasi controllers
$auth = new AuthController();
$bootcamp = new BootcampController();
$wishlist = new WishlistController();
$order = new OrderController();
$review = new ReviewController();
$cv = new CVController();
$todolist = new TodoListController();

// Router sederhana
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

switch ($action) {
    // Home/Default Route
    case 'home':
        include_once 'views/home.php';
        break;
        
    // Authentication Routes
    case 'login':
        $auth->showLoginPage();
        break;
        
    case 'process_login':
        if ($auth->login()) {
            // Jika login berhasil, redirect ke halaman dashboard
            header('Location: index.php');
        } else {
            // Jika login gagal, kembali ke halaman login dengan pesan error
            header('Location: index.php?action=login&error=invalid');
        }
        break;
        
    case 'signup':
        $auth->showSignupPage();
        break;
        
    case 'process_signup':
        // Validasi password match
        if ($_POST['password'] != $_POST['confirm_password']) {
            header('Location: index.php?action=signup&error=password_mismatch');
            exit();
        }

        if ($auth->signup()) {
            // Jika signup berhasil, redirect ke halaman login dengan pesan sukses
            header('Location: index.php?action=login&success=register');
        } else {
            // Jika email sudah ada
            if ($_POST['alamat_email'] && $_POST['name'] && $_POST['password']) {
                header('Location: index.php?action=signup&error=email_exists');
            } else {
                // Jika field kosong
                header('Location: index.php?action=signup&error=empty');
            }
        }
        break;
        
    case 'logout':
        $auth->logout();
        break;
        
    case 'update_profile':
        if ($auth->updateProfile()) {
            header('Location: views/auth/dashboard/dashboard.php?success=profile_updated');
        } else {
            header('Location: views/auth/dashboard/dashboard.php?error=update_failed');
        }
        break;

    case 'upload_photo':
        if ($auth->uploadProfilePhoto($_FILES['profile_photo'])) {
            header('Location: views/auth/dashboard/dashboard.php?success=photo_updated');
        } else {
            header('Location: views/auth/dashboard/dashboard.php?error=photo_upload_failed');
        }
        break;
        
    case 'update_password':
        if ($auth->updatePassword()) {
            header('Location: views/auth/dashboard/dashboard.php?success=password_updated');
        } else {
            header('Location: views/auth/dashboard/dashboard.php?error=password_update_failed');
        }
        break;
        
    case 'delete_account':
        if ($auth->deleteAccount()) {
            header('Location: index.php?action=login&success=account_deleted');
        } else {
            header('Location: views/auth/dashboard/dashboard.php?error=delete_failed');
        }
        break;
        
    // Bootcamp Routes
    case 'bootcamps':
        $bootcamp->index();
        break;
        
    case 'bootcamp_category':
        $bootcamp->category();
        break;
        
    case 'bootcamp_detail':
        $bootcamp->detail();
        break;
        
    case 'bootcamp_search':
        $bootcamp->search();
        break;
        
    case 'my_bootcamps':
        $bootcamp->myBootcamps();
        break;
        
    // Wishlist Routes
    case 'wishlist':
        $wishlist->index();
        break;
        
    case 'add_to_wishlist':
        $wishlist->add();
        break;
        
    case 'remove_from_wishlist':
        $wishlist->remove();
        break;
        
    // Order Routes
    case 'checkout':
        $order->checkout();
        break;
        
    case 'process_order':
        $order->processOrder();
        break;
        
    case 'order_success':
        $order->orderSuccess();
        break;
        
    case 'my_orders':
        $order->myOrders();
        break;
        
    case 'order_detail':
        $order->orderDetail();
        break;

    // Optional: Add support for order cancellation and retrying payment
    case 'cancel_order':
        if (isset($_POST['order_id'])) {
            // Implement order cancellation logic
            header('Location: index.php?action=my_orders&message=order_cancelled');
        } else {
            header('Location: index.php?action=my_orders');
        }
        break;
        
    case 'retry_payment':
        if (isset($_GET['id'])) {
            // Redirect to checkout page with the order ID
            header('Location: index.php?action=checkout&id=' . $_GET['id']);
        } else {
            header('Location: index.php?action=my_orders');
        }
        break;
        
    // Review Routes
    case 'add_review':
        $review->addReview();
        break;
        
    case 'get_bootcamp_reviews':
        $review->getBootcampReviews();
        break;
        
    // CV Builder Routes
    case 'cv_builder':
        $cv->index();
        break;
        
    case 'cv_save':
        $cv->save();
        break;
        
    case 'cv_preview':
        $cv->preview();
        break;
        
    case 'cv_pdf':
        $cv->generatePDF();
        break;
        
    // TodoList Routes
    case 'todolist':
        $todolist->index();
        break;
        
    case 'todo_create':
        $todolist->create();
        break;
        
    case 'todo_update':
        $todolist->update();
        break;
        
    case 'todo_update_status':
        $todolist->updateStatus();
        break;
        
    case 'todo_delete':
        $todolist->delete();
        break;
        
    case 'todo_get':
        $todolist->getTodo();
        break;
        
    // Contact/Support Route
    case 'contact_support':
        // Implement contact support page or redirect to about page
        header('Location: views/about/index.php');
        break;
        
    default:
        // Default action adalah home
        include_once 'views/home.php';
        break;
}
?>