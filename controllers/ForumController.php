<?php
require_once 'config/database.php';
require_once 'models/ForumPost.php';
require_once 'models/ForumReply.php';

class ForumController {
    private $db;
    private $forumPost;
    private $forumReply;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->forumPost = new ForumPost($this->db);
        $this->forumReply = new ForumReply($this->db);
    }

    // Menampilkan daftar semua post forum
    public function index() {
        // Cek apakah user sudah login
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Ambil data posts
        $stmt = $this->forumPost->readAll($limit, $offset);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Hitung total posts untuk pagination
        $total_posts = $this->forumPost->countAll();
        $total_pages = ceil($total_posts / $limit);

        // Set data untuk view
        $page_title = "Forum Diskusi - Campus Hub";
        $current_page = "forum";

        include_once 'views/forum/index.php';
    }

    // Menampilkan detail post dan replies
    public function detail() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_GET['id'])) {
            header('Location: index.php?action=forum');
            exit();
        }

        $post_id = $_GET['id'];
        $this->forumPost->id = $post_id;
        $post = $this->forumPost->readOne();

        if (!$post) {
            header('Location: index.php?action=forum&error=post_not_found');
            exit();
        }

        // Ambil replies
        $stmt = $this->forumReply->getByPostId($post_id);
        $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $page_title = htmlspecialchars($post['title']) . " - Forum Diskusi";
        $current_page = "forum";

        include_once 'views/forum/detail.php';
    }

    // Menampilkan form untuk membuat post baru
    public function create() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $page_title = "Buat Post Baru - Forum Diskusi";
        $current_page = "forum";

        include_once 'views/forum/create.php';
    }

    // Memproses pembuatan post baru
    public function store() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=login');
            exit();
        }

        if (empty($_POST['title']) || empty($_POST['content'])) {
            header('Location: index.php?action=forum_create&error=empty_fields');
            exit();
        }

        $this->forumPost->user_id = $_SESSION['user_id'];
        $this->forumPost->title = $_POST['title'];
        $this->forumPost->content = $_POST['content'];

        if ($this->forumPost->create()) {
            header('Location: index.php?action=forum_detail&id=' . $this->forumPost->id . '&success=post_created');
        } else {
            header('Location: index.php?action=forum_create&error=create_failed');
        }
        exit();
    }

    // Menampilkan form edit post
    public function edit() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
            header('Location: index.php?action=forum');
            exit();
        }

        $post_id = $_GET['id'];
        $this->forumPost->id = $post_id;
        $post = $this->forumPost->readOne();

        if (!$post || $post['user_id'] != $_SESSION['user_id']) {
            header('Location: index.php?action=forum&error=unauthorized');
            exit();
        }

        $page_title = "Edit Post - Forum Diskusi";
        $current_page = "forum";

        include_once 'views/forum/edit.php';
    }

    // Memproses update post
    public function update() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
            header('Location: index.php?action=forum');
            exit();
        }

        if (empty($_POST['title']) || empty($_POST['content'])) {
            header('Location: index.php?action=forum_edit&id=' . $_POST['id'] . '&error=empty_fields');
            exit();
        }

        $this->forumPost->id = $_POST['id'];
        $this->forumPost->user_id = $_SESSION['user_id'];
        $this->forumPost->title = $_POST['title'];
        $this->forumPost->content = $_POST['content'];

        if ($this->forumPost->update()) {
            header('Location: index.php?action=forum_detail&id=' . $_POST['id'] . '&success=post_updated');
        } else {
            header('Location: index.php?action=forum_edit&id=' . $_POST['id'] . '&error=update_failed');
        }
        exit();
    }

    // Menghapus post
    public function deletePost() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
            header('Location: index.php?action=forum');
            exit();
        }

        $this->forumPost->id = $_POST['id'];
        $this->forumPost->user_id = $_SESSION['user_id'];

        if ($this->forumPost->delete()) {
            header('Location: index.php?action=forum&success=post_deleted');
        } else {
            header('Location: index.php?action=forum&error=delete_failed');
        }
        exit();
    }

    // Menambah reply
    public function addReply() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $redirect_url = isset($_POST['post_id']) ? 'index.php?action=forum_detail&id=' . $_POST['post_id'] : 'index.php?action=forum';
            header('Location: ' . $redirect_url);
            exit();
        }

        if (empty($_POST['content']) || empty($_POST['post_id'])) {
            header('Location: index.php?action=forum_detail&id=' . $_POST['post_id'] . '&error=empty_content');
            exit();
        }

        $this->forumReply->post_id = $_POST['post_id'];
        $this->forumReply->user_id = $_SESSION['user_id'];
        $this->forumReply->content = $_POST['content'];

        if ($this->forumReply->create()) {
            header('Location: index.php?action=forum_detail&id=' . $_POST['post_id'] . '&success=reply_added');
        } else {
            header('Location: index.php?action=forum_detail&id=' . $_POST['post_id'] . '&error=reply_failed');
        }
        exit();
    }

    // Edit reply
    public function editReply() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if (empty($_POST['content']) || empty($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => 'Content cannot be empty']);
            exit();
        }

        $this->forumReply->id = $_POST['id'];
        $this->forumReply->user_id = $_SESSION['user_id'];
        $this->forumReply->content = $_POST['content'];

        if ($this->forumReply->update()) {
            echo json_encode(['success' => true, 'message' => 'Reply updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed']);
        }
        exit();
    }

    // Hapus reply
    public function deleteReply() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if (empty($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid reply ID']);
            exit();
        }

        $this->forumReply->id = $_POST['id'];
        $this->forumReply->user_id = $_SESSION['user_id'];

        if ($this->forumReply->delete()) {
            echo json_encode(['success' => true, 'message' => 'Reply deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Delete failed']);
        }
        exit();
    }

    // Pencarian post
    public function search() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        if (empty($keyword)) {
            header('Location: index.php?action=forum');
            exit();
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $stmt = $this->forumPost->search($keyword, $limit, $offset);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $page_title = "Hasil Pencarian: " . htmlspecialchars($keyword) . " - Forum Diskusi";
        $current_page = "forum";

        include_once 'views/forum/search.php';
    }

    // Post milik user
    public function myPosts() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $stmt = $this->forumPost->getUserPosts($_SESSION['user_id'], $limit, $offset);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $page_title = "Post Saya - Forum Diskusi";
        $current_page = "forum";

        include_once 'views/forum/my_posts.php';
    }
}                                                                                           