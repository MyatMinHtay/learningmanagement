<x-adminlayout>
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4">Created Assignment Reports</h6>
                    
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.reports.assignments') }}" class="mb-4">
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
                                <label for="status" class="form-label">Filter by Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <a href="{{ route('admin.reports.assignments') }}" class="btn btn-secondary me-2">Reset</a>
                                <a href="{{ route('admin.reports.assignments.pdf', request()->query()) }}" class="btn btn-success">
                                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Results Summary -->
                    <div class="alert alert-info">
                        <strong>Total Assignments:</strong> {{ $assignments->total() }} assignments found
                    </div>

                    <!-- Assignments Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Assignment Title</th>
                                    <th scope="col">Course</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Submissions</th>
                                    <th scope="col">Created Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration + ($assignments->currentPage() - 1) * $assignments->perPage() }}</th>
                                        <td>
                                            <strong>{{ $assignment->assignment_title ?? 'Untitled Assignment' }}</strong>
                                            @if($assignment->description)
                                                <br><small class="text-muted">{{ Str::limit($assignment->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assignment->course)
                                                <strong>{{ $assignment->course->name }}</strong>
                                                @if($assignment->course->creator)
                                                    <br><small class="text-muted">by {{ $assignment->course->creator->username }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">No Course</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assignment->course && $assignment->course->creator)
                                                <strong>{{ $assignment->course->creator->username }}</strong>
                                                <br><small class="text-muted">{{ $assignment->course->creator->email }}</small>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        
                                       
                                        <td>
                                            @php
                                                $submissionCount = $assignment->submissions ? $assignment->submissions->count() : 0;
                                                $enrolledCount = $assignment->course && $assignment->course->students ? $assignment->course->students->count() : 0;
                                            @endphp
                                            <span class="badge bg-info">{{ $submissionCount }} / {{ $enrolledCount }}</span>
                                            @if($enrolledCount > 0)
                                                <br><small class="text-muted">{{ number_format(($submissionCount / $enrolledCount) * 100, 1) }}% submitted</small>
                                            @endif
                                        </td>
                                        <td>{{ $assignment->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fa fa-inbox fa-3x mb-3"></i>
                                                <p>No assignments found matching your criteria.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $assignments->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-adminlayout>