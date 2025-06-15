<?php
require_once 'models/Bootcamp.php';
require_once 'models/Category.php';
require_once 'models/Review.php';
require_once 'models/Wishlist.php';
require_once 'config/database.php';

class BootcampController {
    private $database;
    private $db;
    private $bootcamp;
    private $category;
    private $review;
    private $wishlist;

    public function __construct() {
        // Initialize database connection
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        
        // Initialize models
        $this->bootcamp = new Bootcamp($this->db);
        $this->category = new Category($this->db);
        $this->review = new Review($this->db);
        $this->wishlist = new Wishlist($this->db);
    }

    // Show all bootcamps
    public function index() {
        // Set up pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;

        // Get bootcamps
        $stmt = $this->bootcamp->readAll($limit, $offset);
        $bootcamps = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get categories for filter sidebar
        $categoryStmt = $this->category->readAll();
        $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total bootcamps for pagination
        $total_bootcamps = $this->bootcamp->countAll();
        $total_pages = ceil($total_bootcamps / $limit);

        // Include the view
        include_once 'views/bootcamp/index.php';
    }

    // Show bootcamps by category
    public function category() {
        // Get category ID from URL
        $category_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Missing ID.');
        
        // Get category details
        $this->category->id = $category_id;
        $this->category->readOne();

        if (!$this->category->id) {
            // Category not found
            header('Location: index.php?action=bootcamps');
            exit();
        }

        // Set up pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;

        // Get bootcamps by category
        $stmt = $this->bootcamp->readByCategory($category_id, $limit, $offset);
        $bootcamps = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all categories for filter sidebar
        $categoryStmt = $this->category->readAll();
        $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total bootcamps for pagination
        $total_bootcamps = $this->bootcamp->countByCategory($category_id);
        $total_pages = ceil($total_bootcamps / $limit);

        // Include the view
        include_once 'views/bootcamp/category.php';
    }

    // Show bootcamp details
    public function detail() {
        // Get bootcamp ID from URL
        $bootcamp_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Missing ID.');
        
        // Get bootcamp details
        $this->bootcamp->id = $bootcamp_id;
        $this->bootcamp->readOne();

        if (!$this->bootcamp->id) {
            // Bootcamp not found
            header('Location: index.php?action=bootcamps');
            exit();
        }

        // Check if user is logged in
        $is_logged_in = isset($_SESSION['user_id']);
        $user_enrolled = false;
        $in_wishlist = false;
        $user_review = null;

        if ($is_logged_in) {
            $user_id = $_SESSION['user_id'];
            
            // Check if user is enrolled in this bootcamp
            $user_enrolled = $this->bootcamp->isUserEnrolled($user_id);
            
            // Check if bootcamp is in user's wishlist
            $this->wishlist->user_id = $user_id;
            $this->wishlist->bootcamp_id = $bootcamp_id;
            $in_wishlist = $this->wishlist->checkExist();
            
            // Get user's review if exists
            $this->review->bootcamp_id = $bootcamp_id;
            $this->review->user_id = $user_id;
            if ($this->review->getUserReview()) {
                $user_review = [
                    'id' => $this->review->id,
                    'rating' => $this->review->rating,
                    'review_text' => $this->review->review_text,
                    'created_at' => $this->review->created_at
                ];
            }
        }

        // Get bootcamp reviews
        $this->review->bootcamp_id = $bootcamp_id;
        $reviewStmt = $this->review->getBootcampReviews(10, 0);
        $reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get bootcamp average rating
        $rating_data = $this->review->getBootcampRating();
        $avg_rating = $rating_data['avg_rating'];
        $review_count = $rating_data['review_count'];

        // Include the view
        include_once 'views/bootcamp/detail.php';
    }

    // Search bootcamps
    public function search() {
        // Get search keyword
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        
        if (empty($keyword)) {
            header('Location: index.php?action=bootcamps');
            exit();
        }

        // Set up pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;

        // Search bootcamps
        $stmt = $this->bootcamp->search($keyword, $limit, $offset);
        $bootcamps = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get categories for filter sidebar
        $categoryStmt = $this->category->readAll();
        $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total search results for pagination
        $total_bootcamps = $this->bootcamp->countSearch($keyword);
        $total_pages = ceil($total_bootcamps / $limit);

        // Include the view
        include_once 'views/bootcamp/search.php';
    }

    // Show user's enrolled bootcamps
    public function myBootcamps() {
        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // Set up pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;

        // Get user's bootcamps
        $stmt = $this->bootcamp->getUserBootcamps($user_id, $limit, $offset);
        $bootcamps = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total bootcamps for pagination
        $total_bootcamps = $this->bootcamp->countUserBootcamps($user_id);
        $total_pages = ceil($total_bootcamps / $limit);

        // Include the view
        include_once 'views/bootcamp/my_bootcamps.php';
    }
}
?>