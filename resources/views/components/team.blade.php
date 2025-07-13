<!-- Team Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Instructors</h6>
            <h1 class="mb-5">Expert Instructors</h1>
        </div>
        <div class="row g-4">

            @foreach ($teachers as $teacher)
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="team-item bg-white shadow-sm rounded-3 overflow-hidden h-100 position-relative">
                    <div class="overflow-hidden position-relative">
                        <img class="img-fluid w-100" style="height: 250px; object-fit: cover;" 
                             src="{{$teacher->userphoto ? asset($teacher->userphoto) : asset('/assets/avatars/user.png')}}" 
                             alt="{{$teacher->username}}">
                        <div class="team-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                            <div class="team-social d-flex">
                                <!-- You can add social media links here if available -->
                                <a class="btn btn-primary btn-square mx-1" href="#" style="display: none;">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a class="btn btn-primary btn-square mx-1" href="#" style="display: none;">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a class="btn btn-primary btn-square mx-1" href="#" style="display: none;">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                   
                    <div class="text-center p-4">
                        <h5 class="mb-2 text-dark">{{$teacher->username}}</h5>
                        <p class="text-muted mb-0">
                            <i class="fas fa-user-tie me-2"></i>{{$teacher->role->role}}
                        </p>
                        @if($teacher->email)
                            <p class="text-muted mb-0 mt-2">
                                <i class="fas fa-envelope me-2"></i>
                                <small>{{$teacher->email}}</small>
                            </p>
                        @endif
                    </div>
                </div>
            </div>  
            @endforeach

        </div>
    </div>
</div>

<style>
.team-item {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.team-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.team-overlay {
    background: rgba(0, 0, 0, 0.7);
    opacity: 0;
    transition: all 0.3s ease;
}

.team-item:hover .team-overlay {
    opacity: 1;
}

.team-social a {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.team-social a:hover {
    transform: scale(1.1);
}

.team-item img {
    transition: all 0.3s ease;
}

.team-item:hover img {
    transform: scale(1.05);
}

.btn-square {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.team-item h5 {
    font-weight: 600;
    font-size: 1.1rem;
}

.team-item p {
    font-size: 0.9rem;
}
</style>
<!-- Team End -->