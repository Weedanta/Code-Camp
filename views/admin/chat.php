<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Management - Code Camp Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Chat message bubbles */
        .message-bubble {
            max-width: 70%;
            word-wrap: break-word;
        }

        .message-user {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            margin-left: auto;
            border-radius: 18px 18px 4px 18px;
        }

        .message-admin {
            background-color: white;
            color: #374151;
            margin-right: auto;
            border: 1px solid #e5e7eb;
            border-radius: 18px 18px 18px 4px;
        }

        /* Active chat item */
        .chat-item.active {
            background-color: #eff6ff !important;
            border-left: 4px solid #3b82f6 !important;
        }

        /* Typing indicator */
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 18px 18px 18px 4px;
            margin-right: auto;
            max-width: 100px;
        }

        .typing-dot {
            width: 6px;
            height: 6px;
            background-color: #6b7280;
            border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out;
        }

        .typing-dot:nth-child(1) {
            animation-delay: -0.32s;
        }

        .typing-dot:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes typing {

            0%,
            80%,
            100% {
                transform: scale(0.8);
                opacity: 0.5;
            }

            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Custom scrollbar */
        #chatMessagesContainer::-webkit-scrollbar {
            width: 6px;
        }

        #chatMessagesContainer::-webkit-scrollbar-track {
            background: #f3f4f6;
        }

        #chatMessagesContainer::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        #chatMessagesContainer::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Message animations */
        .message-enter {
            opacity: 0;
            transform: translateY(10px);
            animation: messageSlideIn 0.3s ease-out forwards;
        }

        @keyframes messageSlideIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <?php

    $page_title = 'Chat Management - Code Camp Admin';

    // Get rooms data from controller
    if (!isset($rooms)) {
        $rooms = [];
    }
    ?>
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="admin.php?action=dashboard" class="flex items-center space-x-2 text-gray-700 hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali ke Dashboard</span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">
                        Admin: <strong><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></strong>
                    </span>
                    <a href="admin.php?action=logout" class="text-sm text-red-600 hover:text-red-700 transition-colors">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Chat Management</h1>
            <div class="flex items-center space-x-3">
                <span id="connectionStatus" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    Online
                </span>
                <button id="refreshBtn" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Chat Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Active Chats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Active Chats</p>
                        <p class="text-2xl font-bold text-gray-900" id="activeChats">
                            <?php echo count(array_filter($rooms, function ($r) {
                                return $r['status'] === 'active';
                            })); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Unread Messages -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Unread Messages</p>
                        <p class="text-2xl font-bold text-gray-900" id="unreadMessages">
                            <?php echo array_sum(array_column($rooms, 'unread_count')); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Messages Today -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m0 0V9a2 2 0 01-2 2H10a2 2 0 01-2-2V7m0 0a2 2 0 00-2 2v6a2 2 0 002 2h8a2 2 0 002-2v-6a2 2 0 00-2-2H8z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Messages Today</p>
                        <p class="text-2xl font-bold text-gray-900" id="messagesToday">0</p>
                    </div>
                </div>
            </div>

            <!-- Response Rate -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Response Rate</p>
                        <p class="text-2xl font-bold text-gray-900">95%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Interface -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Chat List -->
            <div class="lg:col-span-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Active Chats</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" id="chatCount">
                                <?php echo count($rooms); ?>
                            </span>
                        </div>
                    </div>
                    <div class="overflow-y-auto" style="max-height: 500px;">
                        <div id="chatList">
                            <?php if (empty($rooms)): ?>
                                <div class="text-center py-12 text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"></path>
                                    </svg>
                                    <p class="text-sm">No active chats</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($rooms as $room): ?>
                                    <div class="chat-item border-b border-gray-100 p-4 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                        data-room-id="<?php echo $room['id']; ?>"
                                        data-user-name="<?php echo htmlspecialchars($room['user_name']); ?>">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center mb-2">
                                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                                                        <?php echo strtoupper(substr($room['user_name'], 0, 1)); ?>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="text-sm font-semibold text-gray-900 truncate"><?php echo htmlspecialchars($room['user_name']); ?></h4>
                                                        <p class="text-xs text-gray-500 truncate"><?php echo htmlspecialchars($room['user_email']); ?></p>
                                                    </div>
                                                </div>
                                                <?php if ($room['last_message']): ?>
                                                    <p class="text-sm text-gray-600 truncate mb-1">
                                                        <?php echo htmlspecialchars(substr($room['last_message'], 0, 50)); ?>
                                                        <?php if (strlen($room['last_message']) > 50) echo '...'; ?>
                                                    </p>
                                                    <p class="text-xs text-gray-400">
                                                        <?php echo date('H:i', strtotime($room['last_message_time'])); ?>
                                                    </p>
                                                <?php else: ?>
                                                    <p class="text-sm text-gray-400">No messages yet</p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex flex-col items-end space-y-1 ml-2">
                                                <?php if ($room['unread_count'] > 0): ?>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <?php echo $room['unread_count']; ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!$room['admin_id']): ?>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Unassigned
                                                    </span>
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
            <div class="lg:col-span-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Chat Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 hidden" id="chatHeader">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <div id="selectedUserAvatar" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900" id="selectedUserName"></h3>
                                    <p class="text-sm text-gray-500" id="selectedUserEmail"></p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span id="typingIndicatorHeader" class="text-sm text-gray-500 hidden">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                        Typing...
                                    </div>
                                </span>
                                <button id="closeChatBtn" class="inline-flex items-center px-3 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Close Chat
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Messages Container -->
                    <div class="p-4 bg-gray-50 overflow-y-auto" id="chatMessagesContainer" style="height: 450px;">
                        <div id="chatMessages" class="space-y-4">
                            <div id="noChatSelected" class="text-center py-16 text-gray-500">
                                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Select a chat to start messaging</h3>
                                <p class="text-sm">Choose a conversation from the left to view and respond to messages</p>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Input -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-white hidden" id="chatInput">
                        <form id="adminChatForm" class="flex items-end space-x-3">
                            <div class="flex-1">
                                <textarea
                                    id="adminMessageInput"
                                    class="block w-full resize-none border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Type your message..."
                                    rows="2"
                                    maxlength="1000"></textarea>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs text-gray-500" id="adminCharCount">0/1000</span>
                                    <span class="text-xs text-gray-400">Press Enter to send, Shift+Enter for new line</span>
                                </div>
                            </div>
                            <button type="submit" id="adminSendBtn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Send
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentRoomId = null;
            let currentUserName = '';
            let lastMessageId = 0;
            let pollInterval = null;
            let isAdminTyping = false;
            let typingTimeout = null;

            // Elements
            const chatItems = document.querySelectorAll('.chat-item');
            const noChatSelected = document.getElementById('noChatSelected');
            const chatHeader = document.getElementById('chatHeader');
            const chatInput = document.getElementById('chatInput');
            const chatMessages = document.getElementById('chatMessages');
            const adminMessageInput = document.getElementById('adminMessageInput');
            const adminCharCount = document.getElementById('adminCharCount');
            const adminSendBtn = document.getElementById('adminSendBtn');
            const adminChatForm = document.getElementById('adminChatForm');
            const closeChatBtn = document.getElementById('closeChatBtn');
            const refreshBtn = document.getElementById('refreshBtn');

            // Event listeners
            chatItems.forEach(item => {
                item.addEventListener('click', function() {
                    selectChat(this);
                });
            });

            // Admin chat form
            adminChatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                sendAdminMessage();
            });

            // Character count
            adminMessageInput.addEventListener('input', function() {
                adminCharCount.textContent = this.value.length + '/1000';

                // Auto resize
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';

                // Typing indicator
                setAdminTyping();
                clearTimeout(typingTimeout);
                typingTimeout = setTimeout(stopAdminTyping, 3000);
            });

            // Enter to send
            adminMessageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendAdminMessage();
                }
            });

            // Close chat button
            closeChatBtn.addEventListener('click', function() {
                if (currentRoomId) {
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
                document.getElementById('selectedUserEmail').textContent = chatItem.querySelector('p.text-xs.text-gray-500').textContent;
                document.getElementById('selectedUserAvatar').textContent = currentUserName.charAt(0).toUpperCase();

                // Show chat interface
                noChatSelected.classList.add('hidden');
                chatHeader.classList.remove('hidden');
                chatInput.classList.remove('hidden');

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
                                typingIndicator.classList.remove('hidden');
                            } else {
                                typingIndicator.classList.add('hidden');
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
                messageDiv.className = `flex ${isUser ? 'justify-end' : 'justify-start'} message-enter`;

                const time = new Date(message.created_at).toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                messageDiv.innerHTML = `
            <div class="message-bubble ${isUser ? 'message-user' : 'message-admin'} px-4 py-2 shadow-sm">
                <p class="text-sm mb-1">${message.message.replace(/\n/g, '<br>')}</p>
                <p class="text-xs opacity-75">${time}</p>
            </div>
        `;

                chatMessages.appendChild(messageDiv);
                scrollToBottom();
            }

            function sendAdminMessage() {
                const message = adminMessageInput.value.trim();
                if (!message || !currentRoomId) return;

                adminSendBtn.disabled = true;
                adminSendBtn.innerHTML = `
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Sending...
        `;

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
                            adminMessageInput.style.height = 'auto';
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
                        adminSendBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Send
            `;
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
                const badge = chatItem.querySelector('.bg-red-100');
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
</body>

</html>