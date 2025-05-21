<?php
require_once 'config/database.php';

class Bootcamp {
    // Database connection and table name
    private $conn;
    private $table_name = "bootcamps";

    // Object properties
    public $id;
    public $title;
    public $description;
    public $category_id;
    public $instructor_name;
    public $instructor_photo;
    public $price;
    public $discount_price;
    public $start_date;
    public $duration;
    public $image;
    public $status;
    public $created_at;
    public $category_name;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // READ all bootcamps
    public function readAll($limit = 10, $offset = 0) {
        // Query to get all bootcamps with category name
        $query = "SELECT b.*, c.name as category_name 
                FROM " . $this->table_name . " b
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE b.status = 'active'
                ORDER BY b.created_at DESC
                LIMIT ? OFFSET ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // READ bootcamps by category
    public function readByCategory($category_id, $limit = 10, $offset = 0) {
        // Query to get bootcamps by category
        $query = "SELECT b.*, c.name as category_name 
                FROM " . $this->table_name . " b
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE b.category_id = ? AND b.status = 'active'
                ORDER BY b.created_at DESC
                LIMIT ? OFFSET ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(1, $category_id);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // READ a single bootcamp
    public function readOne() {
        // Query to read single bootcamp
        $query = "SELECT b.*, c.name as category_name 
                FROM " . $this->table_name . " b
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE b.id = ?
                LIMIT 0,1";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->id);

        // Execute query
        $stmt->execute();

        // Get row count
        $num = $stmt->rowCount();

        // If bootcamp found
        if($num > 0) {
            // Get record details
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set values to object properties
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->category_id = $row['category_id'];
            $this->instructor_name = $row['instructor_name'];
            $this->instructor_photo = $row['instructor_photo'];
            $this->price = $row['price'];
            $this->discount_price = $row['discount_price'];
            $this->start_date = $row['start_date'];
            $this->duration = $row['duration'];
            $this->image = $row['image'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->category_name = $row['category_name'];

            return true;
        }

        return false;
    }

    // Check if user owns bootcamp
    public function isUserEnrolled($user_id) {
        // Query to check if user has purchased this bootcamp
        $query = "SELECT COUNT(*) as total
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = ? AND oi.bootcamp_id = ? AND o.payment_status = 'completed'";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $this->id);

        // Execute query
        $stmt->execute();

        // Get result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($row['total'] > 0);
    }

    // Search bootcamps
    public function search($keywords, $limit = 10, $offset = 0) {
        // Query to search bootcamps
        $query = "SELECT b.*, c.name as category_name 
                FROM " . $this->table_name . " b
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE b.title LIKE ? OR b.description LIKE ? OR c.name LIKE ?
                ORDER BY b.created_at DESC
                LIMIT ? OFFSET ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize keywords
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        // Bind parameters
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->bindParam(4, $limit, PDO::PARAM_INT);
        $stmt->bindParam(5, $offset, PDO::PARAM_INT);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // Count total bootcamps (for pagination)
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Count bootcamps by category (for pagination)
    public function countByCategory($category_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE category_id = ? AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Count search results (for pagination)
    public function countSearch($keywords) {
        $query = "SELECT COUNT(*) as total 
                FROM " . $this->table_name . " b
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE b.title LIKE ? OR b.description LIKE ? OR c.name LIKE ?";
        
        $stmt = $this->conn->prepare($query);
        
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";
        
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Get user's enrolled bootcamps
    public function getUserBootcamps($user_id, $limit = 10, $offset = 0) {
        // Query to get user's purchased bootcamps
        $query = "SELECT DISTINCT b.*, c.name as category_name 
                FROM " . $this->table_name . " b
                LEFT JOIN categories c ON b.category_id = c.id
                JOIN order_items oi ON b.id = oi.bootcamp_id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.user_id = ? AND o.payment_status = 'completed'
                ORDER BY o.created_at DESC
                LIMIT ? OFFSET ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // Count user's enrolled bootcamps (for pagination)
    public function countUserBootcamps($user_id) {
        $query = "SELECT COUNT(DISTINCT b.id) as total 
                FROM " . $this->table_name . " b
                JOIN order_items oi ON b.id = oi.bootcamp_id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.user_id = ? AND o.payment_status = 'completed'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>