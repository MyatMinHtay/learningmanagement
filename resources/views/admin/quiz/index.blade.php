<x-adminlayout>
  <div class="container">
    <h1 class="text-center bg-purple mt-3">Quizzes</h1>

    <div class="col-12 d-flex justify-content-end my-3">
      <a href="{{ route('quizzes.create') }}" class="btn btn-primary mx-2">
        <i class="fa-solid fa-plus mx-1"></i>Add Quiz
      </a>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-bordered border-1 table-success">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Course</th>
            <th>Course Creator</th>
            <th>Quiz Creator</th>
            <th>Questions</th>
            <th>Marks</th>
            <th>Time Limit</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($quizzes as $quiz)
            <tr>
              <td>{{ $quiz->id }}</td>
              <td>{{ $quiz->title ?? '—' }}</td>
              <td>{{ $quiz->course->name ?? '—' }}</td>
              <td>{{ $quiz->course->creator ? $quiz->course->creator->username : 'N/A' }}</td>
              <td>{{ $quiz->creator ? $quiz->creator->username : 'N/A' }}</td>
              <td>{{ $quiz->total_questions }}</td>
              <td>{{ $quiz->total_marks }}</td>
              <td>
                @if ($quiz->is_time_limited)
                  {{ $quiz->total_time }} min
                @else
                  No Limit
                @endif
              </td>
              <td>
                <a href="{{ route('quizzes.edit', $quiz->id) }}" class="btn btn-info">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
              </td>
              <td>
                <form action="{{ route('quizzes.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Delete this quiz?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="10" class="text-center">No quizzes found</td></tr>
          @endforelse
        </tbody>
      </table>

      <div class="mt-3">
        {{ $quizzes->links() }}
      </div>
    </div>
  </div>
</x-adminlayout>
