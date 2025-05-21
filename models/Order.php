<?php
require_once 'config/database.php';

class Order {
    // Database connection and table name
    private $conn;
    private $table_name = "orders";
    private $items_table = "order_items";

    // Object properties
    public $id;
    public $user_id;
    public $total_amount;
    public $payment_status;
    public $payment_method;
    public $transaction_id;
    public $created_at;
    public $updated_at;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new order
    public function create() {
        // Begin transaction
        $this->conn->beginTransaction();

        try {
            // Query to insert order record
            $query = "INSERT INTO " . $this->table_name . "
                    SET
                        user_id = :user_id,
                        total_amount = :total_amount,
                        payment_status = :payment_status,
                        payment_method = :payment_method,
                        transaction_id = :transaction_id,
                        created_at = :created_at";

            // Prepare query
            $stmt = $this->conn->prepare($query);

            // Sanitize and bind values
            $this->user_id = htmlspecialchars(strip_tags($this->user_id));
            $this->total_amount = htmlspecialchars(strip_tags($this->total_amount));
            $this->payment_status = htmlspecialchars(strip_tags($this->payment_status));
            $this->payment_method = htmlspecialchars(strip_tags($this->payment_method));
            $this->transaction_id = htmlspecialchars(strip_tags($this->transaction_id));
            $this->created_at = date('Y-m-d H:i:s');

            // Bind values
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":total_amount", $this->total_amount);
            $stmt->bindParam(":payment_status", $this->payment_status);
            $stmt->bindParam(":payment_method", $this->payment_method);
            $stmt->bindParam(":transaction_id", $this->transaction_id);
            $stmt->bindParam(":created_at", $this->created_at);

            // Execute query
            $stmt->execute();

            // Get order ID
            $this->id = $this->conn->lastInsertId();

            // Commit transaction
            $this->conn->commit();

            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            return false;
        }
    }

    // Add bootcamp to order
    public function addItem($bootcamp_id, $price) {
        // Query to insert order item
        $query = "INSERT INTO " . $this->items_table . "
                SET
                    order_id = :order_id,
                    bootcamp_id = :bootcamp_id,
                    price = :price,
                    created_at = :created_at";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $bootcamp_id = htmlspecialchars(strip_tags($bootcamp_id));
        $price = htmlspecialchars(strip_tags($price));
        $created_at = date('Y-m-d H:i:s');

        // Bind values
        $stmt->bindParam(":order_id", $this->id);
        $stmt->bindParam(":bootcamp_id", $bootcamp_id);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":created_at", $created_at);

        // Execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update order payment status
    public function updatePaymentStatus() {
        // Query to update payment status
        $query = "UPDATE " . $this->table_name . "
                SET
                    payment_status = :payment_status,
                    transaction_id = :transaction_id
                WHERE id = :id";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $this->payment_status = htmlspecialchars(strip_tags($this->payment_status));
        $this->transaction_id = htmlspecialchars(strip_tags($this->transaction_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":payment_status", $this->payment_status);
        $stmt->bindParam(":transaction_id", $this->transaction_id);
        $stmt->bindParam(":id", $this->id);

        // Execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Read one order
    public function readOne() {
        // Query to read single order
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE id = ? LIMIT 0,1";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->id);

        // Execute query
        $stmt->execute();

        // Get row count
        $num = $stmt->rowCount();

        // If order found
        if($num > 0) {
            // Get record details
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set values to object properties
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->total_amount = $row['total_amount'];
            $this->payment_status = $row['payment_status'];
            $this->payment_method = $row['payment_method'];
            $this->transaction_id = $row['transaction_id'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];

            return true;
        }

        return false;
    }

    // Get order items
    public function getOrderItems() {
        // Query to get order items with bootcamp details
        $query = "SELECT i.*, b.title, b.image 
                FROM " . $this->items_table . " i
                JOIN bootcamps b ON i.bootcamp_id = b.id
                WHERE i.order_id = ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->id);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // Get user orders
    public function getUserOrders($limit = 10, $offset = 0) {
        // Query to get user orders
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE user_id = ?
                ORDER BY created_at DESC
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

    // Count user orders (for pagination)
    public function countUserOrders() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>