<x-layout>
    <div class="container mt-5">
        <div class="course-header">
            <div class="course-img">
                <img src="{{ asset($course->image) }}" alt="">
            </div>

           
        </div>

        <h4 class="show-course-ttl">{{ $course->name }}</h4>

        
             @if(auth()->check() && auth()->user()->role->role == 'student')
    <div class="my-3 ms-auto text-end">
        @php
            $isEnrolled = DB::table('course_students')
                ->where('course_id', $course->id)
                ->where('student_id', auth()->id())
                ->exists();
        @endphp

        @if($isEnrolled)
            <span class="badge bg-success">Enrolled</span>
    
   
            
 

        @else
            <button id="enrollBtn" class="btn btn-primary" data-course-id="{{ $course->id }}">
                Enroll in this Course
            </button>
        @endif

        <div id="enrollMsg" class="mt-2"></div>
    </div>
@endif

<div class="mt-3 text-end">
    @if(auth()->check() && auth()->user()->role->role == 'student')
    @php
        $isEnrolled = DB::table('course_students')
            ->where('course_id', $course->id)
            ->where('student_id', auth()->id())
            ->exists();
    @endphp

    @if($isEnrolled)
        <a href="{{ url('/courses/' . $course->id . '/lessons') }}" class="btn btn-success">
            Go to Lessons
        </a>
    @endif
@endif
</div>




        <div id="accordion">
            @foreach ($course->modules as $index => $module)
                <h3>{{ $index + 1 }}. {{ $module->title }}</h3>
                <div>
                    <div class="module-content">
                        {!! nl2br(e($module->content)) !!}
                    </div>
                </div>
            @endforeach
        </div>
        


        <div class="desc-box my-5">
            <h5 class="desc-title">Description</h5>
            <p class="desc-text">
                {!! $course->description !!}
            </p>
        </div>
    </div>
</x-layout>

<script>
    $(function () {
        $("#accordion").accordion({ collapsible: true });

        $('#enrollBtn').on('click', function () {
            const courseId = $(this).data('course-id');

            $.ajax({
                url: `/courses/${courseId}/enroll`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    $('#enrollMsg').html(`<div class="alert alert-success">${res.message}</div>`);
                    $('#enrollBtn').prop('disabled', true).text('Enrolled');
                },
                error: function (xhr) {
                    const error = xhr.responseJSON?.message || 'Enrollment failed.';
                    $('#enrollMsg').html(`<div class="alert alert-danger">${error}</div>`);
                }
            });
        });
    });
</script>
