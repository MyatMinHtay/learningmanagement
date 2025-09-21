<x-adminlayout>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">Student Details</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                                <li class="breadcrumb-item active">{{ $student->username }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('profile.show', $student->username) }}" class="btn btn-info me-2">
                            <i class="fa fa-user"></i> View Profile
                        </a>
                        <a href="{{ route('admin.reports.courses') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Courses
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Information Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-user"></i> Student Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Username:</strong></div>
                                    <div class="col-sm-9">{{ $student->username }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Email:</strong></div>
                                    <div class="col-sm-9">{{ $student->email }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Position:</strong></div>
                                    <div class="col-sm-9">{{ $student->position ?? 'Not specified' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Role:</strong></div>
                                    <div class="col-sm-9">
                                        <span class="badge bg-primary">{{ ucfirst($student->role->role) }}</span>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Joined Date:</strong></div>
                                    <div class="col-sm-9">{{ $student->created_at ? $student->created_at->format('M d, Y H:i') : 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center studentdetailsphoto">
                                @if($student->userphoto)
                                    <img src="{{ asset($student->userphoto) }}" alt="{{ $student->username }}" class="img-fluid rounded-circle" style="max-width: 150px; max-height: 150px;">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                        <i class="fa fa-user fa-4x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['total_courses'] }}</h4>
                                <p class="mb-0">Enrolled Courses</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-book fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['completed_quizzes'] }}</h4>
                                <p class="mb-0">Completed Quizzes</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['submitted_assignments'] }}</h4>
                                <p class="mb-0">Submitted Assignments</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-tasks fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['average_quiz_score'] }}%</h4>
                                <p class="mb-0">Average Quiz Score</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrolled Courses -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-book"></i> Enrolled Courses ({{ $stats['total_courses'] }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($student->courses->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Course Name</th>
                                            <th>Category</th>
                                            <th>Instructor</th>
                                            <th>Enrollment Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student->courses as $index => $course)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $course->name }}</strong>
                                                    @if($course->duration)
                                                        <br><small class="text-muted">{{ $course->duration }} hours</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($course->category)
                                                        <span class="badge" style="background-color: {{ $course->category->color }}">
                                                            {{ $course->category->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">No category</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($course->creator)
                                                        {{ $course->creator->username }}
                                                    @else
                                                        <span class="text-muted">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>{{ $course->pivot->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('courses.details', $course->id) }}" class="btn btn-sm btn-outline-primary" title="View Course Details">
                                                        <i class="fa fa-eye"></i> Details
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fa fa-book fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Courses Enrolled</h5>
                                <p class="text-muted">This student is not enrolled in any courses yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Quiz Attempts -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-question-circle"></i> Recent Quiz Attempts
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($student->quizAttempts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Quiz Title</th>
                                            <th>Course</th>
                                            <th>Score</th>
                                            <th>Status</th>
                                            <th>Attempt Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student->quizAttempts->take(5) as $index => $attempt)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $attempt->quiz->title }}</td>
                                                <td>{{ $attempt->quiz->course->name }}</td>
                                                <td>
                                                    @if($attempt->is_completed)
                                                        <span class="badge bg-info">{{ $attempt->score }}/{{ $attempt->quiz->total_marks }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($attempt->is_completed)
                                                        <span class="badge bg-success">Completed</span>
                                                    @else
                                                        <span class="badge bg-warning">In Progress</span>
                                                    @endif
                                                </td>
                                                <td>{{ $attempt->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fa fa-question-circle fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Quiz Attempts</h5>
                                <p class="text-muted">This student hasn't attempted any quizzes yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Assignments -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-tasks"></i> Recent Assignments
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($student->assignments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Assignment Title</th>
                                            <th>Course</th>
                                            <th>Status</th>
                                            <th>Mark</th>
                                            <th>Submission Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student->assignments->take(5) as $index => $assignment)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $assignment->assignment_title }}</td>
                                                <td>{{ $assignment->course->name }}</td>
                                                <td>
                                                    @if($assignment->status === 'submitted')
                                                        <span class="badge bg-success">Submitted</span>
                                                    @elseif($assignment->status === 'graded')
                                                        <span class="badge bg-info">Graded</span>
                                                    @else
                                                        <span class="badge bg-warning">{{ ucfirst($assignment->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($assignment->mark !== null)
                                                        <span class="badge bg-primary">{{ $assignment->mark }}</span>
                                                    @else
                                                        <span class="text-muted">Not graded</span>
                                                    @endif
                                                </td>
                                                <td>{{ $assignment->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fa fa-tasks fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Assignments</h5>
                                <p class="text-muted">This student hasn't submitted any assignments yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-adminlayout>