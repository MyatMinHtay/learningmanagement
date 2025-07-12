<x-adminlayout>
    <div class="container">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col">
                <h1 class="text-center bg-purple mt-3">Notifications</h1>
            </div>
            <div class="col-auto">
                @if(in_array('teachers', explode(',', auth()->user()->role->permissions)) || in_array('all', explode(',', auth()->user()->role->permissions)))
                    <a href="{{ route('notifications.create-deadline') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i>Create Deadline Notification
                    </a>
                @endif
                <form method="POST" action="{{ route('notifications.read-all') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-check-double me-1"></i>Mark All Read
                    </button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @if($notifications->count() > 0)
                    @foreach($notifications as $notification)
                        @php
                            $isUrgent = in_array($notification->type, ['quiz_deadline_urgent', 'assignment_deadline_urgent']);
                            $urgencyLevel = $notification->data['urgency_level'] ?? '';
                            $daysUntilDeadline = $notification->data['days_until_deadline'] ?? null;
                            $reminderDays = $notification->data['reminder_days'] ?? null;
                        @endphp
                        <div class="card mb-3 {{ $notification->is_read ? '' : 'border-primary' }} {{ $isUrgent && !$notification->is_read ? 'border-danger shadow-lg' : '' }}">
                            <div class="card-body {{ $isUrgent && !$notification->is_read ? 'bg-danger-subtle' : '' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1 col-10">
                                        <div class="d-flex align-items-center mb-2">
                                            <h5 class="card-title mb-0 me-2 {{ $isUrgent ? 'text-danger fw-bold' : '' }}">
                                                {{ $notification->title }}
                                            </h5>
                                            @if(!$notification->is_read)
                                                <span class="badge {{ $isUrgent ? 'bg-danger' : 'bg-primary' }}">
                                                    {{ $isUrgent ? 'URGENT' : 'New' }}
                                                </span>
                                            @endif
                                            @if($urgencyLevel === 'URGENT')
                                                <span class="badge bg-danger ms-1 animate-pulse">
                                                    <i class="fa-solid fa-exclamation-triangle me-1"></i>URGENT
                                                </span>
                                            @endif
                                            <span class="badge {{ $isUrgent ? 'bg-warning' : 'bg-secondary' }} ms-2">
                                                @switch($notification->type)
                                                    @case('enrollment')
                                                        <i class="fa-solid fa-user-plus me-1"></i>Enrollment
                                                        @break
                                                    @case('assignment_submitted')
                                                        <i class="fa-solid fa-file-upload me-1"></i>Assignment
                                                        @break
                                                    @case('deadline_reminder')
                                                        <i class="fa-solid fa-clock me-1"></i>Deadline
                                                        @break
                                                    @case('quiz_deadline_urgent')
                                                        <i class="fa-solid fa-question-circle me-1"></i>Quiz Alert
                                                        @break
                                                    @case('assignment_deadline_urgent')
                                                        <i class="fa-solid fa-file-text me-1"></i>Assignment Alert
                                                        @break
                                                @endswitch
                                            </span>
                                        </div>

                                        <p class="card-text {{ $isUrgent ? 'fw-semibold' : '' }}">{{ $notification->message }}</p>
                                        
                                        {{-- Display Assignment Title Prominently for Assignment Notifications --}}
                                        @if(($notification->type === 'assignment_deadline_urgent' || ($notification->type === 'deadline_reminder' && isset($notification->data['assignment_title']))) && !empty($notification->data['assignment_title']))
                                            <div class="alert alert-primary border-primary py-2 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa-solid fa-clipboard-list me-2 text-primary"></i>
                                                    <div>
                                                        <strong class="text-primary">Assignment:</strong>
                                                        <span class="fs-6 fw-bold text-dark">{{ $notification->data['assignment_title'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        {{-- Display Course and Quiz/Assignment Info --}}
                                        @if(!empty($notification->data['course_id']))
                                            @php
                                                $course = \App\Models\Course::find($notification->data['course_id']);
                                                $quiz = null;
                                                if (!empty($notification->data['quiz_id'])) {
                                                    $quiz = \App\Models\Quiz::find($notification->data['quiz_id']);
                                                }
                                            @endphp
                                            <div class="alert alert-light border py-2 mb-2">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong><i class="fa-solid fa-book me-1"></i>Course:</strong>
                                                        <span class="text-primary">{{ $course ? $course->name : 'N/A' }}</span>
                                                    </div>
                                                    @if($quiz)
                                                        <div class="col-md-6">
                                                            <strong><i class="fa-solid fa-question-circle me-1"></i>Quiz:</strong>
                                                            <span class="text-info">{{ $quiz->title }}</span>
                                                        </div>
                                                    @elseif($notification->type === 'assignment_deadline_urgent' || $notification->type === 'deadline_reminder')
                                                        <div class="col-md-6">
                                                            <strong><i class="fa-solid fa-file-text me-1"></i>Assignment:</strong>
                                                            <span class="text-warning">
                                                                {{ $notification->data['assignment_title'] ?? 'Course Assignment' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if(!empty($notification->data['deadline_date']))
                                            @php
                                                $deadlineDate = \Carbon\Carbon::parse($notification->data['deadline_date']);
                                                $now = \Carbon\Carbon::now();
                                                $timeUntilDeadline = $now->diffForHumans($deadlineDate, true);
                                                $isOverdue = $deadlineDate->isPast();
                                            @endphp
                                            <div class="alert {{ $isOverdue ? 'alert-danger' : ($daysUntilDeadline == 1 ? 'alert-warning' : 'alert-info') }} py-2 mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa-solid fa-clock me-2"></i>
                                                    <div>
                                                        <strong>Deadline:</strong> {{ $deadlineDate->format('M j, Y \a\t h:i A') }}
                                                        <br>
                                                        <small class="{{ $isOverdue ? 'text-danger' : '' }}">
                                                            {{ $isOverdue ? 'Overdue by ' . $timeUntilDeadline : 'Due in ' . $timeUntilDeadline }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="d-flex align-items-center text-muted">
                                            <small>
                                                <i class="fa-solid fa-user me-1"></i>
                                                From: {{ $notification->sender ? $notification->sender->username : 'System' }}
                                            </small>
                                            <small class="ms-3">
                                                <i class="fa-solid fa-calendar me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        @if(!$notification->is_read)
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="fa-solid fa-check"></i> Mark Read
                                                </button>
                                            </form>
                                        @endif
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center">
                        <div class="card">
                            <div class="card-body py-5">
                                <i class="fa-solid fa-bell-slash fa-3x text-muted mb-3"></i>
                                <h4>No notifications yet</h4>
                                <p class="text-muted">You'll see notifications here when you have new activity.</p>
                            </div>
                        </div>
                    </div>
                @endif
                        </div>
        </div>
    </div>

    <style>
        .animate-pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .border-danger.shadow-lg {
            box-shadow: 0 0.5rem 1rem rgba(220, 53, 69, 0.3) !important;
        }
        
        .bg-danger-subtle {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }
        
        .card {
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
    </style>
    </x-adminlayout> 