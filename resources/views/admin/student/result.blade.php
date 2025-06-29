<x-adminlayout>
    <div class="container mt-5">
        <div class="col-12 d-flex justify-content-start my-5">
            <a href="{{ route('student.quizzes', auth()->user()->id) }}" class="btn btn-success"><i class="fa-solid fa-arrow-left"></i>Back</a>
        </div>
        <h3>{{ $quiz->title }} - Result</h3>
        <h4>Your Grade: {{ $attempt->grade }}</h4>
        <p><strong>Your Score:</strong> {{ $attempt->score }} / {{ $quiz->questions->count() }}</p>

        @foreach($attempt->answers as $answer)
            @php
                $question = $answer->question;
                $correctChoice = $question->choices->firstWhere('is_correct', true);
            @endphp

            <div class="card mb-4">
                <div class="card-body">
                    <h5>{{ $loop->index + 1 }}. {{ $question->text }}</h5>

                    @foreach($question->choices as $choice)
                        <div class="@if($choice->id == $correctChoice->id) text-success @elseif($choice->id == $answer->choice_id) text-danger @endif">
                            <input type="radio" disabled
                                   @checked($choice->id == $answer->choice_id)>
                            {{ $choice->text }}
                            @if($choice->id == $correctChoice->id)
                                <span class="badge bg-success">Correct</span>
                            @elseif($choice->id == $answer->choice_id)
                                <span class="badge bg-danger">Your Answer</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- <a href="{{ route('showlesson' , ['quiz' => $quiz->id]) }}" class="btn btn-primary">Back to Dashboard</a> --}}
    </div>
</x-adminlayout>
