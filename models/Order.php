<?php
require_once 'config/database.php';

class Order {
    // Database connection and table name
    private $conn;
    private $table_name = "orders";
    private $items_table = "order_items";

    // Object properties
    public $id;
    public $order_number;
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
        try {
            // Generate order number if not set
            if (empty($this->order_number)) {
                $this->order_number = $this->generateOrderNumber();
            }

            // Query to insert order record
            $query = "INSERT INTO " . $this->table_name . "
                    (order_number, user_id, total_amount, payment_status, payment_method, transaction_id, created_at)
                    VALUES (:order_number, :user_id, :total_amount, :payment_status, :payment_method, :transaction_id, :created_at)";

            // Prepare query
            $stmt = $this->conn->prepare($query);

            // Sanitize and validate values
            $this->user_id = (int)$this->user_id;
            $this->total_amount = (float)$this->total_amount;
            $this->payment_status = htmlspecialchars(strip_tags($this->payment_status));
            $this->payment_method = htmlspecialchars(strip_tags($this->payment_method));
            $this->transaction_id = htmlspecialchars(strip_tags($this->transaction_id));
            $this->created_at = date('Y-m-d H:i:s');

            // Validate required fields
            if ($this->user_id <= 0) {
                throw new Exception('Invalid user ID');
            }
            if ($this->total_amount <= 0) {
                throw new Exception('Invalid total amount');
            }
            if (empty($this->payment_status)) {
                throw new Exception('Payment status is required');
            }
            if (empty($this->payment_method)) {
                throw new Exception('Payment method is required');
            }

            // Bind values
            $stmt->bindParam(":order_number", $this->order_number);
            $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(":total_amount", $this->total_amount);
            $stmt->bindParam(":payment_status", $this->payment_status);
            $stmt->bindParam(":payment_method", $this->payment_method);
            $stmt->bindParam(":transaction_id", $this->transaction_id);
            $stmt->bindParam(":created_at", $this->created_at);

            // Execute query
            if ($stmt->execute()) {
                // Get order ID
                $this->id = $this->conn->lastInsertId();
                
                // Log successful creation
                error_log("Order created successfully: ID = " . $this->id);
                return true;
            } else {
                // Log error info
                $errorInfo = $stmt->errorInfo();
                error_log("Order creation failed: " . implode(' - ', $errorInfo));
                return false;
            }

        } catch (Exception $e) {
            // Log error
            error_log("Order creation exception: " . $e->getMessage());
            return false;
        }
    }

    // Add bootcamp to order
    public function addItem($bootcamp_id, $price) {
        try {
            // Validate inputs
            $bootcamp_id = (int)$bootcamp_id;
            $price = (float)$price;
            
            if ($bootcamp_id <= 0) {
                throw new Exception('Invalid bootcamp ID');
            }
            if ($price <= 0) {
                throw new Exception('Invalid price');
            }
            if (empty($this->id)) {
                throw new Exception('Order ID is required');
            }

            // Query to insert order item
            $query = "INSERT INTO " . $this->items_table . "
                    (order_id, bootcamp_id, price, created_at)
                    VALUES (:order_id, :bootcamp_id, :price, :created_at)";

            // Prepare query
            $stmt = $this->conn->prepare($query);

            $created_at = date('Y-m-d H:i:s');

            // Bind values
            $stmt->bindParam(":order_id", $this->id, PDO::PARAM_INT);
            $stmt->bindParam(":bootcamp_id", $bootcamp_id, PDO::PARAM_INT);
            $stmt->bindParam(":price", $price);
            $stmt->bindParam(":created_at", $created_at);

            // Execute query
            if ($stmt->execute()) {
                error_log("Order item added successfully: Order ID = " . $this->id . ", Bootcamp ID = " . $bootcamp_id);
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("Order item creation failed: " . implode(' - ', $errorInfo));
                return false;
            }

        } catch (Exception $e) {
            error_log("Order item creation exception: " . $e->getMessage());
            return false;
        }
    }

    // Update order payment status
    public function updatePaymentStatus() {
        try {
            // Validate inputs
            if (empty($this->id)) {
                throw new Exception('Order ID is required');
            }
            if (empty($this->payment_status)) {
                throw new Exception('Payment status is required');
            }

            // Query to update payment status
            $query = "UPDATE " . $this->table_name . "
                    SET payment_status = :payment_status,
                        transaction_id = :transaction_id,
                        updated_at = :updated_at
                    WHERE id = :id";

            // Prepare query
            $stmt = $this->conn->prepare($query);

            // Sanitize values
            $this->payment_status = htmlspecialchars(strip_tags($this->payment_status));
            $this->transaction_id = htmlspecialchars(strip_tags($this->transaction_id));
            $updated_at = date('Y-m-d H:i:s');

            // Bind values
            $stmt->bindParam(":payment_status", $this->payment_status);
            $stmt->bindParam(":transaction_id", $this->transaction_id);
            $stmt->bindParam(":updated_at", $updated_at);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

            // Execute query
            if ($stmt->execute()) {
                error_log("Payment status updated successfully: Order ID = " . $this->id . ", Status = " . $this->payment_status);
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("Payment status update failed: " . implode(' - ', $errorInfo));
                return false;
            }

        } catch (Exception $e) {
            error_log("Payment status update exception: " . $e->getMessage());
            return false;
        }
    }

    // Read one order
    public function readOne() {
        try {
            // Validate ID
            if (empty($this->id) || $this->id <= 0) {
                return false;
            }

            // Query to read single order
            $query = "SELECT * FROM " . $this->table_name . " 
                    WHERE id = :id LIMIT 1";

            // Prepare query
            $stmt = $this->conn->prepare($query);

            // Bind ID
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

            // Execute query
            $stmt->execute();

            // Get row count
            $num = $stmt->rowCount();

            // If order found
            if ($num > 0) {
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
                $this->updated_at = $row['updated_at'] ?? null;

                return true;
            }

            return false;

        } catch (Exception $e) {
            error_log("Order read exception: " . $e->getMessage());
            return false;
        }
    }

    // Get order items
    public function getOrderItems() {
        try {
            // Validate order ID
            if (empty($this->id) || $this->id <= 0) {
                return false;
            }

            // Query to get order items with bootcamp details
            $query = "SELECT i.*, b.title, b.image, b.instructor_name
                    FROM " . $this->items_table . " i
                    JOIN bootcamps b ON i.bootcamp_id = b.id
                    WHERE i.order_id = :order_id
                    ORDER BY i.created_at DESC";

            // Prepare query
            $stmt = $this->conn->prepare($query);

            // Bind ID
            $stmt->bindParam(":order_id", $this->id, PDO::PARAM_INT);

            // Execute query
            $stmt->execute();

            return $stmt;

        } catch (Exception $e) {
            error_log("Get order items exception: " . $e->getMessage());
            return false;
        }
    }

    // Get user orders
    public function getUserOrders($limit = 10, $offset = 0) {
        try {
            // Validate inputs
            if (empty($this->user_id) || $this->user_id <= 0) {
                return false;
            }

            $limit = max(1, (int)$limit);
            $offset = max(0, (int)$offset);

            // Query to get user orders
            $query = "SELECT * FROM " . $this->table_name . " 
                    WHERE user_id = :user_id
                    ORDER BY created_at DESC
                    LIMIT :limit OFFSET :offset";

            // Prepare query
            $stmt = $this->conn->prepare($query);

            // Bind parameters
            $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);

            // Execute query
            $stmt->execute();

            return $stmt;

        } catch (Exception $e) {
            error_log("Get user orders exception: " . $e->getMessage());
            return false;
        }
    }

    // Count user orders (for pagination)
    public function countUserOrders() {
        try {
            // Validate user ID
            if (empty($this->user_id) || $this->user_id <= 0) {
                return 0;
            }

            $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row['total'];

        } catch (Exception $e) {
            error_log("Count user orders exception: " . $e->getMessage());
            return 0;
        }
    }

    // Get order by transaction ID
    public function getByTransactionId($transaction_id) {
        try {
            if (empty($transaction_id)) {
                return false;
            }

            $query = "SELECT * FROM " . $this->table_name . " 
                    WHERE transaction_id = :transaction_id LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":transaction_id", $transaction_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Set object properties
                $this->id = $row['id'];
                $this->order_number = $row['order_number'];
                $this->user_id = $row['user_id'];
                $this->total_amount = $row['total_amount'];
                $this->payment_status = $row['payment_status'];
                $this->payment_method = $row['payment_method'];
                $this->transaction_id = $row['transaction_id'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'] ?? null;

                return true;
            }

            return false;

        } catch (Exception $e) {
            error_log("Get order by transaction ID exception: " . $e->getMessage());
            return false;
        }
    }

    // Check if order exists and belongs to user
    public function belongsToUser($user_id) {
        return ($this->user_id == $user_id);
    }

    // Get order statistics for user
    public function getUserOrderStats($user_id) {
        try {
            $query = "SELECT 
                        COUNT(*) as total_orders,
                        SUM(CASE WHEN payment_status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                        SUM(CASE WHEN payment_status = 'completed' THEN total_amount ELSE 0 END) as total_spent
                    FROM " . $this->table_name . " 
                    WHERE user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Get user order stats exception: " . $e->getMessage());
            return false;
        }
    }

    // Generate unique order number
    private function generateOrderNumber() {
        $year = date('Y');
        $month = date('m');
        
        // Get the last order number for this month
        $query = "SELECT order_number FROM " . $this->table_name . " 
                  WHERE order_number LIKE :pattern 
                  ORDER BY order_number DESC LIMIT 1";
        
        $pattern = "ORD-{$year}{$month}-%";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pattern", $pattern);
        $stmt->execute();
        
        $lastOrder = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($lastOrder) {
            // Extract the sequence number and increment it
            $lastNumber = $lastOrder['order_number'];
            $sequence = (int)substr($lastNumber, -6) + 1;
        } else {
            // First order of the month
            $sequence = 1;
        }
        
        // Format: ORD-YYYYMM-XXXXXX (6 digit sequence)
        return sprintf("ORD-%s%s-%06d", $year, $month, $sequence);
    }

    // Get order by order number
    public function getByOrderNumber($order_number) {
        try {
            if (empty($order_number)) {
                return false;
            }

            $query = "SELECT * FROM " . $this->table_name . " 
                    WHERE order_number = :order_number LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":order_number", $order_number);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Set object properties
                $this->id = $row['id'];
                $this->order_number = $row['order_number'];
                $this->user_id = $row['user_id'];
                $this->total_amount = $row['total_amount'];
                $this->payment_status = $row['payment_status'];
                $this->payment_method = $row['payment_method'];
                $this->transaction_id = $row['transaction_id'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'] ?? null;

                return true;
            }

            return false;

        } catch (Exception $e) {
            error_log("Get order by order number exception: " . $e->getMessage());
            return false;
        }
    }
}
?>