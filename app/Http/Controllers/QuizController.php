<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


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
}
