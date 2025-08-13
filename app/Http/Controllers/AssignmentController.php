<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Exception;

class AssignmentController extends Controller
{
    public function showAssignments()
    {
        try {
            $user = auth()->user();

            // If the user is a student
            if ($user->role->role === 'student') {
                $assignments = Assignment::with(['course', 'student'])
                    ->where('student_id', $user->id)
                    ->latest()
                    ->paginate(10);

               

            // If the user is an teacher
            } elseif ($user->role->role === 'teacher') {
                // Get course IDs that the instructor created
                $courseIds = Course::where('created_by', $user->id)->pluck('id');

                $assignments = Assignment::with(['course', 'student'])
                    ->whereIn('course_id', $courseIds)
                    ->latest()
                    ->paginate(10);

            } else {
                // Optional: for other roles, redirect or deny
                return redirect()->back()->with('warning', 'Unauthorized access.');
            }

            return view('admin.assignments.index', compact('assignments'));

        } catch (Exception $e) {
            Log::error('Error in showAssignments: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load assignments. Please try again.');
        }
    }

    public function create()
    {
        try {
            $student = auth()->user();

            $courses = Course::whereHas('students', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })->get();
            
            return view('admin.assignments.create', compact('courses'));

        } catch (Exception $e) {
            Log::error('Error in assignment create: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load assignment creation form. Please try again.');
        }
    }

    /**
     * Handle student assignment submission with file uploads and enrollment validation
     * Validates enrollment, processes multiple file uploads, creates assignment record, notifies teacher
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'assignment_title' => 'required|string|max:255',
                'files' => 'required|array',
                'files.*' => 'file|mimes:pdf,zip|max:20480',
                'note' => 'nullable|string|max:1000',
            ]);

            $student = auth()->user();

            // Verify student is actually enrolled in the selected course
            $isEnrolled = \DB::table('course_students')
                ->where('course_id', $request->course_id)
                ->where('student_id', $student->id)
                ->exists();

            if (!$isEnrolled) {
                return redirect()->back()->with('error', 'You are not enrolled in the selected course.');
            }

            // Check if student has already submitted this specific assignment
            $alreadySubmitted = Assignment::hasStudentSubmittedAssignment(
                $request->course_id,
                $student->id,
                $request->assignment_title
            );

            if ($alreadySubmitted) {
                return redirect()->back()->with('error', 'You have already submitted an assignment with this title for this course.');
            }

            // Process and store multiple assignment files securely
            $paths = [];
            foreach ($request->file('files') as $file) {
                $paths[] = $file->store("assignments/{$request->course_id}", 'public');
            }

            // Create assignment record with pending status
            $assignment = Assignment::create([
                'course_id' => $request->course_id,
                'student_id' => $student->id,
                'assignment_title' => $request->assignment_title,
                'files' => json_encode($paths),
                'status' => 'pending',
                'remark' => $request->note,
            ]);

            // Notify course teacher about new assignment submission
            $course = Course::find($request->course_id);
            if ($course && $course->created_by) {
                Notification::createAssignmentSubmissionNotification(
                    $course->created_by,
                    $student->id,
                    $assignment->id,
                    $course->name
                );
            }

            return redirect()->route('assignments.index')->with('success', 'Assignment submitted successfully.');

        } catch (Exception $e) {
            Log::error('Error in assignment store: ' . $e->getMessage());
            return redirect()->back()->with('warning', 'Failed to submit assignment. Please try again.');
        }
    }

    /**
     * Update assignment status and grade by authorized teacher
     * Validates teacher authorization, updates assignment status, assigns marks and feedback
     */
    public function updateStatus(Request $request, Assignment $assignment)
    {
        try {
            $user = auth()->user();

            // Verify user is a teacher with proper role
            if ($user->role->role !== 'teacher') {
                return redirect()->back()->with('warning', 'Only teacher can update assignment status.');
            }

            // Ensure only the course creator can review assignments for their course
            if ($assignment->course->created_by !== $user->id) {
                return redirect()->back()->with('warning', 'You are not authorized to review this assignment.');
            }

            // Validate status update data
            $request->validate([
                'status' => 'required|in:accepted,rejected',
                'mark' => 'nullable|numeric|min:0|max:100',
                'remark' => 'nullable|string|max:1000',
            ]);

            // Update assignment with teacher's evaluation
            $assignment->update([
                'status' => $request->status,
                'mark' => $request->mark,
                'remark' => $request->remark,
            ]);

            return redirect()->back()->with('success', 'Assignment status updated successfully.');

        } catch (Exception $e) {
            Log::error('Error in updateStatus: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update assignment status. Please try again.');
        }
    }

    public function edit(Assignment $assignment)
    {
        try {
            return view('admin.assignments.edit', compact('assignment'));

        } catch (Exception $e) {
            Log::error('Error in assignment edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load assignment edit form. Please try again.');
        }
    }

    /**
     * Update assignment files and reset status to pending
     * Handles file replacement, deletes old files, resets assignment to pending status
     */
    public function update(Request $request, Assignment $assignment)
    {
        try {
            $request->validate([
                'files' => 'nullable|array',
                'files.*' => 'file|mimes:pdf,zip|max:20480',
                'assignment_title' => 'required|string|max:255',
                'note' => 'nullable|string|max:1000',
            ]);

            // Replace assignment files if new ones are uploaded
            if ($request->hasFile('files')) {
                // Delete existing assignment files from storage
                $existingFiles = json_decode($assignment->files, true) ?? [];
                foreach ($existingFiles as $file) {
                    \Storage::disk('public')->delete($file);
                }

                // Store new assignment files
                $paths = [];
                foreach ($request->file('files') as $file) {
                    $paths[] = $file->store("assignments/{$assignment->course_id}", 'public');
                }

                // Update assignment files field
                $assignment->files = json_encode($paths);
            }

            // Update assignment note and reset status for re-evaluation
            $assignment->remark = $request->note;
            $assignment->assignment_title = $request->assignment_title;
            $assignment->status = 'pending'; // Reset status when updated
            $assignment->save();

            return redirect()->route('assignments.index')->with('success', 'Assignment updated successfully.');

        } catch (Exception $e) {
            Log::error('Error in assignment update: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update assignment. Please try again.');
        }
    }

    public function reportTable(Request $request)
    {
        try {
            $query = Assignment::with(['course', 'course.creator', 'student'])
                ->whereNotNull('assignment_title')
                ->where('assignment_title', '!=', '');
            
            // Filter by teacher/creator
            if ($request->has('teacher') && $request->teacher != '') {
                $query->whereHas('course', function($q) use ($request) {
                    $q->where('created_by', $request->teacher);
                });
            }
            
            // Filter by course
            if ($request->has('course') && $request->course != '') {
                $query->where('course_id', $request->course);
            }
            
            // Filter by status
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }
            
            // Filter by date range
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            $assignments = $query->latest()->paginate(15);
            $teachers = \App\Models\User::whereHas('role', function($q) {
                $q->where('role', 'teacher');
            })->get();
            $courses = Course::all();
            
            return view('admin.reports.assignments', compact('assignments', 'teachers', 'courses'));
            
        } catch (Exception $e) {
            Log::error('Error in assignment report table: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load assignment reports. Please try again.');
        }
    }

    public function submissionReportTable(Request $request)
    {
        try {
            // Get submissions (assignments with student_id not null and files uploaded)
            $query = Assignment::with(['course', 'student', 'course.creator'])
                ->whereNotNull('student_id')
                ->whereNotNull('files')
                ->where('files', '!=', '[]')
                ->where('files', '!=', '');
            
            // Filter by student
            if ($request->has('student') && $request->student != '') {
                $query->where('student_id', $request->student);
            }
            
            // Filter by course
            if ($request->has('course') && $request->course != '') {
                $query->where('course_id', $request->course);
            }
            
            // Filter by status
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }
            
            // Filter by teacher
            if ($request->has('teacher') && $request->teacher != '') {
                $query->whereHas('course', function($q) use ($request) {
                    $q->where('created_by', $request->teacher);
                });
            }
            
            // Filter by date range
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('updated_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('updated_at', '<=', $request->date_to);
            }
            
            $submissions = $query->latest('updated_at')->paginate(15);

           
            $students = \App\Models\User::whereHas('role', function($q) {
                $q->where('role', 'student');
            })->get();

            $teachers = \App\Models\User::whereHas('role', function($q) {
                $q->where('role', 'teacher');
            })->get();
            $courses = Course::all();
            
            return view('admin.reports.assignment-submissions', compact('submissions', 'students', 'teachers', 'courses'));
            
        } catch (Exception $e) {
            Log::error('Error in assignment submission report table: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load assignment submission reports. Please try again.');
        }
    }
}
