<?php
require_once 'config/database.php';

class Category {
    // Database connection and table name
    private $conn;
    private $table_name = "categories";

    // Object properties
    public $id;
    public $name;
    public $icon;
    public $created_at;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // READ all categories
    public function readAll() {
        // Query to get all categories
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // READ one category
    public function readOne() {
        // Query to read single category
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->id);

        // Execute query
        $stmt->execute();

        // Get row count
        $num = $stmt->rowCount();

        // If category found
        if($num > 0) {
            // Get record details
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set values to object properties
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->icon = $row['icon'];
            $this->created_at = $row['created_at'];

            return true;
        }

        return false;
    }
}
?>