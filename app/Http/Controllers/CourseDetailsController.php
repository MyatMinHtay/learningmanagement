<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class CourseDetailsController extends Controller
{
    /**
     * Display course details with enrolled students
     */
    public function show(Course $course)
    {
        try {
            // Load course with relationships
            $course->load([
                'creator',
                'category',
                'students' => function ($query) {
                    $query->with('role')
                          ->orderBy('course_students.created_at', 'desc');
                },
                'quizzes',
                'assignments',
                'modules'
            ]);

            // dd($course);

            

         

            

            // Get course statistics
            $stats = [
                'total_students' => $course->students->count(),
                'total_quizzes' => $course->coursequizzes->count(),
                'total_assignments' => $course->assignments->count(),
                'total_modules' => $course->modules->count(),
            ];

            

            // Check user permissions
            $user = Auth::user();
            $canView = $user->role->role === 'adminstrator' || 
                      ($user->role->role === 'teacher' && $course->created_by === $user->id);

            if (!$canView) {
                return redirect()->back()->with('error', 'You do not have permission to view this course details.');
            }

            
            return view('admin.courses.details', compact('course', 'stats'));

        } catch (Exception $e) {
            Log::error('Error in course details show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load course details. Please try again.');
        }
    }

    /**
     * Export course details to PDF
     */
    public function exportPDF(Course $course)
    {
        try {
            // Load course with relationships
            $course->load([
                'creator',
                'category',
                'students' => function ($query) {
                    $query->with('role')
                          ->orderBy('course_students.created_at', 'desc');
                },
                'quizzes',
                'assignments',
                'modules'
            ]);

            // Get course statistics
            $stats = [
                'total_students' => $course->students->count(),
                'total_quizzes' => $course->coursequizzes->count(),
                'total_assignments' => $course->assignments->count(),
                'total_modules' => $course->modules->count(),
            ];

            // Check user permissions
            $user = Auth::user();
            $canExport = $user->role->role === 'adminstrator' || 
                        ($user->role->role === 'teacher' && $course->created_by === $user->id);

            if (!$canExport) {
                return redirect()->back()->with('error', 'You do not have permission to export this course details.');
            }

            // Generate PDF
            $pdf = Pdf::loadView('admin.courses.details-pdf', compact('course', 'stats'));
            $pdf->setPaper('A4', 'portrait');

            $filename = 'course-details-' . $course->id . '-' . date('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);

        } catch (Exception $e) {
            Log::error('Error in course details PDF export: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to export course details. Please try again.');
        }
    }

    /**
     * Get enrolled students data for AJAX requests
     */
    public function getEnrolledStudents(Course $course)
    {
        try {
            // Check user permissions
            $user = Auth::user();
            $canView = $user->role->role === 'adminstrator' || 
                      ($user->role->role === 'teacher' && $course->created_by === $user->id);

            if (!$canView) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $students = $course->students()->with('role')
                             ->orderBy('course_students.created_at', 'desc')
                             ->get();

            return response()->json([
                'success' => true,
                'students' => $students,
                'total' => $students->count()
            ]);

        } catch (Exception $e) {
            Log::error('Error getting enrolled students: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to load students'], 500);
        }
    }
}