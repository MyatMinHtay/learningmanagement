<x-adminlayout>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">Course Details</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.reports.courses') }}">Course Reports</a></li>
                                <li class="breadcrumb-item active">{{ $course->name }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('courses.details.pdf', $course->id) }}" class="btn btn-danger me-2">
                            <i class="fa fa-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('admin.reports.courses') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Information Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-book"></i> Course Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Course Name:</strong></div>
                                    <div class="col-sm-9">{{ $course->name }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Category:</strong></div>
                                    <div class="col-sm-9">
                                        @if($course->category)
                                            <span class="badge" style="background-color: {{ $course->category->color }}">
                                                {{ $course->category->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">No category</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Created By:</strong></div>
                                    <div class="col-sm-9">
                                        @if($course->creator)
                                            {{ $course->creator->username }}
                                            <small class="text-muted">({{ $course->creator->email }})</small>
                                        @else
                                            <span class="text-muted">Unknown</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Duration:</strong></div>
                                    <div class="col-sm-9">{{ $course->duration ? $course->duration : 'Not specified' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Created Date:</strong></div>
                                    <div class="col-sm-9">{{ $course->created_at->format('M d, Y H:i') }}</div>
                                </div>
                                @if($course->description)
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Description:</strong></div>
                                    <div class="col-sm-9"> {!! $course->description !!} </div>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                @if($course->image)
                                    <img src="{{ asset($course->image) }}" alt="{{ $course->name }}" class="img-fluid rounded">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fa fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['total_students'] }}</h4>
                                <p class="mb-0">Enrolled Students</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-users fa-2x"></i>
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
                                <h4 class="mb-0">{{ $stats['total_quizzes'] }}</h4>
                                <p class="mb-0">Quizzes</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-question-circle fa-2x"></i>
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
                                <h4 class="mb-0">{{ $stats['total_assignments'] }}</h4>
                                <p class="mb-0">Assignments</p>
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
                                <h4 class="mb-0">{{ $stats['total_modules'] }}</h4>
                                <p class="mb-0">Modules</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-book fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrolled Students Table -->
        <div class="row py-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-users"></i> Enrolled Students ({{ $stats['total_students'] }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($course->students->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Student Name</th>
                                            <th>Email</th>
                                            <th>Position</th>
                                            <th>Enrollment Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($course->students as $index => $student)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($student->userphoto)
                                                            <img src="{{ asset($student->userphoto) }}" alt="{{ $student->username }}" class="rounded-circle me-2" width="32" height="32">
                                                        @else
                                                            <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                                <i class="fa fa-user text-white"></i>
                                                            </div>
                                                        @endif
                                                        <strong>{{ $student->username }}</strong>
                                                    </div>
                                                </td>
                                                <td>{{ $student->email }}</td>
                                                <td>{{ $student->position ?? 'Not specified' }}</td>
                                                <td>{{ $student->pivot->created_at ? $student->pivot->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('students.details', $student->id) }}" class="btn btn-sm btn-outline-primary" title="View Student Details">
                                                        <i class="fa fa-eye"></i> Details
                                                    </a>
                                                    <a href="{{ route('profile.show', $student->username) }}" class="btn btn-sm btn-outline-info" title="View Profile">
                                                        <i class="fa fa-user"></i> Profile
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fa fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Students Enrolled</h5>
                                <p class="text-muted">This course doesn't have any enrolled students yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Quizzes Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-question-circle"></i> Course Quizzes ({{ $stats['total_quizzes'] }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($course->coursequizzes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Quiz Title</th>
                                            <th>Questions</th>
                                            <th>Duration</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($course->coursequizzes as $index => $quiz)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $quiz->title }}</strong></td>
                                                <td>{{ $quiz->questions->count() ?? 0 }} questions</td>
                                                <td>{{ $quiz->is_time_limited ? $quiz->total_time . ' minutes' : 'No limit' }}</td>
                                                
                                                <td>{{ $quiz->created_at ? $quiz->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fa fa-question-circle fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Quizzes Available</h5>
                                <p class="text-muted">This course doesn't have any quizzes yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Assignments Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-tasks"></i> Course Assignments ({{ $stats['total_assignments'] }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($course->assignments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Assignment Title</th>
                                            <th>Student</th>
                                            <th>Status</th>
                                            <th>Mark</th>
                                            <th>Submitted Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($course->assignments as $index => $assignment)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $assignment->assignment_title ?? 'Unknown' }}</strong></td>
                                                <td>
                                                    @if($assignment->student)
                                                        {{ $assignment->student->username }}
                                                    @else
                                                        <span class="text-muted">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($assignment->status == 'accepted')
                                                        <span class="badge bg-success">Accepted</span>
                                                    @elseif($assignment->status == 'rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>{{ $assignment->mark ?? 'Not graded' }}</td>
                                                <td>{{ $assignment->created_at ? $assignment->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fa fa-tasks fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Assignments Available</h5>
                                <p class="text-muted">This course doesn't have any assignments yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Modules Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-book"></i> Course Modules ({{ $stats['total_modules'] }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($course->modules->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Module Title</th>
                                            <th>Content</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($course->modules->sortBy('order') as $index => $module)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $module->title }}</strong></td>
                                                <td>{{ Str::limit($module->content ?? 'No content', 50) }}</td>
                                                <td>{{ $module->created_at ? $module->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fa fa-book fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Modules Available</h5>
                                <p class="text-muted">This course doesn't have any modules yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-adminlayout>