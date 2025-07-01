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

    public function store(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'files' => 'required|array',
                'files.*' => 'file|mimes:pdf,zip|max:20480',
                'note' => 'nullable|string|max:1000',
            ]);

            $student = auth()->user();

            // Optional: confirm student is actually enrolled in this course
            $isEnrolled = \DB::table('course_students')
                ->where('course_id', $request->course_id)
                ->where('student_id', $student->id)
                ->exists();

            if (!$isEnrolled) {
                return redirect()->back()->with('error', 'You are not enrolled in the selected course.');
            }

            $paths = [];
            foreach ($request->file('files') as $file) {
                $paths[] = $file->store("assignments/{$request->course_id}", 'public');
            }

            $assignment = Assignment::create([
                'course_id' => $request->course_id,
                'student_id' => $student->id,
                'files' => json_encode($paths),
                'status' => 'pending',
                'remark' => $request->note,
            ]);

            // Create notification for course creator
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

    public function updateStatus(Request $request, Assignment $assignment)
    {
        try {
            $user = auth()->user();

            // Optional: check if user is instructor of the course linked to the assignment
            if ($user->role->role !== 'teacher') {
                return redirect()->back()->with('warning', 'Only teacher can update assignment status.');
            }

            // Ensure this instructor created the course
            if ($assignment->course->created_by !== $user->id) {
                return redirect()->back()->with('warning', 'You are not authorized to review this assignment.');
            }

            // Validate input
            $request->validate([
                'status' => 'required|in:accepted,rejected',
                'mark' => 'nullable|numeric|min:0|max:100',
                'remark' => 'nullable|string|max:1000',
            ]);

            // Update assignment status and remark
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

    public function update(Request $request, Assignment $assignment)
    {
        try {
            $request->validate([
                'files' => 'nullable|array',
                'files.*' => 'file|mimes:pdf,zip|max:20480',
                'note' => 'nullable|string|max:1000',
            ]);

            // If new files are uploaded, delete old files first
            if ($request->hasFile('files')) {
                // Delete existing files
                $existingFiles = json_decode($assignment->files, true) ?? [];
                foreach ($existingFiles as $file) {
                    \Storage::disk('public')->delete($file);
                }

                // Store new files
                $paths = [];
                foreach ($request->file('files') as $file) {
                    $paths[] = $file->store("assignments/{$assignment->course_id}", 'public');
                }

                // Update assignment files field
                $assignment->files = json_encode($paths);
            }

            // Update other fields
            $assignment->remark = $request->note;
            $assignment->status = 'pending'; // Reset status when updated
            $assignment->save();

            return redirect()->route('assignments.index')->with('success', 'Assignment updated successfully.');

        } catch (Exception $e) {
            Log::error('Error in assignment update: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update assignment. Please try again.');
        }
    }
}
