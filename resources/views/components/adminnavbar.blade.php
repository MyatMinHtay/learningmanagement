<div class="container-fluid">
     <nav class="navbar bg-body-tertiary sticky-top customnavbar mt-2">
          <div class="container-fluid customnavbarchild">

               <div class="navbar-toggler" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                    aria-controls="offcanvasNavbar">
                    <i class="fa-solid fa-bars"></i>
               </div>


               <a class="navbar-brand fontcolor" href="/">Learning Management</a>

               <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar"
                    aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                         <div class="userimgcontainer" style="background: url('{{ asset(auth()->user()->userphoto) }}'); background-position: center;
                         background-repeat: no-repeat;
                         background-size: cover;">
                              {{-- user photo --}}
                         </div>
                         <h5 class="offcanvas-title" id="offcanvasNavbarLabel">{{ auth()->user()->username }}
                              ({{ auth()->user()->role->role }}) </h5>

                         <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                              aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">


                         <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">

                              @if (auth()->user()->role->role == 'student')
                                   <li class="nav-item">
                                        <a class="nav-link" href="{{ route('student.courses', auth()->user()->id) }}">

                                             <i class="fa-solid fa-c fs-5 icon"></i>
                                             Courses</a>
                                   </li>

                                   <li class="nav-item">
                                        <a class="nav-link" href="{{ route('assignments.index') }}">

                                             <i class="fa-solid fa-a fs-5 icon"></i>
                                             Assignments</a>
                                   </li>

                                   <li class="nav-item">
                                        <a class="nav-link" href="{{ route('student.quizzes', auth()->user()->id) }}">

                                             <i class="fa-solid fa-q fs-5 icon"></i>
                                             Quizzes</a>
                                   </li>

                                   <li class="nav-item">
                                        <a class="nav-link position-relative" href="{{ route('notifications.index') }}">
                                             <i class="fas fa-bell fs-5 icon"></i>
                                             Notifications
                                             <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                                                  0
                                             </span>
                                        </a>
                                   </li>
                              @endif

                              @if (auth()->user()->role->role == 'teacher')
                                   <li class="nav-item">
                                        <a class="nav-link" href="/admin/courses">

                                             <i class="fa-solid fa-c fs-5 icon"></i>
                                             Courses</a>
                                   </li>

                                   <li class="nav-item">
                                        <a class="nav-link" href="{{ route('assignments.index') }}">

                                             <i class="fa-solid fa-a fs-5 icon"></i>
                                             Assignments</a>
                                   </li>

                                   <li class="nav-item">
                                        <a class="nav-link" href="{{ route('lessons.index') }}">

                                             <i class="fa-solid fa-l fs-5 icon"></i>
                                             Lessons</a>
                                   </li>

                                   <li class="nav-item">
                                        <a class="nav-link" href="{{ route('quizzes.index') }}">

                                             <i class="fa-solid fa-q fs-5 icon"></i>
                                             Quizs</a>
                                   </li>

                                   <li class="nav-item">
                                        <a class="nav-link position-relative" href="{{ route('notifications.index') }}">
                                             <i class="fas fa-bell fs-5 icon"></i>
                                             Notifications
                                             <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                                                  0
                                             </span>
                                        </a>
                                   </li>
                              @endif

                              @if (auth()->user()->role->role == 'adminstrator')
                                   <li class="nav-item">
                                        <a class="nav-link" href="/admin/roles">
                                             <i class="fa-solid fa-r fs-5 icon"></i>
                                             Roles</a>
                                   </li>
                                   <li class="nav-item">
                                        <a class="nav-link" href="/admin/users">
                                             <i class="fa-solid fa-u fs-5 icon"></i>
                                             Users</a>
                                   </li>
                              @endif

                              

                              <li class="nav-item">
                                   <a class="nav-link" href="/logout">
                                        <i class="fa-solid fa-circle-chevron-left fs-5 icon"></i>
                                        Logout</a>
                              </li>






                         </ul>

                    </div>
               </div>
          </div>
     </nav>
</div>




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

// Update count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateNotificationCount();
});

// Update count every 30 seconds
setInterval(updateNotificationCount, 30000);
</script>

