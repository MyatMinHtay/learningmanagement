<x-adminlayout>
    <div class="container mt-5">
        <h1 class="text-center form_header">Submit Assignment</h1>
    
        <form action="{{ route('assignments.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
    
            <div class="modal-body row g-3">
                <div class="col-md-12">
                    <label for="course_id">Select Course</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">-- Choose a Course --</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-error name="course_id" />
                </div>
    
                <div class="col-md-12">
                    <label for="files">Upload Assignment Files (PDF/ZIP)</label>
                    <input type="file" name="files[]" class="form-control" multiple required>
                    <x-error name="files" />
                    <x-error name="files.*" />
                </div>
    
                <div class="col-md-12">
                    <label for="note">Optional Note to Instructor</label>
                    <textarea name="note" rows="3" class="form-control" placeholder="Add a note (optional)">{{ old('note') }}</textarea>
                </div>
            </div>
    
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Submit Assignment</button>
            </div>
        </form>
    </div>
    </x-adminlayout>
    