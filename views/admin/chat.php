<?php
// views/admin/chat.php - Admin Chat Interface (Standalone)

// Pastikan ini adalah halaman admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: admin.php?action=login');
    exit;
}

// Get rooms data from controller
if (!isset($rooms)) {
    $rooms = [];
}

// Calculate statistics
$activeRooms = array_filter($rooms, function ($r) {
    return $r['status'] === 'active';
});
$totalUnread = array_sum(array_column($rooms, 'unread_count'));
$assignedRooms = array_filter($rooms, function ($r) {
    return !empty($r['admin_id']);
});
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Management - Code Camp Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#1e40af',
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom Styles */
        .chat-item:hover {
            background-color: #f9fafb;
        }

        .chat-item.active {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
        }

        .message-bubble {
            max-width: 60% !important;
            /* Dikurangi dari 70% ke 60% */
            word-wrap: break-word;
            word-break: break-word;
            padding: 8px 12px !important;
            /* Padding lebih kecil */
            border-radius: 18px !important;
            /* Rounded lebih kecil */
            font-size: 14px;
            /* Font size lebih kecil */
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .message-user {
            background-color: #007bff;
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 4px !important;
        }

        .message-admin {
            background-color: #f8f9fa;
            color: #333;
            margin-right: auto;
            border: 1px solid #e9ecef;
            border-bottom-left-radius: 4px !important;
        }

        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 12px 16px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 18px 18px 18px 4px;
            margin-right: auto;
            max-width: 80px;
        }

        .typing-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #9ca3af;
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

        /* Scrollbar Styling */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Connection Status Animation */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Loading Animation */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .lg\:col-span-2 {
                grid-column: span 1;
            }

            .grid-cols-1.lg\:grid-cols-3 {
                grid-template-rows: auto 1fr;
            }
        }

        /* Chat Messages Container dengan scroll yang halus */
        #chatMessagesContainer {
            height: 450px !important;
            /* Tinggi container diperbesar */
            overflow-y: auto !important;
            scroll-behavior: smooth;
            padding: 16px;
            background-color: #f8f9fa;
        }

        /* Custom scrollbar untuk container chat */
        #chatMessagesContainer::-webkit-scrollbar {
            width: 6px;
        }

        #chatMessagesContainer::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #chatMessagesContainer::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        #chatMessagesContainer::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body class="bg-gray-50">
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

    <div class="h-full bg-gray-50">
        <!-- Header Section -->
        <div class="bg-white shadow-sm border-b border-gray-200 mb-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-3 mb-4 sm:mb-0">
                        <div class="bg-blue-500 rounded-lg p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Chat Management</h1>
                            <p class="text-sm text-gray-600">Kelola komunikasi dengan pengguna secara real-time</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div id="connectionStatus" class="flex items-center space-x-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span>Online</span>
                        </div>

                        <button id="refreshBtn" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Active Chats -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Chat Aktif</dt>
                                    <dd class="text-lg font-medium text-gray-900" id="activeChatsCount">
                                        <?php echo count($activeRooms); ?>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unread Messages -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pesan Belum Dibaca</dt>
                                    <dd class="text-lg font-medium text-gray-900" id="unreadMessagesCount">
                                        <?php echo $totalUnread; ?>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Chats -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Chat Assigned</dt>
                                    <dd class="text-lg font-medium text-gray-900" id="assignedChatsCount">
                                        <?php echo count($assignedRooms); ?>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Response Rate -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Response Rate</dt>
                                    <dd class="text-lg font-medium text-gray-900">95%</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chat Interface -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 h-[600px]">
                    <!-- Chat List Sidebar -->
                    <div class="lg:border-r border-gray-200 flex flex-col">
                        <!-- Search and Filter -->
                        <div class="p-4 border-b border-gray-200">
                            <div class="space-y-3">
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="chatSearch"
                                        placeholder="Cari chat..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="flex space-x-2">
                                    <select id="statusFilter" class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Semua Status</option>
                                        <option value="active">Aktif</option>
                                        <option value="closed">Ditutup</option>
                                    </select>

                                    <select id="assignFilter" class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Semua Chat</option>
                                        <option value="assigned">Assigned</option>
                                        <option value="unassigned">Unassigned</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Chat List Header -->
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-medium text-gray-900">
                                    Active Chats <span id="chatCount" class="text-gray-500">(<?php echo count($rooms); ?>)</span>
                                </h3>
                            </div>
                        </div>

                        <!-- Chat List -->
                        <div class="flex-1 overflow-y-auto" id="chatListContainer">
                            <div id="chatList">
                                <?php if (empty($rooms)): ?>
                                    <div class="text-center py-8 text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        <p class="text-sm font-medium">Belum ada chat aktif</p>
                                        <p class="text-xs text-gray-400 mt-1">Chat akan muncul di sini ketika pengguna memulai percakapan</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($rooms as $room): ?>
                                        <div class="chat-item border-b border-gray-100 p-4 cursor-pointer hover:bg-gray-50 transition-colors duration-150"
                                            data-room-id="<?php echo $room['id']; ?>"
                                            data-user-name="<?php echo htmlspecialchars($room['user_name']); ?>"
                                            data-user-email="<?php echo htmlspecialchars($room['user_email']); ?>"
                                            data-status="<?php echo $room['status']; ?>"
                                            data-assigned="<?php echo $room['admin_id'] ? 'assigned' : 'unassigned'; ?>">

                                            <div class="flex items-start space-x-3">
                                                <!-- User Avatar -->
                                                <div class="relative flex-shrink-0">
                                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                        <?php echo strtoupper(substr($room['user_name'], 0, 1)); ?>
                                                    </div>
                                                    <?php if ($room['unread_count'] > 0): ?>
                                                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                                                            <?php echo min($room['unread_count'], 9); ?><?php echo $room['unread_count'] > 9 ? '+' : ''; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Chat Info -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <h4 class="text-sm font-medium text-gray-900 truncate">
                                                            <?php echo htmlspecialchars($room['user_name']); ?>
                                                        </h4>
                                                        <div class="flex items-center space-x-1">
                                                            <?php if ($room['status'] === 'active'): ?>
                                                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                                            <?php else: ?>
                                                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                                            <?php endif; ?>

                                                            <?php if (!$room['admin_id']): ?>
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                    Unassigned
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <p class="text-xs text-gray-500 mb-1"><?php echo htmlspecialchars($room['user_email']); ?></p>

                                                    <?php if ($room['last_message']): ?>
                                                        <p class="text-sm text-gray-600 truncate">
                                                            <?php echo htmlspecialchars(substr($room['last_message'], 0, 60)); ?>
                                                            <?php if (strlen($room['last_message']) > 60) echo '...'; ?>
                                                        </p>
                                                        <p class="text-xs text-gray-400 mt-1">
                                                            <?php echo date('H:i', strtotime($room['last_message_time'])); ?>
                                                        </p>
                                                    <?php else: ?>
                                                        <p class="text-sm text-gray-400 italic">Belum ada pesan</p>
                                                    <?php endif; ?>

                                                    <?php if ($room['admin_id']): ?>
                                                        <p class="text-xs text-blue-600 mt-1">
                                                            <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                            </svg>
                                                            <?php echo htmlspecialchars($room['admin_name']); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Messages Area -->
                    <div class="lg:col-span-2 flex flex-col">
                        <!-- Chat Header -->
                        <div id="chatHeader" class="hidden p-4 border-b border-gray-200 bg-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div id="selectedUserAvatar" class="w-10 h-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    </div>
                                    <div>
                                        <h3 id="selectedUserName" class="text-lg font-medium text-gray-900"></h3>
                                        <p id="selectedUserEmail" class="text-sm text-gray-500"></p>
                                        <div id="typingIndicatorHeader" class="text-xs text-green-600 hidden">
                                            <div class="flex items-center space-x-1">
                                                <div class="typing-dot"></div>
                                                <div class="typing-dot"></div>
                                                <div class="typing-dot"></div>
                                                <span class="ml-2">Sedang mengetik...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <button id="closeChatBtn" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Close Chat
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Messages Container -->
                        <div id="chatMessagesContainer" class="flex-1 overflow-y-auto bg-gray-50 p-4">
                            <div id="chatMessages" class="space-y-4">
                                <!-- No Chat Selected -->
                                <div id="noChatSelected" class="flex flex-col items-center justify-center h-full text-gray-500">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium mb-2">Pilih chat untuk memulai</h3>
                                    <p class="text-sm text-center max-w-sm">Pilih percakapan dari daftar sebelah kiri untuk melihat dan membalas pesan pengguna</p>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div id="chatInput" class="hidden border-t border-gray-200 bg-white p-4">
                            <form id="adminChatForm" class="space-y-3">
                                <div class="flex space-x-3">
                                    <div class="flex-1">
                                        <textarea
                                            id="adminMessageInput"
                                            rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                            placeholder="Ketik pesan Anda..."
                                            maxlength="1000"></textarea>
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-xs text-gray-500">
                                                <span id="adminCharCount">0</span>/1000 karakter
                                            </span>
                                            <span class="text-xs text-gray-400">
                                                Tekan Ctrl+Enter untuk mengirim
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col justify-end">
                                        <button
                                            type="submit"
                                            id="adminSendBtn"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            Kirim
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700">Memproses...</span>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notificationToast" class="hidden fixed top-4 right-4 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto z-50">
        <div class="rounded-lg shadow-xs overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div id="toastIcon" class="w-6 h-6 text-green-400">
                            <!-- Icon will be inserted here -->
                        </div>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p id="toastMessage" class="text-sm font-medium text-gray-900"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button id="closeToast" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentRoomId = null;
            let currentUserName = '';
            let currentUserEmail = '';
            let lastMessageId = 0;
            let pollInterval;
            let isAdminTyping = false;
            let typingTimeout;
            let connectionLost = false;

            // DOM Elements
            const elements = {
                chatList: document.getElementById('chatList'),
                chatHeader: document.getElementById('chatHeader'),
                chatMessages: document.getElementById('chatMessages'),
                chatInput: document.getElementById('chatInput'),
                noChatSelected: document.getElementById('noChatSelected'),
                adminMessageInput: document.getElementById('adminMessageInput'),
                adminChatForm: document.getElementById('adminChatForm'),
                adminSendBtn: document.getElementById('adminSendBtn'),
                closeChatBtn: document.getElementById('closeChatBtn'),
                refreshBtn: document.getElementById('refreshBtn'),
                adminCharCount: document.getElementById('adminCharCount'),
                chatSearch: document.getElementById('chatSearch'),
                statusFilter: document.getElementById('statusFilter'),
                assignFilter: document.getElementById('assignFilter'),
                connectionStatus: document.getElementById('connectionStatus'),
                loadingOverlay: document.getElementById('loadingOverlay'),
                notificationToast: document.getElementById('notificationToast'),
                chatMessagesContainer: document.getElementById('chatMessagesContainer')
            };

            // Initialize
            init();

            function init() {
                bindEvents();
                updateConnectionStatus(true);

                // Start periodic updates
                setInterval(updateChatList, 30000); // Update chat list every 30 seconds
                setInterval(checkConnection, 10000); // Check connection every 10 seconds
            }

            function bindEvents() {
                // Chat item clicks
                document.addEventListener('click', function(e) {
                    const chatItem = e.target.closest('.chat-item');
                    if (chatItem) {
                        selectChat(chatItem);
                    }
                });

                // Message input events
                if (elements.adminMessageInput) {
                    elements.adminMessageInput.addEventListener('input', handleMessageInput);
                    elements.adminMessageInput.addEventListener('keydown', handleKeyDown);
                }

                // Form submit
                if (elements.adminChatForm) {
                    elements.adminChatForm.addEventListener('submit', handleFormSubmit);
                }

                // Button clicks
                if (elements.closeChatBtn) {
                    elements.closeChatBtn.addEventListener('click', handleCloseChat);
                }

                if (elements.refreshBtn) {
                    elements.refreshBtn.addEventListener('click', handleRefresh);
                }

                // Search and filters
                if (elements.chatSearch) {
                    elements.chatSearch.addEventListener('input', handleSearch);
                }

                if (elements.statusFilter) {
                    elements.statusFilter.addEventListener('change', handleFilterChange);
                }

                if (elements.assignFilter) {
                    elements.assignFilter.addEventListener('change', handleFilterChange);
                }

                // Toast close
                const closeToast = document.getElementById('closeToast');
                if (closeToast) {
                    closeToast.addEventListener('click', hideToast);
                }
            }

            function selectChat(chatItem) {
                // Remove active class from all items
                document.querySelectorAll('.chat-item').forEach(item => {
                    item.classList.remove('active');
                });

                // Add active class to selected item
                chatItem.classList.add('active');

                // Get room data
                currentRoomId = parseInt(chatItem.dataset.roomId);
                currentUserName = chatItem.dataset.userName;
                currentUserEmail = chatItem.dataset.userEmail;

                // Update UI
                updateChatHeader(currentUserName, currentUserEmail);
                showChatInterface();

                // Reset message tracking
                lastMessageId = 0;

                // Load messages
                loadMessages(true);

                // Start polling
                startPolling();

                // Mark as read
                markChatAsRead(chatItem);

                showToast('Chat dipilih', 'success');
            }

            function updateChatHeader(userName, userEmail) {
                const selectedUserName = document.getElementById('selectedUserName');
                const selectedUserEmail = document.getElementById('selectedUserEmail');
                const selectedUserAvatar = document.getElementById('selectedUserAvatar');

                if (selectedUserName) selectedUserName.textContent = userName;
                if (selectedUserEmail) selectedUserEmail.textContent = userEmail;
                if (selectedUserAvatar) selectedUserAvatar.textContent = userName.charAt(0).toUpperCase();
            }

            function showChatInterface() {
                if (elements.noChatSelected) elements.noChatSelected.style.display = 'none';
                if (elements.chatHeader) elements.chatHeader.classList.remove('hidden');
                if (elements.chatInput) elements.chatInput.classList.remove('hidden');

                // Clear messages
                if (elements.chatMessages) {
                    elements.chatMessages.innerHTML = '';
                }
            }

            function loadMessages(isInitial = false) {
                if (!currentRoomId) return;

                const url = `admin.php?action=chat_get_messages&room_id=${currentRoomId}&last_id=${lastMessageId}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Add new messages
                            data.messages.forEach(message => {
                                addMessage(message);
                                lastMessageId = Math.max(lastMessageId, parseInt(message.id));
                            });

                            // Update typing indicator
                            updateTypingIndicator(data.typing || []);

                            // Scroll to bottom if initial load or new messages
                            if (isInitial || data.messages.length > 0) {
                                scrollToBottom();
                            }

                            updateConnectionStatus(true);
                        } else {
                            console.error('Failed to load messages:', data.message);
                            updateConnectionStatus(false);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading messages:', error);
                        updateConnectionStatus(false);
                    });
            }

            function addMessage(message) {
                if (!elements.chatMessages) return;

                const isUser = message.sender_type === 'user';
                const messageDiv = document.createElement('div');
                messageDiv.className = `flex ${isUser ? 'justify-end' : 'justify-start'} mb-4`;

                const time = new Date(message.created_at).toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const senderName = message.sender_name || (isUser ? currentUserName : 'Admin');

                messageDiv.innerHTML = `
                    <div class="message-bubble ${isUser ? 'message-user' : 'message-admin'}">
                        <div class="text-xs opacity-75 mb-1">${escapeHtml(senderName)}</div>
                        <div class="message-content">${escapeHtml(message.message)}</div>
                        <div class="text-xs opacity-75 mt-1">${time}</div>
                    </div>
                `;

                elements.chatMessages.appendChild(messageDiv);
            }

            function updateTypingIndicator(typingUsers) {
                // Remove existing typing indicators
                const existingIndicators = elements.chatMessages.querySelectorAll('.typing-indicator');
                existingIndicators.forEach(indicator => indicator.remove());

                // Add new typing indicators
                if (typingUsers.length > 0) {
                    const typingDiv = document.createElement('div');
                    typingDiv.className = 'flex justify-start mb-4';
                    typingDiv.innerHTML = `
                        <div class="typing-indicator">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    `;
                    elements.chatMessages.appendChild(typingDiv);
                    scrollToBottom();
                }

                // Update header typing indicator
                const headerIndicator = document.getElementById('typingIndicatorHeader');
                if (headerIndicator) {
                    headerIndicator.classList.toggle('hidden', typingUsers.length === 0);
                }
            }

            function handleMessageInput() {
                const input = elements.adminMessageInput;
                if (!input) return;

                // Update character count
                if (elements.adminCharCount) {
                    elements.adminCharCount.textContent = input.value.length;
                }

                // Handle typing indicator
                if (input.value.trim() && !isAdminTyping) {
                    setAdminTyping();
                }

                clearTimeout(typingTimeout);
                typingTimeout = setTimeout(() => {
                    if (isAdminTyping) {
                        stopAdminTyping();
                    }
                }, 3000);
            }

            function handleKeyDown(e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    sendAdminMessage();
                }
            }

            function handleFormSubmit(e) {
                e.preventDefault();
                sendAdminMessage();
            }

            function sendAdminMessage() {
                const input = elements.adminMessageInput;
                if (!input || !currentRoomId) return;

                const message = input.value.trim();
                if (!message) return;

                // Disable send button
                if (elements.adminSendBtn) {
                    elements.adminSendBtn.disabled = true;
                    elements.adminSendBtn.innerHTML = `
                        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mengirim...
                    `;
                }

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
                            // Clear input
                            input.value = '';
                            if (elements.adminCharCount) {
                                elements.adminCharCount.textContent = '0';
                            }

                            // Stop typing indicator
                            stopAdminTyping();

                            // Add message to chat
                            const messageObj = {
                                id: data.messageId,
                                sender_type: 'admin',
                                sender_name: '<?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>',
                                message: message,
                                created_at: new Date().toISOString()
                            };
                            addMessage(messageObj);

                            lastMessageId = Math.max(lastMessageId, parseInt(data.messageId));
                            scrollToBottom();

                            showToast('Pesan terkirim', 'success');
                        } else {
                            showToast('Gagal mengirim pesan: ' + (data.message || 'Error tidak dikenal'), 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Error mengirim pesan', 'error');
                    })
                    .finally(() => {
                        // Re-enable send button
                        if (elements.adminSendBtn) {
                            elements.adminSendBtn.disabled = false;
                            elements.adminSendBtn.innerHTML = `
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Kirim
                        `;
                        }
                    });
            }

            function setAdminTyping() {
                if (isAdminTyping || !currentRoomId) return;

                isAdminTyping = true;

                const formData = new FormData();
                formData.append('room_id', currentRoomId);

                fetch('admin.php?action=chat_set_typing', {
                    method: 'POST',
                    body: formData
                }).catch(error => console.error('Error setting typing:', error));
            }

            function stopAdminTyping() {
                if (!isAdminTyping || !currentRoomId) return;

                isAdminTyping = false;

                const formData = new FormData();
                formData.append('room_id', currentRoomId);

                fetch('admin.php?action=chat_stop_typing', {
                    method: 'POST',
                    body: formData
                }).catch(error => console.error('Error stopping typing:', error));
            }

            function handleCloseChat() {
                if (!currentRoomId) return;

                if (confirm('Apakah Anda yakin ingin menutup chat ini?')) {
                    showLoading();

                    const formData = new FormData();
                    formData.append('room_id', currentRoomId);

                    fetch('admin.php?action=chat_close_room', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            hideLoading();
                            if (data.success) {
                                showToast('Chat berhasil ditutup', 'success');
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            } else {
                                showToast('Gagal menutup chat: ' + (data.message || 'Error tidak dikenal'), 'error');
                            }
                        })
                        .catch(error => {
                            hideLoading();
                            console.error('Error:', error);
                            showToast('Error menutup chat', 'error');
                        });
                }
            }

            function handleRefresh() {
                showLoading();
                setTimeout(() => {
                    location.reload();
                }, 500);
            }

            function handleSearch() {
                filterChats();
            }

            function handleFilterChange() {
                filterChats();
            }

            function filterChats() {
                const searchTerm = elements.chatSearch?.value.toLowerCase() || '';
                const statusFilter = elements.statusFilter?.value || '';
                const assignFilter = elements.assignFilter?.value || '';

                const chatItems = document.querySelectorAll('.chat-item');
                let visibleCount = 0;

                chatItems.forEach(item => {
                    const userName = item.dataset.userName.toLowerCase();
                    const userEmail = item.dataset.userEmail.toLowerCase();
                    const status = item.dataset.status;
                    const assigned = item.dataset.assigned;

                    const matchesSearch = !searchTerm ||
                        userName.includes(searchTerm) ||
                        userEmail.includes(searchTerm);

                    const matchesStatus = !statusFilter || status === statusFilter;
                    const matchesAssign = !assignFilter || assigned === assignFilter;

                    const isVisible = matchesSearch && matchesStatus && matchesAssign;

                    item.style.display = isVisible ? 'block' : 'none';
                    if (isVisible) visibleCount++;
                });

                // Update count
                const chatCount = document.getElementById('chatCount');
                if (chatCount) {
                    chatCount.textContent = `(${visibleCount})`;
                }
            }

            function startPolling() {
                if (pollInterval) {
                    clearInterval(pollInterval);
                }

                pollInterval = setInterval(() => {
                    loadMessages();
                }, 3000);
            }

            function stopPolling() {
                if (pollInterval) {
                    clearInterval(pollInterval);
                    pollInterval = null;
                }
            }

            function markChatAsRead(chatItem) {
                const badge = chatItem.querySelector('.bg-red-500');
                if (badge) {
                    badge.remove();
                }
            }

            function scrollToBottom() {
                if (elements.chatMessagesContainer) {
                    elements.chatMessagesContainer.scrollTop = elements.chatMessagesContainer.scrollHeight;
                }
            }

            function updateChatList() {
                // Refresh chat list without full reload
                fetch('admin.php?action=ajax_chat_list')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.html) {
                            const currentActiveId = document.querySelector('.chat-item.active')?.dataset.roomId;
                            elements.chatList.innerHTML = data.html;

                            // Restore active state
                            if (currentActiveId) {
                                const activeItem = document.querySelector(`[data-room-id="${currentActiveId}"]`);
                                if (activeItem) {
                                    activeItem.classList.add('active');
                                }
                            }

                            // Update statistics
                            updateStatistics(data.stats);
                        }
                    })
                    .catch(error => console.error('Error updating chat list:', error));
            }

            function updateStatistics(stats) {
                if (!stats) return;

                const activeChatsCount = document.getElementById('activeChatsCount');
                const unreadMessagesCount = document.getElementById('unreadMessagesCount');
                const assignedChatsCount = document.getElementById('assignedChatsCount');

                if (activeChatsCount) activeChatsCount.textContent = stats.active_rooms || 0;
                if (unreadMessagesCount) unreadMessagesCount.textContent = stats.unread_messages || 0;
                if (assignedChatsCount) assignedChatsCount.textContent = stats.assigned_rooms || 0;
            }

            function checkConnection() {
                fetch('admin.php?action=ping')
                    .then(response => response.json())
                    .then(data => {
                        updateConnectionStatus(data.success);
                    })
                    .catch(error => {
                        updateConnectionStatus(false);
                    });
            }

            function updateConnectionStatus(isOnline) {
                if (!elements.connectionStatus) return;

                if (isOnline) {
                    elements.connectionStatus.className = 'flex items-center space-x-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm';
                    elements.connectionStatus.innerHTML = `
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span>Online</span>
                    `;
                    connectionLost = false;
                } else {
                    elements.connectionStatus.className = 'flex items-center space-x-2 px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm';
                    elements.connectionStatus.innerHTML = `
                        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                        <span>Offline</span>
                    `;

                    if (!connectionLost) {
                        showToast('Koneksi terputus', 'error');
                        connectionLost = true;
                    }
                }
            }

            function showLoading() {
                if (elements.loadingOverlay) {
                    elements.loadingOverlay.classList.remove('hidden');
                }
            }

            function hideLoading() {
                if (elements.loadingOverlay) {
                    elements.loadingOverlay.classList.add('hidden');
                }
            }

            function showToast(message, type = 'info') {
                if (!elements.notificationToast) return;

                const toastMessage = document.getElementById('toastMessage');
                const toastIcon = document.getElementById('toastIcon');

                if (toastMessage) toastMessage.textContent = message;

                if (toastIcon) {
                    const icons = {
                        success: `<svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>`,
                        error: `<svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>`,
                        info: `<svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>`
                    };
                    toastIcon.innerHTML = icons[type] || icons.info;
                }

                elements.notificationToast.classList.remove('hidden');

                // Auto hide after 3 seconds
                setTimeout(() => {
                    hideToast();
                }, 3000);
            }

            function hideToast() {
                if (elements.notificationToast) {
                    elements.notificationToast.classList.add('hidden');
                }
            }

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) {
                    return map[m];
                });
            }

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                stopPolling();
                if (isAdminTyping) {
                    stopAdminTyping();
                }
            });

            // Handle visibility change
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopPolling();
                } else if (currentRoomId) {
                    startPolling();
                }
            });
        });
    </script>
</body>

</html>