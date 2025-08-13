<x-adminlayout>
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4">Assignment Submission Reports</h6>
                    
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.reports.assignment-submissions') }}" class="mb-4">
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
                                <label for="status" class="form-label">Filter by Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Graded</option>
                                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late Submission</option>
                                </select>
                            </div>
                            <div class="col-md-2">
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
                                <a href="{{ route('admin.reports.assignment-submissions') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Results Summary -->
                    <div class="alert alert-info">
                        <strong>Total Submissions:</strong> {{ $submissions->total() }} submissions found
                    </div>

                    <!-- Submissions Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Student</th>
                                    <th scope="col">Assignment</th>
                                    <th scope="col">Course</th>
                                    <th scope="col">Teacher</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Mark</th>
                                    <th scope="col">Submitted Date</th>
                                    <th scope="col">Actions</th>
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
                                            @if($submission->assignment_title)
                                                <strong>{{ $submission->assignment_title }}</strong>
                                            @else
                                                <span class="text-muted">Untitled Assignment</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->course)
                                                <strong>{{ $submission->course->name }}</strong>
                                            @else
                                                <span class="text-muted">No Course</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->course && $submission->course->creator)
                                                <strong>{{ $submission->course->creator->username }}</strong>
                                                <br><small class="text-muted">{{ $submission->course->creator->email }}</small>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->mark !== null)
                                                <span class="badge bg-success">Graded</span>
                                            @elseif($submission->files && $submission->files != '[]')
                                                <span class="badge bg-info">Submitted</span>
                                            @else
                                                <span class="badge bg-secondary">Not Submitted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->mark !== null)
                                                @php
                                                    $gradeClass = 'bg-success';
                                                    
                                                @endphp
                                                <span class="badge {{ $gradeClass }}">{{ $submission->mark }}</span>
                                            @else
                                                <span class="badge bg-secondary">Not Graded</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->updated_at)
                                                {{ \Carbon\Carbon::parse($submission->updated_at)->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-muted">Not submitted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->files && $submission->files != '[]')
                                                @php
                                                    $files = json_decode($submission->files, true);
                                                @endphp
                                                @if(is_array($files) && count($files) > 0)
                                                    @foreach($files as $index => $file)
                                                        <a href="{{ asset('storage/' . $file) }}" class="btn btn-sm btn-outline-primary me-1 mb-1" target="_blank">
                                                            <i class="fa fa-download"></i> File {{ $index + 1 }}
                                                        </a>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No files</span>
                                                @endif
                                            @else
                                                <span class="text-muted">No files</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fa fa-inbox fa-3x mb-3"></i>
                                                <p>No assignment submissions found matching your criteria.</p>
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