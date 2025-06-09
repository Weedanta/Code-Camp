<?php
// views/chat/index.php - Chat interface untuk user

// Set current page untuk header
$current_dashboard_page = 'chat';
$page_title = 'Chat dengan Admin - Code Camp';

// Base URL untuk assets dan links
$base_url = '';

// Include header yang benar
include_once 'views/includes/header.php';

// Get room data from controller
if (!isset($room) || !isset($messages)) {
    echo '<script>alert("Error: Data chat tidak ditemukan"); window.location.href = "index.php";</script>';
    exit;
}

$admin_name = $room['admin_name'] ?? 'Admin';
$admin_status = $room['admin_id'] ? 'Terhubung' : 'Menunggu admin';
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>

        <!-- Chat Header -->
        <div class="bg-white rounded-t-lg shadow-md p-4 border-b">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($admin_name); ?></h3>
                        <p class="text-sm text-gray-500" id="adminStatus"><?php echo $admin_status; ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span id="connectionStatus" class="w-3 h-3 bg-green-500 rounded-full"></span>
                    <span class="text-sm text-gray-500">Online</span>
                </div>
            </div>
        </div>

        <!-- Chat Messages Container -->
        <div id="chatContainer" class="bg-white shadow-md h-96 overflow-y-auto p-4 space-y-4" style="height: 400px;">
            <?php if (empty($messages)): ?>
                <div class="text-center text-gray-500 py-8">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"></path>
                    </svg>
                    <p>Mulai percakapan dengan admin</p>
                    <p class="text-sm mt-1">Ketik pesan di bawah untuk memulai chat</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <?php $isUser = $message['sender_type'] === 'user'; ?>
                    <div class="message flex <?php echo $isUser ? 'justify-end' : 'justify-start'; ?>" data-message-id="<?php echo $message['id']; ?>">
                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg <?php echo $isUser ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-900'; ?>">
                            <p class="text-sm"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                            <p class="text-xs mt-1 opacity-70">
                                <?php echo date('H:i', strtotime($message['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <!-- Typing Indicator -->
            <div id="typingIndicator" class="hidden">
                <div class="flex justify-start">
                    <div class="bg-gray-200 text-gray-900 max-w-xs px-4 py-2 rounded-lg">
                        <div class="flex items-center space-x-1">
                            <span class="text-sm">Admin sedang mengetik</span>
                            <div class="flex space-x-1">
                                <div class="w-1 h-1 bg-gray-500 rounded-full animate-bounce"></div>
                                <div class="w-1 h-1 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-1 h-1 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="bg-white rounded-b-lg shadow-md p-4 border-t">
            <form id="chatForm" class="flex items-center space-x-3">
                <div class="flex-1">
                    <textarea 
                        id="messageInput" 
                        placeholder="Ketik pesan Anda..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        rows="1"
                        maxlength="1000"
                    ></textarea>
                </div>
                <button 
                    type="submit" 
                    id="sendButton"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <span>Kirim</span>
                </button>
            </form>
            <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                <span id="charCount">0/1000</span>
                <span>Tekan Enter untuk kirim, Shift+Enter untuk baris baru</span>
            </div>
        </div>

        <!-- Quick Messages -->
        <div class="mt-4 bg-white rounded-lg shadow-md p-4">
            <h4 class="font-semibold text-gray-900 mb-3">Pesan Cepat:</h4>
            <div class="flex flex-wrap gap-2">
                <button class="quick-message bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm transition-colors" data-message="Halo, saya butuh bantuan">
                    Halo, saya butuh bantuan
                </button>
                <button class="quick-message bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm transition-colors" data-message="Saya ada masalah dengan bootcamp">
                    Masalah bootcamp
                </button>
                <button class="quick-message bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm transition-colors" data-message="Bagaimana cara pembayaran?">
                    Cara pembayaran?
                </button>
                <button class="quick-message bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm transition-colors" data-message="Terima kasih atas bantuannya">
                    Terima kasih
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.getElementById('chatContainer');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const chatForm = document.getElementById('chatForm');
    const typingIndicator = document.getElementById('typingIndicator');
    const charCount = document.getElementById('charCount');
    const connectionStatus = document.getElementById('connectionStatus');
    
    let lastMessageId = <?php echo !empty($messages) ? max(array_column($messages, 'id')) : 0; ?>;
    let typingTimeout;
    let isTyping = false;
    let pollInterval;

    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        
        // Update character count
        charCount.textContent = this.value.length + '/1000';
        
        // Handle typing indicator
        if (this.value.trim() && !isTyping) {
            setTyping();
        }
        
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => {
            if (isTyping) {
                stopTyping();
            }
        }, 3000);
    });

    // Handle Enter key
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Send message
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    // Quick messages
    document.querySelectorAll('.quick-message').forEach(button => {
        button.addEventListener('click', function() {
            messageInput.value = this.dataset.message;
            messageInput.focus();
            charCount.textContent = messageInput.value.length + '/1000';
        });
    });

    function sendMessage() {
        const message = messageInput.value.trim();
        if (!message) return;

        sendButton.disabled = true;
        sendButton.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

        fetch('index.php?action=chat_send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                messageInput.style.height = 'auto';
                charCount.textContent = '0/1000';
                stopTyping();
                addMessage({
                    id: data.messageId,
                    sender_type: 'user',
                    message: message,
                    created_at: new Date().toISOString()
                });
                lastMessageId = Math.max(lastMessageId, data.messageId);
            } else {
                alert('Gagal mengirim pesan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim pesan');
        })
        .finally(() => {
            sendButton.disabled = false;
            sendButton.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg><span>Kirim</span>';
        });
    }

    function addMessage(message) {
        const isUser = message.sender_type === 'user';
        const messageDiv = document.createElement('div');
        messageDiv.className = `message flex ${isUser ? 'justify-end' : 'justify-start'}`;
        messageDiv.dataset.messageId = message.id;

        const time = new Date(message.created_at).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });

        messageDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${isUser ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-900'}">
                <p class="text-sm">${message.message.replace(/\n/g, '<br>')}</p>
                <p class="text-xs mt-1 opacity-70">${time}</p>
            </div>
        `;

        chatContainer.insertBefore(messageDiv, typingIndicator);
        scrollToBottom();
    }

    function setTyping() {
        if (isTyping) return;
        
        isTyping = true;
        fetch('index.php?action=chat_typing', {
            method: 'POST'
        });
    }

    function stopTyping() {
        if (!isTyping) return;
        
        isTyping = false;
        fetch('index.php?action=chat_stop_typing', {
            method: 'POST'
        });
    }

    function pollMessages() {
        fetch(`index.php?action=chat_get_messages&last_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new messages
                    data.messages.forEach(message => {
                        addMessage(message);
                        lastMessageId = Math.max(lastMessageId, message.id);
                    });

                    // Update typing indicator
                    if (data.typing && data.typing.length > 0) {
                        typingIndicator.classList.remove('hidden');
                    } else {
                        typingIndicator.classList.add('hidden');
                    }

                    // Update connection status
                    connectionStatus.className = 'w-3 h-3 bg-green-500 rounded-full';
                }
            })
            .catch(error => {
                console.error('Polling error:', error);
                connectionStatus.className = 'w-3 h-3 bg-red-500 rounded-full';
            });
    }

    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Start polling for new messages
    pollInterval = setInterval(pollMessages, 3000);

    // Initial scroll to bottom
    scrollToBottom();

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (pollInterval) {
            clearInterval(pollInterval);
        }
        if (isTyping) {
            stopTyping();
        }
    });
});
</script>

<?php include_once 'views/includes/footer.php'; ?>