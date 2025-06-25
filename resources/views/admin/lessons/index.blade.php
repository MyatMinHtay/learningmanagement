<x-adminlayout>
    <div class="container">

        <h1 class="text-center bg-purple mt-3">Lessons</h1>

        <div class="col-12 d-flex justify-content-end my-3">
            <a href="{{ route('lessons.create') }}" class="btn btn-primary mx-2">
                <i class="fa-solid fa-plus mx-1"></i>Add Lesson
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered border-1 table-primary">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Course</th>
                        <th>Title</th>
                        <th>Video</th>
                        <th>Attachment</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lessons as $lesson)
                        <tr>
                            <td>{{ $lesson->id }}</td>
                            <td>{{ $lesson->course->name }}</td>
                            <td>{{ $lesson->title }}</td>
                            
                            <td>
                                @if(Str::contains($lesson->video, 'youtube'))
                                    @php
                                        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^\&\?\/]+)/', $lesson->video, $matches);
                                        $youtubeId = $matches[1] ?? null;
                                    @endphp
                                    @if($youtubeId)
                                        <iframe width="180" height="100" src="https://www.youtube.com/embed/{{ $youtubeId }}" frameborder="0" allowfullscreen></iframe>
                                    @else
                                        Invalid YouTube Link
                                    @endif
                                @elseif(Str::contains($lesson->video, 'vimeo'))
                                    <iframe src="{{ $lesson->video }}" width="180" height="100" frameborder="0" allowfullscreen></iframe>
                                @elseif($lesson->video)
                                    <video width="180" height="100" controls>
                                        <source src="{{ asset('storage/' . $lesson->video) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    N/A
                                @endif
                            </td>

                            <td>
                                @if($lesson->attachment)
                                   
                                    <a href="{{ asset('storage/' . $lesson->attachment) }}" target="_blank">Download</a>

                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('lessons.edit', $lesson->id) }}" class="btn btn-info">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </td>
                            <td>
                                <form action="{{ route('lessons.destroy', $lesson->id) }}" method="POST" onsubmit="return confirm('Delete this lesson?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">No lessons available</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $lessons->links() }}
            </div>
        </div>

    </div>
</x-adminlayout>
