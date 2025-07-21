<x-adminlayout>
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4">Created Quiz Reports</h6>
                    
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.reports.quizzes') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="teacher" class="form-label">Filter by Teacher</label>
                                <select name="teacher" id="teacher" class="form-select">
                                    <option value="">All Teachers</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ request('teacher') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->username }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
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
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('admin.reports.quizzes') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Results Summary -->
                    <div class="alert alert-info">
                        <strong>Total Quizzes:</strong> {{ $quizzes->total() }} quizzes found
                    </div>

                    <!-- Quizzes Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Quiz Title</th>
                                    <th scope="col">Course</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Questions</th>
                                    <th scope="col">Time Limit</th>
                                    <th scope="col">Created Date</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quizzes as $quiz)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration + ($quizzes->currentPage() - 1) * $quizzes->perPage() }}</th>
                                        <td>
                                            <strong>{{ $quiz->title }}</strong>
                                            @if($quiz->description)
                                                <br><small class="text-muted">{{ Str::limit($quiz->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($quiz->course)
                                                <strong>{{ $quiz->course->name }}</strong>
                                                @if($quiz->course->creator)
                                                    <br><small class="text-muted">by {{ $quiz->course->creator->username }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">No Course</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($quiz->course && $quiz->course->creator)
                                                <strong>{{ $quiz->course->creator->username }}</strong>
                                                <br><small class="text-muted">{{ $quiz->course->creator->email }}</small>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $quiz->questions->count() ?? 0 }} questions</span>
                                        </td>
                                        <td>{{ $quiz->time_limit ? $quiz->time_limit . ' minutes' : 'No limit' }}</td>
                                        <td>{{ $quiz->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($quiz->course)
                                                <a href="{{ route('quiz.start', [$quiz->course->id, $quiz->id]) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fa fa-play"></i> Preview
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fa fa-inbox fa-3x mb-3"></i>
                                                <p>No quizzes found matching your criteria.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $quizzes->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-adminlayout>