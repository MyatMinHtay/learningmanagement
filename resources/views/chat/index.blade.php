<x-adminlayout title="Chat">
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-comments"></i> Chat</h4>
                    </div>
                    <div class="card-body">
                        @if(count($conversations) > 0)
                            <div class="list-group">
                                @foreach($conversations as $conversation)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if(Auth::user()->role->role === 'student')
                                                        <img src="{{ $conversation['teacher']->userphoto ? asset($conversation['teacher']->userphoto) : asset('assets/avatars/user.png') }}" 
                                                             class="rounded-circle" 
                                                             width="50" height="50" alt="Teacher"
                                                             style="object-fit: cover;">
                                                    @else
                                                        <img src="{{ $conversation['student']->userphoto ? asset($conversation['student']->userphoto) : asset('assets/avatars/user.png') }}" 
                                                             class="rounded-circle" 
                                                             width="50" height="50" alt="Student"
                                                             style="object-fit: cover;">
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">
                                                        @if(Auth::user()->role->role === 'student')
                                                            {{ $conversation['teacher']->username }}
                                                        @else
                                                            {{ $conversation['student']->username }}
                                                        @endif
                                                    </h6>
                                                    <p class="mb-1 text-muted">
                                                        <small>Course: {{ $conversation['course']->name }}</small>
                                                    </p>
                                                    @if($conversation['last_message'])
                                                        <p class="mb-1 text-muted small">
                                                            {{ Str::limit($conversation['last_message']->message, 60) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                @if($conversation['unread_count'] > 0)
                                                    <span class="badge bg-danger me-2">
                                                        {{ $conversation['unread_count'] }}
                                                    </span>
                                                @endif
                                                <div class="text-end">
                                                    @if($conversation['last_message'])
                                                        <small class="text-muted">
                                                            {{ $conversation['last_message']->created_at->diffForHumans() }}
                                                        </small>
                                                    @endif
                                                    <br>
                                                    @if(Auth::user()->role->role === 'student')
                                                        <a href="{{ route('student.chat.show', [$conversation['course']->id, $conversation['teacher']->id]) }}" 

                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-comment"></i> Chat
                                                        </a>
                                                    @else
                                                        <a href="{{ route('chat.show', [$conversation['course']->id, $conversation['student']->id]) }}" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-comment"></i> Chat
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-comments text-muted fa-3x mb-3"></i>
                                <h5 class="text-muted">No conversations yet</h5>
                                <p class="text-muted">
                                    @if(Auth::user()->role->role === 'student')
                                        Once you enroll in courses, you can chat with your teachers here.
                                    @else
                                        Once students enroll in your courses, their conversations will appear here.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .list-group-item {
            border: none;
            border-radius: 10px !important;
            margin-bottom: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .list-group-item img {
            border: 2px solid #e9ecef;
            transition: border-color 0.3s ease;
        }

        .list-group-item:hover img {
            border-color: #007bff;
        }

        .badge {
            font-size: 0.75rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
    </style>
</x-adminlayout> 