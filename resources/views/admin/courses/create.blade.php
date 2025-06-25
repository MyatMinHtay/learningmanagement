<x-adminlayout>

    <div class="container mt-5">
        <h1 class="text-center form_header">Add Course</h1>

        <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf

            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label for="name">Course Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    <x-error name="name" />
                </div>

                <div class="col-md-6">
                    <label for="image">Image</label>
                    <input type="file" name="image" class="form-control">
                    <x-error name="image" />
                </div>

                <div class="col-md-12">
                    <label for="description">Description</label>
                    <textarea name="description" id="course_description" rows="3" class="form-control"></textarea>
                    <x-error name="description" />
                </div>

                <div class="col-md-12">
                    <label for="duration">Duration (days)</label>
                    <input type="number" name="duration" class="form-control" value="{{ old('duration') }}">
                    <x-error name="duration" />
                </div>

                <!-- Course Modules -->
                <div class="col-md-12">
                    <label>Course Modules</label>
                    <div id="modulesWrapper">
                        <!-- Module Input Template -->
                        <div class="module-group mb-3">
                            <h6 class="fw-bold">Modules 1</h6>
                            <input type="text" name="modules[0][title]" class="form-control mb-2" placeholder="Module Title" required>
                            <textarea name="modules[0][content]" class="form-control module-content" rows="2" placeholder="Module Content"></textarea>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-info mt-2" id="addModuleBtn">+ Add Module</button>
                </div>

                <div class="col-md-12">
                    
                    <x-error name="modules.*.title"></x-error>
                    <x-error name="modules.*.content"></x-error>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Create Course</button>
            </div>
        </form>
    </div>

</x-adminlayout>

<!-- Summernote + Dynamic Module JS -->
<script>
    $(document).ready(function () {
        $('#course_description').summernote({
            placeholder: 'Write a brief course overview...',
            height: 150
        });

        let moduleIndex = 1;

        $('#addModuleBtn').click(function () {
            $('#modulesWrapper').append(`
                <div class="module-group mb-3">
                    <h6 class="fw-bold">Modules ${moduleIndex + 1}</h6>
                    <input type="text" name="modules[${moduleIndex}][title]" class="form-control mb-2" placeholder="Module Title" required>
                    <textarea name="modules[${moduleIndex}][content]" class="form-control module-content" rows="2" placeholder="Module Content"></textarea>
                    <button type="button" class="btn btn-sm btn-danger mt-1 removeModule">Remove</button>
                </div>
            `);
            moduleIndex++;
        });

        $(document).on('click', '.removeModule', function () {
            $(this).closest('.module-group').remove();
        });
    });
</script>
