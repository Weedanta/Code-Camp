<?php
require_once 'models/Wishlist.php';
require_once 'config/database.php';

class WishlistController {
    private $database;
    private $db;
    private $wishlist;

    public function __construct() {
        // Initialize database connection
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        
        // Initialize model
        $this->wishlist = new Wishlist($this->db);
    }

    // Show user's wishlist
    public function index() {
        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $this->wishlist->user_id = $user_id;

        // Set up pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;

        // Get wishlist items
        $stmt = $this->wishlist->getUserWishlist($limit, $offset);
        $wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total wishlist items for pagination
        $total_items = $this->wishlist->countUserWishlist();
        $total_pages = ceil($total_items / $limit);

        // Include the view
        include_once 'views/wishlist/index.php';
    }

    // Add bootcamp to wishlist
    public function add() {
        // Set content type to JSON
        header('Content-Type: application/json');
        
        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'Please login to add to wishlist'
            ];
            echo json_encode($response);
            exit();
        }

        // Get bootcamp ID
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['bootcamp_id'])) {
            $response = [
                'success' => false,
                'message' => 'Invalid request'
            ];
            echo json_encode($response);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $bootcamp_id = $_POST['bootcamp_id'];

        // Validate bootcamp_id
        if (empty($bootcamp_id) || !is_numeric($bootcamp_id)) {
            $response = [
                'success' => false,
                'message' => 'Invalid bootcamp ID'
            ];
            echo json_encode($response);
            exit();
        }

        // Add to wishlist
        $this->wishlist->user_id = $user_id;
        $this->wishlist->bootcamp_id = $bootcamp_id;

        if ($this->wishlist->add()) {
            $response = [
                'success' => true,
                'message' => 'Bootcamp added to wishlist'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to add to wishlist or already in wishlist'
            ];
        }

        echo json_encode($response);
        exit();
    }

    // Remove bootcamp from wishlist
    public function remove() {
        // Set content type to JSON
        header('Content-Type: application/json');
        
        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'Please login to remove from wishlist'
            ];
            echo json_encode($response);
            exit();
        }

        // Get bootcamp ID
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['bootcamp_id'])) {
            $response = [
                'success' => false,
                'message' => 'Invalid request method or missing bootcamp ID'
            ];
            echo json_encode($response);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $bootcamp_id = $_POST['bootcamp_id'];

        // Validate bootcamp_id
        if (empty($bootcamp_id) || !is_numeric($bootcamp_id)) {
            $response = [
                'success' => false,
                'message' => 'Invalid bootcamp ID'
            ];
            echo json_encode($response);
            exit();
        }

        // Remove from wishlist
        $this->wishlist->user_id = $user_id;
        $this->wishlist->bootcamp_id = $bootcamp_id;

        if ($this->wishlist->remove()) {
            $response = [
                'success' => true,
                'message' => 'Bootcamp removed from wishlist'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to remove from wishlist or item not found'
            ];
        }

        echo json_encode($response);
        exit();
    }
}
?>