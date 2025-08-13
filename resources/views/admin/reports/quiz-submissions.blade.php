<x-adminlayout>
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4">Quiz Submission Reports</h6>
                    
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.reports.quiz-submissions') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="student" class="form-label">Filter by Student</label>
                                <select name="student" id="student" class="form-select">
                                    <option value="">All Students</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ request('student') == $student->id ? 'selected' : '' }}>
                                            {{ $student->username }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="quiz" class="form-label">Filter by Quiz</label>
                                <select name="quiz" id="quiz" class="form-select">
                                    <option value="">All Quizzes</option>
                                    @foreach($quizzes as $quiz)
                                        <option value="{{ $quiz->id }}" {{ request('quiz') == $quiz->id ? 'selected' : '' }}>
                                            {{ $quiz->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="course" class="form-label">Filter by Course</label>
                                <select name="course" id="course" class="form-select">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                            {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label for="grade_min" class="form-label">Min Grade</label>
                                <input type="number" name="grade_min" id="grade_min" class="form-control" min="0" max="100" value="{{ request('grade_min') }}" placeholder="0">
                            </div>
                            <div class="col-md-1">
                                <label for="grade_max" class="form-label">Max Grade</label>
                                <input type="number" name="grade_max" id="grade_max" class="form-control" min="0" max="100" value="{{ request('grade_max') }}" placeholder="100">
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('admin.reports.quiz-submissions') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Results Summary -->
                    <div class="alert alert-info">
                        <strong>Total Submissions:</strong> {{ $submissions->total() }} submissions found
                        @if($submissions->count() > 0 && $averagePercentage > 0)
                            | <strong>Average Score:</strong> {{ number_format($averagePercentage, 2) }}%
                        @endif
                    </div>

                    <!-- Submissions Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Student</th>
                                    <th scope="col">Quiz</th>
                                    <th scope="col">Course</th>
                                    <th scope="col">Grade</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Submitted Date</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($submissions as $submission)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration + ($submissions->currentPage() - 1) * $submissions->perPage() }}</th>
                                        <td>
                                            @if($submission->user)
                                                <strong>{{ $submission->user->username }}</strong>
                                                <br><small class="text-muted">{{ $submission->user->email }}</small>
                                            @else
                                                <span class="text-muted">Unknown Student</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->quiz)
                                                <strong>{{ $submission->quiz->title }}</strong>
                                                @if($submission->quiz->description)
                                                    <br><small class="text-muted">{{ Str::limit($submission->quiz->description, 30) }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Quiz Deleted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->quiz && $submission->quiz->course)
                                                <strong>{{ $submission->quiz->course->name }}</strong>
                                                @if($submission->quiz->course->creator)
                                                    <br><small class="text-muted">by {{ $submission->quiz->course->creator->username }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">No Course</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->is_completed && $submission->quiz)
                                                @php
                                                    $totalMarks = $submission->quiz->calculateTotalMarks();
                                                    $percentage = $totalMarks > 0 ? ($submission->score / $totalMarks) * 100 : 0;
                                                    $gradeClass = 'bg-success';
                                                    if($percentage < 60) $gradeClass = 'bg-danger';
                                                    elseif($percentage < 80) $gradeClass = 'bg-warning';
                                                @endphp
                                                <span class="badge {{ $gradeClass }}">{{ $submission->score }}</span>
                                                
                                            @else
                                                <span class="badge bg-secondary">Not Completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->created_at)
                                                <span class="badge bg-success">Completed</span>
                                            @else
                                                <span class="badge bg-warning">In Progress</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->created_at)
                                                {{ $submission->created_at->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-muted">Not completed</span>
                                            @endif
                                        </td>
                                       
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fa fa-inbox fa-3x mb-3"></i>
                                                <p>No quiz submissions found matching your criteria.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $submissions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-adminlayout>