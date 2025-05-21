<?php
// Memanggil AuthController
require_once 'controllers/AuthController.php';

// Inisialisasi controller
$auth = new AuthController();

// Router sederhana
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

switch ($action) {
    case 'home':
        include_once 'views/home.php';
        break;
        
    case 'login':
        $auth->showLoginPage();
        break;
        
    case 'process_login':
        if ($auth->login()) {
            // Jika login berhasil, redirect ke halaman dashboard
            header('Location: views/auth/dashboard/dashboard.php');
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
        
    default:
        // Default action adalah home
        include_once 'views/home.php';
        break;
}
?>