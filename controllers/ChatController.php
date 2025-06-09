<?php
// controllers/ChatController.php - Chat Controller

require_once 'models/Chat.php';
require_once 'helper/SecurityHelper.php';

class ChatController {
    private $chat;

    public function __construct() {
        $this->chat = new Chat();
    }

    /**
     * Show chat interface for user
     */
    public function index() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $room = $this->chat->getOrCreateRoom($userId);
        
        if (!$room) {
            $_SESSION['error'] = 'Tidak dapat membuat room chat';
            header('Location: index.php');
            exit;
        }

        // Mark messages as read
        $this->chat->markMessagesAsRead($room['id'], 'user', $userId);
        
        // Get messages
        $messages = $this->chat->getRoomMessages($room['id']);
        
        include 'views/chat/index.php';
    }

    /**
     * Send message via AJAX
     */
    public function sendMessage() {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $message = SecurityHelper::sanitizeInput($input['message'] ?? '');
        
        if (empty(trim($message))) {
            echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $room = $this->chat->getOrCreateRoom($userId);
        
        if (!$room) {
            echo json_encode(['success' => false, 'message' => 'Cannot create chat room']);
            exit;
        }

        $messageId = $this->chat->sendMessage($room['id'], 'user', $userId, $message);
        
        if ($messageId) {
            // Remove typing indicator
            $this->chat->removeTyping($room['id'], 'user', $userId);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Message sent',
                'messageId' => $messageId,
                'timestamp' => date('H:i')
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }
    }

    /**
     * Get new messages via AJAX
     */
    public function getMessages() {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $room = $this->chat->getOrCreateRoom($userId);
        
        if (!$room) {
            echo json_encode(['success' => false, 'message' => 'Room not found']);
            exit;
        }

        $lastMessageId = intval($_GET['last_id'] ?? 0);
        
        // Get messages after last_id
        $messages = $this->chat->getRoomMessages($room['id']);
        $newMessages = array_filter($messages, function($msg) use ($lastMessageId) {
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
    }

    /**
     * Set typing indicator
     */
    public function setTyping() {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $room = $this->chat->getOrCreateRoom($userId);
        
        if ($room) {
            $this->chat->setTyping($room['id'], 'user', $userId);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    /**
     * Remove typing indicator
     */
    public function stopTyping() {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $room = $this->chat->getOrCreateRoom($userId);
        
        if ($room) {
            $this->chat->removeTyping($room['id'], 'user', $userId);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount() {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['count' => 0]);
            exit;
        }

        $count = $this->chat->getUnreadCount($_SESSION['user_id']);
        echo json_encode(['count' => $count]);
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

        $rooms = $this->chat->getAllActiveRooms();
        
        include 'views/admin/chat.php';
    }

    /**
     * Admin send message
     */
    public function adminSendMessage() {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $roomId = intval($input['room_id'] ?? 0);
        $message = SecurityHelper::sanitizeInput($input['message'] ?? '');
        
        if (empty(trim($message)) || !$roomId) {
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }

        $adminId = $_SESSION['admin_id'];
        
        // Assign admin to room if not assigned
        $room = $this->chat->getRoomById($roomId);
        if ($room && !$room['admin_id']) {
            $this->chat->assignAdminToRoom($roomId, $adminId);
        }

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
    }

    /**
     * Admin get messages
     */
    public function adminGetMessages() {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $roomId = intval($_GET['room_id'] ?? 0);
        $lastMessageId = intval($_GET['last_id'] ?? 0);

        if (!$roomId) {
            echo json_encode(['success' => false, 'message' => 'Room ID required']);
            exit;
        }

        // Mark user messages as read
        $this->chat->markMessagesAsRead($roomId, 'admin', $_SESSION['admin_id']);

        $messages = $this->chat->getRoomMessages($roomId);
        $newMessages = array_filter($messages, function($msg) use ($lastMessageId) {
            return $msg['id'] > $lastMessageId;
        });

        // Get typing indicators
        $typing = $this->chat->getTypingUsers($roomId, 'admin', $_SESSION['admin_id']);

        echo json_encode([
            'success' => true,
            'messages' => array_values($newMessages),
            'typing' => $typing
        ]);
    }

    /**
     * Admin typing indicator
     */
    public function adminSetTyping() {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false]);
            exit;
        }

        $roomId = intval($_POST['room_id'] ?? 0);
        if ($roomId) {
            $this->chat->setTyping($roomId, 'admin', $_SESSION['admin_id']);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    /**
     * Close chat room
     */
    public function closeRoom() {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $roomId = intval($_POST['room_id'] ?? 0);
        
        if ($this->chat->closeRoom($roomId)) {
            echo json_encode(['success' => true, 'message' => 'Room closed']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to close room']);
        }
    }

    /**
     * Get admin chat stats
     */
    public function getAdminStats() {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false]);
            exit;
        }

        $stats = $this->chat->getChatStats();
        echo json_encode(['success' => true, 'stats' => $stats]);
    }
}