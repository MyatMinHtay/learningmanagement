<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\SystemRole;
use App\Models\User;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Assignment;
use App\Models\Lesson;
use App\Models\CourseStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class DashboardController extends Controller
{
    public function show()
    {
        try {
            if (Auth::check()) {
                $users = User::count();
                $systemroles = SystemRole::all();
                
                // Get user permissions from role
                $userPermissions = explode(',', auth()->user()->role->permissions);
                
                // Check permissions instead of roles
                if (in_array('all', $userPermissions) || in_array('admins', $userPermissions)) {
                    return redirect()->route('users');
                } else if (in_array('students', $userPermissions)) {
                    return redirect()->route('students.dashboard');
                } else if (in_array('teachers', $userPermissions)) {
                    return redirect()->route('teachercourses');
                } else {
                    return back()->with('warning', 'Access denied! You do not have the required permissions to access this page.');
                }
            } else {
                return redirect('/login')->with('warning', 'access deined! Only Admin Can Access This Page');
            }

        } catch (Exception $e) {
            Log::error('Error in dashboard show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load dashboard. Please try again.');
        }
    }

    public function showStudentDashboard()
    {
        try {
            $user = Auth::user();
            return redirect()->route('student.courses', $user->id);

        } catch (Exception $e) {
            Log::error('Error in showStudentDashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load student dashboard. Please try again.');
        }
    }

    public function showAnalytics(){
        
        try {
            // Basic counts
            $totalUsers = User::count();
            $totalCourses = Course::count();
            $totalQuizzes = Quiz::count();
            $totalAssignments = Assignment::whereNotNull('assignment_title')->distinct('assignment_title')->count();
            $totalLessons = Lesson::count();
            
             
            // User distribution by role
            $usersByRole = User::join('system_roles', 'users.role_id', '=', 'system_roles.id')
                ->select('system_roles.role as role_name', DB::raw('count(*) as count'))
                ->groupBy('system_roles.role')
                ->get();

              
            
            $adminCount = $usersByRole->where('role_name', 'adminstrator')->first()->count ?? 0;
            $teacherCount = $usersByRole->where('role_name', 'teacher')->first()->count ?? 0;
            $studentCount = $usersByRole->where('role_name', 'student')->first()->count ?? 0;
            
            // Course enrollment statistics
            $totalEnrollments = CourseStudent::count();
            $avgEnrollmentPerCourse = $totalCourses > 0 ? round($totalEnrollments / $totalCourses, 1) : 0;
             
            // Top performing courses with enrollment count
            $topCourses = Course::withCount('students')
                ->with(['creator'])
                ->orderBy('students_count', 'desc')
                ->limit(10)
                ->get()
                ->map(function($course) {
                    // Calculate completion rate based on quiz attempts
                    $enrolledStudents = $course->students_count;
                    $completedQuizzes = QuizAttempt::whereHas('quiz', function($query) use ($course) {
                        $query->where('course_id', $course->id);
                    })->where('is_completed', true)->distinct('student_id')->count();
                    
                    $course->completion_rate = $enrolledStudents > 0 ? round(($completedQuizzes / $enrolledStudents) * 100, 1) : 0;
                    
                    // Calculate average quiz score for this course
                    $avgScore = QuizAttempt::whereHas('quiz', function($query) use ($course) {
                        $query->where('course_id', $course->id);
                    })->where('is_completed', true)->avg('score');
                    
                    $course->avg_quiz_score = $avgScore ? round($avgScore, 1) : 0;
                    
                    return $course;
                });

              
            
            // Recent quiz submissions
            $recentQuizSubmissions = QuizAttempt::with(['user', 'quiz'])
                ->where('is_completed', true)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Recent assignment submissions
            $recentAssignmentSubmissions = Assignment::with(['user', 'course'])
                ->whereNotNull('student_id')
                ->whereNotNull('files')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Monthly registration data for chart
            $monthlyRegistrations = User::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
            
            // Course creation data for chart
            $monthlyCourses = Course::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
            
            // Quiz performance data
            $quizPerformance = QuizAttempt::select(
                DB::raw('AVG(score) as avg_score'),
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(CASE WHEN score >= 70 THEN 1 ELSE 0 END) as passed_attempts')
            )->where('is_completed', true)->first();
            
            $passRate = $quizPerformance->total_attempts > 0 ? 
                round(($quizPerformance->passed_attempts / $quizPerformance->total_attempts) * 100, 1) : 0;
            
            // Activity statistics
            $activeUsersToday = User::whereDate('updated_at', today())->count();
            $newRegistrationsThisWeek = User::where('created_at', '>=', now()->subWeek())->count();
            $coursesCreatedThisMonth = Course::where('created_at', '>=', now()->subMonth())->count();
            
            // Assignment submission rate
            $totalAssignmentTasks = Assignment::whereNotNull('assignment_title')
                ->distinct('assignment_title', 'course_id')
                ->count();
            $submittedAssignments = Assignment::whereNotNull('student_id')
                ->whereNotNull('files')
                ->count();
            $assignmentSubmissionRate = $totalAssignmentTasks > 0 ? 
                round(($submittedAssignments / $totalAssignmentTasks) * 100, 1) : 0;
            
            return view('admin.analytics.index', compact(
                'totalUsers', 'totalCourses', 'totalQuizzes', 'totalAssignments', 'totalLessons',
                'adminCount', 'teacherCount', 'studentCount',
                'totalEnrollments', 'avgEnrollmentPerCourse',
                'topCourses', 'recentQuizSubmissions', 'recentAssignmentSubmissions',
                'monthlyRegistrations', 'monthlyCourses',
                'quizPerformance', 'passRate',
                'activeUsersToday', 'newRegistrationsThisWeek', 'coursesCreatedThisMonth',
                'assignmentSubmissionRate'
            ));
            
        } catch (Exception $e) {
            Log::error('Error in analytics dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load analytics dashboard. Please try again.');
        }
    }
}
