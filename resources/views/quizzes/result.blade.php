<x-layout>
    @php
        // Calculate percentage based on marks earned vs total marks
        $totalQuestions = $quiz->questions->count();
        $totalMarks = $quiz->calculateTotalMarks();
        $marksEarned = $attempt->score ?? 0;
        $percentage = $totalMarks > 0 ? ($marksEarned / $totalMarks) * 100 : 0;
        $displayPercentage = round($percentage, 1);
        $gradeValue = $displayPercentage;
        $progressWidth = min(max($gradeValue, 0), 100);
    @endphp
    
    <div class="container mt-5">
        <!-- Header Section with Results -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 result-header">
                    <div class="card-body text-center p-5">
                        <div class="result-icon mb-4">
                            @if($displayPercentage >= 80)
                                <i class="fas fa-trophy text-warning" style="font-size: 4rem;"></i>
                            @elseif($displayPercentage >= 60)
                                <i class="fas fa-medal text-info" style="font-size: 4rem;"></i>
                            @else
                                <i class="fas fa-chart-line text-secondary" style="font-size: 4rem;"></i>
                            @endif
                        </div>
                        <h2 class="mb-3 text-primary">{{ $quiz->title }}</h2>
                        <h1 class="display-4 mb-4 
                            @if($displayPercentage >= 80) text-success 
                            @elseif($displayPercentage >= 60) text-warning 
                            @else text-danger 
                            @endif">
                            {{ $displayPercentage }}%
                        </h1>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <h5 class="text-muted mb-2">Your Score</h5>
                                    <h3 class="text-primary mb-0">{{ $marksEarned }} / {{ $totalMarks }} marks</h3>
                                    <small class="text-muted">{{ $attempt->answers->where('choice.is_correct', true)->count() }} / {{ $totalQuestions }} questions correct</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <h5 class="text-muted mb-2">Grade Status</h5>
                                    <h3 class="mb-0 
                                        @if($displayPercentage >= 80) text-success 
                                        @elseif($displayPercentage >= 60) text-warning 
                                        @else text-danger 
                                        @endif">
                                        @if($displayPercentage >= 80) Excellent! 
                                        @elseif($displayPercentage >= 60) Good Job! 
                                        @else Keep Trying! 
                                        @endif
                                    </h3>
                                </div>
                            </div>
                        </div>
                        
                                        <!-- Progress Bar -->
        <div class="mt-4">
            <div class="progress" style="height: 12px;">
                <div class="progress-bar 
                    @if($gradeValue >= 80) bg-success 
                    @elseif($gradeValue >= 60) bg-warning 
                    @else bg-danger 
                    @endif" 
                    role="progressbar" 
                    style="width: {{ $progressWidth }}%"
                    aria-valuenow="{{ $gradeValue }}" 
                    aria-valuemin="0" 
                    aria-valuemax="100">
                </div>
            </div>
            <small class="text-muted mt-2 d-block">{{ $gradeValue }}%</small>
        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Review Section -->
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="mb-4">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-clipboard-list text-primary me-2"></i>
                        Detailed Review
                    </h4>
                </div>

                @foreach($quiz->questions as $question)
                    @php
                        // Find if user answered this question
                        $userAnswer = $attempt->answers->where('question_id', $question->id)->first();
                        $correctChoice = $question->choices->firstWhere('is_correct', true);
                        $isAnswered = $userAnswer !== null;
                        $isCorrect = $isAnswered && ($userAnswer->choice_id == $correctChoice->id);
                        $questionMarks = $question->marks;
                        $earnedMarks = $isCorrect ? $questionMarks : 0;
                    @endphp

                    <div class="card mb-4 shadow-sm border-0 question-card">
                        <div class="card-header bg-light border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-dark">
                                    <span class="badge bg-primary me-2">{{ $loop->index + 1 }}</span>
                                    {{ $question->text }}
                                    <small class="text-muted ms-2">({{ $questionMarks }} marks)</small>
                                </h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-info px-2 py-1">
                                        {{ $earnedMarks }}/{{ $questionMarks }}
                                    </span>
                                    @if($isAnswered)
                                        @if($isCorrect)
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-check me-1"></i>
                                                Correct
                                            </span>
                                        @else
                                            <span class="badge bg-danger px-3 py-2">
                                                <i class="fas fa-times me-1"></i>
                                                Incorrect
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary px-3 py-2">
                                            <i class="fas fa-question me-1"></i>
                                            Not Answered
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body p-4">
                            @foreach($question->choices as $choice)
                                @if($isAnswered)
                                    {{-- Show answered question with correct answer highlighted --}}
                                    <div class="choice-option mb-3 p-3 rounded
                                        @if($choice->id == $correctChoice->id) bg-success-subtle border-success
                                        @elseif($choice->id == $userAnswer->choice_id && !$isCorrect) bg-danger-subtle border-danger
                                        @else bg-light border-light
                                        @endif">
                                        
                                        <div class="d-flex align-items-center">
                                            <div class="choice-radio me-3">
                                                <input type="radio" disabled
                                                       @checked($choice->id == $userAnswer->choice_id)
                                                       class="form-check-input">
                                            </div>
                                            
                                            <div class="choice-text flex-grow-1">
                                                <span class="@if($choice->id == $correctChoice->id) text-success fw-bold
                                                          @elseif($choice->id == $userAnswer->choice_id && !$isCorrect) text-danger fw-bold
                                                          @else text-dark
                                                          @endif">
                                                    {{ $choice->text }}
                                                </span>
                                            </div>
                                            
                                            <div class="choice-badges">
                                                @if($choice->id == $correctChoice->id)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>
                                                        Correct Answer
                                                    </span>
                                                @elseif($choice->id == $userAnswer->choice_id && !$isCorrect)
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>
                                                        Your Answer
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Show unanswered question without revealing correct answer --}}
                                    <div class="choice-option mb-3 p-3 rounded bg-light border-light">
                                        <div class="d-flex align-items-center">
                                            <div class="choice-radio me-3">
                                                <input type="radio" disabled class="form-check-input">
                                            </div>
                                            
                                            <div class="choice-text flex-grow-1">
                                                <span class="text-muted">{{ $choice->text }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            
                            @if(!$isAnswered)
                                <div class="mt-3 p-3 bg-warning-subtle border border-warning rounded">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                        <small class="text-muted">
                                            <strong>You did not answer this question.</strong> 
                                            
                                        </small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Action Buttons -->
                <div class="text-center mt-5 mb-4">
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('showlesson', ['course' => $quiz->course_id]) }}" class="btn btn-primary btn-lg px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Course
                        </a>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .result-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .result-header .card-body {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            color: #333;
        }
        
        .stat-card {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 10px 0;
        }
        
        .question-card {
            transition: all 0.3s ease;
            border-left: 4px solid #007bff;
        }
        
        .question-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .choice-option {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .choice-option:hover {
            background-color: #f8f9fa !important;
        }
        
        .bg-success-subtle {
            background-color: #d1edff !important;
            border-color: #28a745 !important;
        }
        
        .bg-danger-subtle {
            background-color: #f8d7da !important;
            border-color: #dc3545 !important;
        }
        
        .bg-warning-subtle {
            background-color: #fff3cd !important;
            border-color: #ffc107 !important;
        }
        
        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }
        
        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }
        
        .result-icon {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .btn-lg {
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .card {
            border-radius: 15px;
        }
        
        .badge {
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .result-header .card-body {
                padding: 2rem !important;
            }
            
            .display-4 {
                font-size: 2.5rem;
            }
            
            .d-flex.gap-3 {
                flex-direction: column;
            }
            
            .btn-lg {
                margin-bottom: 10px;
            }
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate progress bar on page load
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                const targetWidth = progressBar.style.width;
                progressBar.style.width = '0%';
                
                setTimeout(() => {
                    progressBar.style.width = targetWidth;
                }, 500);
            }
        });
    </script>
</x-layout>
