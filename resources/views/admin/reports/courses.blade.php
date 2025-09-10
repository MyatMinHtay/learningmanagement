<x-adminlayout>
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4">Created Course Reports</h6>
                    
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.reports.courses') }}" class="mb-4">
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
                                <label for="category" class="form-label">Filter by Category</label>
                                <select name="category" id="category" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
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
                                <a href="{{ route('admin.reports.courses') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Results Summary -->
                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Total Courses:</strong> {{ $courses->total() }} courses found
                        </div>
                        <div>
                            <a href="{{ route('admin.reports.courses.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                                <i class="fa fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>

                    <!-- Courses Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Course Name</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Students Enrolled</th>
                                    <th scope="col">Duration</th>
                                    <th scope="col">Created Date</th>
                                    <th scope="col">Actions</th>
                                    <th scope="col">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courses as $course)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration + ($courses->currentPage() - 1) * $courses->perPage() }}</th>
                                        <td>
                                            <strong>{{ $course->name }}</strong>
                                           @if($course->description)
                                                <br>
                                                <small class="text-muted">{{ Str::limit(strip_tags($course->description), 50) }}</small>
                                            @endif

                                        </td>
                                        <td>
                                            @if($course->category)
                                                <span class="badge bg-info">{{ $course->category->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">No Category</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($course->creator)
                                                <strong>{{ $course->creator->username }}</strong>
                                                <br><small class="text-muted">{{ $course->creator->email }}</small>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $course->students->count() }} students</span>
                                        </td>
                                        <td>{{ $course->duration ?? 'Not specified' }}</td>
                                        <td>{{ $course->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('courses.details', $course->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-eye"></i> Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fa fa-inbox fa-3x mb-3"></i>
                                                <p>No courses found matching your criteria.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $courses->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-adminlayout>