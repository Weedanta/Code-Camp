<?php
require_once 'config/database.php';

class Wishlist {
    // Database connection and table name
    private $conn;
    private $table_name = "wishlists";

    // Object properties
    public $id;
    public $user_id;
    public $bootcamp_id;
    public $created_at;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Add bootcamp to wishlist
    public function add() {
        // Check if already in wishlist
        if ($this->checkExist()) {
            return true;
        }

        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_id = :user_id,
                    bootcamp_id = :bootcamp_id,
                    created_at = :created_at";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->bootcamp_id = htmlspecialchars(strip_tags($this->bootcamp_id));
        $this->created_at = date('Y-m-d H:i:s');

        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":bootcamp_id", $this->bootcamp_id);
        $stmt->bindParam(":created_at", $this->created_at);

        // Execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Remove bootcamp from wishlist
    public function remove() {
        // Query to delete record
        $query = "DELETE FROM " . $this->table_name . " 
                WHERE user_id = :user_id AND bootcamp_id = :bootcamp_id";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->bootcamp_id = htmlspecialchars(strip_tags($this->bootcamp_id));

        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":bootcamp_id", $this->bootcamp_id);

        // Execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Check if bootcamp already in user's wishlist
    public function checkExist() {
        // Query to check if exists
        $query = "SELECT id FROM " . $this->table_name . " 
                WHERE user_id = ? AND bootcamp_id = ?
                LIMIT 0,1";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $this->bootcamp_id);

        // Execute query
        $stmt->execute();

        // Get row count
        $num = $stmt->rowCount();

        // If found
        return ($num > 0);
    }

    // Get user's wishlist bootcamps
    public function getUserWishlist($limit = 10, $offset = 0) {
        // Query to get wishlist bootcamps with bootcamp details
        $query = "SELECT b.*, c.name as category_name, w.id as wishlist_id 
                FROM " . $this->table_name . " w
                JOIN bootcamps b ON w.bootcamp_id = b.id
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE w.user_id = ?
                ORDER BY w.created_at DESC
                LIMIT ? OFFSET ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // Count user's wishlist items (for pagination)
    public function countUserWishlist() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>