@php
    use Illuminate\Support\Str;
@endphp

@if(Str::contains($lesson->video, 'youtube'))
    @php
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^\&\?\/]+)/', $lesson->video, $matches);
        $youtubeId = $matches[1] ?? null;
    @endphp
    @if($youtubeId)
        <iframe width="100%" height="400" src="https://www.youtube.com/embed/{{ $youtubeId }}" frameborder="0" allowfullscreen></iframe>
    @else
        <p>Invalid YouTube Link</p>
    @endif
@elseif(Str::contains($lesson->video, 'vimeo'))
    <iframe src="{{ $lesson->video }}" width="100%" height="400" frameborder="0" allowfullscreen></iframe>
@elseif($lesson->video)
    <video width="100%" height="400" controls>
        <source src="{{ asset('storage/' . $lesson->video) }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
@else
    <p>N/A</p>
@endif
