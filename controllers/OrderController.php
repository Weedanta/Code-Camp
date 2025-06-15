<?php
require_once 'models/Order.php';
require_once 'models/Bootcamp.php';
require_once 'config/database.php';

class OrderController {
    private $database;
    private $db;
    private $order;
    private $bootcamp;

    public function __construct() {
        // Initialize database connection
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        
        // Initialize models
        $this->order = new Order($this->db);
        $this->bootcamp = new Bootcamp($this->db);
    }

    // Show checkout page
    public function checkout() {
        // Check if user is logged in
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        // Get bootcamp ID
        $bootcamp_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($bootcamp_id <= 0) {
            header('Location: index.php?action=bootcamps&error=invalid_bootcamp');
            exit();
        }

        // Get bootcamp details
        $this->bootcamp->id = $bootcamp_id;
        if (!$this->bootcamp->readOne()) {
            header('Location: index.php?action=bootcamps&error=bootcamp_not_found');
            exit();
        }

        // Check if user already purchased this bootcamp
        $user_id = $_SESSION['user_id'];
        if ($this->bootcamp->isUserEnrolled($user_id)) {
            // User already enrolled
            header('Location: index.php?action=bootcamp_detail&id=' . $bootcamp_id . '&message=already_enrolled');
            exit();
        }

        // Include checkout view
        include_once 'views/checkout/index.php';
    }

    // Process order
    public function processOrder() {
        // Check if user is logged in
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        if (!isset($_SESSION['user_id'])) {
            $this->redirectWithError('login', 'Please login first');
            return;
        }

        // Check if it's a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('', 'Invalid request method');
            return;
        }

        try {
            // Get and validate form data
            $user_id = (int)$_SESSION['user_id'];
            $bootcamp_id = isset($_POST['bootcamp_id']) ? (int)$_POST['bootcamp_id'] : 0;
            $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : 'credit_card';

            // Validate bootcamp ID
            if ($bootcamp_id <= 0) {
                throw new Exception('Invalid bootcamp ID');
            }

            // Validate payment method
            $allowed_methods = ['credit_card', 'bank_transfer', 'digital_wallet'];
            if (!in_array($payment_method, $allowed_methods)) {
                $payment_method = 'credit_card';
            }

            // Get bootcamp details
            $this->bootcamp->id = $bootcamp_id;
            if (!$this->bootcamp->readOne()) {
                throw new Exception('Bootcamp not found');
            }

            // Check if user already purchased this bootcamp
            if ($this->bootcamp->isUserEnrolled($user_id)) {
                $this->redirectWithError('bootcamp_detail&id=' . $bootcamp_id, 'You are already enrolled in this bootcamp');
                return;
            }

            // Calculate final price (use discount if available)
            $final_price = $this->calculateFinalPrice();

            // Validate price
            if ($final_price <= 0) {
                throw new Exception('Invalid bootcamp price');
            }

            // Set order data
            $this->order->user_id = $user_id;
            $this->order->total_amount = $final_price;
            $this->order->payment_status = 'pending';
            $this->order->payment_method = $payment_method;
            $this->order->transaction_id = $this->generateTransactionId();
            // order_number akan di-generate otomatis di model

            // Log order attempt for debugging
            error_log("Creating order for user: $user_id, bootcamp: $bootcamp_id, amount: $final_price");

            // Create order
            if (!$this->order->create()) {
                throw new Exception('Failed to create order');
            }

            // Log successful order creation
            error_log("Order created successfully with ID: " . $this->order->id);

            // Add bootcamp to order
            if (!$this->order->addItem($bootcamp_id, $final_price)) {
                throw new Exception('Failed to add bootcamp to order');
            }

            // Process payment (simulate for now)
            $payment_result = $this->processPayment($payment_method, $final_price);
            
            if ($payment_result['success']) {
                // Update payment status to completed
                $this->order->payment_status = 'completed';
                $this->order->transaction_id = $payment_result['transaction_id'];
                
                if (!$this->order->updatePaymentStatus()) {
                    throw new Exception('Failed to update payment status');
                }

                // Log successful payment
                error_log("Payment completed for order: " . $this->order->id);

                // Redirect to success page
                header('Location: index.php?action=order_success&id=' . $this->order->id);
                exit();
            } else {
                // Payment failed
                $this->order->payment_status = 'failed';
                $this->order->updatePaymentStatus();
                throw new Exception('Payment processing failed: ' . $payment_result['error']);
            }

        } catch (Exception $e) {
            // Log error for debugging
            error_log("Order processing error: " . $e->getMessage());
            
            // Redirect with error message
            $redirect_url = isset($bootcamp_id) ? 'checkout&id=' . $bootcamp_id : 'bootcamps';
            $this->redirectWithError($redirect_url, 'Payment processing failed. Please try again.');
        }
    }

    // Calculate final price considering discounts
    private function calculateFinalPrice() {
        // Use discount price if available, otherwise use regular price
        if (!empty($this->bootcamp->discount_price) && $this->bootcamp->discount_price > 0) {
            return (float)$this->bootcamp->discount_price;
        }
        return (float)$this->bootcamp->price;
    }

    // Generate unique transaction ID
    private function generateTransactionId() {
        return 'TRX-' . date('Ymd') . '-' . strtoupper(uniqid());
    }

    // Simulate payment processing
    private function processPayment($payment_method, $amount) {
        // In real implementation, this would integrate with payment gateway
        // For now, we'll simulate different scenarios
        
        try {
            // Simulate processing time
            usleep(500000); // 0.5 seconds
            
            // Simulate success (95% success rate for demo)
            $success_rate = 95;
            $random = mt_rand(1, 100);
            
            if ($random <= $success_rate) {
                return [
                    'success' => true,
                    'transaction_id' => $this->generateTransactionId(),
                    'message' => 'Payment completed successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Payment gateway temporarily unavailable',
                    'transaction_id' => null
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Payment processing error: ' . $e->getMessage(),
                'transaction_id' => null
            ];
        }
    }

    // Helper method to redirect with error message
    private function redirectWithError($action, $message) {
        $url = 'index.php';
        if (!empty($action)) {
            $url .= '?action=' . $action;
            $url .= '&error=' . urlencode($message);
        } else {
            $url .= '?error=' . urlencode($message);
        }
        header('Location: ' . $url);
        exit();
    }

    // Show order success page
    public function orderSuccess() {
        // Check if user is logged in
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        // Get order ID
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($order_id <= 0) {
            header('Location: index.php?error=Invalid order ID');
            exit();
        }

        // Get order details
        $this->order->id = $order_id;
        if (!$this->order->readOne()) {
            header('Location: index.php?error=Order not found');
            exit();
        }

        // Ensure this order belongs to the logged-in user
        if ($this->order->user_id != $_SESSION['user_id']) {
            header('Location: index.php?error=Unauthorized access');
            exit();
        }

        // Ensure order is completed
        if ($this->order->payment_status !== 'completed') {
            header('Location: index.php?action=my_orders&error=Order not completed');
            exit();
        }

        // Get order items
        $orderItems = $this->order->getOrderItems();
        $items = $orderItems->fetchAll(PDO::FETCH_ASSOC);

        // Include success view
        if (file_exists('views/checkout/success.php')) {
            include_once 'views/checkout/success.php';
        } elseif (file_exists('views/checkout/succes.php')) {
            include_once 'views/checkout/succes.php';
        } else {
            $this->showSuccessMessage();
        }
    }

    // Fallback success message
    private function showSuccessMessage() {
        echo '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Order Success - Code Camp</title>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        </head>
        <body class="bg-gray-50">
            <div class="min-h-screen flex items-center justify-center">
                <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full text-center">
                    <div class="text-green-500 text-6xl mb-4">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-4">Payment Successful!</h1>
                    <p class="text-gray-600 mb-2">Order ID: #' . str_pad($this->order->id, 8, '0', STR_PAD_LEFT) . '</p>
                    <p class="text-gray-600 mb-2">Transaction ID: ' . htmlspecialchars($this->order->transaction_id) . '</p>
                    <p class="text-gray-600 mb-6">Amount: Rp ' . number_format($this->order->total_amount, 0, ',', '.') . '</p>
                    <div class="space-y-3">
                        <a href="index.php?action=my_bootcamps" class="block bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors">
                            Go to My Bootcamps
                        </a>
                        <a href="index.php?action=my_orders" class="block border border-blue-600 text-blue-600 px-6 py-3 rounded-md hover:bg-blue-50 transition-colors">
                            View All Orders
                        </a>
                        <a href="index.php" class="block text-gray-600 hover:text-blue-600 transition-colors">
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </body>
        </html>';
    }

    // Show orders history
    public function myOrders() {
        // Check if user is logged in
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $this->order->user_id = $user_id;

        // Set up pagination
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Get orders
        $stmt = $this->order->getUserOrders($limit, $offset);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total orders for pagination
        $total_orders = $this->order->countUserOrders();
        $total_pages = ceil($total_orders / $limit);

        // Include view
        include_once 'views/order/history.php';
    }

    // Show order details
    public function orderDetail() {
        // Check if user is logged in
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        // Get order ID
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($order_id <= 0) {
            header('Location: index.php?action=my_orders&error=Invalid order ID');
            exit();
        }

        // Get order details
        $this->order->id = $order_id;
        if (!$this->order->readOne()) {
            header('Location: index.php?action=my_orders&error=Order not found');
            exit();
        }

        // Ensure this order belongs to the logged-in user
        if ($this->order->user_id != $_SESSION['user_id']) {
            header('Location: index.php?action=my_orders&error=Unauthorized access');
            exit();
        }

        // Get order items
        $orderItems = $this->order->getOrderItems();
        $items = $orderItems->fetchAll(PDO::FETCH_ASSOC);

        // Include view
        include_once 'views/order/detail.php';
    }
}
?>