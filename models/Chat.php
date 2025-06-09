<?php
// models/Chat.php - Chat Model untuk sistem chat dengan admin

class Chat {
    private $conn;
    private $table_rooms = 'chat_rooms';
    private $table_messages = 'chat_messages';
    private $table_typing = 'chat_typing';

    public function __construct() {
        // Perbaiki path - gunakan relative path yang benar
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // ==================== ROOM MANAGEMENT ====================
    
    /**
     * Get or create chat room for user
     */
    public function getOrCreateRoom($userId) {
        try {
            // Check if room already exists
            $stmt = $this->conn->prepare("
                SELECT * FROM {$this->table_rooms} 
                WHERE user_id = ? AND status = 'active'
                ORDER BY updated_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            $room = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($room) {
                return $room;
            }
            
            // Create new room
            $stmt = $this->conn->prepare("
                INSERT INTO {$this->table_rooms} (user_id, status) 
                VALUES (?, 'active')
            ");
            
            if ($stmt->execute([$userId])) {
                $roomId = $this->conn->lastInsertId();
                return $this->getRoomById($roomId);
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Get or create room error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get room by ID
     */
    public function getRoomById($roomId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, u.name as user_name, u.alamat_email as user_email,
                       a.name as admin_name, a.email as admin_email
                FROM {$this->table_rooms} r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN admin a ON r.admin_id = a.id
                WHERE r.id = ?
            ");
            $stmt->execute([$roomId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get room by ID error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all active rooms for admin
     */
    public function getAllActiveRooms() {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, u.name as user_name, u.alamat_email as user_email,
                       a.name as admin_name,
                       (SELECT COUNT(*) FROM {$this->table_messages} m 
                        WHERE m.room_id = r.id AND m.is_read = FALSE AND m.sender_type = 'user') as unread_count,
                       (SELECT m.message FROM {$this->table_messages} m 
                        WHERE m.room_id = r.id ORDER BY m.created_at DESC LIMIT 1) as last_message,
                       (SELECT m.created_at FROM {$this->table_messages} m 
                        WHERE m.room_id = r.id ORDER BY m.created_at DESC LIMIT 1) as last_message_time
                FROM {$this->table_rooms} r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN admin a ON r.admin_id = a.id
                WHERE r.status = 'active'
                ORDER BY r.updated_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all active rooms error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Assign admin to room
     */
    public function assignAdminToRoom($roomId, $adminId) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE {$this->table_rooms} 
                SET admin_id = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            return $stmt->execute([$adminId, $roomId]);
        } catch (PDOException $e) {
            error_log("Assign admin to room error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Close chat room
     */
    public function closeRoom($roomId) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE {$this->table_rooms} 
                SET status = 'closed', updated_at = NOW() 
                WHERE id = ?
            ");
            return $stmt->execute([$roomId]);
        } catch (PDOException $e) {
            error_log("Close room error: " . $e->getMessage());
            return false;
        }
    }

    // ==================== MESSAGE MANAGEMENT ====================

    /**
     * Send message
     */
    public function sendMessage($roomId, $senderType, $senderId, $message) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO {$this->table_messages} (room_id, sender_type, sender_id, message) 
                VALUES (?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$roomId, $senderType, $senderId, $message])) {
                // Update room timestamp
                $this->updateRoomTimestamp($roomId);
                return $this->conn->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Send message error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get messages in room
     */
    public function getRoomMessages($roomId, $limit = 50, $offset = 0) {
        try {
            $stmt = $this->conn->prepare("
                SELECT m.*, 
                       CASE 
                           WHEN m.sender_type = 'user' THEN u.name 
                           WHEN m.sender_type = 'admin' THEN a.name 
                       END as sender_name,
                       CASE 
                           WHEN m.sender_type = 'user' THEN u.alamat_email 
                           WHEN m.sender_type = 'admin' THEN a.email 
                       END as sender_email
                FROM {$this->table_messages} m
                LEFT JOIN users u ON m.sender_type = 'user' AND m.sender_id = u.id
                LEFT JOIN admin a ON m.sender_type = 'admin' AND m.sender_id = a.id
                WHERE m.room_id = ?
                ORDER BY m.created_at ASC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$roomId, $limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get room messages error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mark messages as read
     */
    public function markMessagesAsRead($roomId, $readerType, $readerId) {
        try {
            // Mark messages from other party as read
            $oppositeType = $readerType === 'user' ? 'admin' : 'user';
            
            $stmt = $this->conn->prepare("
                UPDATE {$this->table_messages} 
                SET is_read = TRUE 
                WHERE room_id = ? AND sender_type = ? AND is_read = FALSE
            ");
            return $stmt->execute([$roomId, $oppositeType]);
        } catch (PDOException $e) {
            error_log("Mark messages as read error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread message count for user
     */
    public function getUnreadCount($userId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as count
                FROM {$this->table_messages} m
                JOIN {$this->table_rooms} r ON m.room_id = r.id
                WHERE r.user_id = ? AND m.sender_type = 'admin' AND m.is_read = FALSE
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get unread count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total unread messages for all admin
     */
    public function getTotalUnreadForAdmin() {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as count
                FROM {$this->table_messages} m
                WHERE m.sender_type = 'user' AND m.is_read = FALSE
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get total unread for admin error: " . $e->getMessage());
            return 0;
        }
    }

    // ==================== TYPING INDICATOR ====================

    /**
     * Set typing status
     */
    public function setTyping($roomId, $senderType, $senderId) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO {$this->table_typing} (room_id, sender_type, sender_id) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE created_at = NOW()
            ");
            return $stmt->execute([$roomId, $senderType, $senderId]);
        } catch (PDOException $e) {
            error_log("Set typing error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove typing status
     */
    public function removeTyping($roomId, $senderType, $senderId) {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM {$this->table_typing} 
                WHERE room_id = ? AND sender_type = ? AND sender_id = ?
            ");
            return $stmt->execute([$roomId, $senderType, $senderId]);
        } catch (PDOException $e) {
            error_log("Remove typing error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get typing users in room
     */
    public function getTypingUsers($roomId, $excludeType = null, $excludeId = null) {
        try {
            $sql = "
                SELECT t.*, 
                       CASE 
                           WHEN t.sender_type = 'user' THEN u.name 
                           WHEN t.sender_type = 'admin' THEN a.name 
                       END as sender_name
                FROM {$this->table_typing} t
                LEFT JOIN users u ON t.sender_type = 'user' AND t.sender_id = u.id
                LEFT JOIN admin a ON t.sender_type = 'admin' AND t.sender_id = a.id
                WHERE t.room_id = ? AND t.created_at > DATE_SUB(NOW(), INTERVAL 10 SECOND)
            ";
            
            $params = [$roomId];
            
            if ($excludeType && $excludeId) {
                $sql .= " AND NOT (t.sender_type = ? AND t.sender_id = ?)";
                $params[] = $excludeType;
                $params[] = $excludeId;
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get typing users error: " . $e->getMessage());
            return [];
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Update room timestamp
     */
    private function updateRoomTimestamp($roomId) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE {$this->table_rooms} 
                SET updated_at = NOW() 
                WHERE id = ?
            ");
            return $stmt->execute([$roomId]);
        } catch (PDOException $e) {
            error_log("Update room timestamp error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean old typing indicators
     */
    public function cleanOldTyping() {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM {$this->table_typing} 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL 15 SECOND)
            ");
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Clean old typing error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get chat statistics
     */
    public function getChatStats() {
        try {
            $stats = [];
            
            // Total active rooms
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM {$this->table_rooms} WHERE status = 'active'");
            $stmt->execute();
            $stats['active_rooms'] = $stmt->fetchColumn();
            
            // Total messages today
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM {$this->table_messages} WHERE DATE(created_at) = CURDATE()");
            $stmt->execute();
            $stats['messages_today'] = $stmt->fetchColumn();
            
            // Unread messages
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM {$this->table_messages} WHERE is_read = FALSE");
            $stmt->execute();
            $stats['unread_messages'] = $stmt->fetchColumn();
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Get chat stats error: " . $e->getMessage());
            return [];
        }
    }
}
?>