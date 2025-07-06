<x-adminlayout>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Category Details: {{ $category->name }}</h3>
                    <div>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Categories
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Category Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Name:</strong></div>
                                        <div class="col-sm-8">{{ $category->name }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Color:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge" style="background-color: {{ $category->color }}; color: white;">
                                                {{ $category->color }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Sort Order:</strong></div>
                                        <div class="col-sm-8">{{ $category->sort_order }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Total Courses:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-info">{{ $category->courses->count() }}</span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Created:</strong></div>
                                        <div class="col-sm-8">{{ $category->created_at->format('M d, Y \a\t h:i A') }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Updated:</strong></div>
                                        <div class="col-sm-8">{{ $category->updated_at->format('M d, Y \a\t h:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Preview</h5>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-4">
                                        <span class="badge p-3" style="background-color: {{ $category->color }}; color: white; font-size: 1.2em;">
                                            {{ $category->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($category->courses->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title">Recent Courses in This Category</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Course Name</th>
                                                <th>Duration</th>
                                                <th>Students</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($category->courses as $course)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $course->name }}</strong>
                                                        @if($course->description)
                                                            <br><small class="text-muted">{{ Str::limit($course->description, 80) }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $course->duration ? $course->duration . ' hours' : 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $course->students->count() }}</span>
                                                    </td>
                                                    <td>{{ $course->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                        <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-outline-warning">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card mt-4">
                            <div class="card-body text-center">
                                <i class="fa fa-graduation-cap fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No courses assigned to this category yet.</p>
                                <a href="{{ route('courses.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Create Course
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-adminlayout> 