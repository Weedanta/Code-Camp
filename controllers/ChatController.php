<?php
// controllers/ChatController.php - Chat Controller

require_once 'models/Chat.php';

class ChatController {
    private $chat;

    public function __construct() {
        $this->chat = new Chat();
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Show chat interface for user
     */
    public function index() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Anda harus login terlebih dahulu untuk menggunakan chat';
            header('Location: index.php?action=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        try {
            $room = $this->chat->getOrCreateRoom($userId);
            
            if (!$room) {
                $_SESSION['error'] = 'Tidak dapat membuat room chat. Silakan coba lagi.';
                header('Location: index.php');
                exit;
            }

            // Mark messages as read
            $this->chat->markMessagesAsRead($room['id'], 'user', $userId);
            
            // Get messages
            $messages = $this->chat->getRoomMessages($room['id']);
            
            // Include the chat view
            include 'views/chat/index.php';
            
        } catch (Exception $e) {
            error_log("Chat index error: " . $e->getMessage());
            $_SESSION['error'] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
            header('Location: index.php');
            exit;
        }
    }

    /**
     * Send message via AJAX
     */
    public function sendMessage() {
        header('Content-Type: application/json');

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized - Please login first']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
                exit;
            }
            
            $message = trim($input['message'] ?? '');
            
            if (empty($message)) {
                echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
                exit;
            }

            if (strlen($message) > 1000) {
                echo json_encode(['success' => false, 'message' => 'Message too long (max 1000 characters)']);
                exit;
            }

            $userId = $_SESSION['user_id'];
            $room = $this->chat->getOrCreateRoom($userId);
            
            if (!$room) {
                echo json_encode(['success' => false, 'message' => 'Cannot create chat room']);
                exit;
            }

            // Sanitize message
            $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

            $messageId = $this->chat->sendMessage($room['id'], 'user', $userId, $message);
            
            if ($messageId) {
                // Remove typing indicator
                $this->chat->removeTyping($room['id'], 'user', $userId);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Message sent successfully',
                    'messageId' => $messageId,
                    'timestamp' => date('H:i')
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send message']);
            }
            
        } catch (Exception $e) {
            error_log("Send message error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Internal server error']);
        }
    }

    /**
     * Get new messages via AJAX
     */
    public function getMessages() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];
            $room = $this->chat->getOrCreateRoom($userId);
            
            if (!$room) {
                echo json_encode(['success' => false, 'message' => 'Room not found']);
                exit;
            }

            $lastMessageId = intval($_GET['last_id'] ?? 0);
            
            // Get all messages and filter new ones
            $allMessages = $this->chat->getRoomMessages($room['id']);
            $newMessages = array_filter($allMessages, function($msg) use ($lastMessageId) {
                return $msg['id'] > $lastMessageId;
            });

            // Mark new admin messages as read
            if (!empty($newMessages)) {
                $this->chat->markMessagesAsRead($room['id'], 'user', $userId);
            }

            // Get typing indicators
            $typing = $this->chat->getTypingUsers($room['id'], 'user', $userId);

            echo json_encode([
                'success' => true,
                'messages' => array_values($newMessages),
                'typing' => $typing
            ]);
            
        } catch (Exception $e) {
            error_log("Get messages error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Internal server error']);
        }
    }

    /**
     * Set typing indicator
     */
    public function setTyping() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];
            $room = $this->chat->getOrCreateRoom($userId);
            
            if ($room) {
                $this->chat->setTyping($room['id'], 'user', $userId);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            
        } catch (Exception $e) {
            error_log("Set typing error: " . $e->getMessage());
            echo json_encode(['success' => false]);
        }
    }

    /**
     * Remove typing indicator
     */
    public function stopTyping() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];
            $room = $this->chat->getOrCreateRoom($userId);
            
            if ($room) {
                $this->chat->removeTyping($room['id'], 'user', $userId);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            
        } catch (Exception $e) {
            error_log("Stop typing error: " . $e->getMessage());
            echo json_encode(['success' => false]);
        }
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['count' => 0]);
            exit;
        }

        try {
            $count = $this->chat->getUnreadCount($_SESSION['user_id']);
            echo json_encode(['count' => $count]);
            
        } catch (Exception $e) {
            error_log("Get unread count error: " . $e->getMessage());
            echo json_encode(['count' => 0]);
        }
    }

    // ==================== ADMIN METHODS ====================

    /**
     * Admin chat interface
     */
    public function adminIndex() {
        // This should be called from AdminController
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: admin.php?action=login');
            exit;
        }

        try {
            $rooms = $this->chat->getAllActiveRooms();
            include 'views/admin/chat.php';
            
        } catch (Exception $e) {
            error_log("Admin chat index error: " . $e->getMessage());
            $_SESSION['error'] = 'Terjadi kesalahan saat memuat chat';
            header('Location: admin.php?action=dashboard');
            exit;
        }
    }

    /**
     * Admin send message
     */
    public function adminSendMessage() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $roomId = intval($input['room_id'] ?? 0);
            $message = trim($input['message'] ?? '');
            
            if (empty($message) || !$roomId) {
                echo json_encode(['success' => false, 'message' => 'Invalid input']);
                exit;
            }

            $adminId = $_SESSION['admin_id'];
            
            // Assign admin to room if not assigned
            $room = $this->chat->getRoomById($roomId);
            if ($room && !$room['admin_id']) {
                $this->chat->assignAdminToRoom($roomId, $adminId);
            }

            // Sanitize message
            $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

            $messageId = $this->chat->sendMessage($roomId, 'admin', $adminId, $message);
            
            if ($messageId) {
                $this->chat->removeTyping($roomId, 'admin', $adminId);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Message sent',
                    'messageId' => $messageId,
                    'timestamp' => date('H:i')
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send message']);
            }
            
        } catch (Exception $e) {
            error_log("Admin send message error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Internal server error']);
        }
    }

    /**
     * Admin get messages
     */
    public function adminGetMessages() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            $roomId = intval($_GET['room_id'] ?? 0);
            $lastMessageId = intval($_GET['last_id'] ?? 0);

            if (!$roomId) {
                echo json_encode(['success' => false, 'message' => 'Room ID required']);
                exit;
            }

            // Mark user messages as read
            $this->chat->markMessagesAsRead($roomId, 'admin', $_SESSION['admin_id']);

            $allMessages = $this->chat->getRoomMessages($roomId);
            $newMessages = array_filter($allMessages, function($msg) use ($lastMessageId) {
                return $msg['id'] > $lastMessageId;
            });

            // Get typing indicators
            $typing = $this->chat->getTypingUsers($roomId, 'admin', $_SESSION['admin_id']);

            echo json_encode([
                'success' => true,
                'messages' => array_values($newMessages),
                'typing' => $typing
            ]);
            
        } catch (Exception $e) {
            error_log("Admin get messages error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Internal server error']);
        }
    }

    /**
     * Admin typing indicator
     */
    public function adminSetTyping() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false]);
            exit;
        }

        try {
            $roomId = intval($_POST['room_id'] ?? 0);
            if ($roomId) {
                $this->chat->setTyping($roomId, 'admin', $_SESSION['admin_id']);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            
        } catch (Exception $e) {
            error_log("Admin set typing error: " . $e->getMessage());
            echo json_encode(['success' => false]);
        }
    }

    /**
     * Admin stop typing indicator
     */
    public function adminStopTyping() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false]);
            exit;
        }

        try {
            $roomId = intval($_POST['room_id'] ?? 0);
            if ($roomId) {
                $this->chat->removeTyping($roomId, 'admin', $_SESSION['admin_id']);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            
        } catch (Exception $e) {
            error_log("Admin stop typing error: " . $e->getMessage());
            echo json_encode(['success' => false]);
        }
    }

    /**
     * Close chat room
     */
    public function closeRoom() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            $roomId = intval($_POST['room_id'] ?? 0);
            
            if ($this->chat->closeRoom($roomId)) {
                echo json_encode(['success' => true, 'message' => 'Room closed']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to close room']);
            }
            
        } catch (Exception $e) {
            error_log("Close room error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Internal server error']);
        }
    }

    /**
     * Get admin chat stats
     */
    public function getAdminStats() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false]);
            exit;
        }

        try {
            $stats = $this->chat->getChatStats();
            echo json_encode(['success' => true, 'stats' => $stats]);
            
        } catch (Exception $e) {
            error_log("Get admin stats error: " . $e->getMessage());
            echo json_encode(['success' => false]);
        }
    }
}
?>