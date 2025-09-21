<x-adminlayout title="Chat with {{ $otherUser->username }}">
    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card chat-card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    <img src="{{ $otherUser->userphoto ? asset($otherUser->userphoto) : asset('assets/avatars/user.png') }}" 
                                         class="rounded-circle" 
                                         width="40" height="40" alt="User"
                                         style="object-fit: cover;">
                                </div>
                                <div>
                                    <h5 class="mb-0">
                                        <i class="fas fa-comments"></i> 
                                        {{ $otherUser->username }}
                                    </h5>
                                    <small class="text-white-50">{{ $course->name }}</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span id="typing-indicator" class="text-white-50 small me-3" style="display: none;">
                                    <i class="fas fa-circle text-success"></i> Online
                                </span>

                                @if (auth()->user()->role->role == 'teacher')
                                    <a href="{{ route('chat.index') }}" class="btn btn-light btn-sm">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                @elseif (auth()->user()->role->role == 'student')
                                    <a href="{{ route('student.chat.index') }}" class="btn btn-light btn-sm">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                @endif
                                
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Chat Messages -->
                        <div id="chat-messages" class="chat-messages-container">
                            @foreach($messages as $message)
                                <div class="message-wrapper {{ $message->sender_id === Auth::id() ? 'sent' : 'received' }}" 
                                     data-message-id="{{ $message->id }}">
                                    <div class="message-bubble {{ $message->sender_id === Auth::id() ? 'sent-bubble' : 'received-bubble' }}">
                                        <div class="message-content">
                                            {{ $message->message }}
                                        </div>
                                        <div class="message-time">
                                            {{ $message->created_at->format('g:i A') }}
                                        </div>
                                    </div>
                                    <div class="message-sender">
                                        {{ $message->sender->username }}
                                    </div>
                                </div>
                            @endforeach
                            <div id="messages-end"></div>
                        </div>

                        <!-- Message Input -->
                        <div class="chat-input-container">
                            <form id="chat-form" method="POST" action="{{ route('chat.store') }}">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                                
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control chat-input" 
                                           name="message" 
                                           id="message-input"
                                           placeholder="Type your message..." 
                                           required
                                           maxlength="1000">
                                    <button class="btn btn-primary chat-send-btn" type="submit" id="send-btn">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .chat-card {
            border: none;
            border-radius: 15px;
            height: 70vh;
            display: flex;
            flex-direction: column;
        }

        .chat-card .card-header {
            border-radius: 15px 15px 0 0;
            padding: 1rem;
            border: none;
        }

        .chat-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            scrollbar-width: thin;
            scrollbar-color: #dee2e6 transparent;
        }

        .chat-messages-container::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages-container::-webkit-scrollbar-thumb {
            background: #dee2e6;
            border-radius: 3px;
        }

        .message-wrapper {
            margin-bottom: 1rem;
            display: flex;
            flex-direction: column;
            animation: messageSlideIn 0.3s ease-out;
        }

        .message-wrapper.sent {
            align-items: flex-end;
        }

        .message-wrapper.received {
            align-items: flex-start;
        }

        .message-bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .sent-bubble {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-bottom-right-radius: 6px;
        }

        .received-bubble {
            background: white;
            color: #333;
            border: 1px solid #e9ecef;
            border-bottom-left-radius: 6px;
        }

        .message-content {
            font-size: 14px;
            line-height: 1.4;
            margin-bottom: 4px;
        }

        .message-time {
            font-size: 11px;
            opacity: 0.7;
            text-align: right;
        }

        .message-sender {
            font-size: 11px;
            margin-top: 4px;
            opacity: 0.6;
        }

        .sent .message-sender {
            text-align: right;
        }

        .received .message-sender {
            text-align: left;
        }

        .chat-input-container {
            padding: 1rem;
            background: white;
            border-top: 1px solid #e9ecef;
            border-radius: 0 0 15px 15px;
        }

        .chat-input {
            border: 1px solid #dee2e6;
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 14px;
            border-right: none;
        }

        .chat-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .chat-send-btn {
            border-radius: 25px;
            padding: 12px 20px;
            border: 1px solid #007bff;
            background: #007bff;
            transition: all 0.3s ease;
        }

        .chat-send-btn:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        .chat-send-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .user-avatar img {
            border: 2px solid rgba(255,255,255,0.3);
        }

        @keyframes messageSlideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-10px); }
        }

        .typing-indicator {
            animation: typing 1.5s infinite;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .chat-card {
                height: 80vh;
                margin: 0.5rem;
            }
            
            .message-bubble {
                max-width: 85%;
            }
            
            .chat-input-container {
                padding: 0.75rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.getElementById('chat-messages');
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');
            const sendBtn = document.getElementById('send-btn');
            let lastMessageId = 0;
            let isLoading = false;

            // Get last message ID for sync
            const messageWrappers = chatMessages.querySelectorAll('.message-wrapper[data-message-id]');
            if (messageWrappers.length > 0) {
                lastMessageId = Math.max(...Array.from(messageWrappers).map(el => parseInt(el.dataset.messageId)));
            }

            // Scroll to bottom
            function scrollToBottom() {
                const messagesEnd = document.getElementById('messages-end');
                messagesEnd.scrollIntoView({ behavior: 'smooth' });
            }

            // Initial scroll to bottom
            scrollToBottom();

            // Handle form submission
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (isLoading) return;
                
                const formData = new FormData(chatForm);
                const message = messageInput.value.trim();
                
                if (!message) return;

                // Disable send button
                isLoading = true;
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                // Add message to chat immediately
                const tempMessageId = 'temp-' + Date.now();
                addMessageToChat(message, '{{ Auth::user()->username }}', true, tempMessageId);
                
                // Clear input
                messageInput.value = '';
                
                // Scroll to bottom
                scrollToBottom();

                // Send via AJAX
                fetch('{{ route("chat.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update the temporary message with real data
                        const tempMessage = document.querySelector(`[data-message-id="${tempMessageId}"]`);
                        if (tempMessage) {
                            tempMessage.dataset.messageId = data.message.id;
                            lastMessageId = Math.max(lastMessageId, data.message.id);
                        }
                    } else {
                        // Remove the temporary message
                        const tempMessage = document.querySelector(`[data-message-id="${tempMessageId}"]`);
                        if (tempMessage) {
                            tempMessage.remove();
                        }
                        alert('Failed to send message. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Remove the temporary message
                    const tempMessage = document.querySelector(`[data-message-id="${tempMessageId}"]`);
                    if (tempMessage) {
                        tempMessage.remove();
                    }
                    alert('Failed to send message. Please try again.');
                })
                .finally(() => {
                    // Re-enable send button
                    isLoading = false;
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                });
            });

            function addMessageToChat(message, senderName, isSent, messageId = null) {
                const messageWrapper = document.createElement('div');
                messageWrapper.className = `message-wrapper ${isSent ? 'sent' : 'received'}`;
                if (messageId) {
                    messageWrapper.dataset.messageId = messageId;
                }
                
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', { 
                    hour: 'numeric', 
                    minute: '2-digit',
                    hour12: true 
                });

                messageWrapper.innerHTML = `
                    <div class="message-bubble ${isSent ? 'sent-bubble' : 'received-bubble'}">
                        <div class="message-content">${message}</div>
                        <div class="message-time">${timeString}</div>
                    </div>
                    <div class="message-sender">${senderName}</div>
                `;

                const messagesEnd = document.getElementById('messages-end');
                messagesEnd.parentNode.insertBefore(messageWrapper, messagesEnd);
                
                // Scroll to bottom
                setTimeout(() => scrollToBottom(), 100);
            }

            // Sync messages every 10 seconds
            function syncMessages() {
                fetch('{{ route("chat.messages", [$course->id, $otherUser->id]) }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.messages && data.messages.length > 0) {
                            // Filter new messages
                            const newMessages = data.messages.filter(msg => msg.id > lastMessageId);
                            
                            if (newMessages.length > 0) {
                                // Add new messages to chat
                                newMessages.forEach(message => {
                                    const isSent = message.sender_id === {{ Auth::id() }};
                                    addMessageToChat(message.message, message.sender.username, isSent, message.id);
                                    lastMessageId = Math.max(lastMessageId, message.id);
                                });
                                
                                // Update chat badge count
                                updateChatBadgeCount();
                            }
                        }
                    })
                    .catch(error => console.error('Error syncing messages:', error));
            }

            // Auto-sync messages every 10 seconds
            setInterval(syncMessages, 10000);

            // Function to update chat badge count
            function updateChatBadgeCount() {
                const chatBadge = document.getElementById('chat-notification-badge') || 
                                  document.getElementById('admin-chat-badge') || 
                                  document.getElementById('admin-chat-badge-teacher');
                
                if (chatBadge) {
                    fetch('/chat/unread-count')
                        .then(response => response.json())
                        .then(data => {
                            if (data.count > 0) {
                                chatBadge.textContent = data.count > 99 ? '99+' : data.count;
                                chatBadge.style.display = 'inline';
                            } else {
                                chatBadge.style.display = 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching chat unread count:', error);
                        });
                }
            }

            // Update badge count when entering chat
            updateChatBadgeCount();

            // Focus on input when page loads
            messageInput.focus();

            // Enter key to send message
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    chatForm.dispatchEvent(new Event('submit'));
                }
            });
        });
    </script>
</x-adminlayout> 