<x-adminlayout>
    <div class="container">

        <h1 class="text-center bg-purple mt-3">Courses</h1>


        <div class="table-responsive">
            <table class="table table-hover table-bordered border-1 table-primary">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Duration (days)</th>
                        <th>Lesson</th>
                        <th>Assignment</th>
                        <th>Quiz</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($studentCourses as $course)
                        <tr>
                            <td>{{ $course->id }}</td>
                            <td>{{ $course->name }}</td>
                            <td>
                                @if ($course->image)
                                    <img src="{{ asset($course->image) }}" width="60">
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $course->duration ?? '—' }}</td>
                            <td class="text-center">
                                <a href="{{ route('showlesson', $course->id) }}" class="btn btn-info">
                                    <i class="fa-solid fa-l fs-5 icon"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('assignments.create', [$course->id, auth()->user()->id]) }}" class="btn btn-info">
                                    <i class="fa-solid fa-a fs-5 icon"></i>
                                </a>
                                
                            </td>
                            <td class="text-center">
                                @if($course->quizzes)
                                    <a href="{{ route('quiz.start', [$course->id,$course->quizzes->id]) }}" class="btn btn-success">
                                        <i class="fa-solid fa-q fs-5 icon"></i>
                                    </a>
                                @else
                                    <a href="#" class="btn btn-danger" onclick="alert('No quiz available')">
                                        <i class="fa-solid fa-q fs-5 icon"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">No courses available</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $studentCourses->links() }}
            </div>
        </div>

       
        

    </div>
</x-adminlayout>
