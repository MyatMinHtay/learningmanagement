<x-adminlayout>
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <!-- Header Section -->
                <div class="text-center mb-5">
                    <div class="bg-gradient-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-bell text-white" style="font-size: 2rem;"></i>
                    </div>
                    <h2 class="text-primary mb-2">Create Deadline Notification</h2>
                    <p class="text-muted lead">Send deadline reminders to your students</p>
                </div>

                <!-- Main Form Card -->
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-gradient-primary text-white py-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h4 class="mb-0">Deadline Details</h4>
                                <small class="opacity-75">Configure your notification settings</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-5">
                        <form method="POST" action="{{ route('notifications.store-deadline') }}" id="deadlineForm">
                            @csrf
                            
                            <!-- Course Selection -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="course_id" class="form-label fw-bold">
                                        <i class="fas fa-book text-primary me-2"></i>Select Course
                                    </label>
                                    <select name="course_id" id="course_id" class="form-select form-select-lg" required>
                                        <option value="">🎓 Choose a course...</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="text-danger mt-2">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Type Selection -->
                                <div class="col-md-6">
                                    <label for="type" class="form-label fw-bold">
                                        <i class="fas fa-tag text-primary me-2"></i>Notification Type
                                    </label>
                                    <select name="type" id="type" class="form-select form-select-lg" required>
                                        <option value="">📋 Choose type...</option>
                                        <option value="quiz_deadline" {{ old('type') == 'quiz_deadline' ? 'selected' : '' }}>
                                            📝 Quiz Deadline
                                        </option>
                                        <option value="assignment_deadline" {{ old('type') == 'assignment_deadline' ? 'selected' : '' }}>
                                            📚 Assignment Deadline
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger mt-2">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Assignment Title (Conditional) -->
                            <div class="mb-4" id="assignmentTitleDiv" style="display: none;">
                                <label for="assignment_title" class="form-label fw-bold">
                                    <i class="fas fa-file-alt text-primary me-2"></i>Assignment Title
                                </label>
                                <input type="text" name="assignment_title" id="assignment_title" class="form-control form-control-lg" 
                                       value="{{ old('assignment_title') }}" placeholder="e.g., Assignment 1, Chapter 3 Exercise, Final Project">
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    Specify which assignment this deadline is for (e.g., "Assignment 1", "Final Project")
                                </div>
                                @error('assignment_title')
                                    <div class="text-danger mt-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Title Input -->
                            <div class="mb-4">
                                <label for="title" class="form-label fw-bold">
                                    <i class="fas fa-heading text-primary me-2"></i>Notification Title
                                </label>
                                <input type="text" name="title" id="title" class="form-control form-control-lg" 
                                       value="{{ old('title') }}" placeholder="e.g., Quiz 1 Deadline Reminder" required>
                                @error('title')
                                    <div class="text-danger mt-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Message Input -->
                            <div class="mb-4">
                                <label for="message" class="form-label fw-bold">
                                    <i class="fas fa-comment-alt text-primary me-2"></i>Message Content
                                </label>
                                <textarea name="message" id="message" class="form-control" rows="5" 
                                          placeholder="📢 Enter your deadline notification message..." required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="text-danger mt-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Deadline Date -->
                            <div class="mb-4">
                                <label for="deadline_date" class="form-label fw-bold">
                                    <i class="fas fa-clock text-primary me-2"></i>Deadline Date & Time
                                </label>
                                <input type="datetime-local" name="deadline_date" id="deadline_date" 
                                       class="form-control form-control-lg" value="{{ old('deadline_date') }}" required>
                                @error('deadline_date')
                                    <div class="text-danger mt-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    Students will receive reminders as the deadline approaches
                                </div>
                            </div>

                            <!-- Auto Reminder Settings -->
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-bell-ring me-2"></i>Auto Reminder Settings
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="reminder_value" class="form-label fw-bold">
                                                <i class="fas fa-calendar-check text-info me-2"></i>Send reminder when deadline is
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="reminder_value" id="reminder_value" 
                                                       class="form-control form-control-lg" 
                                                       value="{{ old('reminder_value', 1) }}" 
                                                       min="1" max="365" required>
                                                <select name="reminder_unit" id="reminder_unit" class="form-select form-select-lg" style="max-width: 150px;">
                                                    <option value="seconds" {{ old('reminder_unit') == 'seconds' ? 'selected' : '' }}>seconds</option>
                                                    <option value="minutes" {{ old('reminder_unit') == 'minutes' ? 'selected' : '' }}>minutes</option>
                                                    <option value="hours" {{ old('reminder_unit') == 'hours' ? 'selected' : '' }}>hours</option>
                                                    <option value="days" {{ old('reminder_unit', 'days') == 'days' ? 'selected' : '' }}>days</option>
                                                </select>
                                                <span class="input-group-text bg-primary text-white">away</span>
                                            </div>
                                            <div class="form-text mt-2">
                                                <i class="fas fa-info-circle text-info me-1"></i>
                                                Example: "30 seconds", "5 minutes", "2 hours", or "1 days" before deadline
                                            </div>
                                            @error('reminder_value')
                                                <div class="text-danger mt-2">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                            @error('reminder_unit')
                                                <div class="text-danger mt-2">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Notifications
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>Send Notification
                                    <span class="spinner-border spinner-border-sm ms-2 d-none" id="submitSpinner"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="card border-0 shadow-sm mt-4" id="previewCard" style="display: none;">
                    <div class="card-header bg-gradient-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-eye me-2"></i>Preview
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="notification-preview p-3 bg-light rounded">
                            <h6 class="preview-title text-primary mb-2"></h6>
                            <p class="preview-message mb-2"></p>
                            <div class="preview-assignment-section" style="display: none;">
                                <small class="text-info">
                                    <i class="fas fa-file-alt me-1"></i>
                                    Assignment: <span class="preview-assignment-title"></span>
                                </small><br>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Deadline: <span class="preview-deadline"></span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .form-control-lg, .form-select-lg {
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
            border-radius: 0.5rem;
            border: 2px solid #e3e6f0;
            transition: all 0.3s ease;
        }
        
        .form-control-lg:focus, .form-select-lg:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .card {
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .btn-lg {
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .notification-preview {
            border-left: 4px solid #667eea;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
        // Enhanced form handling with preview
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const titleInput = document.getElementById('title');
            const messageInput = document.getElementById('message');
            const deadlineInput = document.getElementById('deadline_date');
            const previewCard = document.getElementById('previewCard');
            const submitBtn = document.getElementById('submitBtn');
            const submitSpinner = document.getElementById('submitSpinner');
            const assignmentTitleDiv = document.getElementById('assignmentTitleDiv');
            const assignmentTitleInput = document.getElementById('assignment_title');
            
            // Auto-fill functionality with improved templates
            typeSelect.addEventListener('change', function() {
                const type = this.value;
                
                if (!titleInput.value) {
                    if (type === 'quiz_deadline') {
                        titleInput.value = '📝 Quiz Deadline Reminder';
                    } else if (type === 'assignment_deadline') {
                        titleInput.value = '📚 Assignment Deadline Reminder';
                    }
                }
                
                if (!messageInput.value) {
                    if (type === 'quiz_deadline') {
                        messageInput.value = '🎯 Important Reminder: Your quiz deadline is approaching!\n\nPlease complete your quiz before the deadline to avoid any penalties. If you have any questions, feel free to reach out.\n\nGood luck! 🍀';
                    } else if (type === 'assignment_deadline') {
                        messageInput.value = '📋 Assignment Deadline Approaching!\n\nThis is a friendly reminder that your assignment submission deadline is coming up. Please ensure you submit your work on time.\n\nLate submissions may affect your grade. Contact me if you need any clarification.\n\nBest regards! 👨‍🏫';
                    }
                }

                if (type === 'assignment_deadline') {
                    assignmentTitleDiv.style.display = 'block';
                    if (!assignmentTitleInput.value) {
                        assignmentTitleInput.value = 'Assignment 1'; // Default value
                    }
                } else {
                    assignmentTitleDiv.style.display = 'none';
                    assignmentTitleInput.value = ''; // Clear if not assignment
                }
                
                updatePreview();
            });
            
            // Real-time preview update
            [titleInput, messageInput, deadlineInput].forEach(input => {
                input.addEventListener('input', updatePreview);
            });

            // Assignment title input update
            assignmentTitleInput.addEventListener('input', updatePreview);
            
            function updatePreview() {
                const title = titleInput.value;
                const message = messageInput.value;
                const deadline = deadlineInput.value;
                const assignmentTitle = assignmentTitleInput.value;
                const type = typeSelect.value;
                
                if (title || message || deadline || assignmentTitle) {
                    previewCard.style.display = 'block';
                    previewCard.classList.add('animate-fade-in');
                    
                    document.querySelector('.preview-title').textContent = title || 'Notification Title';
                    document.querySelector('.preview-message').textContent = message || 'Notification message will appear here...';
                    
                    // Show/hide assignment section based on type
                    const assignmentSection = document.querySelector('.preview-assignment-section');
                    if (type === 'assignment_deadline' && assignmentTitle) {
                        assignmentSection.style.display = 'block';
                        document.querySelector('.preview-assignment-title').textContent = assignmentTitle;
                    } else {
                        assignmentSection.style.display = 'none';
                    }
                    
                    if (deadline) {
                        const date = new Date(deadline);
                        document.querySelector('.preview-deadline').textContent = date.toLocaleString();
                    } else {
                        document.querySelector('.preview-deadline').textContent = 'Not set';
                    }
                } else {
                    previewCard.style.display = 'none';
                }
            }
            
            // Enhanced form submission
            document.getElementById('deadlineForm').addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                submitSpinner.classList.remove('d-none');
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Sending... <span class="spinner-border spinner-border-sm ms-2"></span>';
            });
            
            // Set minimum date to now (allow immediate testing)
            const now = new Date();
            now.setMinutes(now.getMinutes() + 1); // Allow at least 1 minute from now
            deadlineInput.min = now.toISOString().slice(0, 16);
        });
    </script>
</x-adminlayout> 