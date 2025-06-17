
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
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="/team" class="dropdown-item">Our Team</a>
                        <a href="/testimonial" class="dropdown-item">Testimonial</a>
                        
                    </div>
                </div>
                <a href="/contact" class="nav-item nav-link">Contact</a>
                {{-- @auth
                    <div class="nav-item dropdown">
                    <a href="/" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">{{Auth::user()->username}}</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="/profile/{{auth()->user()->username}}" class="dropdown-item">Profile</a>
                        <a href="/admin/users" class="dropdown-item">Dashboard</a>
                        <a href="/logout" class="dropdown-item">Logout</a>
                        
                    </div>
                </div>
                @endauth --}}
            </div>
            <a href="/login" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Join Now<i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </nav>
    <!-- Navbar End -->