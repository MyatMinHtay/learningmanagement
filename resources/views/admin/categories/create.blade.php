<x-adminlayout>

    <div class="container mt-5">
        <h1 class="text-center form_header">Add Category</h1>

        <form action="{{ route('categories.store') }}" method="POST" class="modal-content">
            @csrf

            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label for="name">Category Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    <x-error name="name" />
                </div>

                <div class="col-md-6">
                    <label for="color">Color</label>
                    <input type="color" name="color" class="form-control" value="{{ old('color', '#007bff') }}" required>
                    <x-error name="color" />
                </div>

                <div class="col-md-12">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                    <x-error name="sort_order" />
                    <small class="form-text text-muted">Lower numbers appear first</small>
                </div>

                <div class="col-md-12">
                    <h5>Preview</h5>
                    <div class="d-flex align-items-center">
                        <span class="badge me-2" id="category-preview" style="background-color: #007bff; color: white;">
                            <span id="preview-text">Category Name</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success">Create Category</button>
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