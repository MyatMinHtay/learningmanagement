<x-adminlayout>
    <div class="container">
        <h1 class="text-center bg-purple my-5">Quizzes </h1>
    
        <div class="table-responsive">
            <table class="table table-hover table-bordered border-1 table-primary">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Quiz Title</th>
                        <th>Course Name</th>
                        <th>Total Marks</th>
                        <th>Total Questions</th>
                        <th>Time Limit</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quizzes as $quiz)
                        @php
                            $attempt = $quiz->attempts->where('student_id', auth()->id())->sortByDesc('ended_at')->first();
                           
                        @endphp
                        <tr>
                            <td>{{ $quiz->id }}</td>
                            <td>{{ $quiz->title ?? 'Untitled' }}</td>
                            <td>{{ $quiz->course->name }}</td>
                            <td>{{ $quiz->total_marks }}</td>
                            <td>{{ $quiz->total_questions }}</td>
                            <td>
                                @if($quiz->is_time_limited)
                                    {{ $quiz->total_time }} mins
                                @else
                                    Unlimited
                                @endif
                            </td>
                            <td>
                                @if($attempt)
                                    @if($attempt->is_completed)
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning">In Progress</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Not Started</span>
                                @endif
                            </td>
                            <td>
                                @if($attempt && $attempt->is_completed)
                                    {{ $attempt->score }}/{{ $quiz->total_marks }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!$attempt || !$attempt->is_completed)
                                    <a href="{{ route('quiz.start', [$quiz->course->id, $quiz->id]) }}" class="btn btn-success">
                                        <i class="fa-solid fa-play fs-5 icon"></i> Start
                                    </a>
                                @else
                                    <a href="{{ route('student.quiz.result', $quiz->id) }}" class="btn btn-primary">
                                        <i class="fa-solid fa-eye fs-5 icon"></i> View Result
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No quizzes available for this course.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $quizzes->links() }}
            </div>
        </div>
    </div>
    </x-adminlayout>
    