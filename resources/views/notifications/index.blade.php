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
                        <div class="card mb-3 {{ $notification->is_read ? '' : 'border-primary' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <h5 class="card-title mb-0 me-2">{{ $notification->title }}</h5>
                                            @if(!$notification->is_read)
                                                <span class="badge bg-primary">New</span>
                                            @endif
                                            <span class="badge bg-secondary ms-2">
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
                                                @endswitch
                                            </span>
                                        </div>


                                        <p class="card-text">{{ $notification->message }}</p>
                                        @if(!empty($notification->data['deadline_date']))
                                            <p>
                                                Deadline: {{ \Carbon\Carbon::parse($notification->data['deadline_date'])->format('d M, h:i A') }}
                                            </p>
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
                                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" 
                                              onsubmit="return confirm('Delete this notification?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </button>
                                        </form>
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
</x-adminlayout> 