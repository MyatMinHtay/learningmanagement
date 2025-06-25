<div class="container-xxl py-5">
    <div class="container">

        <div class="course-boxes">

            @forelse ($courses as $course)
                <a href="{{ url('/courses/' . Str::slug($course->id)) }}" class="course-box">
                    <div class="course-img">
                        @if ($course->image)
                            <img src="{{ asset($course->image) }}" alt="{{ $course->name }}">
                        @else
                            <img src="{{ asset('$course->image') }}" alt="{{ $course->name }}">
                        @endif
                    </div>

                    <h4 class="course-title">{{ $course->name }}</h4>

                    <div class="course-categories">
                        {{-- Assuming you have categories related to courses, otherwise remove --}}
                        {{-- Example static categories: --}}
                        <span class="course-category">HTML</span>
                        <span class="course-category">CSS</span>
                    </div>
                </a>
            @empty
                <p>No courses found.</p>
            @endforelse
        </div>

    </div>
</div>
