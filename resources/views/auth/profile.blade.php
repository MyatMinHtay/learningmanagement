<x-layout>
<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <img src="{{ asset($user->userphoto) }}" alt="{{ $user->username }}" 
                         class="rounded-circle me-3" style="width: 80px; height: 80px; object-fit: cover;">
                    <h3 class="mb-0">{{ $user->username }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th scope="row" style="width: 150px;">Email</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Role</th>
                                <td>{{ $user->role->role ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Status</th>
                                <td>
                                    @switch($user->status)
                                        @case('A')
                                            <span class="badge bg-success">Active</span>
                                            @break
                                        @case('D')
                                            <span class="badge bg-danger">Disabled</span>
                                            @break
                                        @case('L')
                                            <span class="badge bg-warning text-dark">Locked</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">Unknown</span>
                                    @endswitch
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">Joined At</th>
                                <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('profile.edit', $user->id) }}" class="btn btn-outline-primary">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
</x-layout>
