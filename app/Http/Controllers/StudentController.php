<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Assignment;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class StudentController extends Controller
{
    /**
     * Display student details with courses, quizzes, and assignments
     */
    public function show(User $student)
    {
        try {
            // Check if the user is actually a student
            if ($student->role->role !== 'student') {
                return redirect()->back()->with('error', 'User is not a student.');
            }

            // Check user permissions
            $user = Auth::user();
            $canView = $user->role->role === 'adminstrator' || 
                      ($user->role->role === 'teacher' && $this->teacherCanViewStudent($user, $student));

            if (!$canView) {
                return redirect()->back()->with('error', 'You do not have permission to view this student details.');
            }

            // Load student with relationships
            $student->load([
                'role',
                'courses' => function ($query) {
                    $query->with(['creator', 'category'])
                          ->orderBy('course_students.created_at', 'desc');
                },
                'quizAttempts' => function ($query) {
                    $query->with(['quiz.course'])
                          ->orderBy('created_at', 'desc');
                },
                'assignments' => function ($query) {
                    $query->with(['course'])
                          ->orderBy('created_at', 'desc');
                }
            ]);

            // dd($student);

            // Calculate statistics
            $stats = [
                'total_courses' => $student->courses->count(),
                'completed_quizzes' => $student->quizAttempts->where('is_completed', true)->count(),
                'submitted_assignments' => $student->assignments->count(),
                'average_quiz_score' => $this->calculateAverageQuizScore($student),
            ];

            return view('admin.students.details', compact('student', 'stats'));

        } catch (Exception $e) {
            Log::error('Error in student details show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load student details. Please try again.');
        }
    }

    /**
     * Display student courses
     */
    public function courses(User $student)
    {
        try {
            // Check permissions
            $user = Auth::user();
            $canView = $user->role->role === 'admin' || 
                      ($user->role->role === 'teacher' && $this->teacherCanViewStudent($user, $student));

            if (!$canView) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $courses = $student->courses()->with(['creator', 'category'])
                              ->orderBy('course_students.created_at', 'desc')
                              ->paginate(10);

            return response()->json([
                'success' => true,
                'courses' => $courses,
                'total' => $courses->total()
            ]);

        } catch (Exception $e) {
            Log::error('Error getting student courses: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to load courses'], 500);
        }
    }

    /**
     * Display student quiz attempts
     */
    public function quizAttempts(User $student)
    {
        try {
            // Check permissions
            $user = Auth::user();
            $canView = $user->role->role === 'admin' || 
                      ($user->role->role === 'teacher' && $this->teacherCanViewStudent($user, $student));

            if (!$canView) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $quizAttempts = $student->quizAttempts()->with(['quiz.course'])
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(10);

            return response()->json([
                'success' => true,
                'quiz_attempts' => $quizAttempts,
                'total' => $quizAttempts->total()
            ]);

        } catch (Exception $e) {
            Log::error('Error getting student quiz attempts: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to load quiz attempts'], 500);
        }
    }

    /**
     * Display student assignments
     */
    public function assignments(User $student)
    {
        try {
            // Check permissions
            $user = Auth::user();
            $canView = $user->role->role === 'admin' || 
                      ($user->role->role === 'teacher' && $this->teacherCanViewStudent($user, $student));

            if (!$canView) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $assignments = $student->assignments()->with(['course'])
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(10);

            return response()->json([
                'success' => true,
                'assignments' => $assignments,
                'total' => $assignments->total()
            ]);

        } catch (Exception $e) {
            Log::error('Error getting student assignments: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to load assignments'], 500);
        }
    }

    /**
     * Check if teacher can view student (they share at least one course)
     */
    private function teacherCanViewStudent(User $teacher, User $student)
    {
        // Get courses created by the teacher
        $teacherCourses = Course::where('created_by', $teacher->id)->pluck('id');
        
        // Check if student is enrolled in any of teacher's courses
        $sharedCourses = $student->courses()->whereIn('courses.id', $teacherCourses)->exists();
        
        return $sharedCourses;
    }

    /**
     * Calculate average quiz score for student
     */
    private function calculateAverageQuizScore(User $student)
    {
        $completedAttempts = $student->quizAttempts->where('is_completed', true);
        
        if ($completedAttempts->count() === 0) {
            return 0;
        }
        
        $totalScore = $completedAttempts->sum('score');
        $totalPossible = $completedAttempts->sum(function ($attempt) {
            return $attempt->quiz->total_marks ?? 0;
        });
        
        return $totalPossible > 0 ? round(($totalScore / $totalPossible) * 100, 2) : 0;
    }
}