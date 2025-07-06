<x-adminlayout>
    <div class="container">

        <h1 class="text-center bg-purple mt-3">Course Categories</h1>

        <div class="col-12 d-flex justify-content-end my-3">
            <a href="{{ route('categories.create') }}" class="btn btn-primary mx-2">
                <i class="fa-solid fa-plus mx-1"></i>Add Category
            </a>
        </div>

        

        <div class="table-responsive">
            <table class="table table-hover table-bordered border-1 table-primary">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Color</th>
                        <th>Courses</th>
                        <th>Sort Order</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $category->color }}; color: white;">
                                    {{ $category->color }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $category->courses_count }}</span>
                            </td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-info">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </td>
                            <td>
                                @if($category->courses_count == 0)
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-danger" disabled title="Cannot delete category with courses">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No categories available</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        </div>

    </div>
</x-adminlayout> 