<x-adminlayout>

    <div class="container mt-5">
        <h1 class="text-center form_header">Edit Lesson</h1>

        <form action="{{ route('lessons.update', $lesson->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Course</label>
                <select name="course_id" class="form-select" required>
                    <option value="">-- Select Course --</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $lesson->course_id) == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
                <x-error name="course_id" />
            </div>

            <div class="mb-3">
                <label class="form-label">Lesson Title</label>
                <input type="text" name="title" class="form-control" required value="{{ old('title', $lesson->title) }}">
                <x-error name="title" />
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" id="lesson_description" rows="4" class="form-control">{{ old('description', $lesson->description) }}</textarea>
                <x-error name="description" />
            </div>

            <div class="mb-3">
                <label class="form-label">Video (URL)</label>
                <input type="text" name="video" class="form-control" value="{{ old('video', $lesson->video) }}">
                <x-error name="video" />
            </div>

            <div class="mb-3">
                <label class="form-label">Replace Uploaded Video (optional)</label>
                <input type="file" name="video_file" class="form-control">
                <x-error name="video_file" />
            </div>

            <div class="mb-3">
                <label class="form-label">Replace Attachment (optional)</label>
                <input type="file" name="attachment" class="form-control">
                <x-error name="attachment" />
            </div>

            <button type="submit" class="btn btn-primary">Update Lesson</button>
        </form>
    </div>

</x-adminlayout>

<script>
    $(document).ready(function () {
        $('#lesson_description').summernote({
            height: 150
        });
    });
</script>
