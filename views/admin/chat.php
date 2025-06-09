<?php
// views/admin/chat.php - Admin chat interface

$page_title = 'Chat Management - Code Camp Admin';
include_once 'partials/sidebar.php';

// Get rooms data from controller
if (!isset($rooms)) {
    $rooms = [];
}
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chat Management</h1>
        <div class="d-flex align-items-center">
            <span id="connectionStatus" class="badge badge-success me-2">
                <i class="fas fa-circle"></i> Online
            </span>
            <button id="refreshBtn" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Chat Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Chats</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeChats">
                                <?php echo count(array_filter($rooms, function($r) { return $r['status'] === 'active'; })); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Unread Messages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="unreadMessages">
                                <?php echo array_sum(array_column($rooms, 'unread_count')); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Messages Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="messagesToday">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Response Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">95%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Interface -->
    <div class="row">
        <!-- Chat List -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Active Chats</h6>
                    <span class="badge badge-primary" id="chatCount"><?php echo count($rooms); ?></span>
                </div>
                <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                    <div id="chatList">
                        <?php if (empty($rooms)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-comments fa-3x mb-3"></i>
                                <p>No active chats</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($rooms as $room): ?>
                                <div class="chat-item border-bottom p-3 cursor-pointer hover-bg-light" 
                                     data-room-id="<?php echo $room['id']; ?>"
                                     data-user-name="<?php echo htmlspecialchars($room['user_name']); ?>">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 40px; height: 40px; font-size: 16px;">
                                                    <?php echo strtoupper(substr($room['user_name'], 0, 1)); ?>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0 font-weight-bold"><?php echo htmlspecialchars($room['user_name']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($room['user_email']); ?></small>
                                                </div>
                                            </div>
                                            <?php if ($room['last_message']): ?>
                                                <p class="mb-1 text-truncate" style="max-width: 200px;">
                                                    <?php echo htmlspecialchars(substr($room['last_message'], 0, 50)); ?>
                                                    <?php if (strlen($room['last_message']) > 50) echo '...'; ?>
                                                </p>
                                                <small class="text-muted">
                                                    <?php echo date('H:i', strtotime($room['last_message_time'])); ?>
                                                </small>
                                            <?php else: ?>
                                                <p class="mb-1 text-muted">No messages yet</p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-right">
                                            <?php if ($room['unread_count'] > 0): ?>
                                                <span class="badge badge-danger"><?php echo $room['unread_count']; ?></span>
                                            <?php endif; ?>
                                            <?php if (!$room['admin_id']): ?>
                                                <span class="badge badge-warning d-block mt-1">Unassigned</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3" id="chatHeader" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div id="selectedUserAvatar" class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px;">
                            </div>
                            <div>
                                <h6 class="mb-0 font-weight-bold" id="selectedUserName"></h6>
                                <small class="text-muted" id="selectedUserEmail"></small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span id="typingIndicatorHeader" class="text-muted me-3" style="display: none;">
                                <i class="fas fa-circle text-success"></i> Typing...
                            </span>
                            <button id="closeChatBtn" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-times"></i> Close Chat
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body" id="chatMessagesContainer" style="height: 400px; overflow-y: auto;">
                    <div id="chatMessages" class="d-flex flex-column">
                        <div id="noChatSelected" class="text-center py-5 text-muted">
                            <i class="fas fa-comment-dots fa-4x mb-3"></i>
                            <h5>Select a chat to start messaging</h5>
                            <p>Choose a conversation from the left to view and respond to messages</p>
                        </div>
                    </div>
                </div>

                <div class="card-footer" id="chatInput" style="display: none;">
                    <form id="adminChatForm" class="d-flex align-items-end">
                        <div class="flex-grow-1 me-3">
                            <textarea 
                                id="adminMessageInput" 
                                class="form-control" 
                                placeholder="Type your message..."
                                rows="2"
                                maxlength="1000"
                            ></textarea>
                            <small class="text-muted">
                                <span id="adminCharCount">0/1000</span> • 
                                Press Enter to send, Shift+Enter for new line
                            </small>
                        </div>
                        <button type="submit" id="adminSendBtn" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-item:hover {
    background-color: #f8f9fc !important;
}

.chat-item.active {
    background-color: #e3f2fd !important;
    border-left: 4px solid #007bff !important;
}

.message-bubble {
    max-width: 70%;
    word-wrap: break-word;
}

.message-user {
    background-color: #007bff;
    color: white;
    margin-left: auto;
}

.message-admin {
    background-color: #f8f9fa;
    color: #333;
    margin-right: auto;
}

.typing-indicator {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 8px 12px;
    background-color: #f8f9fa;
    border-radius: 18px;
    margin-right: auto;
    max-width: 100px;
}

.typing-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #999;
    animation: typing 1.4s infinite ease-in-out;
}

.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes typing {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentRoomId = null;
    let currentUserName = '';
    let lastMessageId = 0;
    let pollInterval;
    let isAdminTyping = false;
    let typingTimeout;

    const chatList = document.getElementById('chatList');
    const chatHeader = document.getElementById('chatHeader');
    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const noChatSelected = document.getElementById('noChatSelected');
    const adminMessageInput = document.getElementById('adminMessageInput');
    const adminChatForm = document.getElementById('adminChatForm');
    const adminSendBtn = document.getElementById('adminSendBtn');
    const closeChatBtn = document.getElementById('closeChatBtn');
    const refreshBtn = document.getElementById('refreshBtn');
    const adminCharCount = document.getElementById('adminCharCount');

    // Chat item click handlers
    document.querySelectorAll('.chat-item').forEach(item => {
        item.addEventListener('click', function() {
            selectChat(this);
        });
    });

    // Message input handlers
    adminMessageInput.addEventListener('input', function() {
        adminCharCount.textContent = this.value.length + '/1000';
        
        if (this.value.trim() && !isAdminTyping) {
            setAdminTyping();
        }
        
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => {
            if (isAdminTyping) {
                stopAdminTyping();
            }
        }, 3000);
    });

    adminMessageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendAdminMessage();
        }
    });

    // Form submit
    adminChatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        sendAdminMessage();
    });

    // Close chat
    closeChatBtn.addEventListener('click', function() {
        if (currentRoomId && confirm('Are you sure you want to close this chat?')) {
            closeChat(currentRoomId);
        }
    });

    // Refresh button
    refreshBtn.addEventListener('click', function() {
        location.reload();
    });

    function selectChat(chatItem) {
        // Remove active class from all items
        document.querySelectorAll('.chat-item').forEach(item => {
            item.classList.remove('active');
        });

        // Add active class to selected item
        chatItem.classList.add('active');

        // Get room data
        currentRoomId = chatItem.dataset.roomId;
        currentUserName = chatItem.dataset.userName;

        // Update UI
        document.getElementById('selectedUserName').textContent = currentUserName;
        document.getElementById('selectedUserEmail').textContent = chatItem.querySelector('small').textContent;
        document.getElementById('selectedUserAvatar').textContent = currentUserName.charAt(0).toUpperCase();

        // Show chat interface
        noChatSelected.style.display = 'none';
        chatHeader.style.display = 'block';
        chatInput.style.display = 'block';

        // Load messages
        loadMessages();

        // Start polling
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(loadMessages, 3000);

        // Mark as read
        markAsRead(chatItem);
    }

    function loadMessages() {
        if (!currentRoomId) return;

        fetch(`admin.php?action=chat_get_messages&room_id=${currentRoomId}&last_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new messages
                    data.messages.forEach(message => {
                        addMessage(message);
                        lastMessageId = Math.max(lastMessageId, message.id);
                    });

                    // Update typing indicator
                    const typingIndicator = document.getElementById('typingIndicatorHeader');
                    if (data.typing && data.typing.length > 0) {
                        typingIndicator.style.display = 'inline';
                    } else {
                        typingIndicator.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading messages:', error);
            });
    }

    function addMessage(message) {
        const isUser = message.sender_type === 'user';
        const messageDiv = document.createElement('div');
        messageDiv.className = `d-flex mb-3 ${isUser ? 'justify-content-end' : 'justify-content-start'}`;

        const time = new Date(message.created_at).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });

        messageDiv.innerHTML = `
            <div class="message-bubble p-3 rounded ${isUser ? 'message-user' : 'message-admin'}">
                <p class="mb-1">${message.message.replace(/\n/g, '<br>')}</p>
                <small class="opacity-75">${time}</small>
            </div>
        `;

        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    function sendAdminMessage() {
        const message = adminMessageInput.value.trim();
        if (!message || !currentRoomId) return;

        adminSendBtn.disabled = true;
        adminSendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        fetch('admin.php?action=chat_send_message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                room_id: currentRoomId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                adminMessageInput.value = '';
                adminCharCount.textContent = '0/1000';
                stopAdminTyping();
                
                addMessage({
                    id: data.messageId,
                    sender_type: 'admin',
                    message: message,
                    created_at: new Date().toISOString()
                });
                
                lastMessageId = Math.max(lastMessageId, data.messageId);
            } else {
                alert('Failed to send message: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error sending message');
        })
        .finally(() => {
            adminSendBtn.disabled = false;
            adminSendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send';
        });
    }

    function setAdminTyping() {
        if (isAdminTyping || !currentRoomId) return;
        
        isAdminTyping = true;
        fetch('admin.php?action=chat_set_typing', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `room_id=${currentRoomId}`
        });
    }

    function stopAdminTyping() {
        if (!isAdminTyping || !currentRoomId) return;
        
        isAdminTyping = false;
        fetch('admin.php?action=chat_stop_typing', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `room_id=${currentRoomId}`
        });
    }

    function closeChat(roomId) {
        fetch('admin.php?action=chat_close_room', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `room_id=${roomId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to close chat: ' + data.message);
            }
        });
    }

    function markAsRead(chatItem) {
        const badge = chatItem.querySelector('.badge-danger');
        if (badge) {
            badge.remove();
        }
    }

    function scrollToBottom() {
        const container = document.getElementById('chatMessagesContainer');
        container.scrollTop = container.scrollHeight;
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (pollInterval) {
            clearInterval(pollInterval);
        }
        if (isAdminTyping) {
            stopAdminTyping();
        }
    });
});
</script>

<?php include_once 'partials/footer.php'; ?>