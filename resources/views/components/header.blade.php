
<!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary container" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->


    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="{{ route('homeslug') }}" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>Online Learning Platform</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="{{ route('homeslug') }}" class="nav-item nav-link {{ Request::is('/home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('about') }}" class="nav-item nav-link {{ Request::is('about') ? 'active' : '' }}">About</a>
                <a href="{{ route('courses') }}" class="nav-item nav-link {{ Request::is('courses') ? 'active' : '' }}">Courses</a>
                <a href="{{ route('contact') }}" class="nav-item nav-link {{ Request::is('contact') ? 'active' : '' }}">Contact</a>
                @auth
                    <!-- Chat Link -->
                    @if (
                       auth()->user()->role->role == 'student'
                    )
                        <a href="{{ route('student.chat.index') }}" class="nav-item nav-link position-relative" title="Chat">
                            <i class="fas fa-comments"></i>
                            <span id="chat-notification-badge" class="position-absolute badge badge-danger rounded-pill" style="display: none; font-size: 0.6rem; top: 8px; right: 8px;">
                                0
                            </span>
                        </a>
                    @endif

                    @if (
                       auth()->user()->role->role == 'teacher'
                    )
                        <a href="{{ route('chat.index') }}" class="nav-item nav-link position-relative" title="Chat">
                            <i class="fas fa-comments"></i>
                            <span id="chat-notification-badge" class="position-absolute badge badge-danger rounded-pill" style="display: none; font-size: 0.6rem; top: 8px; right: 8px;">
                                0
                            </span>
                        </a>
                    @endif

                    <!-- Notifications Bell -->
                    @if (
                       auth()->user()->role->role == 'student'
                    )
                        <a href="{{ route('student.notifications.index') }}" class="nav-item nav-link position-relative" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span id="user-notification-badge" class="position-absolute badge badge-danger rounded-pill" style="display: none; font-size: 0.6rem; top: 8px; right: 8px;">
                                0
                            </span>
                        </a>
                    @endif

                    @if (
                       auth()->user()->role->role == 'teacher'
                    )
                        <a href="{{ route('notifications.index') }}" class="nav-item nav-link position-relative" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span id="user-notification-badge" class="position-absolute badge badge-danger rounded-pill" style="display: none; font-size: 0.6rem; top: 8px; right: 8px;">
                                0
                            </span>
                        </a>
                    @endif

                    
                    <div class="nav-item dropdown show">
                        <a href="{{ route('homeslug') }}" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">{{Auth::user()->username}}</a>
                        <div class="dropdown-menu fade-down m-0 show">
                            <a href="{{ route('profile.show', auth()->user()->username) }}" class="dropdown-item">Profile</a>
                            
                            @if (auth()->user()->role->role == 'teacher')
                                <a href="{{ route('teacher.dashboard') }}" class="dropdown-item">Dashboard</a>
                            @endif
                            @if (auth()->user()->role->role == 'student')
                                <a href="{{ route('students.dashboard') }}" class="dropdown-item">Dashboard</a>
                            @endif
                            @if (auth()->user()->role->role == 'adminstrator')
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item">Dashboard</a>
                            @endif
                            <a href="#" class="dropdown-item" onclick="handleLogout()">Logout</a>
                            
                        </div>
                    </div>
                @else 
                    <a href="{{ route('studentlogin') }}" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Login<i class="fa fa-arrow-right ms-3"></i></a>
                @endauth
               
            </div>
            
            

            

            
        </div>
    </nav>
    <div class="mt-5">
        <!-- Navbar End -->

    <!-- Include Toastify Notifications -->
    <x-toastify-notifications />
    
    <!-- Toast Notification Styles -->
    <style>
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .toast {
            min-width: 300px;
            max-width: 350px;
        }
        
        .toast-header {
            border-bottom: none !important;
        }
        
        .toast-body {
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        
        #notification-toast-container .toast:hover {
            transform: scale(1.02);
            transition: transform 0.2s ease;
        }
    </style>
    
    @auth
    <script>
    // Function to update notification count for regular users
    const badge = document.getElementById('user-notification-badge');
    const chatBadge = document.getElementById('chat-notification-badge');


    
    function updateUserNotificationCount() {
        fetch("{{ route('notifications.unread-count') }}")
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching notification count:', error);
            });
    }

    // Function to show toast notifications
    function showNotificationToast(notification) {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('notification-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'notification-toast-container';
            toastContainer.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 350px;
            `;
            document.body.appendChild(toastContainer);
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'toast show';
        toast.style.cssText = `
            margin-bottom: 10px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideInRight 0.3s ease-out;
        `;
        
        toast.innerHTML = `
            <div class="toast-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 7px 7px 0 0;">
                <i class="fas fa-bell me-2"></i>
                <strong class="me-auto">New Notification</strong>
                <small class="text-light">${notification.created_at}</small>
                <button type="button" class="btn-close btn-close-white ms-2" onclick="this.closest('.toast').remove()"></button>
            </div>
            <div class="toast-body" style="padding: 12px;">
                <h6 class="mb-2" style="color: #333; font-weight: 600;">${notification.title}</h6>
                <p class="mb-2" style="color: #666; font-size: 0.9rem; line-height: 1.4;">${notification.message}</p>
                <small class="text-muted">From: ${notification.sender}</small>
                <div class="mt-2">
                    <button class="btn btn-sm btn-primary me-2" onclick="markNotificationAsRead('${notification.id}', this.closest('.toast'))">
                        <i class="fas fa-check me-1"></i>Mark as Read
                    </button>
                    <a href="{{ auth()->user()->role->role === 'teacher' ? route('notifications.index') : route('student.notifications.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-list me-1"></i>View All
                    </a>
                </div>
            </div>
        `;

        toastContainer.appendChild(toast);

        // No auto-remove - only manual close
    }

    // Function to fetch and show recent notifications as toasts
     function showRecentNotificationsAsToasts() {
         // Get shown notifications from localStorage
         const shownNotifications = JSON.parse(localStorage.getItem('shownNotifications') || '[]');
         
         fetch("{{ route('notifications.recent') }}")
             .then(response => response.json())
             .then(data => {
                 if (data.notifications && data.notifications.length > 0) {
                     // Filter out already shown notifications
                     const newNotifications = data.notifications.filter(notification => 
                         !shownNotifications.includes(notification.id.toString())
                     );
                     
                     if (newNotifications.length > 0) {
                         // Show up to 3 most recent new notifications as toasts
                         newNotifications.slice(0, 3).forEach((notification, index) => {
                             setTimeout(() => {
                                 showNotificationToast(notification);
                                 // Mark notification as shown
                                 shownNotifications.push(notification.id.toString());
                                 localStorage.setItem('shownNotifications', JSON.stringify(shownNotifications));
                             }, index * 500); // Stagger the toasts
                         });
                     }
                 }
             })
             .catch(error => {
                 console.error('Error fetching notifications:', error);
             });
     }

    // Function to mark notification as read (global scope for onclick)
    window.markNotificationAsRead = function(notificationId, toastElement = null) {
        // Check if user is authenticated
        @guest
            alert('You must be logged in to mark notifications as read.');
            return;
        @endguest
        
        // Check if CSRF token exists
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('CSRF token not found. Please refresh the page.');
            return;
        }
        
        const url = "{{ route('notifications.read', ':id') }}".replace(':id', notificationId);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                throw new Error('Server returned non-JSON response');
            }
        })
        .then(data => {
            if (data.success) {
                updateUserNotificationCount();
                if (toastElement) {
                    toastElement.style.animation = 'slideOutRight 0.3s ease-in';
                    setTimeout(() => toastElement.remove(), 300);
                    showSuccessToast('Notification marked as read!');

                    // Remove from shown notifications so it can appear again if needed
                    const shownNotifications = JSON.parse(localStorage.getItem('shownNotifications') || '[]');
                    const updatedShown = shownNotifications.filter(id => id !== notificationId.toString());
                    localStorage.setItem('shownNotifications', JSON.stringify(updatedShown));
                }
            } else {
                alert('Failed to mark notification as read: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Failed to mark notification as read. Please try again.');
        });

    };
    
    // Clear notification localStorage on logout
    window.clearNotificationStorage = function() {
        localStorage.removeItem('shownNotifications');
    };

    // Function to update chat unread count
    function updateChatUnreadCount() {
        if (chatBadge) {
            fetch("{{ route('chat.unread-count') }}")
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

    // Function to show simple success toast
     function showSuccessToast(message) {
         let toastContainer = document.getElementById('notification-toast-container');
         if (!toastContainer) {
             toastContainer = document.createElement('div');
             toastContainer.id = 'notification-toast-container';
             toastContainer.style.cssText = `
                 position: fixed;
                 top: 20px;
                 right: 20px;
                 z-index: 9999;
                 max-width: 350px;
             `;
             document.body.appendChild(toastContainer);
         }

         const toast = document.createElement('div');
         toast.className = 'toast show';
         toast.style.cssText = `
             margin-bottom: 10px;
             background: #d4edda;
             border: 1px solid #c3e6cb;
             border-radius: 8px;
             box-shadow: 0 4px 12px rgba(0,0,0,0.15);
             animation: slideInRight 0.3s ease-out;
         `;
         
         toast.innerHTML = `
             <div class="toast-body" style="padding: 12px; color: #155724;">
                 <i class="fas fa-check-circle me-2"></i>
                 ${message}
                 <button type="button" class="btn-close ms-auto" onclick="this.closest('.toast').remove()" style="float: right;"></button>
             </div>
         `;

         toastContainer.appendChild(toast);

         // No auto-remove - only manual close
     }

     

    // Update count on page load and show notifications as toasts
     document.addEventListener('DOMContentLoaded', function() {
         if (badge) {
             updateUserNotificationCount();
         }
         if (chatBadge) {
             updateChatUnreadCount();
         }
         
         // Show recent notifications as toasts after a short delay
         setTimeout(() => {
             showRecentNotificationsAsToasts();
         }, 2000);
     });

    // Update count every 30 seconds
    if (badge) {
        setInterval(updateUserNotificationCount, 30000);
    }
    if (chatBadge) {
        setInterval(updateChatUnreadCount, 30000);
    }
    
    // Handle logout with notification storage cleanup
    function handleLogout() {
        clearNotificationStorage();
        window.location.href = '{{ route("logout") }}';
    }
    
    </script>
    @endauth
    </div>