<x-adminlayout>

    <div class="container mt-5">
        <h1 class="text-center form_header">Update Category</h1>

        <form action="{{ route('categories.update', $category->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')

            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label for="name">Category Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                    <x-error name="name" />
                </div>

                <div class="col-md-6">
                    <label for="color">Color</label>
                    <input type="color" name="color" class="form-control" value="{{ old('color', $category->color) }}" required>
                    <x-error name="color" />
                </div>

                <div class="col-md-12">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                    <x-error name="sort_order" />
                    <small class="form-text text-muted">Lower numbers appear first</small>
                </div>

                <div class="col-md-12">
                    <h5>Preview</h5>
                    <div class="d-flex align-items-center">
                        <span class="badge me-2" id="category-preview" style="background-color: {{ $category->color }}; color: white;">
                            <span id="preview-text">{{ $category->name }}</span>
                        </span>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Note:</strong> This category is currently used by {{ $category->course_count }} course(s).
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success">Update Category</button>
            </div>
        </form>
    </div>

</x-adminlayout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.querySelector('input[name="name"]');
    const colorInput = document.querySelector('input[name="color"]');
    const preview = document.getElementById('category-preview');
    const previewText = document.getElementById('preview-text');

    function updatePreview() {
        const name = nameInput.value || 'Category Name';
        const color = colorInput.value || '#007bff';
        
        previewText.textContent = name;
        preview.style.backgroundColor = color;
    }

    nameInput.addEventListener('input', updatePreview);
    colorInput.addEventListener('input', updatePreview);
    
    updatePreview();
});
</script> 