<div class="container-fluid">
     <nav class="navbar bg-body-tertiary sticky-top">
          <div class="container-fluid">
               <a class="navbar-brand fontcolor" href="/">Learning Management</a>
               
               <div class="d-flex align-items-center">
                    <div class="userimgcontainer me-2" style="background: url('{{ asset(auth()->user()->userphoto) }}'); background-position: center; background-repeat: no-repeat; background-size: cover; width: 40px; height: 40px; border-radius: 50%;">
                         {{-- user photo --}}
                    </div>
                    <span class="me-3">{{ auth()->user()->username }} ({{ auth()->user()->role->role }})</span>
                    <a href="#" class="btn btn-outline-danger btn-sm" onclick="handleLogout()">
                         <i class="fa-solid fa-circle-chevron-left"></i> Logout
                    </a>
               </div>
          </div>
     </nav>
     
     <!-- Menu Buttons -->
     <nav class="navbar bg-light border-bottom">
          <form class="container-fluid justify-content-start">

               @if (auth()->user()->role->role == 'student')
                    <a href="{{ route('student.courses', auth()->user()->id) }}" class="btn {{ request()->routeIs('student.courses') ? 'btn-primary' : 'btn-outline-primary' }} me-2" type="button">
                         <i class="fa-solid fa-c"></i> Courses
                    </a>
                    <a href="{{ route('assignments.index') }}" class="btn {{ request()->routeIs('assignments.*') ? 'btn-success' : 'btn-outline-success' }} me-2" type="button">
                         <i class="fa-solid fa-a"></i> Assignments
                    </a>
                    <a href="{{ route('student.quizzes', auth()->user()->id) }}" class="btn {{ request()->routeIs('student.quizzes') ? 'btn-info' : 'btn-outline-info' }} me-2" type="button">
                         <i class="fa-solid fa-q"></i> Quizzes
                    </a>
                    <a href="{{ route('chat.index') }}" class="btn {{ request()->routeIs('chat.*') ? 'btn-secondary' : 'btn-outline-secondary' }} me-2 position-relative" type="button">
                         <i class="fas fa-comments"></i> Chat
                         <span id="admin-chat-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
                    </a>
                    <a href="{{ route('notifications.index') }}" class="btn {{ request()->routeIs('notifications.*') ? 'btn-warning' : 'btn-outline-warning' }} me-2 position-relative" type="button">
                         <i class="fas fa-bell"></i> Notifications
                         <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
                    </a>
               @endif

               @if (auth()->user()->role->role == 'teacher')
                    <a href="{{ route('admincourses') }}" class="btn {{ request()->is('admin/courses*') ? 'btn-primary' : 'btn-outline-primary' }} me-2" type="button">
                         <i class="fa-solid fa-c"></i> Courses
                    </a>
                    <a href="{{ route('assignments.index') }}" class="btn {{ request()->routeIs('assignments.*') ? 'btn-success' : 'btn-outline-success' }} me-2" type="button">
                         <i class="fa-solid fa-a"></i> Assignments
                    </a>
                    <a href="{{ route('lessons.index') }}" class="btn {{ request()->routeIs('lessons.*') ? 'btn-info' : 'btn-outline-info' }} me-2" type="button">
                         <i class="fa-solid fa-l"></i> Lessons
                    </a>
                    <a href="{{ route('quizzes.index') }}" class="btn {{ request()->routeIs('quizzes.*') ? 'btn-warning' : 'btn-outline-warning' }} me-2" type="button">
                         <i class="fa-solid fa-q"></i> Quizzes
                    </a>
                    <a href="{{ route('chat.index') }}" class="btn {{ request()->routeIs('chat.*') ? 'btn-secondary' : 'btn-outline-secondary' }} me-2 position-relative" type="button">
                         <i class="fas fa-comments"></i> Chat
                         <span id="admin-chat-badge-teacher" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
                    </a>
                    <a href="{{ route('notifications.index') }}" class="btn {{ request()->routeIs('notifications.*') ? 'btn-dark' : 'btn-outline-dark' }} me-2 position-relative" type="button">
                         <i class="fas fa-bell"></i> Notifications
                         <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
                    </a>
               @endif

               @if (auth()->user()->role->role == 'adminstrator')
                    <a href="/admin/roles" class="btn {{ request()->is('admin/roles*') ? 'btn-primary' : 'btn-outline-primary' }} me-2" type="button">
                         <i class="fa-solid fa-r"></i> Roles
                    </a>
                    <a href="/admin/users" class="btn {{ request()->is('admin/users*') ? 'btn-success' : 'btn-outline-success' }} me-2" type="button">
                         <i class="fa-solid fa-u"></i> Users
                    </a>
                    
                    <!-- Admin Report Tables -->
                    <div class="btn-group me-2" role="group">
                         <button type="button" class="btn {{ request()->is('admin/reports*') ? 'btn-info' : 'btn-outline-info' }} dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                              <i class="fa-solid fa-table"></i> Reports
                         </button>
                         <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="/admin/reports/courses"><i class="fa-solid fa-c"></i> Created Course Table</a></li>
                              <li><a class="dropdown-item" href="/admin/reports/quizzes"><i class="fa-solid fa-q"></i> Created Quiz Table</a></li>
                              <li><a class="dropdown-item" href="/admin/reports/quiz-submissions"><i class="fa-solid fa-clipboard-check"></i> Submitted Quiz Table</a></li>
                              <li><a class="dropdown-item" href="/admin/reports/assignments"><i class="fa-solid fa-a"></i> Created Assignment Table</a></li>
                              <li><a class="dropdown-item" href="/admin/reports/assignment-submissions"><i class="fa-solid fa-file-text"></i> Submitted Assignment Table</a></li>
                         </ul>
                    </div>
               @endif
               
          </form>
     </nav>
</div>




<!-- Include Toastify Notifications -->
<x-toastify-notifications />

<script>
// Function to update notification count
function updateNotificationCount() {
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
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

// Function to update chat unread count
function updateChatUnreadCount() {
    const chatBadge = document.getElementById('admin-chat-badge') || document.getElementById('admin-chat-badge-teacher');
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

// Update count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateNotificationCount();
    updateChatUnreadCount();
});

// Update count every 30 seconds
setInterval(updateNotificationCount, 30000);
setInterval(updateChatUnreadCount, 30000);

// Clear notification storage
function clearNotificationStorage() {
    localStorage.removeItem('shownNotifications');
}

// Handle logout with notification storage cleanup
function handleLogout() {
    clearNotificationStorage();
    window.location.href = '/logout';
}
</script>

