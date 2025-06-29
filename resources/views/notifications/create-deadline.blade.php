<x-adminlayout>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Create Deadline Notification</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('notifications.store-deadline') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="course_id" class="form-label">Select Course</label>
                                <select name="course_id" id="course_id" class="form-select" required>
                                    <option value="">Choose a course...</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Notification Type</label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="">Choose type...</option>
                                    <option value="quiz_deadline" {{ old('type') == 'quiz_deadline' ? 'selected' : '' }}>
                                        Quiz Deadline
                                    </option>
                                    <option value="assignment_deadline" {{ old('type') == 'assignment_deadline' ? 'selected' : '' }}>
                                        Assignment Deadline
                                    </option>
                                </select>
                                @error('type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control" 
                                       value="{{ old('title') }}" placeholder="e.g., Quiz 1 Deadline Reminder" required>
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea name="message" id="message" class="form-control" rows="4" 
                                          placeholder="Enter your deadline notification message..." required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="deadline_date" class="form-label">Deadline Date</label>
                                <input type="datetime-local" name="deadline_date" id="deadline_date" 
                                       class="form-control" value="{{ old('deadline_date') }}" required>
                                @error('deadline_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Set the deadline date and time for students to be aware of.</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('notifications.index') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left me-1"></i>Back to Notifications
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-paper-plane me-1"></i>Send Notification
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-fill title based on type selection
        document.getElementById('type').addEventListener('change', function() {
            const titleInput = document.getElementById('title');
            const currentTitle = titleInput.value;
            
            if (!currentTitle) {
                const type = this.value;
                if (type === 'quiz_deadline') {
                    titleInput.value = 'Quiz Deadline Reminder';
                } else if (type === 'assignment_deadline') {
                    titleInput.value = 'Assignment Deadline Reminder';
                }
            }
        });

        // Auto-fill message based on type selection
        document.getElementById('type').addEventListener('change', function() {
            const messageInput = document.getElementById('message');
            const currentMessage = messageInput.value;
            
            if (!currentMessage) {
                const type = this.value;
                if (type === 'quiz_deadline') {
                    messageInput.value = 'Reminder: Please complete your quiz before the deadline. Late submissions may not be accepted.';
                } else if (type === 'assignment_deadline') {
                    messageInput.value = 'Reminder: Please submit your assignment before the deadline. Late submissions may affect your grade.';
                }
            }
        });
    </script>
</x-adminlayout> 