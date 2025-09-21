<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Courses</h6>
            <h1 class="mb-5">Popular Courses</h1>
        </div>

        <div class="course-boxes">
            @forelse ($courses as $course)
                <a href="{{ route('courses.show', $course->id) }}" class="course-box">
                    <div class="course-img">
                        @if ($course->image)
                            <img src="{{ asset($course->image) }}" alt="{{ $course->name }}">
                        @else
                            <img src="{{ asset('assets/img/course-1.jpg') }}" alt="{{ $course->name }}">
                        @endif
                    </div>

                    <div class="course-content">
                        <h4 class="course-title">{{ $course->name }}</h4>

                        <div class="course-categories">
                            @if($course->category)
                                <span class="course-category" style="background-color: {{ $course->category->color }}; color: white;">
                                    {{ $course->category->name }}
                                </span>
                            @else
                                <span class="course-category-default">
                                    General
                                </span>
                            @endif
                        </div>

                        

                        <div class="course-meta">
                            @if($course->duration)
                                <div class="course-duration">
                                    <i class="fa fa-clock text-primary"></i> 
                                    <span>{{ $course->duration }} days</span>
                                </div>
                            @endif
                            
                            <div class="course-students">
                                <i class="fa fa-users text-primary"></i> 
                                <span>{{ $course->students->count() }} students</span>
                            </div>
                        </div>

                        <div class="course-footer">
                            <small class="text-muted">
                                <i class="fa fa-calendar"></i> 
                                Created {{ $course->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-12 text-center">
                    <div class="no-courses">
                        <i class="fa fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No courses found</h4>
                        <p class="text-muted">Try adjusting your search criteria or browse all courses.</p>
                        <a href="{{ route('courses') }}" class="btn btn-primary">View All Courses</a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($courses->hasPages())
            <div class="row mt-5">
                <div class="col-12 d-flex justify-content-center">
                    {{ $courses->appends(request()->query())->links() }}
                </div>
            </div>
        @endif

    </div>
</div>
