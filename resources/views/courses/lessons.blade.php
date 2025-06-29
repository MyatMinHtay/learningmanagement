<x-layout>
    <div class="container mt-5">
        <h4 class="mb-4">{{ $course->name }} - Lessons</h4>

        <div class="row">
            <!-- Video Player and Content -->
            <div class="col-md-8" id="lessonPlayer">
                @php $first = $lessons->first(); @endphp

                @if($first)
                    <h5 id="lessonTitle">{{ $first->title }}</h5>
                    <div id="lessonVideo" class="mb-3">
                        @include('components.lesson-video', ['lesson' => $first])
                    </div>
                    <div id="lessonContent" class="mb-4">
                        {!! $first->description !!}
                    </div>
                    <div id="lessonAttachment">
                        @if($first->attachment)
                            <a href="{{ asset($first->attachment) }}" class="btn btn-outline-primary" download>
                                Download Attachment
                            </a>
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning">
                        No lessons available for this course.
                    </div>
                @endif


                <div id="courseQuiz" class="mt-3">
                    @if($course->quizzes)
                        <a href="{{ route('quiz.start', ['quiz' => $course->quizzes->id, 'course' => $course->id]) }}" class="btn btn-success">
                            Start Course Quiz
                        </a>
                    @endif
                </div>

            </div>

            <!-- Playlist Sidebar -->
            <div class="col-md-4">
                <h6 class="mb-3">Lesson Playlist</h6>
                <ul class="list-group" id="lessonList">
                    @foreach($lessons as $lesson)
                        <li class="list-group-item lesson-item cursor-pointer" data-lesson='@json($lesson)'>
                            {{ $loop->index + 1 }}. {{ $lesson->title }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-layout>

<script>
    function renderVideo(lesson) {
        let videoHtml = '';
        if (lesson.video.includes('youtube')) {
            const match = lesson.video.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|embed\/)([^\&\?\/]+)/);
            if (match && match[1]) {
                videoHtml = `<iframe width="100%" height="400" src="https://www.youtube.com/embed/${match[1]}" frameborder="0" allowfullscreen></iframe>`;
            } else {
                videoHtml = "Invalid YouTube link.";
            }
        } else if (lesson.video.includes('vimeo')) {
            videoHtml = `<iframe src="${lesson.video}" width="100%" height="400" frameborder="0" allowfullscreen></iframe>`;
        } else if (lesson.video) {
            videoHtml = `<video width="100%" height="400" controls><source src="/storage/${lesson.video}" type="video/mp4">Your browser does not support the video tag.</video>`;
        } else {
            videoHtml = 'N/A';
        }
        return videoHtml;
    }

    $(document).ready(function() {
        $('.lesson-item').on('click', function() {
            const lesson = $(this).data('lesson');
            $('#lessonTitle').text(lesson.title);
            $('#lessonVideo').html(renderVideo(lesson));
            $('#lessonContent').html(lesson.description);
            if (lesson.attachment) {
                $('#lessonAttachment').html(`<a href="${lesson.attachment}" class="btn btn-outline-primary" download>Download Attachment</a>`);
            } else {
                $('#lessonAttachment').html('');
            }
        });
    });
</script>
