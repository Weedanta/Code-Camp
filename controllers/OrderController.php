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
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        // Get bootcamp ID
        $bootcamp_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Missing bootcamp ID.');

        // Get bootcamp details
        $this->bootcamp->id = $bootcamp_id;
        if (!$this->bootcamp->readOne()) {
            header('Location: index.php?action=bootcamps');
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
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        // Check if it's a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit();
        }

        // Get form data
        $user_id = $_SESSION['user_id'];
        $bootcamp_id = isset($_POST['bootcamp_id']) ? $_POST['bootcamp_id'] : die('ERROR: Missing bootcamp ID.');
        $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'credit_card';

        // Get bootcamp details
        $this->bootcamp->id = $bootcamp_id;
        if (!$this->bootcamp->readOne()) {
            header('Location: index.php?action=bootcamps');
            exit();
        }

        // Check if user already purchased this bootcamp
        if ($this->bootcamp->isUserEnrolled($user_id)) {
            // User already enrolled
            header('Location: index.php?action=bootcamp_detail&id=' . $bootcamp_id . '&message=already_enrolled');
            exit();
        }

        // Set order data
        $this->order->user_id = $user_id;
        $this->order->total_amount = $this->bootcamp->price; // Use discounted price if available
        $this->order->payment_status = 'pending';
        $this->order->payment_method = $payment_method;
        $this->order->transaction_id = 'TRX-' . strtoupper(uniqid());

        // Create order
        if ($this->order->create()) {
            // Add bootcamp to order
            if ($this->order->addItem($bootcamp_id, $this->bootcamp->price)) {
                // Simulate payment processing
                $this->order->payment_status = 'completed';
                if ($this->order->updatePaymentStatus()) {
                    // Order completed successfully
                    header('Location: index.php?action=order_success&id=' . $this->order->id);
                    exit();
                }
            }
        }

        // If we reach here, something went wrong
        header('Location: index.php?action=checkout&id=' . $bootcamp_id . '&error=payment_failed');
        exit();
    }

    // Show order success page
    public function orderSuccess() {
        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        // Get order ID
        $order_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Missing order ID.');

        // Get order details
        $this->order->id = $order_id;
        if (!$this->order->readOne()) {
            header('Location: index.php');
            exit();
        }

        // Ensure this order belongs to the logged-in user
        if ($this->order->user_id != $_SESSION['user_id']) {
            header('Location: index.php');
            exit();
        }

        // Get order items
        $orderItems = $this->order->getOrderItems();
        $items = $orderItems->fetchAll(PDO::FETCH_ASSOC);

        // Include success view - Fixed path
        if (file_exists('views/checkout/success.php')) {
            include_once 'views/checkout/success.php';
        } elseif (file_exists('views/checkout/succes.php')) {
            // Fallback untuk typo filename
            include_once 'views/checkout/succes.php';
        } else {
            // Jika file tidak ada, buat response sederhana
            $this->showSuccessMessage();
        }
    }

    // Fallback success message jika file view tidak ada
    private function showSuccessMessage() {
        echo '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Order Success - Code Camp</title>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        </head>
        <body class="bg-gray-50">
            <div class="min-h-screen flex items-center justify-center">
                <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full text-center">
                    <div class="text-green-500 text-6xl mb-4">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-4">Payment Successful!</h1>
                    <p class="text-gray-600 mb-6">Your order #' . str_pad($this->order->id, 8, '0', STR_PAD_LEFT) . ' has been completed successfully.</p>
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
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $this->order->user_id = $user_id;

        // Set up pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
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
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        // Get order ID
        $order_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Missing order ID.');

        // Get order details
        $this->order->id = $order_id;
        if (!$this->order->readOne()) {
            header('Location: index.php?action=my_orders');
            exit();
        }

        // Ensure this order belongs to the logged-in user
        if ($this->order->user_id != $_SESSION['user_id']) {
            header('Location: index.php?action=my_orders');
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