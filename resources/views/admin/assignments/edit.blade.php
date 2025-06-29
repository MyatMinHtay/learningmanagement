<x-adminlayout>
    <div class="container mt-5">
        <h1 class="text-center form_header">Edit Assignment</h1>
    
        <form action="{{ route('assignments.update', $assignment->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            @method('PATCH')
    
            <div class="modal-body row g-3">
                <div class="col-md-12">
                    <label for="files">Replace Assignment Files (optional)</label>
                    <input type="file" name="files[]" class="form-control" multiple>
                    <small class="text-muted">Leave empty if you don't want to change existing files.</small>
                    <x-error name="files" />
                    <x-error name="files.*" />
                </div>
    
                <div class="col-md-12">
                    <label for="note">Optional Note to Instructor</label>
                    <textarea name="note" rows="3" class="form-control">{{ old('note', $assignment->remark) }}</textarea>
                </div>
            </div>
    
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Update Assignment</button>
            </div>
        </form>
    </div>
</x-adminlayout>
    