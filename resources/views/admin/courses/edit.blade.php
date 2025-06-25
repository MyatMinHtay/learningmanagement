<x-adminlayout>

    <div class="container mt-5">
        <h1 class="text-center form_header">Update Course</h1>

        <form action="{{ route('courses.update', $course->id) }}" method="POST" id="courseForm" class="forms py-5" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="p-3 mx-auto col-12 col-lg-8 rounded-2">

                {{-- Course Fields --}}
                <div class="mb-4">
                    <label for="name">Course Name</label>
                    <input type="text" class="form-control inputbox" value="{{ old('name', $course->name) }}" name="name" id="name">
                    <x-error name="name" />
                </div>

                <div class="mb-4">
                    <label for="description">Course Description</label>
                    <textarea name="description" id="course_description" class="form-control inputbox textareaInput" rows="4">{{ old('description', $course->description) }}</textarea>
                    <x-error name="description" />
                </div>

                <div class="mb-4">
                    <label for="duration">Duration (days)</label>
                    <input type="number" class="form-control inputbox" value="{{ old('duration', $course->duration) }}" name="duration" id="duration">
                    <x-error name="duration" />
                </div>

                <div class="mb-4">
                    <label for="image">Course Image</label>
                    <input type="file" class="form-control inputbox" name="image">
                    <x-error name="image" />
                    @if ($course->image)
                        <div class="mt-2">
                            <img src="{{ asset($course->image) }}" alt="Course Image" style="max-width: 150px;">
                        </div>
                    @endif
                </div>

                {{-- Modules Section --}}
                <div class="mt-4">
                    <label class="fw-bold">Course Modules</label>
                    <div id="modulesWrapper">
                        @foreach ($course->modules as $module)
                            <div class="module-group border rounded p-3 mb-3">
                                <input type="hidden" name="modules[{{ $loop->index }}][id]" value="{{ $module->id }}">
                                <div class="mb-2">
                                    <label>Title</label>
                                    <input type="text" name="modules[{{ $loop->index }}][title]" value="{{ $module->title }}" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <label>Content</label>
                                    <textarea name="modules[{{ $loop->index }}][content]" class="form-control module-content" rows="2">{{ $module->content }}</textarea>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-module">Remove</button>
                            </div>
                        @endforeach
                    </div>
                    

                    <button type="button" class="btn btn-info btn-sm" id="addModuleBtn">+ Add Module</button>

                    <x-error name="modules.*.title" />
                    <x-error name="modules.*.content" />
                </div>
            </div>

            {{-- Buttons --}}
            <div class="formbtnboxes col-6">
                
                <button type="submit" class="formSubtmitBtn">Update</button>
            </div>
        </form>

        
    </div>

</x-adminlayout>

<script>
    let moduleIndex = {{ count($course->modules) }};
    $(document).ready(function () {
        $('#course_description').summernote({
            placeholder: 'Write a brief course overview...',
            height: 150
        });

        $('#addModuleBtn').click(function () {
            $('#modulesWrapper').append(`
                <div class="module-group border rounded p-3 mb-3">
                    <div class="mb-2">
                        <label>Title</label>
                        <input type="text" name="modules[${moduleIndex}][title]" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Content</label>
                        <textarea name="modules[${moduleIndex}][content]" class="form-control module-content" rows="2"></textarea>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-module">Remove</button>
                </div>
            `);
            moduleIndex++;
        });

        $(document).on('click', '.remove-module', function () {
            $(this).closest('.module-group').remove();
        });
    });
</script>
