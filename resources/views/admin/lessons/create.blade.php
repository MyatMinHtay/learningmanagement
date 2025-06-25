<x-adminlayout>

    <div class="container mt-5">
        <h1 class="text-center form_header">Add Lesson</h1>

        <form action="{{ route('lessons.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf

            <div class="modal-body row g-3">

                <div class="col-md-6">
                    <label for="course_id">Select Course</label>
                    <select name="course_id" class="form-control" required>
                        <option value="">-- Choose Course --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                    <x-error name="course_id" />
                </div>

                <div class="col-md-6">
                    <label for="title">Lesson Title</label>
                    <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
                    <x-error name="title" />
                </div>

                <div class="col-md-12">
                    <label for="description">Description</label>
                    <textarea name="description" id="lesson_description" rows="4" class="form-control">{{ old('description') }}</textarea>
                    <x-error name="description" />
                </div>

                <div class="col-md-6">
                    <label for="video">Video (URL or Upload)</label>
                    <input type="text" name="video" class="form-control" placeholder="Paste YouTube/Vimeo URL or skip for upload">
                    <x-error name="video" />
                </div>

                <div class="col-md-6">
                    <label for="video_file">Upload Video File (optional)</label>
                    <input type="file" name="video_file" class="form-control">
                    <x-error name="video_file" />
                </div>

                <div class="col-md-12">
                    <label for="attachment">Additional Attachment (PDF, etc)</label>
                    <input type="file" name="attachment" class="form-control">
                    <x-error name="attachment" />
                </div>

            </div>

            <div class="modal-footer mt-5">
                <button type="submit" class="btn btn-success">Create Lesson</button>
            </div>
        </form>
    </div>

</x-adminlayout>

<script>
    $(document).ready(function () {
        $('#lesson_description').summernote({
            placeholder: 'Write a lesson summary...',
            height: 150
        });
    });
</script>
