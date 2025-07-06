<x-adminlayout>
    <div class="container">

        <h1 class="text-center bg-purple mt-3">Courses</h1>

        <div class="row my-3">
            <div class="col-md-6">
                <form method="GET" action="{{ route('admincourses') }}" class="d-flex">
                    <select name="category" class="form-control me-2">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-primary">Filter</button>
                </form>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <a href="{{ route('categories.index') }}" class="btn btn-info mx-2">
                    <i class="fa-solid fa-tags mx-1"></i>Manage Categories
                </a>
                <a href="{{ route('courses.create') }}" class="btn btn-primary mx-2">
                    <i class="fa-solid fa-plus mx-1"></i>Add Course
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered border-1 table-primary">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Duration (days)</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        <tr>
                            <td>{{ $course->id }}</td>
                            <td>{{ $course->name }}</td>
                            <td>
                                @if ($course->category)
                                    <span class="badge" style="background-color: {{ $course->category->color }}; color: white;">
                                        {{ $course->category->name }}
                                    </span>
                                @else
                                    <span class="text-muted">No category</span>
                                @endif
                            </td>
                            <td>
                                @if ($course->image)
                                    <img src="{{ asset($course->image) }}" width="60">
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $course->duration ?? '—' }}</td>
                            <td>
                                <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-info">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </td>
                            <td>
                                <form action="{{ route('courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Delete this course?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No courses available</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $courses->links() }}
            </div>
        </div>

       
        

    </div>
</x-adminlayout>
