
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->


    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="/" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>eLEARNING</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="/" class="nav-item nav-link active">Home</a>
                <a href="/about" class="nav-item nav-link">About</a>
                <a href="/courses" class="nav-item nav-link">Courses</a>
             
                <a href="/contact" class="nav-item nav-link">Contact</a>
                @auth
                    <!-- Notifications Bell -->
                    @if (
                       auth()->user()->role->role == 'student' || auth()->user()->role->role == 'teacher'
                    )
                        <a href="{{ route('notifications.index') }}" class="nav-item nav-link position-relative">
                            <i class="fas fa-bell"></i>
                            <span id="user-notification-badge" class="position-absolute badge badge-danger rounded-pill" style="display: none; font-size: 0.6rem; top: 8px; right: 8px;">
                                0
                            </span>
                        </a>
                    @endif

                    
                    <div class="nav-item dropdown show">
                        <a href="/" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">{{Auth::user()->username}}</a>
                        <div class="dropdown-menu fade-down m-0 show">
                            <a href="/profile/{{auth()->user()->username}}" class="dropdown-item">Profile</a>
                            
                            <a href="/admin/dashboard" class="dropdown-item">Dashboard</a>
                            <a href="/logout" class="dropdown-item">Logout</a>
                            
                        </div>
                    </div>
                @else 
                    <a href="/login" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Login<i class="fa fa-arrow-right ms-3"></i></a>
                @endauth
               
            </div>
            
            

            

            
        </div>
    </nav>
    <div class="mt-5">
        <!-- Navbar End -->

    @if (session('success'))
    <x-alert type='success'>{{ session('success') }}</x-alert>
    @endif
    
    @if (session('warning'))
    <x-alert type='warning'>{{ session('warning') }}</x-alert>
    @endif
    
    @if (session('danger'))
    <x-alert type='danger'>{{ session('danger') }}</x-alert>
    @endif
    
    <x-showerror name="error"></x-showerror>
    
    @auth
    <script>
    // Function to update notification count for regular users
    const badge = document.getElementById('user-notification-badge');

    
    function updateUserNotificationCount() {
        fetch('/notifications/unread-count')
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

    // Update count on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (badge) {
            updateUserNotificationCount();
        }
    });

    // Update count every 30 seconds
    if (badge) {
        setInterval(updateUserNotificationCount, 30000);
    }
    
    </script>
    @endauth
    </div>