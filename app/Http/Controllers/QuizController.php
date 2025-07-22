<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Question;
use App\Models\Option;
use App\Models\Choice;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\QuizAttempt;
use Illuminate\Database\QueryException;
use Exception;



class QuizController extends Controller
{
    public function index(User $student)
    {
        try {
            $quizzes = Quiz::whereHas('attempts', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with('course')
            ->latest()
            ->paginate(10);

            return view('admin.student.quizzes', compact('quizzes', 'student'));

        } catch (Exception $e) {
            Log::error('Error in quiz index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load quizzes. Please try again.');
        }
    }


    public function adminindex()
    {
        try {
            $teacher = auth()->user();

            $quizzes = Quiz::with(['course', 'course.creator'])
                ->where('created_by', $teacher->id)
                ->latest()
                ->paginate(10);

            return view('admin.quiz.index', compact('quizzes'));

        } catch (Exception $e) {
            Log::error('Error in quiz adminindex: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load quizzes. Please try again.');
        }
    }


    public function create()
    {
        try {
            $teacher = auth()->user();
            $courses = Course::where('created_by', $teacher->id)->get();
            return view('admin.quiz.create', compact('courses'));

        } catch (Exception $e) {
            Log::error('Error in quiz create: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load quiz creation form. Please try again.');
        }
    }

    /**
     * Create a new quiz with questions and choices in a transaction
     * Validates quiz data, ensures each question has correct answers, creates quiz structure
     */
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

            // Validate that each question has at least one correct answer
            foreach ($validated['questions'] as $index => $question) {
                $hasCorrect = collect($question['choices'])->contains('is_correct', true);
                if (! $hasCorrect) {
                    throw new \Exception("Question #" . ($index + 1) . " must have at least one correct choice.");
                }
            }

            // Calculate total marks from questions
            $calculatedTotalMarks = collect($validated['questions'])->sum('marks');
            
            // Create quiz with metadata
            $quiz = Quiz::create([
                'course_id' => $validated['course_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'created_by' => auth()->id() ?? 1,
                'total_marks' => $calculatedTotalMarks,
                'total_questions' => $validated['total_questions'],
                'total_time' => $validated['is_time_limited'] ? $validated['total_time'] : null,
                'is_time_limited' => $validated['is_time_limited'],
            ]);

            // Create questions and their multiple choice options
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

            if ($e instanceof QueryException && ($e->errorInfo[1] ?? null) === 1062) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'A quiz already exists for the selected course. Please edit the existing quiz instead.']);
            }
            
            

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Something went wrong while creating the quiz.' . $e->getMessage()]);
        }
    }

    public function edit(Quiz $quiz)
    {
        try {
            $courses = Course::all(); 
            return view('admin.quiz.edit', compact('quiz', 'courses'));

        } catch (Exception $e) {
            Log::error('Error in quiz edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load quiz edit form. Please try again.');
        }
    }

    /**
     * Update existing quiz by rebuilding all questions and choices
     * Removes all existing questions/choices and recreates them with new data
     */
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
                // Calculate total marks from questions
                $calculatedTotalMarks = collect($validated['questions'])->sum('marks');
                
                // Update quiz metadata
                $quiz->update([
                    'title' => $validated['title'],
                    'course_id' => $validated['course_id'],
                    'description' => $validated['description'],
                    'total_questions' => $validated['total_questions'],
                    'total_marks' => $calculatedTotalMarks,
                    'is_time_limited' => $validated['is_time_limited'],
                    'total_time' => $validated['is_time_limited'] ? $validated['total_time'] : null,
                ]);

                // Remove old questions and choices completely
                $quiz->questions()->each(function ($question) {
                    $question->choices()->delete();
                    $question->delete();
                });

                // Recreate all questions and choices with updated data
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
        try {
            $quiz->delete();
            return redirect()->route('quizzes.index')->with('success', 'Quiz deleted successfully.');

        } catch (Exception $e) {
            Log::error('Error in quiz destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to delete quiz. Please try again.');
        }
    }


    /**
     * Start quiz attempt with enrollment and completion validation
     * Validates student enrollment, checks for existing attempts, creates or resumes attempt
     */
    public function start(Course $course,Quiz $quiz)
    {
        try {
            $student = auth()->user();

            // Validate user role - only students can take quizzes
            if ($student->role->role != 'student') {
                return redirect()->back()->with('danger', 'Only students can attempt quizzes.');
            }

            // Check if student is enrolled in the course
            if (!DB::table('course_students')->where('course_id', $course->id)->where('student_id', $student->id)->exists()) {
                return redirect()->back()->with('danger', 'You are not enrolled in this course.');
            }

            // Check if student has already completed this quiz
            $attempt = DB::table('quiz_attempts')
                ->where('quiz_id', $quiz->id)
                ->where('student_id', $student->id)
                ->first();

            if ($attempt && $attempt->is_completed) {
                return redirect()->back()->with('danger','You have already completed this quiz.');
            }

            // Validate quiz belongs to the correct course
            if (!$course->quizzes || $course->quizzes->id !== $quiz->id) {
                return redirect()->back()->with('danger', 'This quiz does not belong to the selected course.');
            }

            // Create new attempt or resume existing incomplete attempt
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

        } catch (Exception $e) {
            Log::error('Error in quiz start: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to start quiz. Please try again.');
        }
    }


    /**
     * Submit quiz answers, calculate score, and assign grade
     * Processes student answers, calculates score percentage, assigns grade based on performance
     */
    public function submit(Request $request, Quiz $quiz)
    {
        try {
            $request->validate([
                'attempt_id' => 'required|exists:quiz_attempts,id',
                'answers' => 'nullable|array',
            ],[
                'answers.required' => 'Please provide the answers.',
            ]);

            $attempt = QuizAttempt::where('id', $request->attempt_id)
                ->where('quiz_id', $quiz->id)
                ->where('student_id', auth()->id())
                ->firstOrFail();

            // Prevent duplicate submissions
            if ($attempt->is_completed) {
                return redirect()->back()->with('error', 'Quiz already submitted.');
            }

            $marksEarned = 0;
            $totalMarks = $quiz->calculateTotalMarks();

            // Process each question and save student answers
            foreach ($quiz->questions as $question) {
                $selectedChoiceId = $request->input("answers.{$question->id}");

                if ($selectedChoiceId) {
                    QuizAnswer::create([
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'choice_id' => $selectedChoiceId,
                    ]);

                    // Check if selected answer is correct and add question marks
                    $correctChoice = $question->choices()->where('is_correct', true)->first();
                    if ($correctChoice && $correctChoice->id == $selectedChoiceId) {
                        $marksEarned += $question->marks;
                    }
                }
            }

            // Calculate percentage and assign grade based on marks earned
            $percentage = $totalMarks > 0 ? ($marksEarned / $totalMarks) * 100 : 0;
            $grade = '';

            if ($percentage < 50) {
                $grade = 'Normal';
                $message = 'You need to work harder to improve your score.';
                $status = 'danger';
            } elseif ($percentage < 80) {
                $grade = 'Good';
                $message = 'You did well, but there is room for improvement.';
                $status = 'warning';
            } else {
                $grade = 'Excellent';
                $message = 'You are doing great! Keep up the good work.';
                $status = 'success';
            }

            // Finalize quiz attempt with marks earned and grade
            $attempt->update([
                'score' => $marksEarned,
                'is_completed' => true,
                'ended_at' => now(),
                'grade' => $grade,
            ]);

            return redirect()->route('quiz.result', $quiz->id)->with($status, $message);

        } catch (Exception $e) {
            Log::error('Error in quiz submit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to submit quiz. Please try again.');
        }
    }

    /**
     * Display quiz results with detailed answer analysis
     * Shows student's completed quiz attempt with correct/incorrect answer details
     */
    public function result(Quiz $quiz)
    {
        try {
            // Load quiz questions for percentage calculation
            $quiz->load('questions');
            
            // Get student's latest completed quiz attempt with all answer details
            $attempt = QuizAttempt::with(['answers', 'answers.choice', 'answers.question', 'answers.question.choices'])
                ->where('quiz_id', $quiz->id)
                ->where('student_id', auth()->id())
                ->where('is_completed', true)
                ->latest('ended_at')
                ->first();

            if (!$attempt) {
                return redirect()->route('student.quizzes', ['student' => auth()->id()])->withErrors(['error' => 'You have not attempted this quiz.']);
            }

            return view('quizzes.result', compact('quiz', 'attempt'));

        } catch (Exception $e) {
            Log::error('Error in quiz result: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load quiz result. Please try again.');
        }
    }

    public function adminresult(Quiz $quiz)
    {
        try {
            // Load quiz questions for percentage calculation
            $quiz->load('questions');
            
            $attempt = QuizAttempt::with(['answers', 'answers.choice', 'answers.question', 'answers.question.choices'])
                ->where('quiz_id', $quiz->id)
                ->where('student_id', auth()->id())
                ->where('is_completed', true)
                ->latest('ended_at')
                ->firstOrFail();

            return view('admin.student.result', compact('quiz', 'attempt'));

        } catch (Exception $e) {
            Log::error('Error in quiz adminresult: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load quiz results. Please try again.');
        }
    }

    public function showStudentQuizzes(){
        try {
            $quizzes = Quiz::with('course')->latest()->paginate(10);
            return view('admin.student.quizzes.index', compact('quizzes'));

        } catch (Exception $e) {
            Log::error('Error in showStudentQuizzes: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load student quizzes. Please try again.');
        }
    }

    public function reportTable(Request $request)
    {
        try {
            $query = Quiz::with(['course', 'course.creator']);
            
            // Filter by teacher/creator
            if ($request->has('teacher') && $request->teacher != '') {
                $query->where('created_by', $request->teacher);
            }
            
            // Filter by course
            if ($request->has('course') && $request->course != '') {
                $query->where('course_id', $request->course);
            }
            
            // Filter by date range
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            $quizzes = $query->latest()->paginate(15);
            $teachers = User::whereHas('role', function($q) {
                $q->where('role', 'teacher');
            })->get();
            $courses = Course::all();
            
            return view('admin.reports.quizzes', compact('quizzes', 'teachers', 'courses'));
            
        } catch (Exception $e) {
            Log::error('Error in quiz report table: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load quiz reports. Please try again.');
        }
    }

    public function submissionReportTable(Request $request)
    {
        try {
            $query = QuizAttempt::with(['user', 'quiz', 'quiz.course']);
            
            // Filter by student
            if ($request->has('student') && $request->student != '') {
                $query->where('student_id', $request->student);
            }
            
            // Filter by quiz
            if ($request->has('quiz') && $request->quiz != '') {
                $query->where('quiz_id', $request->quiz);
            }
            
            // Filter by course
            if ($request->has('course') && $request->course != '') {
                $query->whereHas('quiz', function($q) use ($request) {
                    $q->where('course_id', $request->course);
                });
            }
            
            // Filter by grade range (using percentage calculation)
            if ($request->has('grade_min') && $request->grade_min != '') {
                $query->whereRaw('(score / (SELECT SUM(marks) FROM questions WHERE quiz_id = quiz_attempts.quiz_id)) * 100 >= ?', [$request->grade_min]);
            }
            
            if ($request->has('grade_max') && $request->grade_max != '') {
                $query->whereRaw('(score / (SELECT SUM(marks) FROM questions WHERE quiz_id = quiz_attempts.quiz_id)) * 100 <= ?', [$request->grade_max]);
            }
            
            // Filter by date range
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            $submissions = $query->latest()->paginate(15);
            
            // Calculate average percentage for completed submissions
            $completedSubmissions = $query->where('is_completed', true)->get();
            $averagePercentage = 0;
            
            if ($completedSubmissions->count() > 0) {
                $totalPercentage = 0;
                foreach ($completedSubmissions as $submission) {
                    $totalMarks = $submission->quiz->calculateTotalMarks();
                    if ($totalMarks > 0) {
                        $percentage = ($submission->score / $totalMarks) * 100;
                        $totalPercentage += $percentage;
                    }
                }
                $averagePercentage = $totalPercentage / $completedSubmissions->count();
            }
            
            $students = User::whereHas('role', function($q) {
                $q->where('role', 'student');
            })->get();
            $quizzes = Quiz::with('course')->get();
            $courses = Course::all();
            
            return view('admin.reports.quiz-submissions', compact('submissions', 'students', 'quizzes', 'courses', 'averagePercentage'));
            
        } catch (Exception $e) {
            Log::error('Error in quiz submission report table: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load quiz submission reports. Please try again.');
        }
    }



}
