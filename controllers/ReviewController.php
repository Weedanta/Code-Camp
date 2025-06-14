<?php
require_once 'models/Review.php';
require_once 'models/Bootcamp.php';
require_once 'config/database.php';

class ReviewController
{
    private $database;
    private $db;
    private $review;
    private $bootcamp;

    public function __construct()
    {
        // Initialize database connection
        $this->database = new Database();
        $this->db = $this->database->getConnection();

        // Initialize models
        $this->review = new Review($this->db);
        $this->bootcamp = new Bootcamp($this->db);
    }

    // Add or update review
    public function addReview()
    {
        // Check if user is logged in
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'Please login to submit a review'
            ];
            echo json_encode($response);
            exit();
        }

        // Check if it's a POST request and validate data
        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST' ||
            !isset($_POST['bootcamp_id']) ||
            !isset($_POST['rating']) ||
            !isset($_POST['review_text'])
        ) {
            $response = [
                'success' => false,
                'message' => 'Invalid request'
            ];
            echo json_encode($response);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $bootcamp_id = $_POST['bootcamp_id'];
        $rating = intval($_POST['rating']);
        $review_text = $_POST['review_text'];

        // Check if rating is valid (1-5)
        if ($rating < 1 || $rating > 5) {
            $response = [
                'success' => false,
                'message' => 'Rating must be between 1 and 5'
            ];
            echo json_encode($response);
            exit();
        }

        // Check if user is enrolled in this bootcamp
        $this->bootcamp->id = $bootcamp_id;
        if (!$this->bootcamp->isUserEnrolled($user_id)) {
            $response = [
                'success' => false,
                'message' => 'You can only review bootcamps you have enrolled in'
            ];
            echo json_encode($response);
            exit();
        }

        // Set review data
        $this->review->bootcamp_id = $bootcamp_id;
        $this->review->user_id = $user_id;
        $this->review->rating = $rating;
        $this->review->review_text = $review_text;

        // Add/update review
        if ($this->review->create()) {
            $response = [
                'success' => true,
                'message' => 'Review submitted successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to submit review'
            ];
        }

        echo json_encode($response);
    }

    // Get bootcamp reviews (AJAX)
    public function getBootcampReviews()
    {
        // Get bootcamp ID
        $bootcamp_id = isset($_GET['bootcamp_id']) ? $_GET['bootcamp_id'] : die(json_encode(['error' => 'Missing bootcamp ID']));

        // Set up pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;

        // Get reviews
        $this->review->bootcamp_id = $bootcamp_id;
        $stmt = $this->review->getBootcampReviews($limit, $offset);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total reviews for pagination
        $total_reviews = $this->review->countBootcampReviews();
        $total_pages = ceil($total_reviews / $limit);

        // Return JSON
        echo json_encode([
            'reviews' => $reviews,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_reviews' => $total_reviews
            ]
        ]);
    }

    // Show user's reviews page
    public function myReviews()
    {
        // Check if user is logged in
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // Set up pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Get user's reviews with bootcamp details
        $stmt = $this->review->getUserReviews($user_id, $limit, $offset);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total reviews for pagination
        $total_reviews = $this->review->countUserReviews($user_id);
        $total_pages = ceil($total_reviews / $limit);

        // Include the view
        include_once 'views/review/my_reviews.php';
    }

    // Update review
    public function updateReview()
    {
        // Check if user is logged in
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'Please login to update review'
            ];
            echo json_encode($response);
            exit();
        }

        // Check if it's a POST request and validate data
        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST' ||
            !isset($_POST['review_id']) ||
            !isset($_POST['rating']) ||
            !isset($_POST['review_text'])
        ) {
            $response = [
                'success' => false,
                'message' => 'Invalid request'
            ];
            echo json_encode($response);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $review_id = $_POST['review_id'];
        $rating = intval($_POST['rating']);
        $review_text = $_POST['review_text'];

        // Check if rating is valid (1-5)
        if ($rating < 1 || $rating > 5) {
            $response = [
                'success' => false,
                'message' => 'Rating must be between 1 and 5'
            ];
            echo json_encode($response);
            exit();
        }

        // Set review data
        $this->review->id = $review_id;
        $this->review->user_id = $user_id;
        $this->review->rating = $rating;
        $this->review->review_text = $review_text;

        // Update review
        if ($this->review->updateById()) {
            $response = [
                'success' => true,
                'message' => 'Review updated successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to update review or unauthorized'
            ];
        }

        echo json_encode($response);
    }

    // Delete review
    public function deleteReview()
    {
        // Check if user is logged in
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'Please login to delete review'
            ];
            echo json_encode($response);
            exit();
        }

        // Get review ID
        $review_id = isset($_POST['review_id']) ? $_POST['review_id'] : null;
        if (!$review_id) {
            $response = [
                'success' => false,
                'message' => 'Review ID is required'
            ];
            echo json_encode($response);
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // Set review data
        $this->review->id = $review_id;
        $this->review->user_id = $user_id;

        // Delete review
        if ($this->review->deleteById()) {
            $response = [
                'success' => true,
                'message' => 'Review deleted successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to delete review or unauthorized'
            ];
        }

        echo json_encode($response);
    }

    // Get single review for editing (AJAX)
    public function getReview()
    {
        // Check if user is logged in
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit();
        }

        $review_id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$review_id) {
            echo json_encode(['success' => false, 'message' => 'Review ID is required']);
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // Get review details
        $this->review->id = $review_id;
        $this->review->user_id = $user_id;

        if ($this->review->readOneByUser()) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $this->review->id,
                    'bootcamp_id' => $this->review->bootcamp_id,
                    'rating' => $this->review->rating,
                    'review_text' => $this->review->review_text,
                    'created_at' => $this->review->created_at
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Review not found or unauthorized']);
        }
    }
}
