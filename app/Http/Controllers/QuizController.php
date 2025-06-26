<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Question;
use App\Models\Option;
use App\Models\Choice;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\QuizAttempt;


class QuizController extends Controller
{
     public function index()
    {
        $quizzes = Quiz::with('course')->latest()->paginate(10);
        return view('admin.quiz.index', compact('quizzes'));
    }

    public function adminindex()
    {
        $quizzes = Quiz::with('course')->latest()->paginate(10);
        return view('admin.quiz.index', compact('quizzes'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('admin.quiz.create', compact('courses'));
    }

    public function store(Request $request)
    {

       
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description' => 'nullable|string',
            'total_questions' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'is_time_limited' => 'boolean',
            'total_time' => 'nullable|integer|min:1',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.marks' => 'required|integer|min:1',
            'questions.*.order' => 'required|integer|min:1',
            'questions.*.choices' => 'required|array|min:2',
            'questions.*.choices.*.text' => 'required|string',
            'questions.*.choices.*.is_correct' => 'boolean',
        ]);

        

     

        try {
            DB::beginTransaction();

            // Ensure at least one correct choice per question
            foreach ($validated['questions'] as $index => $question) {
                $hasCorrect = collect($question['choices'])->contains('is_correct', true);
                if (! $hasCorrect) {
                    throw new \Exception("Question #" . ($index + 1) . " must have at least one correct choice.");
                }
            }

            $quiz = Quiz::create([
                'course_id' => $validated['course_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'created_by' => auth()->id() ?? 1,
                'total_marks' => $validated['total_marks'],
                'total_questions' => $validated['total_questions'],
                'total_time' => $validated['is_time_limited'] ? $validated['total_time'] : null,
                'is_time_limited' => $validated['is_time_limited'],
            ]);

            foreach ($validated['questions'] as $q) {
                $question = $quiz->questions()->create([
                    'text' => $q['text'],
                    'marks' => $q['marks'],
                    'order' => $q['order'],
                ]);

                foreach ($q['choices'] as $choice) {
                    $question->choices()->create([
                        'text' => $choice['text'],
                        'is_correct' => $choice['is_correct'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('quizzes.index')->with('success', 'Quiz with questions created.');
        } catch (\Exception $e) {
            DB::rollBack();
            

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Something went wrong while creating the quiz.' . $e->getMessage()]);
        }
    }

    public function edit(Quiz $quiz)
    {
        $courses = Course::all(); 
        return view('admin.quiz.edit', compact('quiz', 'courses'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description' => 'nullable|string',
            'total_questions' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'is_time_limited' => 'boolean',
            'total_time' => 'nullable|integer|min:1',
            'questions' => 'nullable|array',
            'questions.*.text' => 'required|string',
            'questions.*.marks' => 'required|integer',
            'questions.*.order' => 'required|integer',
            'questions.*.choices.*.text' => 'required|string',
            'questions.*.choices.*.is_correct' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($validated, $quiz) {
                $quiz->update([
                    'title' => $validated['title'],
                    'course_id' => $validated['course_id'],
                    'description' => $validated['description'],
                    'total_questions' => $validated['total_questions'],
                    'total_marks' => $validated['total_marks'],
                    'is_time_limited' => $validated['is_time_limited'],
                    'total_time' => $validated['is_time_limited'] ? $validated['total_time'] : null,
                ]);

                // Remove old questions and choices
                $quiz->questions()->each(function ($question) {
                    $question->choices()->delete();
                    $question->delete();
                });

                foreach ($validated['questions'] as $q) {
                    if (!collect($q['choices'])->contains('is_correct', true)) {
                        throw new \Exception("Each question must have at least one correct choice.");
                    }

                    $question = $quiz->questions()->create([
                        'text' => $q['text'],
                        'marks' => $q['marks'],
                        'order' => $q['order'],
                    ]);

                    foreach ($q['choices'] as $choice) {
                        $question->choices()->create([
                            'text' => $choice['text'],
                            'is_correct' => $choice['is_correct'] ?? false,
                        ]);
                    }
                }
            });

            return redirect()->route('quizzes.index')->with('success', 'Quiz updated successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }


    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('quizzes.index')->with('success', 'Quiz deleted successfully.');
    }


    public function start(Quiz $quiz)
{
    $student = auth()->user();

    // Check enrollment
    if (!DB::table('course_students')->where('course_id', $quiz->course_id)->where('student_id', $student->id)->exists()) {
        return back()->with('danger', 'You are not enrolled in this course.');

        
    }

    // Check if already attempted
    $attempt = DB::table('quiz_attempts')
        ->where('quiz_id', $quiz->id)
        ->where('student_id', $student->id)
        ->first();

    if ($attempt && $attempt->is_completed) {
        return redirect()->back()->with([
            'error' => 'You have already completed this quiz.']);
    }

    // Start or resume attempt
    if (!$attempt) {
        $attemptId = DB::table('quiz_attempts')->insertGetId([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    } else {
        $attemptId = $attempt->id;
    }

    $quiz->load('questions.choices');

    return view('quizzes.start', compact('quiz', 'attemptId'));
}


    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'answers' => 'required|array',
        ]);

        $attempt = QuizAttempt::where('id', $request->attempt_id)
            ->where('quiz_id', $quiz->id)
            ->where('student_id', auth()->id())
            ->firstOrFail();

        // Prevent re-submission
        if ($attempt->is_completed) {
            return redirect()->back()->with('error', 'Quiz already submitted.');
        }

        $score = 0;
        $total = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            $selectedChoiceId = $request->input("answers.{$question->id}");

            if ($selectedChoiceId) {
                // Save the answer
                QuizAnswer::create([
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'choice_id' => $selectedChoiceId,
                ]);

                // Compare with correct choice
                $correctChoice = $question->choices()->where('is_correct', true)->first();
                if ($correctChoice && $correctChoice->id == $selectedChoiceId) {
                    $score++;
                }
            }
        }

        // Finalize attempt
        $attempt->update([
            'score' => $score,
            'is_completed' => true,
            'ended_at' => now(),
        ]);

        return redirect()->route('quiz.result', $quiz->id)->with('success', 'Quiz submitted successfully.');
    }

    public function result(Quiz $quiz)
    {
        $attempt = QuizAttempt::with(['answers', 'answers.choice', 'answers.question.choices'])
            ->where('quiz_id', $quiz->id)
            ->where('student_id', auth()->id())
            ->where('is_completed', true)
            ->latest('ended_at')
            ->firstOrFail();

        return view('quizzes.result', compact('quiz', 'attempt'));
    }



}
