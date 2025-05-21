<?php
require_once 'models/User.php';
require_once 'config/database.php';

class AuthController {
    private $database;
    private $db;
    private $user;

    public function __construct() {
        // Inisialisasi koneksi database
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->user = new User($this->db);
    }

    // Menampilkan halaman login
    public function showLoginPage() {
        include_once 'views/auth/login.php';
    }

    // Menampilkan halaman signup
    public function showSignupPage() {
        include_once 'views/auth/signup.php';
    }

    // Proses login
    public function login() {
        // Memastikan request adalah POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        // Mengambil data dari form
        $this->user->alamat_email = isset($_POST['alamat_email']) ? $_POST['alamat_email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        // Mencari user berdasarkan email
        if ($this->user->findByEmail()) {
            // Verifikasi password
            if (password_verify($password, $this->user->password)) {
                // Password valid, buat session
                session_start();
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['name'] = $this->user->name;
                $_SESSION['alamat_email'] = $this->user->alamat_email;
                $_SESSION['no_telepon'] = $this->user->no_telepon;
                
                return true;
            }
        }
        
        return false;
    }

    // Proses signup
    public function signup() {
        // Memastikan request adalah POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        // Mengambil data dari form
        $this->user->name = isset($_POST['name']) ? $_POST['name'] : '';
        $this->user->alamat_email = isset($_POST['alamat_email']) ? $_POST['alamat_email'] : '';
        $this->user->password = isset($_POST['password']) ? $_POST['password'] : '';
        $this->user->no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : '';

        // Validasi input
        if (empty($this->user->name) || empty($this->user->alamat_email) || empty($this->user->password)) {
            return false;
        }

        // Cek apakah email sudah digunakan
        if ($this->user->emailExists()) {
            return false;
        }

        // Buat user baru
        if ($this->user->create()) {
            return true;
        }
        
        return false;
    }

    // Proses logout
    public function logout() {
        // Mulai session
        session_start();
        
        // Hapus semua data session
        $_SESSION = array();
        
        // Hapus session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }
        
        // Destroy session
        session_destroy();
        
        // Redirect ke halaman login
        header("Location: index.php?action=login");
        exit();
    }

    // Update profil user
    public function updateProfile() {
        // Memastikan request adalah POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        // Memastikan user sudah login
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Mengambil data dari form
        $this->user->id = $_SESSION['user_id'];
        $this->user->name = isset($_POST['name']) ? $_POST['name'] : '';
        $this->user->alamat_email = isset($_POST['alamat_email']) ? $_POST['alamat_email'] : '';
        $this->user->no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : '';

        // Update profil
        if ($this->user->update()) {
            // Update data session
            $_SESSION['name'] = $this->user->name;
            $_SESSION['alamat_email'] = $this->user->alamat_email;
            $_SESSION['no_telepon'] = $this->user->no_telepon;
            return true;
        }
        
        return false;
    }

    // Update password
    public function updatePassword() {
        // Memastikan request adalah POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        // Memastikan user sudah login
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Mengambil data dari form
        $this->user->id = $_SESSION['user_id'];
        $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

        // Mencari user berdasarkan ID
        $this->user->alamat_email = $_SESSION['alamat_email'];
        if ($this->user->findByEmail()) {
            // Verifikasi password lama
            if (password_verify($current_password, $this->user->password)) {
                // Password valid, update password baru
                $this->user->password = $new_password;
                if ($this->user->updatePassword()) {
                    return true;
                }
            }
        }
        
        return false;
    }

    // Hapus akun
    public function deleteAccount() {
        // Memastikan user sudah login
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Set ID user
        $this->user->id = $_SESSION['user_id'];

        // Hapus akun
        if ($this->user->delete()) {
            // Logout setelah menghapus akun
            $this->logout();
            return true;
        }
        
        return false;
    }
}
?>