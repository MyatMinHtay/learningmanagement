<x-adminlayout>
  <div class="container">
    <h2 class="text-center my-4">Create New Quiz</h2>

    <form action="{{ route('quizzes.store') }}" method="POST">
      @csrf

      <!-- Quiz Metadata -->
      <div class="mb-3">
        <label class="form-label">Quiz Title</label>
        <input type="text" name="title" class="form-control" required value="{{ old('title', $quiz->title ?? '') }}">
        <x-error name="title" />
      </div>

      <div class="mb-3">
        <label class="form-label">Course</label>
        <select name="course_id" class="form-select" required>
          <option value="">-- Select Course --</option>
          @foreach ($courses as $course)
            <option value="{{ $course->id }}" {{ old('course_id', $quiz->course_id ?? '') == $course->id ? 'selected' : '' }}>
              {{ $course->name }}
            </option>
          @endforeach
        </select>
          <x-error name="course_id" />
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="2">{{ old('description', $quiz->description ?? '') }}</textarea>
        <x-error name="description" />
      </div>

      <div class="mb-3">
        <label class="form-label">Total Questions</label>
        <input type="number" name="total_questions" class="form-control" min="1" required value="{{ old('total_questions', $quiz->total_questions ?? '') }}">
        <x-error name="total_questions" />
      </div>

      <div class="mb-3">
        <label class="form-label">Total Marks</label>
        <input type="number" name="total_marks" class="form-control" min="1" required value="{{ old('total_marks', $quiz->total_marks ?? '') }}">
        <x-error name="total_marks" />
      </div>

      <div class="mb-3">
        <label class="form-label">Time Limit</label>
        <select name="is_time_limited" class="form-select" id="time-limit-select">
          <option value="0" {{ old('is_time_limited', $quiz->is_time_limited ?? 0) == 0 ? 'selected' : '' }}>No</option>
          <option value="1" {{ old('is_time_limited', $quiz->is_time_limited ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
        </select>
        <x-error name="is_time_limited" />
      </div>

      <div class="mb-3" id="time-input" style="display: none;">
        <label class="form-label">Time (in minutes)</label>
        <input type="number" name="total_time" class="form-control" min="1" value="{{ old('total_time', $quiz->total_time ?? '') }}">
        <x-error name="total_time" />
      </div>

      <!-- Questions and Choices -->
      <div id="questions-container"></div>

      <button type="button" class="btn btn-secondary my-3" onclick="addQuestion()">+ Add Question</button>

      <button type="submit" class="btn btn-success">Create Quiz</button>
    </form>
  </div>

  <!-- Templates -->
  <template id="question-template">
    <div class="card mb-4 question-box">
      <div class="card-body">
        <h5 class="card-title">Question <span class="question-number"></span></h5>
        <div class="mb-2">
          <input type="text" name="questions[__INDEX__][text]" class="form-control" placeholder="Question text" required>
        </div>
        <div class="mb-2">
          <input type="number" name="questions[__INDEX__][marks]" class="form-control" placeholder="Marks" min="1" required>
        </div>
        <input type="hidden" name="questions[__INDEX__][order]" value="__ORDER__">

        <div class="choices-container mb-2"></div>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addChoice(this)">+ Add Choice</button>

        <button type="button" class="btn btn-sm btn-danger float-end" onclick="removeQuestion(this)">Remove Question</button>
      </div>
    </div>
  </template>

  <template id="choice-template">
  <div class="input-group mb-2">
    <input type="text" class="form-control" name="questions[__QINDEX__][choices][__CINDEX__][text]" placeholder="Choice text" required>
    
    <!-- Hidden input ensures a value of 0 if checkbox is not checked -->
    <input type="hidden" name="questions[__QINDEX__][choices][__CINDEX__][is_correct]" value="0">
    
    <div class="input-group-text">
      <input type="checkbox" name="questions[__QINDEX__][choices][__CINDEX__][is_correct]" value="1">
      <span class="ms-1">Correct</span>
    </div>
    
    <button type="button" class="btn btn-outline-danger" onclick="removeChoice(this)">×</button>
  </div>
</template>


  <!-- JS -->
  <script>
    let questionIndex = 0;

    function addQuestion() {
      const container = document.getElementById('questions-container');
      const template = document.getElementById('question-template').innerHTML;
      const html = template.replace(/__INDEX__/g, questionIndex).replace(/__ORDER__/g, questionIndex + 1);
      const div = document.createElement('div');
      div.innerHTML = html;
      container.appendChild(div);

      updateQuestionNumbers();
      addChoice(div.querySelector('.btn-outline-primary'));
      addChoice(div.querySelector('.btn-outline-primary'));

      questionIndex++;
    }

    function removeQuestion(button) {
      button.closest('.question-box').remove();
      updateQuestionNumbers();
    }

    function addChoice(button) {
      const questionBox = button.closest('.question-box');
      const qIndex = Array.from(document.querySelectorAll('.question-box')).indexOf(questionBox);
      const container = questionBox.querySelector('.choices-container');
      const cIndex = container.querySelectorAll('.input-group').length;

      const template = document.getElementById('choice-template').innerHTML;
      const html = template.replace(/__QINDEX__/g, qIndex).replace(/__CINDEX__/g, cIndex);

      const div = document.createElement('div');
      div.innerHTML = html;
      container.appendChild(div);
    }

    function removeChoice(button) {
      button.closest('.input-group').remove();
    }

    function updateQuestionNumbers() {
      document.querySelectorAll('.question-number').forEach((el, idx) => {
        el.textContent = idx + 1;
      });
    }

    document.getElementById('time-limit-select').addEventListener('change', function () {
      document.getElementById('time-input').style.display = this.value == 1 ? 'block' : 'none';
    });
  </script>
</x-adminlayout>
