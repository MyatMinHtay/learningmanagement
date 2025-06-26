<x-layout>
    <div class="container mt-5">
        <h4>{{ $quiz->title }}</h4>
        <p>{{ $quiz->description }}</p>

        <form action="{{ url('/quiz/' . $quiz->id . '/submit') }}" method="POST" id="quizForm">
            @csrf
            <input type="hidden" name="attempt_id" value="{{ $attemptId }}">

            @foreach($quiz->questions as $index => $q)
                <div class="mb-4">
                    <h6>{{ $index + 1 }}. {{ $q->text }}</h6>
                    @foreach($q->choices as $choice)
                        <div>
                            <label>
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $choice->id }}">
                                {{ $choice->text }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach

            @if($quiz->is_time_limited)
                <p><strong>Time Remaining:</strong> <span id="timer">{{ $quiz->total_time }}</span> minutes</p>
            @endif

            <button type="submit" class="btn btn-success">Submit Quiz</button>
        </form>
    </div>
</x-layout>

@if($quiz->is_time_limited)
<script>
    let time = {{ $quiz->total_time }} * 60;
    const timerEl = document.getElementById('timer');

    const countdown = setInterval(() => {
        if (time <= 0) {
            clearInterval(countdown);
            document.getElementById('quizForm').submit();
        } else {
            const min = Math.floor(time / 60);
            const sec = time % 60;
            timerEl.textContent = `${min}:${sec.toString().padStart(2, '0')}`;
            time--;
        }
    }, 1000);
</script>
@endif
