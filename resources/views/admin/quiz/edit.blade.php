<x-adminlayout>
  <div class="container">
    <h2 class="text-center my-4">Edit Quiz</h2>

    @if ($errors->has('error'))
      <div class="alert alert-danger">{{ $errors->first('error') }}</div>
    @endif

    <form action="{{ route('quizzes.update', $quiz->id) }}" method="POST">
      @csrf
      @method('PUT')

      <!-- Quiz metadata fields -->
      <div class="mb-3">
        <label class="form-label">Quiz Title</label>
        <input type="text" name="title" class="form-control" required value="{{ old('title', $quiz->title) }}">
        <x-error name="title" />
      </div>

      <div class="mb-3">
        <label class="form-label">Course</label>
        <select name="course_id" class="form-select" required>
          <option value="">-- Select Course --</option>
          @foreach ($courses as $course)
            <option value="{{ $course->id }}" {{ old('course_id', $quiz->course_id) == $course->id ? 'selected' : '' }}>
              {{ $course->name }}
            </option>
          @endforeach
        </select>
        <x-error name="course_id" />
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="2">{{ old('description', $quiz->description) }}</textarea>
        <x-error name="description" />
      </div>

      <div class="mb-3">
        <label class="form-label">Total Questions</label>
        <input type="number" name="total_questions" class="form-control" min="1" required value="{{ old('total_questions', $quiz->total_questions) }}">
        <x-error name="total_questions" />
      </div>

      <div class="mb-3">
        <label class="form-label">Total Marks</label>
        <input type="number" name="total_marks" class="form-control" min="1" required value="{{ old('total_marks', $quiz->total_marks) }}">
        <x-error name="total_marks" />
      </div>

      <div class="mb-3">
        <label class="form-label">Time Limit</label>
        <select name="is_time_limited" class="form-select" id="time-limit-select">
          <option value="0" {{ !$quiz->is_time_limited ? 'selected' : '' }}>No</option>
          <option value="1" {{ $quiz->is_time_limited ? 'selected' : '' }}>Yes</option>
        </select>
        <x-error name="is_time_limited" />
      </div>

      <div class="mb-3" id="time-input" style="{{ $quiz->is_time_limited ? '' : 'display: none;' }}">
        <label class="form-label">Time (in minutes)</label>
        <input type="number" name="total_time" class="form-control" min="1" value="{{ old('total_time', $quiz->total_time) }}">
        <x-error name="total_time" />
      </div>

      <!-- Questions and Choices -->
      <h4 class="mt-5">Questions</h4>
      <div id="questions-container">
        @foreach ($quiz->questions as $qIndex => $question)
          <div class="card mb-4 question-box">
            <div class="card-body">
              <h5 class="card-title">Question {{ $qIndex + 1 }}</h5>

              <input type="hidden" name="questions[{{ $qIndex }}][id]" value="{{ $question->id }}">

              <div class="mb-2">
                <input type="text" name="questions[{{ $qIndex }}][text]" class="form-control"
                  value="{{ old("questions.$qIndex.text", $question->text) }}" required>
              </div>

              <div class="mb-2">
                <input type="number" name="questions[{{ $qIndex }}][marks]" class="form-control" min="1"
                  value="{{ old("questions.$qIndex.marks", $question->marks) }}" required>
              </div>

              <input type="hidden" name="questions[{{ $qIndex }}][order]" value="{{ $question->order }}">

              <div class="choices-container mb-2">
                @foreach ($question->choices as $cIndex => $choice)
                  <div class="input-group mb-2">
                    <input type="text" class="form-control" name="questions[{{ $qIndex }}][choices][{{ $cIndex }}][text]"
                      value="{{ old("questions.$qIndex.choices.$cIndex.text", $choice->text) }}" required>

                    <input type="hidden" name="questions[{{ $qIndex }}][choices][{{ $cIndex }}][is_correct]" value="0">

                    <div class="input-group-text">
                      <input type="checkbox" name="questions[{{ $qIndex }}][choices][{{ $cIndex }}][is_correct]"
                        value="1" {{ old("questions.$qIndex.choices.$cIndex.is_correct", $choice->is_correct) ? 'checked' : '' }}>
                      <span class="ms-1">Correct</span>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <button type="submit" class="btn btn-primary">Update Quiz</button>
    </form>
  </div>

  <script>
    document.getElementById('time-limit-select').addEventListener('change', function () {
      document.getElementById('time-input').style.display = this.value == 1 ? 'block' : 'none';
    });
  </script>
</x-adminlayout>
