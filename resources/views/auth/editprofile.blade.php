<x-layout>
<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-4">
                <h2 class="mb-4">Edit Profile</h2>

                <form action="{{ route('profile.update', $user->username) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- If you want to use PUT --}}
                    {{-- @method('PUT') --}}

                    {{-- Username --}}
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control @error('username') is-invalid @enderror"
                            value="{{ old('username', $user->username) }}"
                            required
                        >
                        <x-error name="username" />
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}"
                            required
                        >
                        <x-error name="email" />
                    </div>

                   

                    {{-- User Photo --}}
                    <div class="mb-3">
                        <label for="userphoto" class="form-label">Profile Photo</label>
                        <input
                            type="file"
                            id="userphoto"
                            name="userphoto"
                            class="form-control @error('userphoto') is-invalid @enderror"
                            accept="image/*"
                        >
                        <small class="form-text text-muted">Current photo:</small>
                        <img src="{{ asset($user->userphoto) }}" alt="Current Photo" class="rounded mt-2" style="width: 100px; height: 100px; object-fit: cover;">
                        <x-error name="userphoto" />
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</main>
</x-layout>
