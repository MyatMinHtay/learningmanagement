<x-adminlayout>
    <div class="container">
        @if (in_array('students', explode(',', auth()->user()->role->permissions)))
            <div class="col-12 d-flex justify-content-end my-5">
                <a href="{{ route('assignments.create') }}" class="btn btn-success">Create Assignment</a>
            </div>
        @endif
        <h1 class="text-center bg-purple my-5">Assignments</h1>
    
        <div class="table-responsive">
            <table class="table table-hover table-bordered border-1 table-primary">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Course</th>
                        <th>Student</th>
                        <th>Files</th>
                        <th>Status</th>
                        <th>Remark</th>
                        @if(in_array('students', explode(',', auth()->user()->role->permissions)) || in_array('teachers', explode(',', auth()->user()->role->permissions)))
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                        <tr>
                            <td>{{ $assignment->id }}</td>
                            <td>{{ $assignment->course->name }}</td>
                            <td>{{ $assignment->student->username }}</td>
                            <td>
                                @foreach (json_decode($assignment->files) as $file)
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="d-block">
                                        Download
                                    </a>
                                @endforeach
                            </td>
                            <td>
                                @if($assignment->status === 'accepted')
                                    <span class="badge bg-success">Accepted</span>
                                @elseif($assignment->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td>{{ $assignment->remark ?? '—' }}</td>
                            @if(in_array('students', explode(',', auth()->user()->role->permissions)) || in_array('teachers', explode(',', auth()->user()->role->permissions)) || in_array('all', explode(',', auth()->user()->role->permissions)))
                                <td>
                                    {{-- Actions for Students: Show Edit button only when assignment is rejected --}}
                                    @if(in_array('students', explode(',', auth()->user()->role->permissions)) && $assignment->status === 'rejected')
                                        <a href="{{ route('assignments.edit', $assignment->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                    @endif

                                    {{-- Actions for Teachers: Show Accept/Reject buttons when assignment is pending --}}
                                    @if(in_array('teachers', explode(',', auth()->user()->role->permissions)) || in_array('all', explode(',', auth()->user()->role->permissions)))
                                        @if($assignment->status === 'pending')
                                            <form method="POST" action="{{ route('assignments.updateStatus', $assignment->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="accepted">
                                                <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                            </form>
            
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $assignment->id }}">
                                                Reject
                                            </button>
            
                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectModal-{{ $assignment->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('assignments.updateStatus', $assignment->id) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Reject Assignment</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="status" value="rejected">
                                                                <div class="mb-3">
                                                                    <label for="remark" class="form-label">Reason for rejection:</label>
                                                                    <textarea name="remark" class="form-control" rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-danger">Reject</button>
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    {{-- Show dash if no actions are available --}}
                                    @if(
                                        (in_array('students', explode(',', auth()->user()->role->permissions)) && $assignment->status !== 'rejected') ||
                                        ((in_array('teachers', explode(',', auth()->user()->role->permissions)) || in_array('all', explode(',', auth()->user()->role->permissions))) && $assignment->status !== 'pending')
                                    )
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="@if(in_array('students', explode(',', auth()->user()->role->permissions)) || in_array('teachers', explode(',', auth()->user()->role->permissions)) || in_array('all', explode(',', auth()->user()->role->permissions))) 7 @else 6 @endif" class="text-center">No assignments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-adminlayout>
    