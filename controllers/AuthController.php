<?php
require_once 'models/User.php';
require_once 'config/database.php';

class AuthController
{
    private $database;
    private $db;
    private $user;

    public function __construct()
    {
        // Inisialisasi koneksi database
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->user = new User($this->db);
    }

    // Helper method untuk memulai session dengan aman
    private function safeSessionStart()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Menampilkan halaman login
    public function showLoginPage()
    {
        // Cek apakah user sudah login
        $this->safeSessionStart();
        if (isset($_SESSION['user_id'])) {
            // Jika sudah login, redirect ke home
            header('Location: index.php');
            exit();
        }

        include_once 'views/auth/login.php';
    }

    // Menampilkan halaman signup
    public function showSignupPage()
    {
        // Cek apakah user sudah login
        $this->safeSessionStart();
        if (isset($_SESSION['user_id'])) {
            // Jika sudah login, redirect ke home
            header('Location: index.php');
            exit();
        }

        include_once 'views/auth/signup.php';
    }

    // Proses login
    public function login()
    {
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
                $this->safeSessionStart();
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
    public function signup()
    {
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
    public function logout()
    {
        // Mulai session dengan aman
        $this->safeSessionStart();

        // Hapus semua data session
        $_SESSION = array();

        // Hapus session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        // Destroy session
        session_destroy();

        // Redirect ke halaman login
        header("Location: index.php?action=login");
        exit();
    }

    // Update profil user
    public function updateProfile()
    {
        // Memastikan request adalah POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        // Memastikan user sudah login
        $this->safeSessionStart();
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

    // Upload foto profil
    // Upload foto profil
    public function uploadProfilePhoto($file)
    {
        // Memastikan user sudah login
        $this->safeSessionStart();
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Validasi file
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Validasi ukuran file (maksimal 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }

        // Mendapatkan ekstensi file
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Validasi ekstensi file (hanya izinkan jpg, jpeg, png, gif)
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($file_extension, $allowed_extensions)) {
            return false;
        }

        // Validasi tipe MIME file
        $allowed_mimes = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed_mimes)) {
            return false;
        }

        // Set direktori upload (path absolut)
        $upload_dir = __DIR__ . '/../assets/images/users/';

        // Buat direktori jika belum ada
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Hapus foto lama jika ada
        $old_files = glob($upload_dir . $_SESSION['user_id'] . '.*');
        foreach ($old_files as $old_file) {
            if (file_exists($old_file)) {
                unlink($old_file);
            }
        }

        // Set nama file (gunakan ID user dengan ekstensi asli)
        $filename = $_SESSION['user_id'] . '.' . $file_extension;
        $upload_path = $upload_dir . $filename;

        // Upload file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Resize image jika terlalu besar
            $this->resizeImage($upload_path, $upload_path, 300, 300);
            return true;
        }

        return false;
    }

    // Method tambahan untuk resize image
    private function resizeImage($source, $destination, $max_width, $max_height)
    {
        // Get image info
        $image_info = getimagesize($source);
        if (!$image_info) return false;

        $width = $image_info[0];
        $height = $image_info[1];
        $type = $image_info[2];

        // Calculate new dimensions
        $ratio = min($max_width / $width, $max_height / $height);
        if ($ratio >= 1) return true; // Image is already small enough

        $new_width = round($width * $ratio);
        $new_height = round($height * $ratio);

        // Create new image resource
        $new_image = imagecreatetruecolor($new_width, $new_height);

        // Load original image
        switch ($type) {
            case IMAGETYPE_JPEG:
                $original = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $original = imagecreatefrompng($source);
                // Preserve transparency
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
                break;
            case IMAGETYPE_GIF:
                $original = imagecreatefromgif($source);
                break;
            default:
                return false;
        }

        if (!$original) return false;

        // Resize
        imagecopyresampled($new_image, $original, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // Save resized image
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($new_image, $destination, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($new_image, $destination);
                break;
            case IMAGETYPE_GIF:
                imagegif($new_image, $destination);
                break;
        }

        // Clean up
        imagedestroy($original);
        imagedestroy($new_image);

        return true;
    }

    // Update password
    public function updatePassword()
    {
        // Memastikan request adalah POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        // Memastikan user sudah login
        $this->safeSessionStart();
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Mengambil data dari form
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
    public function deleteAccount()
    {
        // Memastikan user sudah login
        $this->safeSessionStart();
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Set ID user
        $this->user->id = $_SESSION['user_id'];

        // Hapus akun
        if ($this->user->delete()) {
            // Hapus foto profil jika ada
            $profile_photo = 'assets/images/users/' . $_SESSION['user_id'] . '.jpg';
            if (file_exists($profile_photo)) {
                unlink($profile_photo);
            }

            // Logout setelah menghapus akun
            $this->logout();
            return true;
        }

        return false;
    }

    // Fungsi helper untuk mengecek apakah user sudah login
    public function isLoggedIn()
    {
        $this->safeSessionStart();
        return isset($_SESSION['user_id']);
    }

    // Fungsi helper untuk mendapatkan data user yang sedang login
    public function getCurrentUser()
    {
        $this->safeSessionStart();
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['name'],
                'alamat_email' => $_SESSION['alamat_email'],
                'no_telepon' => $_SESSION['no_telepon']
            ];
        }
        return null;
    }
}
