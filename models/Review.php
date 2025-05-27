<?php
require_once 'config/database.php';

class Review {
    // Database connection and table name
    private $conn;
    private $table_name = "reviews";

    // Object properties
    public $id;
    public $bootcamp_id;
    public $user_id;
    public $rating;
    public $review_text;
    public $created_at;
    public $updated_at;
    public $user_name;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new review
    public function create() {
        // Check if user has already reviewed this bootcamp
        if ($this->checkExist()) {
            return $this->update();
        }

        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    bootcamp_id = :bootcamp_id,
                    user_id = :user_id,
                    rating = :rating,
                    review_text = :review_text,
                    created_at = :created_at";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $this->bootcamp_id = htmlspecialchars(strip_tags($this->bootcamp_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->review_text = htmlspecialchars(strip_tags($this->review_text));
        $this->created_at = date('Y-m-d H:i:s');

        // Bind values
        $stmt->bindParam(":bootcamp_id", $this->bootcamp_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":review_text", $this->review_text);
        $stmt->bindParam(":created_at", $this->created_at);

        // Execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update existing review
    public function update() {
        // Query to update record
        $query = "UPDATE " . $this->table_name . "
                SET
                    rating = :rating,
                    review_text = :review_text
                WHERE bootcamp_id = :bootcamp_id AND user_id = :user_id";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $this->bootcamp_id = htmlspecialchars(strip_tags($this->bootcamp_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->review_text = htmlspecialchars(strip_tags($this->review_text));

        // Bind values
        $stmt->bindParam(":bootcamp_id", $this->bootcamp_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":review_text", $this->review_text);

        // Execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update review by ID (for user's own reviews)
    public function updateById() {
        // Query to update record by ID and user_id (security check)
        $query = "UPDATE " . $this->table_name . "
                SET
                    rating = :rating,
                    review_text = :review_text,
                    updated_at = NOW()
                WHERE id = :id AND user_id = :user_id";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->review_text = htmlspecialchars(strip_tags($this->review_text));

        // Bind values
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":review_text", $this->review_text);

        // Execute query
        if($stmt->execute() && $stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    // Delete review by ID (for user's own reviews)
    public function deleteById() {
        // Query to delete record by ID and user_id (security check)
        $query = "DELETE FROM " . $this->table_name . " 
                WHERE id = :id AND user_id = :user_id";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        // Bind values
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);

        // Execute query
        if($stmt->execute() && $stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    // Check if user has already reviewed this bootcamp
    public function checkExist() {
        // Query to check if exists
        $query = "SELECT id FROM " . $this->table_name . " 
                WHERE bootcamp_id = ? AND user_id = ?
                LIMIT 0,1";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(1, $this->bootcamp_id);
        $stmt->bindParam(2, $this->user_id);

        // Execute query
        $stmt->execute();

        // Get row count
        $num = $stmt->rowCount();

        // If found
        return ($num > 0);
    }

    // Get user's review for a bootcamp
    public function getUserReview() {
        // Query to read user's review
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE bootcamp_id = ? AND user_id = ?
                LIMIT 0,1";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(1, $this->bootcamp_id);
        $stmt->bindParam(2, $this->user_id);

        // Execute query
        $stmt->execute();

        // Get row count
        $num = $stmt->rowCount();

        // If review found
        if($num > 0) {
            // Get record details
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set values to object properties
            $this->id = $row['id'];
            $this->bootcamp_id = $row['bootcamp_id'];
            $this->user_id = $row['user_id'];
            $this->rating = $row['rating'];
            $this->review_text = $row['review_text'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];

            return true;
        }

        return false;
    }

    // Read one review by ID and user_id (for editing)
    public function readOneByUser() {
        // Query to read single review by ID and user_id
        $query = "SELECT r.*, b.title as bootcamp_title 
                FROM " . $this->table_name . " r
                LEFT JOIN bootcamps b ON r.bootcamp_id = b.id
                WHERE r.id = ? AND r.user_id = ?
                LIMIT 0,1";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $this->user_id);

        // Execute query
        $stmt->execute();

        // Get row count
        $num = $stmt->rowCount();

        // If review found
        if($num > 0) {
            // Get record details
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set values to object properties
            $this->id = $row['id'];
            $this->bootcamp_id = $row['bootcamp_id'];
            $this->user_id = $row['user_id'];
            $this->rating = $row['rating'];
            $this->review_text = $row['review_text'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];

            return true;
        }

        return false;
    }

    // Get all reviews for a bootcamp
    public function getBootcampReviews($limit = 10, $offset = 0) {
        // Query to get reviews with user details
        $query = "SELECT r.*, u.name as user_name 
                FROM " . $this->table_name . " r
                JOIN users u ON r.user_id = u.id
                WHERE r.bootcamp_id = ?
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(1, $this->bootcamp_id);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // Get user's reviews with bootcamp details
    public function getUserReviews($user_id, $limit = 10, $offset = 0) {
        // Query to get user's reviews with bootcamp details
        $query = "SELECT r.*, b.title as bootcamp_title, b.image as bootcamp_image, c.name as category_name
                FROM " . $this->table_name . " r
                JOIN bootcamps b ON r.bootcamp_id = b.id
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // Get bootcamp average rating
    public function getBootcampRating() {
        // Query to get average rating
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
                FROM " . $this->table_name . " 
                WHERE bootcamp_id = ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind bootcamp ID
        $stmt->bindParam(1, $this->bootcamp_id);

        // Execute query
        $stmt->execute();

        // Get result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'avg_rating' => round($row['avg_rating'], 1),
            'review_count' => $row['review_count']
        ];
    }

    // Count bootcamp reviews (for pagination)
    public function countBootcampReviews() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE bootcamp_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->bootcamp_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Count user reviews (for pagination)
    public function countUserReviews($user_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>