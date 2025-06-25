<x-adminlayout>
    <div class="container">

        <h1 class="text-center bg-purple mt-3">Courses</h1>

        <div class="col-12 d-flex justify-content-end my-3">
            <a href="{{ route('courses.create') }}" class="btn btn-primary mx-2">
                <i class="fa-solid fa-plus mx-1"></i>Add Course
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered border-1 table-primary">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
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
                        <tr><td colspan="9" class="text-center">No courses available</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $courses->links() }}
            </div>
        </div>

       
        

    </div>
</x-adminlayout>
